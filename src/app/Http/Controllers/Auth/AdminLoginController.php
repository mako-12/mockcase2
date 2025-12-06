<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminLoginRequest;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin_login');
    }

    public function login(AdminLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');


        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();

            if ($user->role !== 1) {
                Auth::logout();
                return back()->withErrors([
                    'email' => '管理者専用アカウントではありません。',
                ])->withInput();
            }

            $request->session()->regenerate();
            return redirect()->route('admin.attendance.list');
        }

        return back()->withErrors([
            'email' => trans('auth.failed'),
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
