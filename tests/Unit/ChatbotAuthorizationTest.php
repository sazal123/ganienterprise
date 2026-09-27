<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Conversation;
use App\Services\GuestTokenService;
use App\Services\ChatbotAuthorizationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChatbotAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    protected GuestTokenService $tokenService;
    protected ChatbotAuthorizationService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenService = new GuestTokenService();
        $this->authService = new ChatbotAuthorizationService($this->tokenService);
    }

    /** @test */
    public function it_generates_cryptographically_secure_random_guest_tokens()
    {
        $token1 = $this->tokenService->generateToken();
        $token2 = $this->tokenService->generateToken();

        $this->assertEquals(64, strlen($token1));
        $this->assertEquals(64, strlen($token2));
        $this->assertNotEquals($token1, $token2);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{64}$/', $token1);

        // Hashing check
        $hash1 = $this->tokenService->hashToken($token1);
        $hash2 = $this->tokenService->hashToken($token1);
        $this->assertEquals(64, strlen($hash1));
        $this->assertEquals($hash1, $hash2);
    }

    /** @test */
    public function it_prevents_guest_a_from_accessing_guest_b_conversation()
    {
        $tokenA = $this->tokenService->generateToken();
        $tokenB = $this->tokenService->generateToken();

        $conversationB = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($tokenB),
            'status' => 'open',
        ]);

        // Guest A tries to access Guest B's conversation -> MUST FAIL
        $isAuthorized = $this->authService->authorizeAccess($conversationB, $tokenA, null);
        $this->assertFalse($isAuthorized, 'Guest A must NOT access Guest B conversation');

        // Guest B tries to access Guest B's conversation -> MUST SUCCEED
        $isAuthorizedB = $this->authService->authorizeAccess($conversationB, $tokenB, null);
        $this->assertTrue($isAuthorizedB, 'Guest B should access Guest B conversation');
    }

    /** @test */
    public function it_prevents_guest_from_accessing_logged_in_customer_conversation()
    {
        $customer = Customer::create([
            'name' => 'Customer One',
            'slug' => 'customer-one',
            'phone' => '01800000001',
            'email' => 'customer1@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $customerConversation = Conversation::create([
            'customer_id' => $customer->id,
            'status' => 'open',
        ]);

        $randomGuestToken = $this->tokenService->generateToken();

        // Guest attempts to access Customer's conversation -> MUST FAIL
        $isAuthorized = $this->authService->authorizeAccess($customerConversation, $randomGuestToken, null);
        $this->assertFalse($isAuthorized, 'Guest must NOT access logged-in customer conversation');

        // Guest without token attempts to access -> MUST FAIL
        $isAuthorizedNoToken = $this->authService->authorizeAccess($customerConversation, null, null);
        $this->assertFalse($isAuthorizedNoToken, 'Guest without token must NOT access customer conversation');
    }

    /** @test */
    public function it_prevents_customer_a_from_accessing_customer_b_conversation()
    {
        $customerA = Customer::create([
            'name' => 'Customer A',
            'slug' => 'customer-a',
            'phone' => '01800000002',
            'email' => 'customera@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $customerB = Customer::create([
            'name' => 'Customer B',
            'slug' => 'customer-b',
            'phone' => '01800000003',
            'email' => 'customerb@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $conversationB = Conversation::create([
            'customer_id' => $customerB->id,
            'status' => 'open',
        ]);

        // Customer A attempts to access Customer B's conversation -> MUST FAIL
        $isAuthorized = $this->authService->authorizeAccess($conversationB, null, $customerA);
        $this->assertFalse($isAuthorized, 'Customer A must NOT access Customer B conversation');

        // Customer B attempts to access Customer B's conversation -> MUST SUCCEED
        $isAuthorizedB = $this->authService->authorizeAccess($conversationB, null, $customerB);
        $this->assertTrue($isAuthorizedB, 'Customer B should access Customer B conversation');
    }

    /** @test */
    public function it_prevents_customer_from_accessing_arbitrary_unowned_conversation()
    {
        $customerA = Customer::create([
            'name' => 'Customer X',
            'slug' => 'customer-x',
            'phone' => '01800000004',
            'email' => 'customerx@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $randomGuestToken = $this->tokenService->generateToken();
        $guestConversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($randomGuestToken),
            'status' => 'open',
        ]);

        // Customer X attempts to access arbitrary guest conversation without token -> MUST FAIL
        $isAuthorized = $this->authService->authorizeAccess($guestConversation, null, $customerA);
        $this->assertFalse($isAuthorized, 'Customer X must NOT access unowned conversation without valid token');
    }

    /** @test */
    public function it_migrates_guest_conversation_to_authenticated_customer_upon_login()
    {
        $guestToken = $this->tokenService->generateToken();
        $guestConversation = Conversation::create([
            'guest_token_hash' => $this->tokenService->hashToken($guestToken),
            'status' => 'open',
        ]);

        $this->assertNull($guestConversation->customer_id);

        $customer = Customer::create([
            'name' => 'Newly Logged Customer',
            'slug' => 'newly-logged',
            'phone' => '01800000005',
            'email' => 'newly@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        // Perform migration
        $migrated = $this->authService->migrateGuestConversationToCustomer($guestConversation, $customer, $guestToken);

        $this->assertTrue($migrated);
        $this->assertEquals($customer->id, $guestConversation->fresh()->customer_id);

        // After migration, Customer can access it
        $canAccess = $this->authService->authorizeAccess($guestConversation->fresh(), null, $customer);
        $this->assertTrue($canAccess);
    }

    /** @test */
    public function it_prevents_guest_from_claiming_existing_customer_conversation()
    {
        $existingCustomer = Customer::create([
            'name' => 'Existing Customer',
            'slug' => 'existing-customer',
            'phone' => '01800000006',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $customerConversation = Conversation::create([
            'customer_id' => $existingCustomer->id,
            'status' => 'open',
        ]);

        $attackerCustomer = Customer::create([
            'name' => 'Attacker Customer',
            'slug' => 'attacker-customer',
            'phone' => '01800000007',
            'email' => 'attacker@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $fakeGuestToken = $this->tokenService->generateToken();

        // Attempt to claim existing customer conversation -> MUST FAIL
        $claimed = $this->authService->migrateGuestConversationToCustomer($customerConversation, $attackerCustomer, $fakeGuestToken);

        $this->assertFalse($claimed, 'Must NOT allow claiming an existing customer conversation');
        $this->assertEquals($existingCustomer->id, $customerConversation->fresh()->customer_id);
    }
}
