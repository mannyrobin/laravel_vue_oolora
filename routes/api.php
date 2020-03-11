<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('admin/vouchers', 'VoucherController@index');
Route::delete('admin/vouchers/{id}', 'VoucherController@destroy');
Route::post('admin/vouchers', 'VoucherController@store');
Route::middleware(['api'])->namespace('Api')->group(function () {
	Route::get('cta/preview/{id}', 'CallToActionController@previewCta');
});


Route::middleware(['auth:api'])->namespace('Api')->group(function () {

	Route::resource('campaigns', 'CampaignController')->only([
		'index', 'store', 'update', 'destroy'
	]);

	Route::post('links/check', 'LinkController@linkCheck');
	Route::put('links/status/{id}', 'LinkController@changeStatus');
	Route::ApiResource('links', 'LinkController');

	Route::resource('pixels', 'PixelController')->only([
		'index', 'store', 'update', 'destroy'
	]);

	Route::resource('domains', 'DomainController')->only([
		'index', 'store', 'update', 'destroy'
	]);

	// Route::put('cta/record-conversion/{id}', 'CallToActionController@recordConversion');
	Route::put('cta/status/{id}', 'CallToActionController@changeStatus');
	Route::post('cta/upload-image', 'CallToActionController@uploadImage');
	Route::post('cta/remove-image', 'CallToActionController@removeImage');
	Route::ApiResource('cta', 'CallToActionController');

	Route::resource('custom-scripts', 'CustomScriptController')->only([
		'index', 'store', 'update', 'destroy'
	]);

	Route::get('analytics/overall-statistic', 'AnalyticsController@overallStatistic');
	Route::get('analytics/top-links', 'AnalyticsController@topLinks');
	Route::get('analytics/referrers', 'AnalyticsController@Referrers');
	Route::get('analytics/link-total', 'AnalyticsController@linkTotal');
	Route::get('analytics/link-daily', 'AnalyticsController@linkDaily');


	Route::get('analytics/views', 'AnalyticsController@views');


});




Route::middleware(['api'])->namespace('Api')->group(function () {

	Route::put('cta/record-conversion/{id}', 'CallToActionController@recordConversion');

});