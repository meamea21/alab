<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    //use RefreshDatabase;

    public function test_successful_login()
    {

        $response = $this->postJson('/api/login', [
            'login' => 'PiotrKowalski',
            'password' => '1983-04-12'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);

        $this->assertNotEmpty($response->json('token'));
    }
}