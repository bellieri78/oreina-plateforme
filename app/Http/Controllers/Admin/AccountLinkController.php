<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use App\Services\MemberUserLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountLinkController extends Controller
{
    public function __construct(private MemberUserLinkService $linker)
    {
    }

    /**
     * Recherche JSON de fiches Member sans compte.
     */
    public function search(Request $request, User $user): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = Member::query()
            ->withoutAccount()
            ->notAnonymized()
            ->where(function ($builder) use ($q) {
                $builder->where('first_name', 'ilike', "%{$q}%")
                    ->orWhere('last_name', 'ilike', "%{$q}%")
                    ->orWhere('email', 'ilike', "%{$q}%")
                    ->orWhere('member_number', 'ilike', "%{$q}%");
            })
            ->orderBy('last_name')
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'member_number'])
            ->map(fn (Member $m) => [
                'id'            => $m->id,
                'name'          => trim($m->first_name . ' ' . $m->last_name),
                'email'         => $m->email,
                'member_number' => $m->member_number,
            ]);

        return response()->json(['results' => $results]);
    }

    /**
     * Rattache une fiche au compte.
     */
    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
        ]);

        $member = Member::findOrFail($validated['member_id']);
        $ok = $this->linker->link($user, $member);

        return redirect()
            ->route('admin.users.show', $user)
            ->with(
                $ok ? 'success' : 'error',
                $ok
                    ? 'Fiche contact rattachée au compte.'
                    : 'Rattachement impossible : ce compte a déjà une fiche, ou la fiche est déjà rattachée à un autre compte.'
            );
    }

    /**
     * Détache la fiche du compte.
     */
    public function destroy(User $user)
    {
        if ($user->member) {
            $this->linker->unlink($user->member);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Fiche contact détachée du compte.');
    }
}
