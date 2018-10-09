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

Route::get('/setianbirthday', function () {

    $graph = new \Microsoft\Graph\Graph();
    $graph = $graph->setAccessToken(\App\Token::fetch());

//    $user = $graph
//        ->createRequest("GET", "/users/iconway@wcsi.org")
//        ->setReturnType(\Microsoft\Graph\Model\User::class)
//        ->execute();

    //$birthday = new DateTime('05-09-1992');
    //$user->setBirthday($birthday);

    $given = new DateTime("1992-05-09");
    $given->setTimezone(new DateTimeZone("UTC"));
    $given->format("Y-m-d H:i:s e");


    $graph
        ->createRequest("PATCH", "/users/iconway@wcsi.org")
        ->attachBody('{"birthday": ' . $given->format("Y-m-d H:i:s e") . '}')
        ->execute();

});