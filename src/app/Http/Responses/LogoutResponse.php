<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\Request;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        // 管理者なら管理者ログイン画面へ
        if ($request->is('admin/*')) {
            return redirect('/admin/login');
        }

        // 一般ユーザーなら /login へ
        return redirect('/login');
    }
}
