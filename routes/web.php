<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfilController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\PetaMonitoring;
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

    Route::get('/settings', function () {
        return view('admin.settings');
    })->middleware('permission:manage settings')->name('pengaturan');

    Route::middleware(['auth', 'permission:manage users'])->get('/users', UserManagement::class)->name('admin.akun');

    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
});
