<?php

namespace App\Policies;

use App\User;
use App\Message;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can like the message.
     *
     * @param  \App\User $user
     * @param  \App\Message $message
     * @return mixed
     */
    public function like(User $user, Message $message)
    {
        $likes = $message->likes()->get();

        $index = $likes->search(function ($item, $key) use ($user) {
            return $item->user_id === $user->id;
        });

        $currentUserNotLikedThisYet = $index === false;

        return $currentUserNotLikedThisYet && $user->id !== $message->user_id;
    }

    /**
     * Determine whether the user can delete the message.
     *
     * @param  \App\User $user
     * @param  \App\Message $message
     * @return mixed
     */
    public function delete(User $user, Message $message)
    {
        return $user->id === $message->user_id;
    }
}
