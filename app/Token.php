<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $dates = [
        'expires_at'
    ];

    protected $fillable = [
        'token',
        'tenant_id',
        'expires_at',
    ];

    public static function fetch()
    {
        $client = new Client();
        $result = $client->post(env('MSG_AUTHORITY_URL') . env('MSG_TOKEN_ENDPOINT'), [
            'form_params' => [
                'tenant'        => env('MSG_TENANT_GUID'),
                'client_id'     => env('MSG_CLIENT_ID'),
                'client_secret' => env('MSG_CLIENT_SECRET'),
                'scope'         => 'https://graph.microsoft.com/.default',
                'grant_type'    => 'client_credentials',
            ]
        ]);

        $response = json_decode($result->getBody());

        Token::find(1)
            ->update([
                'token'      => $response->access_token,
                'expires_at' => strtotime('+ ' . $response->expires_in . ' seconds')
            ]);

        return $response->access_token;
    }
}
