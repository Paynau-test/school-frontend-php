<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScoresController;
use App\Http\Middleware\JwtAuth;
use Illuminate\Support\Facades\Route;

// ── Auth ──────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Home redirect ─────────────────────
Route::get('/', fn() => redirect('/scores'));

// ── Scores (protected) ────────────────
Route::middleware(JwtAuth::class)->group(function () {
    Route::get('/scores', [ScoresController::class, 'index'])->name('scores.index');
    Route::post('/scores', [ScoresController::class, 'store'])->name('scores.store');
});
