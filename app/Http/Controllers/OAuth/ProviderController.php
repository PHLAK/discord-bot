<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class ProviderController extends Controller
{
    abstract public function redirect(): RedirectResponse;

    abstract public function callback(Request $request): RedirectResponse;
}
