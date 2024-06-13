<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

// Mengarahkan rute root ke halaman login
Route::get('/', function () {
    return view('landingPage');
})->name('home');

// Rute untuk halaman login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rute untuk admin dan user dashboard dengan middleware auth dan role
Route::middleware(['auth', 'role:1'])->get('/Admin-Dashboard', function () {
    return view('layouts.admin');
})->name('admin');

Route::middleware(['auth', 'role:2'])->get('/KlickCare', function () {
    return view('layouts.user');
})->name('user');
