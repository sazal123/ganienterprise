<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\GeneralSetting;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        if (!GeneralSetting::where('status', 1)->exists()) {
            GeneralSetting::create([
                'name' => 'Test Store',
                'white_logo' => 'logo.png',
                'dark_logo' => 'logo.png',
                'favicon' => 'favicon.ico',
                'copyright' => 'Copyright 2026',
                'status' => 1,
            ]);
        }

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
