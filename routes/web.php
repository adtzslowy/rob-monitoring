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

Route::prefix('/')->middleware('guest')->group(function() {
    Route::get('/', [FrontEndController::class, 'beranda'])->name('home');
    Route::get('tentang', [FrontEndController::class, 'about'])->name('tentang');
    Route::get('fitur', [FrontEndController::class, 'features'])->name('fitur');
    Route::get('peta-monitoring', [FrontEndController::class, 'maps'])->name('peta');
    Route::get('alur-kerja', [FrontEndController::class, 'workflows'])->name('alur_kerja');
    Route::get('status', [FrontEndController::class, 'stats'])->name('status');
    Route::get('kontak', [FrontEndController::class, 'contact'])->name('kontak');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login/proses', [AuthController::class, 'login'])->name('login.auth');
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
