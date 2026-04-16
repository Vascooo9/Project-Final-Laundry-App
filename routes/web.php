<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Orders (accessible by all authenticated users)
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/',                           [OrderController::class, 'index'])->name('index');
        Route::get('/create',                     [OrderController::class, 'create'])->name('create');
        Route::post('/',                          [OrderController::class, 'store'])->name('store');
        Route::get('/{order}',                    [OrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status',           [OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{order}/payment',           [OrderController::class, 'processPayment'])->name('payment');
        Route::get('/{order}/receipt',            [OrderController::class, 'receipt'])->name('receipt');
    });

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {

        // Services management
        Route::prefix('admin/services')->name('admin.services.')->group(function () {
            Route::get('/',               [ServiceController::class, 'index'])->name('index');
            Route::post('/',              [ServiceController::class, 'store'])->name('store');
            Route::put('/{service}',      [ServiceController::class, 'update'])->name('update');
            Route::delete('/{service}',   [ServiceController::class, 'destroy'])->name('destroy');
        });

        // Reports
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    });
});
