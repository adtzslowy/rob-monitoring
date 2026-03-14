<?php

use App\Http\Controllers\Auth\AuthController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\DeviceManage;
use App\Livewire\Admin\Pengaturan;
use App\Livewire\Admin\PetaMonitoring;
use App\Livewire\Admin\Profile;
use App\Livewire\Admin\SensorList;
use App\Livewire\Admin\UserManagement;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.auth');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

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
