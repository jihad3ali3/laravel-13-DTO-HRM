<?php

use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::prefix('admin')->name('admin::')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', function (){
            return view('admin.auth.login');
        })->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('authenticate');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.admin');
    });
});
