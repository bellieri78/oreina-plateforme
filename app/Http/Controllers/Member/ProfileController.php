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
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
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
            'newsletter_subscribed' => 'boolean',
            'consent_communication' => 'boolean',
            'consent_image' => 'boolean',
        ]);

        // Use the RGPD consent methods for proper history tracking
        if (isset($validated['newsletter_subscribed'])) {
            $member->setRgpdConsent('newsletter', (bool) $validated['newsletter_subscribed'], 'member_portal');
        }

        if (isset($validated['consent_communication'])) {
            $member->setRgpdConsent('communication', (bool) $validated['consent_communication'], 'member_portal');
        }

        if (isset($validated['consent_image'])) {
            $member->setRgpdConsent('image', (bool) $validated['consent_image'], 'member_portal');
        }

        return back()->with('success', 'Préférences mises à jour avec succès.');
    }
}
