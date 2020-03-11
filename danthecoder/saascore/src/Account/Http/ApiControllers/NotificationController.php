<?php

namespace DanTheCoder\SaaSCore\Account\Http\ApiControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Account\Resources\Notification as NotificationResource;

class NotificationController extends Controller
{

	/**
	 * Return a user read or unread notifications
	 */
    public function index(Request $request)
    {
		try {
    		if ( $request->unread === "true" )
    			$notifications = $request->user()->unreadNotifications()->paginate($request->per_page);
			else
				$notifications = $request->user()->readNotifications()->paginate($request->per_page);

	    	return NotificationResource::collection($notifications);
		}
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


	/**
	 * Return the total unread notifications for a given user
	 */
    public function count(Request $request)
    {
    	return $request->user()->unreadNotifications->count();
	}


	/**
	 * Mark all the user notifications as read
	 */
    public function markAllAsRead(Request $request)
    {
    	return $request->user()->unreadNotifications->map(function($n) {
    		$n->markAsRead();
    	});
	}


	/**
	 * Mark a notification as read
	 */
    public function destroy(Request $request, $id)
    {
    	return $request->user()->unreadNotifications->find($id)->markAsRead();
    }

}