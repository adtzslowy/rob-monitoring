<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FrontEndController;
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

Route::prefix('/')->group(function () {
    Route::get('/', [FrontEndController::class, 'beranda'])->name('home');
    Route::get('/tentang', [FrontEndController::class, 'about'])->name('about');
    Route::get('/', [FrontEndController::class, 'beranda'])->name('home');
    Route::get('/', [FrontEndController::class, 'beranda'])->name('home');
    Route::get('/', [FrontEndController::class, 'beranda'])->name('home');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.auth');
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
    Route::get('/peta-monitoring', PetaMonitoring::class)->middleware('permission:view dashboard')->name('peta_monitoring');
    Route::get('/daftar-alat', DeviceManage::class)->name('manajemen_alat');

    Route::middleware(['auth', 'permission:manage users'])->get('/users', UserManagement::class)->name('admin.akun');
    Route::get('/sensor-list', SensorList::class)->middleware('auth')->name('sensor.list');

    Route::get('/profil', Profile::class)->middleware('auth')->name('profil');
    Route::get('/pengaturan', Pengaturan::class)->middleware('auth')->name('pengaturan');
});
