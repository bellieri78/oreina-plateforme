<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ClaimAccountController extends Controller
{
    public function show(User $user)
    {
        if ($user->claimed_at !== null) {
            return redirect()
                ->route('hub.login')
                ->with('info', 'Ce compte a déjà été activé. Connectez-vous.');
        }

        return view('auth.claim-account', ['user' => $user]);
    }

    public function store(Request $request, User $user)
    {
        if ($user->claimed_at !== null) {
            return redirect()
                ->route('hub.login')
                ->with('info', 'Ce compte a déjà été activé.');
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'claimed_at' => now(),
            'email_verified_at' => now(),
        ])->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('journal.submissions.index')
            ->with('success', 'Bienvenue ! Votre compte est activé, vous pouvez suivre vos soumissions.');
    }
}
