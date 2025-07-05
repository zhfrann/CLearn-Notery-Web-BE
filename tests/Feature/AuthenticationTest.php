<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * Registrasi User Baru
     */
    public function testRegisterSuccess(): void
    {
        $this->post('/api/auth/register', [
            'username' => 'user1',
            'email' => 'user1@gmail.com',
            'password' => 'rahasia123',
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'user1',
                    'email' => 'user1@gmail.com',
                    'role' => 'student',
                    'status_akun' => 'aktif'
                ]
            ]);
    }
}
