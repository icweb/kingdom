<?php

namespace App;

use Microsoft\Graph\Graph;
use Illuminate\Database\Eloquent\Model;
use Microsoft\Graph\Model\Subscription;

class MsMailboxSetting extends Model
{
    public $table = 'ms_mailbox_settings';

    public $fillable = [
        'resource_id',
        'externalAudience',
        'externalReplyMessage',
        'internalReplyMessage',
        'scheduledEndDateTime',
        'scheduledStartDateTime',
        'status',
    ];
}
