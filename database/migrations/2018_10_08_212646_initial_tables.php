<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitialTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokens', function(Blueprint $table){
            $table->increments('id');
            $table->longText('token');
            $table->longText('tenant_id');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::create('ms_subscriptions', function(Blueprint $table){
            $table->increments('id');
            $table->string('subscription_id');
            $table->string('resource');
            $table->string('change_type');
            $table->string('client_state');
            $table->string('notification_url');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::create('ms_subscription_notifications', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('ms_subscription_id');
            $table->unsignedInteger('resource_id');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ms_resources', function(Blueprint $table){
            $table->increments('id');
            $table->longText('ms_id')->nullable();
            $table->enum('type', ['USER', 'GROUP']);

            // Universal
            $table->string('displayName')->nullable();

            // Groups
            $table->string('description')->nullable();
            $table->boolean('mailEnabled')->default(false);
            $table->string('mailNickname')->nullable();

            // Users
            $table->unsignedInteger('manager_id')->default(0);
            $table->boolean('accountEnabled')->default(false);
            $table->string('mobilePhone')->nullable();
            $table->string('mail')->nullable();
            $table->string('jobTitle')->nullable();
            $table->string('officeLocation')->nullable();
            $table->string('department')->nullable();
            $table->string('businessPhone_1')->nullable();
            $table->string('businessPhone_2')->nullable();
            $table->string('businessPhone_3')->nullable();
            $table->string('businessPhone_4')->nullable();
            $table->string('businessPhone_5')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
