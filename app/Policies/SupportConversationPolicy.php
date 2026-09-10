<?php

namespace App\Policies;

use App\Models\CustomerAuth;
use App\Models\SupportConversation;
use App\Models\User;
use App\Support\FluxAdminAccess;

class SupportConversationPolicy
{
    public function view(User|CustomerAuth $actor, SupportConversation $conversation): bool
    {
        if ($actor instanceof User) {
            return FluxAdminAccess::userHasPermission($actor, 'view-chat')
                || FluxAdminAccess::userHasPermission($actor, 'manage-communications')
                || FluxAdminAccess::isSuperAdmin($actor);
        }

        return (int) $conversation->customer_auth_id === (int) $actor->id;
    }

    public function postMessage(User|CustomerAuth $actor, SupportConversation $conversation): bool
    {
        if ($actor instanceof User) {
            return FluxAdminAccess::userHasPermission($actor, 'send-chat')
                || FluxAdminAccess::userHasPermission($actor, 'manage-communications')
                || FluxAdminAccess::isSuperAdmin($actor);
        }

        return (int) $conversation->customer_auth_id === (int) $actor->id;
    }
}
