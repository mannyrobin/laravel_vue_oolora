<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DanTheCoder\SaaSCore\Subscription\Models\Plan;

class FrontendController extends Controller
{

    public function index(Request $request)
    {        
    	
    	return redirect('/dashboard');
    	
        $plans = Plan::active()->with('features')->orderBy('sort_order', 'asc')->get();

        return view('frontend.home', compact('plans') );
    }
    
}