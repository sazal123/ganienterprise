<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\GeneralSetting;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChatWidgetFrontendTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure active GeneralSetting exists for frontEnd layout view composer
        GeneralSetting::firstOrCreate(
            ['status' => 1],
            [
                'name'       => 'Gani Enterprise',
                'white_logo' => 'logo.png',
                'dark_logo'  => 'logo.png',
                'favicon'    => 'favicon.ico',
                'copyright'  => 'Gani Enterprise',
            ]
        );
    }

    /** @test */
    public function frontend_master_layout_renders_chat_widget_components()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('gani-chat-toggle-btn', false);
        $response->assertSee('gani-chat-window', false);
        $response->assertSee('gani-chat-input', false);
        $response->assertSee('How can we help you?', false);
    }

    /** @test */
    public function logged_in_customer_receives_personalized_greeting_in_widget()
    {
        $customer = Customer::create([
            'name'     => 'Sabrina Rahman',
            'slug'     => 'sabrina-rahman',
            'phone'    => '01600000099',
            'email'    => 'sabrina@example.com',
            'password' => bcrypt('password'),
            'status'   => 'active',
        ]);

        $response = $this->actingAs($customer, 'customer')->get('/');

        $response->assertStatus(200);
        $response->assertSee('Hi, Sabrina Rahman!', false);
    }
}
