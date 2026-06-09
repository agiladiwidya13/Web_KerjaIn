<?php

use Illuminate\Support\Facades\Route;

// ── Halaman Utama (Index) ────────────────────────────────────
Route::get('/', function () {
    return view('index');
});


// ── Dashboard per role ───────────────────────────────────────
Route::get('/pages/pelajar/dashboard', function () {
    return view('pages.pelajar.dashboard');
});

Route::get('/pages/mentor/dashboard', function () {
    return view('pages.mentor.dashboard');
});

Route::get('/pages/mitra/dashboard', function () {
    return view('pages.mitra.dashboard');
});

// ── Profil per role ──────────────────────────────────────────
Route::get('/pages/pelajar/profile', function () {
    return view('pages.pelajar.profile');
});

Route::get('/pages/mentor/profile', function () {
    return view('pages.mentor.profile');
});

Route::get('/pages/mitra/profile', function () {
    return view('pages.mitra.profile');
});

// ── Browse & Detail Program ──────────────────────────────────
Route::get('/pages/programs', function () {
    return view('pages.programs.browse');
});

Route::get('/pages/programs/{id}', function ($id) {
    return view('pages.programs.detail', ['programId' => $id]);
});

// ── Pelajar: Enrollment Detail ───────────────────────────────
Route::get('/pages/pelajar/enrollments/{id}', function ($id) {
    return view('pages.pelajar.enrollment-detail', ['enrollmentId' => $id]);
});
Route::get('/pages/pelajar/leaderboard', function () {
    return view('pages.pelajar.leaderboard');
});

// ── Fase 3: Sertifikat & Profil Publik ───────────────────────
Route::get('/sertifikat/{id}', [\App\Http\Controllers\CertificateController::class, 'show']);
Route::get('/profil/{id}', [\App\Http\Controllers\PelajarController::class, 'publicProfile']);

// ── Mitra: Program Management ────────────────────────────────
Route::get('/pages/mitra/programs', function () {
    return view('pages.mitra.programs');
});
Route::get('/pages/mitra/candidates', function () {
    return view('pages.mitra.candidates');
});

Route::get('/pages/mitra/programs/{id}', function ($id) {
    return view('pages.mitra.program-detail', ['programId' => $id]);
});

// ── Mentor: Submissions Review ───────────────────────────────
Route::get('/pages/mentor/submissions', function () {
    return view('pages.mentor.submissions');
});
