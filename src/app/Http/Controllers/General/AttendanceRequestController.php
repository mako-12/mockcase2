<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;

class AttendanceRequestController extends Controller
{
    //申請一覧画面表示
    public function index()
    {
        $user = Auth::user();

        $requestData = AttendanceRequest::with('breakRequests')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('general.request_list');
    }
}
