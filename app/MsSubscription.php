<?php

namespace App;

use Microsoft\Graph\Graph;
use Illuminate\Database\Eloquent\Model;
use Microsoft\Graph\Model\Subscription;

class MsSubscription extends Model
{
    public $table = 'ms_subscriptions';

    public $fillable = [
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

    public function produce(Subscription $subscription)
    {
        $sub = new Subscription();
        $sub->setChangeType("updated");
        $sub->setResource("groups");
        $sub->setClientState(md5('ClientStateSecretString'));
        $sub->setNotificationUrl(env('MSG_BASE_URL') . "webhooks");

        $expires = new \DateTime();
        $sub->setExpirationDateTime($expires->modify('+2 days'));

        $result = $this->graph()
            ->createRequest("POST", "/subscriptions")
            ->attachBody($sub)
            ->setReturnType(Subscription::class)
            ->execute();

//        $ms_sub = new MsSubscription();
//        $ms_sub->resource = $subscription->getResource();
//        $ms_sub->change_type = $subscription->getChangeType();
//        $ms_sub->client_state = $subscription->getClientState();
//        $ms_sub->notification_url = $subscription->getNotificationUrl();
//        $ms_sub->expires_at = date('Y-m-d H:i:s', $subscription->getExpirationDateTime()->getTimestamp());
//        $ms_sub->subscription_id = $subscription->getId();
//        $ms_sub->save();

        $ms_sub = MsSubscription::create([
            'resource'          => $subscription->getResource(),
            'change_type'       => $subscription->getChangeType(),
            'client_state'      => $subscription->getClientState(),
            'notification_url'  => $subscription->getNotificationUrl(),
            'expires_at'        => $subscription->getExpirationDateTime()->getTimestamp(),
            'subscription_id'   => $subscription->getId()
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
