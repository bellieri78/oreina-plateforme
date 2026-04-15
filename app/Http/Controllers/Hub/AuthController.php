<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        return view('hub.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('member.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        return view('hub.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'cf-turnstile-response' => ['nullable', new \App\Rules\TurnstileCaptcha()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        // Rattachement automatique à une fiche Member existante par email
        $member = Member::where('email', $validated['email'])
            ->whereNull('user_id')
            ->first();

        if ($member) {
            $member->update(['user_id' => $user->id]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('member.dashboard')
            ->with('success', $member
                ? 'Bienvenue ! Votre compte a été rattaché à votre fiche adhérent. Un email de vérification vous a été envoyé.'
                : 'Bienvenue ! Votre compte a été créé. Un email de vérification vous a été envoyé.'
            );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('hub.home');
    }
}
