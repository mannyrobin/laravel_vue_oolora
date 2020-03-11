<?php

/**
 * Admin Web Routes
 */
Route::middleware(['web', 'auth', 'admin'])->group(function () {

	Route::any('admin/{any?}', function () {
		return view('layouts/vue-admin');
	})->where('any', '[\/\w\.-]*');

});


/**
 * Admin API Routes
 */
Route::prefix('api/admin')->middleware(['auth:api', 'admin'])->namespace('DanTheCoder\SaaSCore\Admin\Http\ApiControllers')->group(function () {
	
	Route::post('users/change-avatar', 'UserController@changeAvatar');
	Route::put('users/restore-account/{id}', 'UserController@restoreAccount');
	Route::resource('users', 'UserController')->only([
		'index', 'store', 'update', 'destroy'
	]);

	Route::post('settings/upload-image', 'SettingController@uploadImage');
	Route::post('settings/send-testmail', 'SettingController@sendTestMail');
	Route::put('settings/plan-onboarding/{id}', 'SettingController@planOnboarding');
	Route::resource('settings', 'SettingController')->only([
		'index', 'store'
	]);

	Route::post('payments/refund', 'PaymentController@refundPayment');
	Route::resource('payments', 'PaymentController')->only([
		'index', 'show'
	]);

	Route::put('plans/update-features', 'PlanController@updateFeatures');
	Route::put('plans/enable/{id}', 'PlanController@enablePlan');
	Route::resource('plans', 'PlanController')->only([
		'index', 'store', 'update', 'destroy'
	]);

	Route::resource('dashboard', 'DashboardController')->only([
		'index'
	]);

});





/**
 * User Web Routes
 */
Route::middleware(['web'])->group(function () {

	// Logout
	Route::get('logout', 'App\Http\Controllers\Auth\LoginController@logout');

	// Webhooks
	Route::namespace('DanTheCoder\SaaSCore\Subscription\Http\Controllers')->group(function () {
		Route::post('webhook/stripe', 'WebhookStripeController@handleWebhook');
		Route::post('webhook/paypal', 'WebhookPaypalController@handleWebhook');
	});


	// Authenticated Web Routes
	Route::middleware(['auth'])->group(function () {

		// Account Vue routes
		Route::any('account/{any?}', function () {
			return view('layouts/vue');
		})->where('any', '[\/\w\.-]*');


		// Billing Vue routes
		Route::any('billing/{any?}', function () {
			return view('layouts/vue');
		})->where('any', '[\/\w\.-]*');


		// Billing
		Route::namespace('DanTheCoder\SaaSCore\Subscription\Http\Controllers')->group(function () {
			Route::get('/pdf/invoice/{id}', 'PdfInvoiceController');
			Route::get('paypal/subscribe', 'PaypalController@subscribe');
			Route::get('paypal/return', 'PaypalController@return');
		});

	});

});


/**
 * User API Routes
 */
Route::prefix('api')->middleware(['auth:api'])->group(function () {


	// Account
	Route::namespace('DanTheCoder\SaaSCore\Account\Http\ApiControllers')->group(function () {

		Route::put('user/password', 'UserController@password');
		Route::post('user/avatar', 'UserController@avatar');
		
		Route::resource('user', 'UserController')->only([
		    'index', 'update', 'destroy'
		]);

		Route::get('notifications/count', 'NotificationController@count');
		Route::delete('notifications/mark-all', 'NotificationController@markAllAsRead');
		Route::resource('notifications', 'NotificationController')->only([
		    'index', 'destroy'
		]);
	});

	
	// Billing
	Route::namespace('DanTheCoder\SaaSCore\Subscription\Http\ApiControllers')->group(function () {

		Route::get('subscription/usage', 'SubscriptionController@usage');
		Route::post('subscription/cancel', 'SubscriptionController@cancel');
		Route::post('subscription/reactivate', 'SubscriptionController@reactivate');
		Route::resource('subscription', 'SubscriptionController')->only([
		    'index'
		]);

		Route::post('stripe/subscribe', 'StripeGatwayController@subscribe');
		Route::post('stripe/update', 'StripeGatwayController@updateSubscription');
		Route::post('stripe/update-card', 'StripeGatwayController@updateCard');
		
		Route::resource('plans', 'PlanController')->only([
		    'index', 'show'
		]);

		Route::resource('invoices', 'InvoiceController')->only([
		    'index', 'show'
		]);

	});		

});