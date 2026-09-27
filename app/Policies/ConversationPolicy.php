<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\Customer;
use App\Models\User;
use App\Services\ChatbotAuthorizationService;

class ConversationPolicy
{
    protected ChatbotAuthorizationService $authService;

    public function __construct(ChatbotAuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Determine whether the user/customer can view the conversation.
     *
     * @param Customer|User|null $user
     * @param Conversation $conversation
     * @param string|null $guestToken
     * @return bool
     */
    public function view($user, Conversation $conversation, ?string $guestToken = null): bool
    {
        return $this->authService->authorizeAccess($conversation, $guestToken, $user);
    }

    /**
     * Determine whether the user/customer can update/send message in the conversation.
     *
     * @param Customer|User|null $user
     * @param Conversation $conversation
     * @param string|null $guestToken
     * @return bool
     */
    public function update($user, Conversation $conversation, ?string $guestToken = null): bool
    {
        return $this->authService->authorizeAccess($conversation, $guestToken, $user);
    }

    /**
     * Determine whether the user/customer can migrate a guest conversation.
     *
     * @param Customer $customer
     * @param Conversation $conversation
     * @param string $guestToken
     * @return bool
     */
    public function migrate(Customer $customer, Conversation $conversation, string $guestToken): bool
    {
        return $this->authService->migrateGuestConversationToCustomer($conversation, $customer, $guestToken);
    }
}
