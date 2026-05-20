<?php

use Illuminate\Support\Facades\Route;

// Halaman utama (index.blade.php)
Route::get('/', function () {
    return view('index');
});

// Halaman profil per role
Route::get('/pages/pelajar/profile', function () {
    return view('pages.pelajar.profile');
});

Route::get('/pages/mentor/profile', function () {
    return view('pages.mentor.profile');
});

Route::get('/pages/mitra/profile', function () {
    return view('pages.mitra.profile');
});
