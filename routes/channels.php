<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;
use App\Services\GuestTokenService;
use App\Services\ChatbotAuthorizationService;

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

/**
 * Private conversation channel authorization.
 *
 * Channel: chat.conversation.{uuid}
 *
 * Rules:
 * - Authenticated Customer: Must own conversation (auth->id() === conversation.customer_id).
 * - Guest Customer: Must possess matching guest token (hash(token) === conversation.guest_token_hash).
 * - Conversation UUID alone without ownership proof IS STRICTLY REJECTED.
 */
Broadcast::channel('chat.conversation.{uuid}', function ($user = null, $uuid = null) {
    $conversation = Conversation::where('uuid', $uuid)->first();

    if (!$conversation) {
        return false;
    }

    $tokenService = app(GuestTokenService::class);
    $authService  = app(ChatbotAuthorizationService::class);

    $guestToken = request()->header('X-Guest-Token') ?? request()->cookie('guest_chat_token');

    return $authService->authorizeAccess($conversation, $guestToken);
});
