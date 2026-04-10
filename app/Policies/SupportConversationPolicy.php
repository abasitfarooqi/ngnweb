<?php

namespace App\Policies;

use App\Models\CustomerAuth;
use App\Models\SupportConversation;
use App\Models\User;

class SupportConversationPolicy
{
    public function view(User|CustomerAuth $actor, SupportConversation $conversation): bool
    {
        if ($actor instanceof User) {
            return true;
        }

        return (int) $conversation->customer_auth_id === (int) $actor->id;
    }

    public function postMessage(User|CustomerAuth $actor, SupportConversation $conversation): bool
    {
        if ($actor instanceof User) {
            return true;
        }

        return (int) $conversation->customer_auth_id === (int) $actor->id;
    }
}
