<?php

namespace DanTheCoder\SaaSCore\Subscription\Http\ApiControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DanTheCoder\SaaSCore\Subscription\Models\Plan;

class PlanController extends Controller
{

    /**
     * Return all active plans
     */
    public function index()
    {
        try {	
        	return Plan::active()->with('features')->orderBy('sort_order', 'asc')->get();
        } 
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Return a plan by ID
     */
    public function show($id)
    {
        try {
            return Plan::active()->with('features')->findOrFail($id);
        } 
        catch (Exception $e) {
            return response(['message' => $e->getMessage()], 404);
        }
    }

}
