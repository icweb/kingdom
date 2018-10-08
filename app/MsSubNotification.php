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

    public function produce($data)
    {
        foreach($data['value'] as $item)
        {
            $ms_sub = MsSubscription::where([
                'subscription_id'   => $item['subscriptionId'],
                'client_state'      => $item['clientState']
            ])->get();

            if(count($ms_sub))
            {
                if(substr($item['resource'], 0, 5) === 'Users')
                {
                    $resource_type = 'USER';
                }
                else
                {
                    $resource_type = 'GROUP';
                }

                foreach($item['resourceData'] as $itemData)
                {
                    $resource = new MsResource();
                    $resource->getOrCreate($itemData['id'], $resource_type);
                }
            }
        }
    }
}
