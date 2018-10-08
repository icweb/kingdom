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

Route::get('/', function () {

    echo 'You have arrived';


});

Route::get('/createusersub', function () {

    $subscription = new \App\MsSubscription();
    return $subscription->produce();

});

Route::get('/webhooks', function (\Illuminate\Http\Request $request) {

    info($request->all());

    if(isset($_GET['validationToken']))
    {
        return response($_GET['validationToken'], 200)->header('Content-Type', 'text/plain');
    }

    return response('', 201)->header('Content-Type', 'text/plain');

});