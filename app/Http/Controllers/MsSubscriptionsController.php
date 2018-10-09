<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatesSubscriptions;
use App\Http\Requests\DeletesSubscriptions;
use App\Http\Requests\RenewsSubscriptions;
use App\Http\Requests\UpdatesSubscriptions;
use App\Http\Requests\ViewsCreateSubscriptionForm;
use App\Http\Requests\ViewsEditSubscriptionForm;
use App\Http\Requests\ViewsSubscriptions;
use App\MsSubscription;
use Illuminate\Http\Request;

class MsSubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\ViewsSubscriptions $request
     * @return \Illuminate\Http\Response
     */
    public function index(ViewsSubscriptions $request)
    {
        return view('subscriptions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Http\Requests\ViewsCreateSubscriptionForm $request
     * @return \Illuminate\Http\Response
     */
    public function create(ViewsCreateSubscriptionForm $request)
    {
        return view('subscriptions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreatesSubscriptions $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatesSubscriptions $request)
    {
        $subscription = new MsSubscription();

        $subscription->produce(
            $request->input('change_type'),
            $request->input('resource'),
            $request->input('auto_renew')
        );

        return redirect()->route('subscriptions.show', $subscription);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Requests\ViewsSubscriptions $request
     * @param  \App\MsSubscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(ViewsSubscriptions $request, MsSubscription $subscription)
    {
        return view('subscriptions.show', [
            'subscription' => $subscription
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Requests\ViewsEditSubscriptionForm $request
     * @param  \App\MsSubscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function edit(ViewsEditSubscriptionForm $request, MsSubscription $subscription)
    {
        return view('subscriptions.edit', [
            'subscription' => $subscription
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatesSubscriptions  $request
     * @param  \App\MsSubscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatesSubscriptions $request, MsSubscription $subscription)
    {
        $subscription->update([
            'auto_renew' => $request->input('auto_renew')
        ]);

        return redirect()->route('subscriptions.show', $subscription);
    }

    /**
     * Renew the specified resource in storage.
     *
     * @param  \App\Http\Requests\RenewsSubscriptions  $request
     * @param  \App\MsSubscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function renew(RenewsSubscriptions $request, MsSubscription $subscription)
    {
        $subscription->renew();

        return redirect()->route('subscriptions.show', $subscription);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\DeletesSubscriptions  $request
     * @param  MsSubscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeletesSubscriptions $request, MsSubscription $subscription)
    {
        $subscription->remove();

        return redirect()->route('subscriptions.index');
    }
}
