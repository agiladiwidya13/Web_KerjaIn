<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PelajarController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\MitraController;
 
/*
|--------------------------------------------------------------------------
| API Routes - KerjaIn
|--------------------------------------------------------------------------
*/
 
Route::middleware('web')->group(function () {
 
    // ── Public ────────────────────────────────────────────────────────────
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/session',   [AuthController::class, 'getSession']);
    Route::post('/logout',   [AuthController::class, 'logout']);
 
    // ── Pelajar ───────────────────────────────────────────────────────────
    Route::middleware('role:pelajar')->prefix('pelajar')->group(function () {
        Route::get('/profile',  [PelajarController::class, 'show']);
        Route::post('/update',  [PelajarController::class, 'update']);
        Route::post('/delete',  [PelajarController::class, 'destroy']);
    });
 
    // ── Mentor ────────────────────────────────────────────────────────────
    Route::middleware('role:mentor')->prefix('mentor')->group(function () {
        Route::get('/profile',  [MentorController::class, 'show']);
        Route::post('/update',  [MentorController::class, 'update']);
        Route::post('/delete',  [MentorController::class, 'destroy']);
    });
 
    // ── Mitra ─────────────────────────────────────────────────────────────
    Route::middleware('role:mitra')->prefix('mitra')->group(function () {
        Route::get('/profile',  [MitraController::class, 'show']);
        Route::post('/update',  [MitraController::class, 'update']);
        Route::post('/delete',  [MitraController::class, 'destroy']);
    });
 
});