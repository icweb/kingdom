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
        Schema::create('ms_tokens', function(Blueprint $table){
            $table->increments('id');
            $table->longText('token');
            $table->longText('tenant_id');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::create('versions', function(Blueprint $table)
        {
            $table->increments('version_id');
            $table->string('versionable_id');
            $table->string('versionable_type');
            $table->string('user_id')->nullable();
            $table->binary('model_data');
            $table->string('reason', 100)->nullable();
            $table->index('versionable_id');
            $table->timestamps();
        });

        Schema::create('ms_subscriptions', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('author_id');
            $table->unsignedInteger('auto_renew')->default(1);
            $table->string('subscription_id');
            $table->string('resource');
            $table->string('change_type');
            $table->string('client_state');
            $table->string('notification_url');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::create('watched_fields', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('author_id');
            $table->unsignedInteger('subscription_id');
            $table->string('field');
            $table->timestamps();
        });

        Schema::create('subscribed_users', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('author_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('subscription_id');
            $table->timestamps();
        });

        Schema::create('changes_queue', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('in_progress')->default(0);
            $table->unsignedInteger('completed')->default(0);
            $table->unsignedInteger('resource_id');
            $table->unsignedInteger('subscription_id');
            $table->unsignedInteger('watched_field_id');
            $table->string('old')->nullable();
            $table->string('new')->nullable();
            $table->timestamps();
        });

        Schema::create('ms_webhooks', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('ms_subscription_id');
            $table->unsignedInteger('ms_resource_id');
            $table->string('change_type');
            $table->timestamps();
        });

        Schema::create('ms_resources', function(Blueprint $table){
            $table->increments('id');
            $table->longText('ms_id')->nullable();
            $table->enum('type', ['USER', 'GROUP']);

            // Universal
            $table->string('displayName')->nullable();
            $table->string('mailNickname')->nullable();
            $table->string('onPremisesLastSyncDateTime')->nullable();
            $table->string('onPremisesSecurityIdentifier')->nullable();
            $table->string('onPremisesSyncEnabled')->nullable();
            $table->longText('proxyAddresses')->nullable();

            // Groups
            $table->longText('description')->nullable();
            $table->boolean('mailEnabled')->default(false);

            $table->string('classification')->nullable();
            $table->string('createdDateTime')->nullable();
            $table->string('groupTypes')->nullable();
            $table->string('renewedDateTime')->nullable();
            $table->string('securityEnabled')->nullable();
            $table->string('visibility')->nullable();
            $table->string('allowExternalSenders')->nullable();
            $table->string('autoSubscribeNewMembers')->nullable();
            $table->string('isSubscribedByMail')->nullable();
            $table->string('unseenCount')->nullable();
            //$table->string('members')->nullable();
            //$table->string('memberOf')->nullable();
            //$table->string('createdOnBehalfOf')->nullable();
            //$table->string('owners')->nullable();
            //$table->string('settings')->nullable();
            //$table->string('extensions')->nullable();
            //$table->string('threads')->nullable();
            //$table->string('calendar')->nullable();
            //$table->string('calendarView')->nullable();
            //$table->string('events')->nullable();
            //$table->string('conversations')->nullable();
            //$table->string('photo')->nullable();
            //$table->string('photos')->nullable();
            //$table->string('acceptedSenders')->nullable();
            //$table->string('rejectedSenders')->nullable();
            //$table->string('drive')->nullable();
            //$table->string('drives')->nullable();
            //$table->string('sites')->nullable();
            //$table->string('planner')->nullable();
            //$table->string('onenote')->nullable();
            //$table->string('groupLifecyclePolicies')->nullable();

            // Users
            $table->string('manager_id')->nullable();
            $table->string('accountEnabled')->nullable();
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

            //$table->string('assignedLicenses')->nullable();
            //$table->string('assignedPlans')->nullable();
            $table->string('city')->nullable();
            $table->string('companyName')->nullable();
            $table->string('country')->nullable();
            $table->string('givenName')->nullable();
            $table->string('imAddresses')->nullable();
            $table->string('onPremisesImmutableId')->nullable();
            $table->string('passwordPolicies')->nullable();
            //$table->string('passwordProfile')->nullable();
            $table->string('postalCode')->nullable();
            $table->string('preferredLanguage')->nullable();
            //$table->string('provisionedPlans')->nullable();
            $table->string('state')->nullable();
            $table->string('streetAddress')->nullable();
            $table->string('surname')->nullable();
            $table->string('usageLocation')->nullable();
            $table->string('userPrincipalName')->nullable();
            $table->string('userType')->nullable();
            //$table->string('mailboxSettings')->nullable();
            $table->string('aboutMe')->nullable();
            $table->string('birthday')->nullable();
            $table->string('hireDate')->nullable();
            $table->string('interests')->nullable();
            $table->string('mySite')->nullable();
            $table->string('pastProjects')->nullable();
            $table->string('preferredName')->nullable();
            $table->string('responsibilities')->nullable();
            $table->string('schools')->nullable();
            $table->string('skills')->nullable();
            $table->string('deviceEnrollmentLimit')->nullable();
            //$table->string('ownedDevices')->nullable();
            //$table->string('registeredDevices')->nullable();
            //$table->string('manager')->nullable();
            //$table->string('directReports')->nullable();
            //$table->string('memberOf')->nullable();
            //$table->string('createdObjects')->nullable();
            //$table->string('ownedObjects')->nullable();
            //$table->string('licenseDetails')->nullable();
            //$table->string('extensions')->nullable();
            //$table->string('outlook')->nullable();
            //$table->string('messages')->nullable();
            //$table->string('mailFolders')->nullable();
            //$table->string('calendar')->nullable();
            //$table->string('calendars')->nullable();
            //$table->string('calendarGroups')->nullable();
            //$table->string('calendarView')->nullable();
            //$table->string('events')->nullable();
            //$table->string('people')->nullable();
            //$table->string('contacts')->nullable();
            //$table->string('contactFolders')->nullable();
            //$table->string('inferenceClassification')->nullable();
            //$table->string('photo')->nullable();
            //$table->string('photos')->nullable();
            //$table->string('drive')->nullable();
            //$table->string('drives')->nullable();
            //$table->string('planner')->nullable();
            //$table->string('onenote')->nullable();
            //$table->string('managedDevices')->nullable();
            //$table->string('managedAppRegistrations')->nullable();
            //$table->string('deviceManagementTroubleshootingEvents')->nullable();
            //$table->string('activities')->nullable();

            $table->timestamps();
        });

        Schema::create('ms_mailbox_settings', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('resource_id');
            $table->string('externalAudience')->nullable();
            $table->string('externalReplyMessage')->nullable();
            $table->string('internalReplyMessage')->nullable();
            $table->string('scheduledEndDateTime')->nullable();
            $table->string('scheduledStartDateTime')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::statement("CREATE VIEW users AS
            (
                SELECT id, displayName as name, mail as email, created_at, updated_at
                FROM ms_resources
                WHERE type = 'USER'
            )
        ");
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
