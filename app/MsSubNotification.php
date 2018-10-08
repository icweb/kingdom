<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MsSubNotification extends Model
{
    public $table = 'ms_subscription_notifications';

    public $fillable = [
        'ms_subscription_id',
        'ms_resource_id',
        'completed_at',
    ];
}
