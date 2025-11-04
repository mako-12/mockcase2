<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\General\AttendanceController as GeneralAttendanceController;
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

// Route::get('/admin/login',[])

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
    Route::get('/attendance/staff/{id}',[AdminAttendanceController::class,'showStaffAttendance'])->name('admin.staff.attendance');
    Route::get('/attendance/{id}', [AdminAttendanceController::class, 'showDetail'])->name('admin.attendance.detail');
    Route::post('/attendance{id}', [AdminAttendanceController::class, 'updateAttendance'])->name('admin.attendance.update');
});

Route::get('/stamp_correction_request/list', [AdminAttendanceController::class, 'showRequest'])->name('admin.attendance.request');

// 一般ユーザー
// Route::prefix('general')->middleware(['auth'])->group(function () {
//     Route::get('/attendance', [GeneralAttendanceController::class, 'index'])->name('general.attendance');
// });

//一般ユーザーページ
Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', [GeneralAttendanceController::class, 'index'])->name('general.attendance');
    Route::post('/attendance', [GeneralAttendanceController::class, 'updateStatus'])->name('attendance.update');
    Route::get('/attendance/list', [GeneralAttendanceController::class, 'showList'])->name('attendance.list');

    //勤怠詳細画面
    Route::get('/attendance/detail/{id}', [GeneralAttendanceController::class, 'showDetail'])->name('attendance.detail');
    Route::post('/attendance/request/{id}', [GeneralAttendanceController::class, 'submitRequest'])->name('attendance.request.submit');


    //申請一覧画面
    Route::get('/request_list', [GeneralAttendanceRequestController::class, 'index'])->name('general.attendance.request');
});
