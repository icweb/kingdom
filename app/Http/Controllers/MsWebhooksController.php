<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MsWebhooksController extends Controller
{
    /**
     * Receive a webhook from Microsoft
     * https://developer.microsoft.com/en-us/graph/docs/concepts/webhooks#managing-subscriptions
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function receive(Request $request)
    {
        if(isset($_GET['validationToken']))
        {
            return response($_GET['validationToken'], 200)->header('Content-Type', 'text/plain');
        }

        $notification = new \App\MsWebhooks();
        $notification->produce($request->all());

        return response('', 201)->header('Content-Type', 'text/plain');
    }
}
