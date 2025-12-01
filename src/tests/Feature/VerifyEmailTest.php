<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class verifyEmailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    //会員登録後、承認メールが送信される
    public function test_register_verify_email_sent()
    {
        $response = $this->post('/register', [
            'name' => "田中　太郎",
            'email' => "test@gmail.com",
            'password' => "password",
            'password_confirmation' => "password",
        ]);

        
    }
}
