<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetFindingController;
use App\Http\Controllers\BeritaAcaraController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Asset Findings — semua user bisa CRUD
        Route::resource('findings', AssetFindingController::class);
        Route::patch('findings/{finding}/status', [AssetFindingController::class, 'updateStatus'])->name('findings.updateStatus');

        // Asset Findings — hanya CPM
        Route::middleware('role:CPM')->group(function () {
            Route::post('findings/{finding}/approve', [AssetFindingController::class, 'approve'])->name('findings.approve');
            Route::post('findings/{finding}/cancel-approve', [AssetFindingController::class, 'cancelApprove'])->name('findings.cancelApprove');
        });

        // Berita Acara — semua user bisa CRUD
        Route::get('ba/{ba}/print', [BeritaAcaraController::class, 'print'])->name('ba.print');
        Route::resource('ba', BeritaAcaraController::class);

        // Berita Acara — hanya CPM
        Route::middleware('role:CPM')->group(function () {
            Route::post('ba/{ba}/approve', [BeritaAcaraController::class, 'approve'])->name('ba.approve');
            Route::post('ba/{ba}/cancel-approve', [BeritaAcaraController::class, 'cancelApprove'])->name('ba.cancelApprove');
        });
    });

