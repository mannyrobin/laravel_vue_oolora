<?php

namespace DanTheCoder\SaaSCore\Admin\Http\ApiControllers;

use Hash;
use Image;
use Stripe;
use Storage;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use DanTheCoder\SaaSCore\Account\Requests\UserRequest;
use DanTheCoder\SaaSCore\Admin\Notifications\AccountCreated;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;
use DanTheCoder\SaaSCore\Admin\Notifications\AccountReactivated;
use DanTheCoder\SaaSCore\Admin\Notifications\AccountDeactivated;
use DanTheCoder\SaaSCore\Account\Resources\User as UserResource;

class UserController extends Controller
{

    private $paypalApiContext;
    private $paypalClientId;
    private $paypalSecret;


    public function __construct()
    {
        // Detect if we are running in live mode or sandbox
        if ( config('services.paypal.settings.mode') === 'live' ) {
            $this->paypalClientId   = config('services.paypal.live_client_id');
            $this->paypalSecret     = config('services.paypal.live_secret');
        } else {
            $this->paypalClientId   = config('services.paypal.sandbox_client_id');
            $this->paypalSecret     = config('services.paypal.sandbox_secret');
        }
        
        // Set the Paypal API Context/Credentials
        $this->paypalApiContext = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->paypalClientId, $this->paypalSecret));
        $this->paypalApiContext->setConfig(config('services.paypal.settings'));
    }
    

    /**
	 * Return all users
	 */
    public function index(Request $request)
    {
		try {  
            
            // Set order by
            $orderBy = ['created_at', 'desc'];

            if ( $request->order_by )
                $orderBy = explode('|', $request->order_by);

	    	return UserResource::collection( User::withTrashed()
                ->with(['roles', 'subscribable'])
                ->orderBy($orderBy[0], $orderBy[1])
	            ->paginate($request->per_page)
	        );
        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }		
    }


    /**
     * Update a user details by admin
     */
    public function update(UserRequest $request)
    {
        // Validate the request
        $request->validated();

        try {

            $user = User::withTrashed()->find($request->id);

            $user->fill([
                'name'      => $request->name,
                'email'     => $request->email,
                'timezone'  => $request->timezone
            ])->save();


            // Add roles
            if ( $request->role_admin ) {
                $user->assignRole('Administrator');
            } else {
                $user->removeRole('Administrator');
            }


            return response(['message' => $request->name . ' account details were successfully updated'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Admin create a new user
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name'      => 'bail|required|string|max:255',
            'email'     => 'bail|required|string|email|max:255|unique:users',
            'password'  => 'bail|required|string|min:6|confirmed',
        ]);
        

        try {

            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();


            // Add roles
            if ( $request->role_admin )
                $user->assignRole('Administrator');


            // Send welcome email with credentials
            $account = [
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => $request->password,
                'action_url'    => config('app.url') . '/login'
            ];

            $user->notify( new AccountCreated($account) );


            // Send email verification email
            $user->sendEmailVerificationNotification();


            return response(['message' => 'User account was successfully created'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
    * Close a user account by admin
    */
    public function destroy(Request $request, $id)
    {

        // Prevent admin from closing their own account
        if ( $request->user()->id == $id )
            return response(['message' => 'You cannot deactivate your own account'], 422);


        try {

            $user = User::withTrashed()->find($id);

            // Check if the user has any active membership
            if ( $user->subscribed('membership') ) {

                // Cancel the subscription from the payment gateway
                $userSubscription = PlanSubscription::where( 'subscribable_id', $user->id )->latest()->first();
                
                if ( $userSubscription->gateway_subscription_id ) {
         

                    // Cancel PayPal subscription
                    if ( $user->paypal_id ) {
                                
                        // Create a PayPal Agreement State Descriptor, explaining the reason to cancel.
                        $agreementStateDescriptor = new \PayPal\Api\AgreementStateDescriptor();
                        $agreementStateDescriptor->setNote("Subscription canceled by administrator");

                        $agreement = \PayPal\Api\Agreement::get( $userSubscription->gateway_subscription_id, $this->paypalApiContext );
                        $agreement->cancel($agreementStateDescriptor, $this->paypalApiContext);

                        // Update PayPal DB info
                        $user->update(['paypal_id' => null]);

                    }


                    // Cancel Stripe subscription immediately and remove card
                    if ( $user->stripe_id ) {

                        Stripe::subscriptions()->cancel( $user->stripe_id, $userSubscription->gateway_subscription_id);

                        Stripe::cards()->delete( $user->stripe_id, $user->stripe_card );

                        // Update Stripe DB info
                        $user->update(['stripe_id' => null, 'stripe_card' => null]);
                    }

                }

                // Cancel DB membership subscription immediately 
                $user->subscription('membership')->cancel(true);

            }

            // Remove the account
            $user->delete();


            // Send notification
            $user->notify( new AccountDeactivated() );

            return response(['message' => 'The user account has been deactivated'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Restore a user that was deleted
     */
    public function restoreAccount($id)
    {
        try {

            $user = User::withTrashed()->find($id);

            $user->restore();

            // Send notification
            $user->notify( new AccountReactivated() );

            return response(['message' => 'User account has been reactivated'], 200);
        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }       
    }


    /**
     * Change a user avatar by admin
     */
    public function changeAvatar(Request $request)
    {
        // Validation the file upload
        $request->validate([
            'file' => 'bail|required|file|image|mimes:jpeg,png|max:2048'
        ]);


        // Check if file exist
        if( ! $request->hasFile('file') )
            return response(['message' => 'No avatar picture was selected'], 422);

        
        $file = $request->file('file');
        $avatarPath = $file->hashName('avatars');


        // Is the file valid
        if( ! $file->isValid() )
            return response(['message' => 'The file is not valid'], 422);


        try {

            // Resize the image
            $image = Image::make($file);

            $image->fit(250, 250, function ($constraint) {
                $constraint->aspectRatio();
            });


            // Save the file to the storage
            Storage::put( $avatarPath, (string) $image->encode() );


            // Find the user
            $user = User::withTrashed()->find($request->id);


            // Delete the old avatar if it isn't the default one
            if ( $user->avatar != 'avatars/default.png' )
                Storage::disk('public')->delete( $user->avatar );


            // Update user table
            $user->fill([
                'avatar' => $avatarPath
            ])->save();


            return response(['avatar' => asset( Storage::url($avatarPath) ), 'message' => 'The user avatar was updated successfully'], 200);
            
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

}