<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Customer;
use App\Models\User;

class ChatbotAuthorizationService
{
    protected GuestTokenService $tokenService;

    public function __construct(GuestTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Authorize access to a conversation.
     *
     * Rules enforced:
     * - Customer A cannot access Customer B's conversation.
     * - Guest A cannot access Guest B's conversation.
     * - Guest cannot access a logged-in customer's conversation.
     * - Unauthenticated user cannot access arbitrary conversation IDs.
     *
     * @param Conversation $conversation
     * @param string|null $guestToken Unhashed token from cookie
     * @param Customer|User|null $actor
     * @return bool
     */
    public function authorizeAccess(Conversation $conversation, ?string $guestToken = null, $actor = null): bool
    {
        // Resolve actor if not explicitly provided
        if ($actor === null) {
            if (auth('customer')->check()) {
                $actor = auth('customer')->user();
            } elseif (auth('web')->check()) {
                $actor = auth('web')->user();
            }
        }

        // 1. Authenticated Storefront Customer
        if ($actor instanceof Customer) {
            // If conversation belongs to another customer, deny immediately
            if ($conversation->customer_id !== null) {
                return (int) $conversation->customer_id === (int) $actor->id;
            }

            // If conversation belongs to admin staff, deny
            if ($conversation->user_id !== null) {
                return false;
            }

            // If conversation is a guest conversation and caller has valid guest token,
            // migrate it to this customer and grant access
            if ($conversation->guest_token_hash !== null && $guestToken !== null) {
                $computedHash = $this->tokenService->hashToken($guestToken);
                if (hash_equals($conversation->guest_token_hash, $computedHash)) {
                    $this->migrateGuestConversationToCustomer($conversation, $actor, $guestToken);
                    return true;
                }
            }

            return false;
        }

        // 2. Authenticated Admin / Staff User
        if ($actor instanceof User) {
            if ((int) $conversation->user_id === (int) $actor->id) {
                return true;
            }
            if ((int) $conversation->assigned_admin_id === (int) $actor->id) {
                return true;
            }
            if ($actor->hasRole('admin') || $actor->can('manage chatbot')) {
                return true;
            }
            return false;
        }

        // 3. Unauthenticated Guest
        if ($conversation->customer_id !== null || $conversation->user_id !== null) {
            // Guest attempting to access a logged-in customer's or staff's conversation -> DENIED
            return false;
        }

        if ($conversation->guest_token_hash !== null && $guestToken !== null) {
            $computedHash = $this->tokenService->hashToken($guestToken);
            return hash_equals($conversation->guest_token_hash, $computedHash);
        }

        return false;
    }

    /**
     * Migrate an unassigned guest conversation to an authenticated customer.
     * Prevents claiming an existing customer's conversation or another guest's conversation.
     *
     * @param Conversation $conversation
     * @param Customer $customer
     * @param string $guestToken Unhashed token from cookie
     * @return bool
     */
    public function migrateGuestConversationToCustomer(Conversation $conversation, Customer $customer, string $guestToken): bool
    {
        // Must not already be owned by a customer or user
        if ($conversation->customer_id !== null || $conversation->user_id !== null) {
            return false;
        }

        // Must have a valid guest token hash matching the cookie token
        if (!$conversation->guest_token_hash) {
            return false;
        }

        $computedHash = $this->tokenService->hashToken($guestToken);
        if (!hash_equals($conversation->guest_token_hash, $computedHash)) {
            return false;
        }

        // Bind conversation to customer
        $conversation->customer_id = $customer->id;
        $conversation->save();

        return true;
    }

    /**
     * Find active open conversation for a guest token.
     *
     * @param string $guestToken
     * @return Conversation|null
     */
    public function findActiveGuestConversation(string $guestToken): ?Conversation
    {
        $hash = $this->tokenService->hashToken($guestToken);
        return Conversation::where('guest_token_hash', $hash)
            ->where('status', 'open')
            ->latest()
            ->first();
    }

    /**
     * Find active open conversation for an authenticated customer.
     *
     * @param int $customerId
     * @return Conversation|null
     */
    public function findActiveCustomerConversation(int $customerId): ?Conversation
    {
        return Conversation::where('customer_id', $customerId)
            ->where('status', 'open')
            ->latest()
            ->first();
    }
}
