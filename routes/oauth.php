<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::name('pocketid.')->prefix('pocketid')->group(function () {
    Route::get('redirect', [Controllers\OAuth\PocketIDController::class, 'redirect'])->name('redirect');
    Route::get('callback', [Controllers\OAuth\PocketIDController::class, 'callback'])->name('callback');
});
