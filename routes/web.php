<?php

use App\Http\Controllers\Auth\AuthController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\PetaMonitoring;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.auth');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->middleware('auth')->group(function() {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/peta-monitoring', PetaMonitoring::class)->name('peta_monitoring');
    Route::get('/settings')->name('pengaturan');
    Route::get('/akun')->name('akun_user');

});
