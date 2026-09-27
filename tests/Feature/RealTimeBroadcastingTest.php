<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Customer;
use App\Events\MessageCreated;
use App\Events\ConversationUpdated;
use App\Events\HumanSupportStarted;
use App\Events\ConversationClosed;
use App\Services\GuestTokenService;
use App\Services\ChatbotAuthorizationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

class RealTimeBroadcastingTest extends TestCase
{
    use DatabaseTransactions;

    protected GuestTokenService $tokenService;
    protected ChatbotAuthorizationService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.openrouter.api_key' => 'sk-or-v1-test-key-999']);
        $this->tokenService = new GuestTokenService();
        $this->authService  = new ChatbotAuthorizationService($this->tokenService);
        Http::preventStrayRequests();
    }

    /** @test */
    public function broadcast_events_use_private_conversation_channel_and_correct_payloads()
    {
        $conversation = Conversation::create([
            'status' => 'open',
            'mode'   => 'ai',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'ai',
            'message_type'    => 'text',
            'message'         => 'Realtime WebSocket message payload test',
        ]);

        $msgEvent = new MessageCreated($message, $conversation, 1);
        $this->assertEquals("private-chat.conversation.{$conversation->uuid}", $msgEvent->broadcastOn()->name);
        $this->assertEquals('message.created', $msgEvent->broadcastAs());
        $this->assertEquals($conversation->uuid, $msgEvent->broadcastWith()['conversation_uuid']);

        $updatedEvent = new ConversationUpdated($conversation, 2);
        $this->assertEquals("private-chat.conversation.{$conversation->uuid}", $updatedEvent->broadcastOn()->name);
        $this->assertEquals('conversation.updated', $updatedEvent->broadcastAs());

        $humanEvent = new HumanSupportStarted($conversation, 'Customer requested human');
        $this->assertEquals("private-chat.conversation.{$conversation->uuid}", $humanEvent->broadcastOn()->name);
        $this->assertEquals('human.support.started', $humanEvent->broadcastAs());

        $closedEvent = new ConversationClosed($conversation);
        $this->assertEquals("private-chat.conversation.{$conversation->uuid}", $closedEvent->broadcastOn()->name);
        $this->assertEquals('conversation.closed', $closedEvent->broadcastAs());
    }

    /** @test */
    public function private_channel_authorization_requires_ownership_proof_and_rejects_uuid_alone()
    {
        $customerA = Customer::create([
            'name'     => 'Realtime Cust A',
            'slug'     => 'rt-cust-a',
            'phone'    => '01900000001',
            'email'    => 'rta@example.com',
            'password' => bcrypt('password'),
            'status'   => 'active',
        ]);

        $customerB = Customer::create([
            'name'     => 'Realtime Cust B',
            'slug'     => 'rt-cust-b',
            'phone'    => '01900000002',
            'email'    => 'rtb@example.com',
            'password' => bcrypt('password'),
            'status'   => 'active',
        ]);

        $guestToken = $this->tokenService->generateToken();

        $customerConversation = Conversation::create([
            'customer_id' => $customerA->id,
            'status'      => 'open',
            'mode'        => 'ai',
        ]);

        $guestConversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($guestToken),
            'status'           => 'open',
            'mode'             => 'ai',
        ]);

        // 1. Authenticated customer A authorized for customer A's conversation
        $this->actingAs($customerA, 'customer');
        $this->assertTrue($this->authService->authorizeAccess($customerConversation));

        // 2. Customer A denied access to Customer B's conversation
        $this->assertFalse($this->authService->authorizeAccess($guestConversation));

        // 3. Guest with valid guest token authorized for guest conversation
        auth('customer')->logout();
        $this->assertTrue($this->authService->authorizeAccess($guestConversation, $guestToken));

        // 4. Conversation UUID alone without token or customer auth is DENIED
        $this->assertFalse($this->authService->authorizeAccess($guestConversation, null));
    }

    /** @test */
    public function sending_message_dispatches_message_created_and_conversation_updated_events()
    {
        Event::fake([
            MessageCreated::class,
            ConversationUpdated::class,
        ]);

        Http::fake([
            'https://openrouter.ai/*' => Http::response([
                'choices' => [
                    ['message' => ['role' => 'assistant', 'content' => 'AI Realtime Response']],
                ],
            ], 200),
        ]);

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'open',
            'mode'             => 'ai',
        ]);

        $response = $this->withHeaders(['X-Guest-Token' => $token])
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => 'Hello WebSocket test',
            ]);

        $response->assertStatus(201);

        Event::assertDispatched(MessageCreated::class);
        Event::assertDispatched(ConversationUpdated::class);
    }

    /** @test */
    public function closing_conversation_dispatches_conversation_closed_event()
    {
        Event::fake([
            ConversationClosed::class,
            ConversationUpdated::class,
        ]);

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status'           => 'open',
            'mode'             => 'ai',
        ]);

        $response = $this->withHeaders(['X-Guest-Token' => $token])
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/close");

        $response->assertStatus(200);

        Event::assertDispatched(ConversationClosed::class);
        Event::assertDispatched(ConversationUpdated::class);
    }
}
