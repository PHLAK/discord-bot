<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
*/

Route::name('plex-event')->post('plex-event', Controllers\Webhooks\PlexEventController::class);
