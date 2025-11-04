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
        $attendanceRequests = AttendanceRequest::with('user', 'attendance')
            ->where('user_id', $user->id)
            ->get();


        return view('general.request_list', compact('attendanceRequests'));
    }
}
