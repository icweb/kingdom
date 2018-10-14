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

Route::get('/getmanagerinfo', function () {

    $graph = new \Microsoft\Graph\Graph();
    $graph = $graph->setAccessToken(\App\Token::fetch());

    $user = $graph
        ->setApiVersion('beta')
        ->createRequest("GET", '/users/iconway@wcsi.org?$select=displayName,accountEnabled,mobilePhone,mail,jobTitle,officeLocation,department,mailNickname,mailboxSettings&$expand=manager')
        ->setReturnType(\Microsoft\Graph\Model\User::class)
        ->execute();

    dd($user);
    //dd($user->getManager());

});

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
    $given = $given->format("Y-m-d H:i:s e");

    $payload = [
        'birthday'    => '1992-05-09T00:00:00Z',
        'aboutMe'     => 'Test about me!',
    ];

    $payload = json_encode($payload);

    info($payload);

    try {

        $graph
            ->createRequest("PATCH", "/users/iconway@wcsi.org")
            ->attachBody($payload)
            ->execute();

    } catch(Exception $e) {

        info($e->getResponse()->getBody()->getContents());

    }

});
