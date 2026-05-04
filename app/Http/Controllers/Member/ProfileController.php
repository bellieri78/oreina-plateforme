<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();
        $currentMembership = $member?->currentMembership();
        $lepisFormat = $currentMembership?->lepisFormatOrDefault();

        return view('member.profile', compact('user', 'member', 'lepisFormat'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return back()->with('error', 'Profil membre non trouvé.');
        }

        $validated = $request->validate([
            'civilite' => 'nullable|string|max:10',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'telephone_fixe' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'interests' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($member->photo_path) {
                Storage::disk('public')->delete($member->photo_path);
            }

            $path = $request->file('photo')->store('members/photos', 'public');
            $validated['photo_path'] = $path;
        }

        unset($validated['photo']);
        $member->update($validated);

        // Update user email if changed
        if ($user->email !== $validated['email']) {
            $user->update(['email' => $validated['email']]);
        }

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function preferences()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        return view('member.preferences', compact('user', 'member'));
    }

    public function updatePreferences(Request $request)
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return back()->with('error', 'Profil membre non trouvé.');
        }

        $validated = $request->validate([
            'newsletter_subscribed'   => 'sometimes|boolean',
            'consent_communication'   => 'sometimes|boolean',
            'consent_image'           => 'sometimes|boolean',
            'directory_opt_in'        => 'sometimes|boolean',
            'directory_phone_visible' => 'sometimes|boolean',
            'directory_groups'        => 'required_if:directory_opt_in,1|array|min:1',
            'directory_groups.*'      => 'in:rhopalo,micro,macro,zygenes',
        ]);

        // 1) Consents simples (newsletter / communication / image)
        $member->setRgpdConsent('newsletter',    (bool) $request->boolean('newsletter_subscribed'),  'member_portal');
        $member->setRgpdConsent('communication', (bool) $request->boolean('consent_communication'), 'member_portal');
        $member->setRgpdConsent('image',         (bool) $request->boolean('consent_image'),         'member_portal');

        // 2) Annuaire — opt-in/opt-out
        $optIn = $request->boolean('directory_opt_in');
        $member->setRgpdConsent(\App\Models\RgpdConsentHistory::TYPE_DIRECTORY, $optIn, 'member_portal');

        // 3) Détails annuaire (groupes + phone visible) — uniquement si opt-in
        if ($optIn) {
            $member->update([
                'directory_groups' => $request->input('directory_groups', []),
                'directory_phone_visible' => (bool) $request->boolean('directory_phone_visible'),
            ]);
        }
        // Si opt-out : on conserve groups et phone_visible pour ré-activation future.

        return back()->with('success', 'Préférences mises à jour avec succès.');
    }
}
