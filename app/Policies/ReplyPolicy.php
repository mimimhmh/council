<?php

namespace App\Policies;

use App\Reply;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    /**
     * @param \App\User $user
     * @param \App\Reply $reply
     * @return bool
     */
    public function update(User $user, Reply $reply)
    {
        return $reply->user_id == $user->id;
    }

    /**
     * @param \App\User $user
     * @return bool
     */
    public function create(User $user)
    {

        if (! $lastReply = $user->refresh()->lastReply) {
            return true;
        }

        return ! $lastReply->wasJustPublished();
    }
}
