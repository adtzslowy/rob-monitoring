<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\FrontEndController;
use App\Livewire\Admin\Analisis as AdminAnalisis;
use App\Livewire\Admin\ContactAdmin;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\DeviceManage;
use App\Livewire\Admin\Pengaturan;
use App\Livewire\Admin\PetaMonitoring;
use App\Livewire\Admin\Profile;
use App\Livewire\Admin\SensorList;
use App\Livewire\Admin\UserManagement;
use App\Services\TelegramServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::prefix('/')->middleware('guest')->group(function () {
    Route::get('/', [FrontEndController::class, 'beranda'])->name('home');
    Route::get('tentang', [FrontEndController::class, 'about'])->name('tentang');
    Route::get('peta-monitoring', [FrontEndController::class, 'maps'])->name('peta');
    Route::get('analisis', function () {
        return view('landing.analisis');
    })->name('analisis');
    Route::get('kritiksaran', [FrontEndController::class, 'contact'])->name('kritiksaran');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login/proses', [AuthController::class, 'login'])->name('login.auth');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.send-otp');

    Route::get('/forgot-password/otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp');
    Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify-otp');
    Route::post('/forgot-password/resend', [ForgotPasswordController::class, 'resendOtp'])->name('password.resend-otp');

    Route::get('/forgot-password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::post('/telegram/webhook', function (Request $request) {
    $update = $request->all();
    app(TelegramServices::class)->handleCommand($update);

    return response()->json(['ok' => true]);
});
/**
 * Dashboard routing
 */
Route::prefix('dashboard')->middleware(['auth', 'permission:view dashboard'])->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/peta-monitoring', PetaMonitoring::class)->name('peta_monitoring');
    Route::get('/daftar-alat', DeviceManage::class)->name('manajemen_alat');

    Route::middleware(['permission:manage users'])->get('/users', UserManagement::class)->name('admin.akun');
    Route::get('/sensor-list', SensorList::class)->name('sensor.list');

    Route::get('/profil', Profile::class)->name('profil');
    Route::get('/pengaturan', Pengaturan::class)->name('pengaturan');
    Route::get('/analisis', AdminAnalisis::class)->name('analisis.data');
    Route::get('/kritiksaran', ContactAdmin::class)->middleware('permission:manage users')->name('kritik.saran');
});
