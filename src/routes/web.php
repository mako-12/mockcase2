<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsvDownloadController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\General\AttendanceController as GeneralAttendanceController;
use App\Http\Controllers\Admin\AttendanceRequestController as AdminAttendanceRequestController;
use App\Http\Controllers\General\AttendanceRequestController as GeneralAttendanceRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//管理者用のログイン・ログアウト
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});

//管理者用ページ
Route::prefix('admin')->middleware(['auth:admin', 'can:isAdmin'])->group(function () {
    Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.list');
    //スタッフリスト
    Route::get('/staff/list', [AdminAttendanceController::class, 'showStaffList'])->name('admin.staff.list');
    // スタッフ別月次勤怠
    Route::get('/attendance/staff/{id}', [AdminAttendanceController::class, 'showStaffAttendance'])->name('admin.staff.attendance');
    //勤怠詳細
    Route::get('/attendance/{id}', [AdminAttendanceController::class, 'showDetail'])->name('admin.attendance.detail');
    Route::post('/attendance{id}', [AdminAttendanceController::class, 'updateAttendance'])->name('admin.attendance.update');
    //申請一覧画面
    Route::get('/stamp_correction_request/list', [AdminAttendanceRequestController::class, 'showRequest'])->name('admin.attendance.request');
    //修正申請承認画面
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminAttendanceRequestController::class, 'showRequestApproval'])->name('admin.request.approval');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminAttendanceRequestController::class, 'approveRequest'])->name('admin.approve.request');

    //csv出力
    Route::get('/csv-download', [CsvDownloadController::class, 'downloadCsv'])->name('csv.download');
});



// 一般ユーザー
// Route::prefix('general')->middleware(['auth'])->group(function () {
//     Route::get('/attendance', [GeneralAttendanceController::class, 'index'])->name('general.attendance');
// });

//一般ユーザーページ
Route::middleware(['auth', 'verified'])->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // });
    Route::get('/attendance', [GeneralAttendanceController::class, 'index'])->name('general.attendance');
    Route::post('/attendance', [GeneralAttendanceController::class, 'updateStatus'])->name('attendance.update');
    Route::get('/attendance/list', [GeneralAttendanceController::class, 'showList'])->name('attendance.list');

    //勤怠詳細画面
    Route::get('/attendance/detail/{id}', [GeneralAttendanceController::class, 'showDetail'])->name('attendance.detail');
    Route::post('/attendance/request/{id}', [GeneralAttendanceController::class, 'submitRequest'])->name('attendance.request.submit');

    //申請一覧画面
    Route::get('/stamp_correction_request/list', [GeneralAttendanceRequestController::class, 'index'])->name('general.attendance.request');


    // Route::get('/test-mail', function () {
    // Mail::raw('これはテストメールです', function ($message) {
    //     $message->to('test@example.com')->subject('テスト送信');
    // });
    // return 'メールを送信しました';
});


//メール認証機能(未承認ユーザーをverifyにリダイレクト)
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

//認証リンククリック時
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])->name('verification.verify');

//再送ボタン
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '承認メールを再送しました！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//承認リンクをクリックしたら承認されるように
// Route::get('/email/check', function () {
//     return redirect()->away('http://localhost:8025/#');
// })->middleware('auth')->name('verification.check');

Route::get('/email/check', function () {
    return redirect()->away('http://localhost:8025/#');
})->name('verification.check');
