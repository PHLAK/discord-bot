<?php

namespace App\Http\Controllers\OAuth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PocketIDController extends ProviderController
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('pocketid')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            /**
             * @var \Laravel\Socialite\Two\User $pocketIdUser
             *
             * @throws \Laravel\Socialite\Two\InvalidStateException
             */
            $pocketIdUser = Socialite::driver('pocketid')->user();
        } catch (InvalidStateException) {
            return redirect()->route('login')->withErrors('An unexpected error occured, please try again');
        }

        $user = User::updateOrCreate([
            'email' => $pocketIdUser->email,
        ], [
            'pocketid_id' => $pocketIdUser->id,
            'name' => $pocketIdUser->name,
            'pocketid_token' => $pocketIdUser->token,
            'pocketid_refresh_token' => $pocketIdUser->refreshToken,
        ]);

        Auth::login($user);

        return redirect('/');
    }
}
