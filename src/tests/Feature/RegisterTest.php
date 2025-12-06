<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    // use DatabaseMigrations;

    // protected function setUp(): void
    // {
    //     parent::setUp();
    //     $this->seed(DatabaseSeeder::class);
    // }


    use RefreshDatabase;


    public function test_register_user_validate_name()
    {
        $response = $this->post('/register', [
            'name' => "",
            'email' => "test@gmail.com",
            'password' => "password",
            'password_confirmation' => "password",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }

    public function test_register_user_validate_email()
    {
        $response = $this->post('/register', [
            'name' => "田中　太郎",
            'email' => "",
            'password' => "password",
            'password_confirmation' => "password",
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_register_user_validate_password_under7()
    {
        $response = $this->post('/register', [
            'name' => "田中　太郎",
            'email' => "test@gmail.com",
            'password' => "passwor",
            'password_confirmation' => "password",
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    public function test_register_user_validate_password_different()
    {
        $response = $this->post('/register', [
            'name' => "田中　太郎",
            'email' => "test@gmail.com",
            'password' => "password1",
            'password_confirmation' => "password2",
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
    }

    public function test_register_user_validate_password()
    {
        $response = $this->post('/register', [
            'name' => "田中　太郎",
            'email' => "test@gmail.com",
            'password' => "",
            'password_confirmation' => "password",
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    //ユーザー登録をして、データが正常に保存されているか
    public function test_register_user_create()
    {
        $response = $this->post('/register', [
            'name' => "田中　太郎",
            'email' => "test@gmail.com",
            'password' => "password",
            'password_confirmation' => "password",
            'email_verified_at' => null,
        ]);

        // $response->assertRedirect('/email/verify');
        $this->assertDatabaseHas(User::class, [
            'name' => "田中　太郎",
            'email' => "test@gmail.com",
        ]);
    }
}
