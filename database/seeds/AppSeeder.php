<?php

use Illuminate\Database\Seeder;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Token::create([
            'tenant_id'     => env('MSG_TENANT_GUID'),
            'token'         => 'NA',
            'expires_at'    => time()
        ]);

        $resource = new \App\MsResource();
        $resource->populateDirectory();
    }
}
