<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MsWebhooks extends Model
{
    public $table = 'ms_webhooks';

    public $fillable = [
        'ms_subscription_id',
        'ms_resource_id',
        'change_type',
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

                $resource = new MsResource();
                $resource = $resource->getOrCreate($item['resourceData']['id'], $resource_type, $item['changeType']);

                MsWebhooks::create([
                    'ms_subscription_id' => $ms_sub[0]->id,
                    'ms_resource_id'     => $resource->id,
                    'change_type'        => $item['changeType'],
                ]);
            }
        }
    }
}
