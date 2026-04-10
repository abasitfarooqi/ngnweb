<?php

use App\Models\CustomerAuth;
use App\Models\SupportConversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('support.staff', function ($actor) {
    return $actor instanceof User;
});

Broadcast::channel('support.customer.{customerAuthId}', function ($actor, int $customerAuthId) {
    if ($actor instanceof User) {
        return true;
    }

    return $actor instanceof CustomerAuth && (int) $actor->id === $customerAuthId;
});

Broadcast::channel('support.conversation.{uuid}', function ($actor, string $uuid) {
    $conversation = SupportConversation::query()->where('uuid', $uuid)->first();
    if (! $conversation) {
        return false;
    }

    if ($actor instanceof User) {
        return true;
    }

    return $actor instanceof CustomerAuth && (int) $conversation->customer_auth_id === (int) $actor->id;
});
