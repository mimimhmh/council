<?php

namespace App\Http\Controllers;

use App\Thread;

class ThreadSubscriptionsController extends Controller
{
    /**
     *
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * @param $channelId
     * @param \App\Thread $thread
     */
    public function store($channelId, Thread $thread)
    {
        $thread->subscribe();
    }

    /**
     * @param $channelId
     * @param \App\Thread $thread
     */
    public function destroy($channelId, Thread $thread)
    {
        $thread->unsubscribe();
    }
}
