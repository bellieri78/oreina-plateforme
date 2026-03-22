<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login and get API token
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        $deviceName = $request->device_name ?? ($request->header('User-Agent') ?? 'API Token');
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'user' => $this->formatUser($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', PasswordRules::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Compte créé avec succès.',
            'user' => $this->formatUser($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Get current authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->formatUser($request->user()),
        ]);
    }

    /**
     * Logout (revoke current token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.',
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'message' => 'Profil mis à jour.',
            'user' => $this->formatUser($user->fresh()),
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', PasswordRules::defaults()],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Le mot de passe actuel est incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Mot de passe mis à jour.',
        ]);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email.',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRules::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Mot de passe réinitialisé avec succès.',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * Verify token for external apps (SSO)
     * Used by BDC, Artemisiae, and other external applications
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'app' => 'nullable|string|in:bdc,artemisiae,mobile',
        ]);

        // Find the token
        $tokenHash = hash('sha256', $request->token);
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->token);

        if (!$accessToken) {
            return response()->json([
                'valid' => false,
                'message' => 'Token invalide ou expiré.',
            ], 401);
        }

        $user = $accessToken->tokenable;

        if (!$user) {
            return response()->json([
                'valid' => false,
                'message' => 'Utilisateur non trouvé.',
            ], 401);
        }

        // Check if token is expired (if expiration is set)
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json([
                'valid' => false,
                'message' => 'Token expiré.',
            ], 401);
        }

        // Return user info for the external app
        return response()->json([
            'valid' => true,
            'user' => $this->formatUser($user),
            'app' => $request->app,
            'permissions' => $this->getAppPermissions($user, $request->app),
        ]);
    }

    /**
     * Format user data for API response
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at?->toISOString(),
        ];
    }

    /**
     * Get user permissions for a specific app
     */
    private function getAppPermissions(User $user, ?string $app): array
    {
        // Default permissions - can be expanded based on user roles
        $permissions = [
            'access' => true,
            'role' => 'user',
        ];

        // Add app-specific permissions
        switch ($app) {
            case 'bdc':
                $permissions['can'] = ['view', 'contribute'];
                break;
            case 'artemisiae':
                $permissions['can'] = ['view', 'submit_observation'];
                break;
            default:
                $permissions['can'] = ['view'];
        }

        return $permissions;
    }
}
