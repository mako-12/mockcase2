<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_admin_login_user_validate_email()
    {
        $response = $this->post('/admin/login', [
            'email' => "",
            'password' => "password",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => "メールアドレスを入力してください",
        ]);
    }

    public function test_admin_login_user_validate_password()
    {
        $response = $this->post('/admin/login', [
            'email' => "admin@wxample.com",
            'password' => "",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => "パスワードを入力してください",
        ]);
    }

    public function test_admin_login_user_validate_different()
    {
        $response = $this->post('/admin/login', [
            'email' => "admin@wxample.com",
            'password' => "password123",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => "ログイン情報が登録されていません",
        ]);
    }
}
