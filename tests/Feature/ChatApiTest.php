<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\GuestTokenService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

class ChatApiTest extends TestCase
{
    use DatabaseTransactions;

    protected GuestTokenService $tokenService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenService = new GuestTokenService();
    }

    /** @test */
    public function guest_can_create_conversation()
    {
        $response = $this->postJson('/api/v1/chat/conversations', [
            'subject' => 'Guest Question',
            'initial_message' => 'Hello, I am a guest visitor.',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Conversation initialized successfully.',
        ]);

        $data = $response->json('data');
        $this->assertNotNull($data['conversation']);
        $this->assertNotNull($data['guest_token']);
        $this->assertEquals(64, strlen($data['guest_token']));

        // Assert cookie attached
        $response->assertCookie(GuestTokenService::COOKIE_NAME);

        // Assert database record created
        $this->assertDatabaseHas('conversations', [
            'id' => $data['conversation']['id'],
            'customer_id' => null,
            'status' => 'open',
        ]);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $data['conversation']['id'],
            'sender_type' => 'guest',
            'message' => 'Hello, I am a guest visitor.',
        ]);
    }

    /** @test */
    public function authenticated_customer_can_create_conversation()
    {
        $customer = Customer::create([
            'name' => 'Authenticated Store Customer',
            'slug' => 'auth-customer',
            'phone' => '01900000001',
            'email' => 'authcustomer@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $response = $this->actingAs($customer, 'customer')
            ->postJson('/api/v1/chat/conversations', [
                'subject' => 'Order Inquiry',
                'initial_message' => 'Where is my package?',
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json('data');
        $this->assertEquals($customer->id, $data['conversation']['customer_id']);
        $this->assertNull($data['conversation']['guest_token_hash']);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $data['conversation']['id'],
            'sender_type' => 'customer',
            'sender_id' => $customer->id,
            'message' => 'Where is my package?',
        ]);
    }

    /** @test */
    public function guest_can_send_message()
    {
        $guestToken = $this->tokenService->generateToken();
        $hash = $this->tokenService->hashToken($guestToken);

        $conversation = Conversation::create([
            'guest_token_hash' => $hash,
            'status' => 'open',
        ]);

        $response = $this->withHeaders(['X-Guest-Token' => $guestToken])
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => 'Guest message content',
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Message sent successfully.',
        ]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_type' => 'guest',
            'sender_id' => null,
            'message' => 'Guest message content',
        ]);
    }

    /** @test */
    public function authenticated_customer_can_send_message()
    {
        $customer = Customer::create([
            'name' => 'Message Sender Customer',
            'slug' => 'sender-customer',
            'phone' => '01900000002',
            'email' => 'sender@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $conversation = Conversation::create([
            'customer_id' => $customer->id,
            'status' => 'open',
        ]);

        // Attempting to send custom sender_type in body must be IGNORED by server
        $response = $this->actingAs($customer, 'customer')
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => 'Hello from customer',
                'sender_type' => 'admin', // Untrusted payload - server MUST override
                'sender_id' => 9999,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'sender_id' => $customer->id,
            'message' => 'Hello from customer',
        ]);
    }

    /** @test */
    public function unauthorized_conversation_access_is_rejected()
    {
        $customerA = Customer::create([
            'name' => 'Customer A',
            'slug' => 'cust-a',
            'phone' => '01900000003',
            'email' => 'custa@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $customerB = Customer::create([
            'name' => 'Customer B',
            'slug' => 'cust-b',
            'phone' => '01900000004',
            'email' => 'custb@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $conversationA = Conversation::create([
            'customer_id' => $customerA->id,
            'status' => 'open',
        ]);

        // Customer B tries to view Customer A's conversation -> 403 Forbidden
        $response = $this->actingAs($customerB, 'customer')
            ->getJson("/api/v1/chat/conversations/{$conversationA->uuid}");

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorized access to conversation.',
        ]);

        // Guest B tries to send message to Guest A conversation -> 403 Forbidden
        $tokenA = $this->tokenService->generateToken();
        $tokenB = $this->tokenService->generateToken();

        $guestConversationA = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($tokenA),
            'status' => 'open',
        ]);

        $guestResponse = $this->withHeaders(['X-Guest-Token' => $tokenB])
            ->postJson("/api/v1/chat/conversations/{$guestConversationA->uuid}/messages", [
                'message' => 'Malicious payload',
            ]);

        $guestResponse->assertStatus(403);
    }

    /** @test */
    public function invalid_input_returns_validation_errors()
    {
        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status' => 'open',
        ]);

        $response = $this->withHeaders(['X-Guest-Token' => $token])
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => '', // Empty message
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Validation failed.',
        ]);
        $response->assertJsonValidationErrors(['message']);
    }

    /** @test */
    public function oversized_message_is_rejected()
    {
        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status' => 'open',
        ]);

        $longMessage = Str::repeat('a', 2001); // Exceeds 2000 character limit

        $response = $this->withHeaders(['X-Guest-Token' => $token])
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => $longMessage,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    /** @test */
    public function rate_limiting_throttles_excessive_message_sending()
    {
        cache()->flush();

        $token = $this->tokenService->generateToken();
        $conversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($token),
            'status' => 'open',
        ]);

        // Send messages up to the 30/min limit
        for ($i = 0; $i < 30; $i++) {
            $response = $this->withHeaders(['X-Guest-Token' => $token])
                ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                    'message' => "Message number {$i}",
                ]);
            $response->assertStatus(201);
        }

        // 31st request exceeds rate limit -> 429 Too Many Requests
        $exceededResponse = $this->withHeaders(['X-Guest-Token' => $token])
            ->postJson("/api/v1/chat/conversations/{$conversation->uuid}/messages", [
                'message' => 'Spam message',
            ]);

        $exceededResponse->assertStatus(429);
    }
}
