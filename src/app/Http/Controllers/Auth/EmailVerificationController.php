<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{

    public function check(Request $request)
    {
        if (!$request->user()) {
            return redirect()->route('login')->with('status', 'ログインしてください');
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('profile.create')->with('status', 'メール認証済みです！');
        }

        return redirect()->route('verification.notice')->with('status', 'まだ認証が完了していません');
    }


    //認証リンククリック時
    public function verify(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('general.attendance')->with('message', 'すでに承認済みです！');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
        return redirect()->route('general.attendance')->with('message', 'メール認証が完了しました！');
    }

    //確認用メール送信画面
    public function index() {}


    //確認メール送信
    public function notification() {}

    //メールリンクの検証
    public function verification() {}
}
