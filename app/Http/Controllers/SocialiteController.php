<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;

class SocialiteController extends Controller
{
    // Redirection vers Google
    public function googleLogin()
    {
        // stateless() empêche l'erreur InvalidStateException en local
        return Socialite::driver('google')->stateless()->redirect();
    }

    // Callback Google


    public function googleCallback()
    {
        try {
            // Forcer Guzzle à utiliser le cacert.pem
            $guzzleClient = new Client([
                'verify' => 'C:\laragon\bin\php\php-8.5.1\extras\ssl\cacert.pem',
            ]);

            $googleUser = Socialite::driver('google')
                ->stateless()
                ->setHttpClient($guzzleClient)
                ->user();

            // Vérification et création de l'utilisateur (ton code existant)
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                Auth::login($user);
                return redirect()->route('home');
            }

            $existingEmail = User::where('email', $googleUser->email)->first();

            if ($existingEmail) {
                $existingEmail->update(['google_id' => $googleUser->id]);
                Auth::login($existingEmail);
                return redirect()->route('home');
            }

            $userData = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make('Password@1234'),
                'google_id' => $googleUser->id,
            ]);

            Auth::login($userData);
            return redirect()->route('home');
        } catch (\Exception $e) {
            // dd($e);
        }
    }
}
