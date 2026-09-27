<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\TelegramAdmin;
use App\Models\TelegramReplySession;
use App\Models\KnowledgeArticle;
use App\Models\AiUsageLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChatbotDatabaseLayerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_create_a_guest_conversation_and_messages()
    {
        $conversation = Conversation::create([
            'guest_token_hash' => hash('sha256', 'guest_secret_token_123'),
            'status' => 'open',
            'mode' => 'ai',
            'subject' => 'Guest product query',
            'last_message_at' => now(),
        ]);

        $this->assertDatabaseHas('conversations', [
            'id' => $conversation->id,
            'status' => 'open',
            'mode' => 'ai',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'guest',
            'message_type' => 'text',
            'message' => 'Hello, is this item in stock?',
        ]);

        $this->assertEquals(1, $conversation->messages()->count());
        $this->assertEquals($conversation->id, $message->conversation->id);
    }

    /** @test */
    public function it_can_create_a_customer_conversation_and_associate_with_customer()
    {
        $customer = Customer::create([
            'name' => 'John Doe',
            'slug' => 'john-doe',
            'phone' => '01700000000',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $conversation = Conversation::create([
            'customer_id' => $customer->id,
            'status' => 'open',
            'mode' => 'ai',
        ]);

        $this->assertEquals(1, $customer->conversations()->count());
        $this->assertEquals($customer->id, $conversation->customer->id);
    }

    /** @test */
    public function it_can_manage_telegram_admin_and_reply_sessions()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'status' => '1',
        ]);

        $telegramAdmin = TelegramAdmin::create([
            'user_id' => $user->id,
            'telegram_chat_id' => '987654321',
            'role' => 'admin',
            'active' => true,
        ]);

        $conversation = Conversation::create([
            'status' => 'open',
            'mode' => 'human',
            'assigned_admin_id' => $user->id,
        ]);

        $replySession = TelegramReplySession::create([
            'telegram_chat_id' => '987654321',
            'conversation_id' => $conversation->id,
            'expires_at' => now()->addMinutes(30),
        ]);

        $this->assertEquals($user->id, $telegramAdmin->user->id);
        $this->assertEquals($user->id, $telegramAdmin->user->telegramAdmin->user_id);
        $this->assertEquals($conversation->id, $replySession->conversation->id);
        $this->assertEquals($user->id, $conversation->assignedAdmin->id);
    }

    /** @test */
    public function it_can_create_knowledge_articles_and_ai_usage_logs()
    {
        $article = KnowledgeArticle::create([
            'title' => 'Return Policy',
            'content' => 'Items can be returned within 7 days.',
            'category' => 'Policies',
            'status' => 'published',
        ]);

        $this->assertDatabaseHas('knowledge_articles', [
            'id' => $article->id,
            'title' => 'Return Policy',
        ]);

        $conversation = Conversation::create([
            'status' => 'open',
            'mode' => 'ai',
        ]);

        $log = AiUsageLog::create([
            'conversation_id' => $conversation->id,
            'model' => 'anthropic/claude-3.5-sonnet',
            'input_tokens' => 120,
            'output_tokens' => 50,
            'total_tokens' => 170,
            'estimated_cost' => 0.0012,
            'status' => 'success',
        ]);

        $this->assertEquals(1, $conversation->aiUsageLogs()->count());
        $this->assertEquals($conversation->id, $log->conversation->id);
    }
}
