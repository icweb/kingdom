<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MsSubNotification extends Model
{
    public $table = 'ms_subscription_notifications';

    public $fillable = [
        'ms_subscription_id',
        'ms_resource_id',
        'change_type',
        'completed_at',
    ];

    public function produce($data)
    {
        //        array (
//            'changeType' => 'updated',
//            //'clientState' => 'bed9a2d4050b19edf8e8657f92fc50bc',
//            'resource' => 'Users/e3752196-b107-4231-bb10-8881777ca031',
//            'resourceData' =>
//                array (
//                    '@odata.type' => '#Microsoft.Graph.Group',
//                    '@odata.id' => 'Groups/e3752196-b107-4231-bb10-8881777ca031',
//                    'id' => 'e3752196-b107-4231-bb10-8881777ca031',
//                    'organizationId' => 'ebe6fd27-0c10-44bb-a3fc-213e729cb8c9',
//                    'sequenceNumber' => 636746356026729019,
//                ),
//            'subscriptionExpirationDateTime' => '2018-10-10T22:21:39+00:00',
//            //'subscriptionId' => 'bbd1c8dd-a2a9-4974-a4b3-1cd43b465027',
//            'tenantId' => 'ebe6fd27-0c10-44bb-a3fc-213e729cb8c9',
//        ),

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
                $resource = $resource->getOrCreate($item['resourceData']['id'], $resource_type);

                MsSubNotification::create([
                    'ms_subscription_id' => $ms_sub[0]->id,
                    'ms_resource_id'     => $resource->id,
                    'change_type'        => $item['changeType'],
                ]);
            }
        }
    }
}
