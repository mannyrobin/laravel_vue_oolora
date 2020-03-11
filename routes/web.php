<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Custom domain
Route::domain('{domain}')->group(function () {

	// Redirect any request without the slug to the main domain
	Route::redirect('/', config('app.url'));

	// Handle domain with the slug
    Route::get('/{slug}', 'PreviewDomainController');
    
});



Route::get('/', 'FrontendController@index');
Route::get('/{slug}', 'PreviewDomainController');

Route::get('test', 'FrontendController@test');
Route::get('features', 'FrontendController@features');

Route::get('/my-voucher', 'VoucherController@getVoucher');
Route::post('/redeem-code', 'VoucherController@utilize')->name('redeem.code');
Route::get('/admin-vouchers', 'VoucherController@index')->name('admin.vouchers');

Auth::routes(['verify' => true]);



// Deprecated will be remove
// and this will be use instead website/linkslug
Route::get('l/{slug}', 'PreviewController');


// Authenticated Routes
Route::middleware(['auth','membership'])->group(function () {

	Route::any('dashboard/{any?}', function () {
		return view('layouts/vue');
	})->where('any', '[\/\w\.-]*');


	Route::any('campaigns/{any?}', function () {
		return view('layouts/vue');
	})->where('any', '[\/\w\.-]*');


	Route::any('links/{any?}', function () {
		return view('layouts/vue');
	})->where('any', '[\/\w\.-]*');


	Route::any('pixels/{any?}', function () {
		return view('layouts/vue');
	})->where('any', '[\/\w\.-]*');


	Route::any('call-to-actions/{any?}', function () {
		return view('layouts/vue');
	})->where('any', '[\/\w\.-]*');


	Route::any('custom-scripts/{any?}', function () {
		return view('layouts/vue');
	})->where('any', '[\/\w\.-]*');


	Route::any('analytics/{any?}', function () {
		return view('layouts/vue');
	})->where('any', '[\/\w\.-]*');

	Route::any('domains/{any?}', function () {
		return view('layouts/vue');
	})->where('any', '[\/\w\.-]*');

});