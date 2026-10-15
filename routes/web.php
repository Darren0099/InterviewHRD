<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\KandidatController;
use App\Http\Controllers\PenilaianController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('throttle:8,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('hrd.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/kandidat', [KandidatController::class, 'index'])->name('kandidat');

    Route::get('/top', [PenilaianController::class, 'top'])->name('top');
    Route::post('/penilaian', [PenilaianController::class, 'store'])->name('penilaian.store');
    Route::put('/penilaian/{penilaian}', [PenilaianController::class, 'update'])->name('penilaian.update');
    Route::delete('/penilaian/{penilaian}', [PenilaianController::class, 'destroy'])->name('penilaian.destroy');
    Route::delete('/regional', [PenilaianController::class, 'destroyRegional'])->name('regional.destroy');

    Route::get('/unduh', [ExportController::class, 'index'])->name('unduh');
    Route::get('/unduh/excel', [ExportController::class, 'excel'])->name('export.excel');
    Route::get('/unduh/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
});
