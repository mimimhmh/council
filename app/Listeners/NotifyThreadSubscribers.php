<?php

namespace App\Listeners;


use App\Events\ThreadReceivedNewReply;

class NotifyThreadSubscribers
{

    /**
     * NotifyThreadSubscribers constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param \App\Events\ThreadReceivedNewReply $event
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        $thread = $event->reply->thread;

        //prepare notifications for all subscribers.
        $thread->subscriptions
            ->where('user_id', '!=', $event->reply->user_id)
            ->each->notify($event->reply);
    }
}
