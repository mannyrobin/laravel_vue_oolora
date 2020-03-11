<?php

namespace DanTheCoder\SaaSCore\Subscription\Http\ApiControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Subscription\Models\Invoice;
use DanTheCoder\SaaSCore\Subscription\Resources\Invoice as InvoiceResource;

class InvoiceController extends Controller
{

	/**
	 * Return all the invoices for the user
	 */
    public function index(Request $request)
    {
		try {  

            // Set order by
            $orderBy = ['created_at', 'desc'];

            if ( $request->order_by )
                $orderBy = explode('|', $request->order_by);


	    	return InvoiceResource::collection( Invoice::whereUser()
                ->with('refund')
	    		->orderBy($orderBy[0], $orderBy[1])
	            ->paginate($request->per_page)
	        );
        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Return a specific invoice for a user
     * @param  Invoice ID  $id
     */
    public function show($id)
    {
        try {
	    	$invoice = new InvoiceResource( Invoice::whereUser()->with('user')->where('id', $id)->with('lines')->firstOrFail() );
            
            return response($invoice, 200);
        
        } 
        catch (Exception $e) {
            return response(['message' => $e->getMessage()], 404);
        }
    }

}
