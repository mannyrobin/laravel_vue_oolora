<?php

namespace DanTheCoder\SaaSCore\Account\Http\ApiControllers;

use Hash;
use Image;
use Stripe;
use Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Account\Requests\UserRequest;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;
use DanTheCoder\SaaSCore\Account\Notifications\PasswordChangeConfirmation;
use DanTheCoder\SaaSCore\Account\Notifications\AccountDeletionConfirmation;

class UserController extends Controller
{

	/**
	 * Return user data based on current authenticated user
	 */
    public function index(Request $request)
    {
        // set the user avatar to full url
        $request->user()->avatar = asset( Storage::url($request->user()->avatar) );

		return $request->user();
    }


    /*
     * Update user details
     */
    public function update(UserRequest $request)
    {

    	// Validate the request
    	$request->validated();


    	// Update account details
        try {

	    	$request->user()->fill([
	            'name' 		=> $request->name,
	            'email' 	=> $request->email,
	            'timezone' 	=> $request->timezone
	        ])->save();

            return response(['message' => 'Your account settings were successfully updated.'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
    * Users deleting their own account
    */
    public function destroy(Request $request, $id)
    {
        try {

            // Cancel PayPal subscription
            /*if ( $request->user()->paypal_id ) {

            }*/


            // Cancel Stripe subscription immediately and remove card
            if ( $request->user()->stripe_id ) {

                $userSubscription = PlanSubscription::where( 'subscribable_id', $request->user()->id )->latest()->first();

                Stripe::subscriptions()->cancel( $request->user()->stripe_id, $userSubscription->gateway_subscription_id);

                Stripe::cards()->delete( $request->user()->stripe_id, $request->user()->stripe_card );

                // Update card info
                $request->user()->update([ 'stripe_id' => null, 'stripe_card' => null ]);
            }


            // Cancel membership subscription immediately 
            $request->user()->subscription('membership')->cancel(true);


            // Remove the account
            $request->user()->delete();


            // Send notification
            $request->user()->notify( new AccountDeletionConfirmation() );

            return response(['message' => 'Your account has been closed.'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Change a user password
     */
    public function password(UserRequest $request)
    {
		// Validate the request
    	$request->validated();


    	// Change the password
		try {

	    	$request->user()->fill([
	            'password' => Hash::make($request->password)
	        ])->save();


            // Send notification
            $request->user()->notify( new PasswordChangeConfirmation() );

            return response(['message' => 'Your account password has been successfully changed.'], 200);
        
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    public function avatar(Request $request)
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

            
            // Delete the old avatar if it isn't the default one
            $userAvatar = auth()->user()->avatar;
            if ( $userAvatar != 'avatars/default.png' )
                Storage::disk('public')->delete( $userAvatar );


            // Update user table
            auth()->user()->fill([
                'avatar' => $avatarPath
            ])->save();

            return response(['avatar' => asset( Storage::url($avatarPath) ), 'message' => 'Your avatar was updated successfully.'], 200);
            
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

}
