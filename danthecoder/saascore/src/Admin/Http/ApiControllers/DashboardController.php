<?php

namespace DanTheCoder\SaaSCore\Admin\Http\ApiControllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Subscription\Models\Invoice;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;

class DashboardController extends Controller
{


	/**
	 * Return all payments
	 */
    public function index(Request $request)
    {
		try {

            $totalUsers = User::count();
            $activeSubscribers = PlanSubscription::where('canceled_immediately', null)->count();
            $payments = Invoice::doesntHave('refund')->sum('total');
            $refunds = Invoice::whereHas('refund')->sum('total');

            $result = [
                'total_users'           => $totalUsers,
                'active_subscribers'    => $activeSubscribers,
                'revenue'               => $payments,
                'refunds'               => $refunds
            ];

            return response($result, 200);

        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

}
