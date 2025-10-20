<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\General\AttendanceController as GeneralAttendanceController;

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
});

// 一般ユーザー
// Route::prefix('general')->middleware(['auth'])->group(function () {
//     Route::get('/attendance', [GeneralAttendanceController::class, 'index'])->name('general.attendance');
// });

//一般ユーザーページ
Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', [GeneralAttendanceController::class, 'index'])->name('general.attendance');
    Route::post('/attendance',[GeneralAttendanceController::class,'updateStatus'])->name('attendance.update');
    Route::get('/attendance/list',[GeneralAttendanceController::class,'showList'])->name('attendance.list');

});
