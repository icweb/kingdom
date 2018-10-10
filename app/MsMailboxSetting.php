<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Versionable\VersionableTrait;

class MsMailboxSetting extends Model
{
    use VersionableTrait;

    protected $keepOldVersions = 10;

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
