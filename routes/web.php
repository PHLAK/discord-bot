<?php

use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index');
Route::view('/login', 'login')->name('login')->middleware('guest');
Route::post('/logout', LogoutController::class)->name('logout')->middleware('auth');
