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

Route::get('/', function () { echo 'You have arrived'; });

/* Webhooks */
Route::post('/webhooks', 'MsWebhooksController@receive');

/* Subscriptions */
Route::post('subscriptions/{subscription}/renew', 'MsSubscriptionsController@renew')->name('subscriptions.renew');
Route::resource('subscriptions', 'MsSubscriptionsController');