<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class GoogleOAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $userSocialite = Socialite::driver('google')->user();
            $userDB = User::query()->where('email', '=', $userSocialite->getEmail())->first();

            if (isset($userDB)) {
                $user = User::query()->find($userDB->id);
                $user->avatar = $userSocialite->getAvatar();
                $user->email_verified_at = now();
                $user->save();

                Auth::login($user);
                return redirect(RouteServiceProvider::HOME);

            } else {
                return Inertia::render('Auth/Register', [
                    'gmail' => $userSocialite->getEmail(),
                    'username' => $userSocialite->getName(),
                ]);
            }
        }
        catch (\Exception $e) {
            return redirect()->route('login')->withErrors(
                [
                    'login' => $e->getMessage(),
                ]
            );
        }

    }
}
