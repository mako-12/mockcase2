<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        Notification::fake();

        $response = $this->post('/register', [
            'name' => "田中　太郎",
            'email' => "test@gmail.com",
            'password' => "password",
            'password_confirmation' => "password",
        ]);

        // $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas(User::class, [
            'name' => '田中　太郎',
            'email' => "test@gmail.com",
        ]);

        $user = User::where('email', 'test@gmail.com')->first();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    //メール認証誘導画面で、「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
    public function test_verify_button_push()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => "田中　太郎",
            'email' => "test@gmail.com",
            'password' => "password",
            'password_confirmation' => "password",
        ]);

        $user = User::where('email', 'test@gmail.com')->first();
        $this->actingAs($user)->get('email/verify');

        $response = $this->get(route('verification.check'));

        $response->assertRedirect('http://localhost:8025/#');
    }



    //メール認証サイトのメール認証を完了すると、勤怠登録画面に遷移する
    public function test_verify_email_completion()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => "田中　太郎",
            'email' => "test@gmail.com",
            'password' => "password",
            'password_confirmation' => "password",
        ]);

        $user = User::where('email', 'test@gmail.com')->first();
        $this->actingAs($user)->get('email/verify');

        Notification::assertSentTo($user, VerifyEmail::class);

        // ③ 認証リンク（署名付きURL）を生成
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',    // Laravelデフォルトのルート名
            now()->addMinutes(60),    // 有効期限
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        // ④ 認証リンクにアクセスする（メールをクリックした想定）
        $response = $this->actingAs($user)->get($verifyUrl);

        $response->assertRedirect('/attendance');

        // ⑥ ユーザーがメール認証済みになっていること
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
