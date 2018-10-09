<?php

namespace App;

use Microsoft\Graph\Graph;
use Illuminate\Database\Eloquent\Model;
use Microsoft\Graph\Model\Subscription;

class MsSubscription extends Model
{
    public $table = 'ms_subscriptions';

    public $fillable = [
        'author_id',
        'auto_renew',
        'subscription_id',
        'resource',
        'change_type',
        'client_state',
        'notification_url',
        'expires_at',
    ];

    private $graph;

    private function graph()
    {
        if(!$this->graph)
        {
            $graph = new Graph();
            $this->graph = $graph->setAccessToken(Token::fetch());
        }

        return $this->graph;
    }

    public function produce($changeType, $resource, $autoRenew = true)
    {
        $sub = new Subscription();
        $sub->setChangeType($changeType);
        $sub->setResource($resource);
        $sub->setClientState(md5('ClientStateSecretString'));
        $sub->setNotificationUrl(env('MSG_BASE_URL') . "webhooks");

        $expires = new \DateTime();
        $sub->setExpirationDateTime($expires->modify('+2 days'));

        $result = $this->graph()
            ->createRequest("POST", "/subscriptions")
            ->attachBody($sub)
            ->setReturnType(Subscription::class)
            ->execute();

        $ms_sub = MsSubscription::create([
            'author_id'         => auth()->user()->id,
            'auto_renew'        => $autoRenew ? 1 : 0,
            'resource'          => $result->getResource(),
            'change_type'       => $result->getChangeType(),
            'client_state'      => $result->getClientState(),
            'notification_url'  => $result->getNotificationUrl(),
            'expires_at'        => date('Y-m-d H:i:s', $result->getExpirationDateTime()->getTimestamp()),
            'subscription_id'   => $result->getId()
        ]);

        return $ms_sub;
    }

    public function renew()
    {
        $expires = new \DateTime();

        $subscription = new Subscription();
        $subscription = $subscription->setExpirationDateTime($expires->modify('+2 days'));

        $result = $this->graph()
            ->createRequest("PATCH", "/subscriptions/" . $this->subscription_id)
            ->attachBody($subscription)
            ->setReturnType(Subscription::class)
            ->execute();

        $this->update(['expires_at' => date('Y-m-d H:i:s', $result->getExpirationDateTime()->getTimestamp())]);
    }

    public function remove()
    {
        $this->graph()
            ->createRequest("DELETE", "/subscriptions/" . $this->subscription_id)
            ->execute();

        $this->delete();
    }
}
