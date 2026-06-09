<?php

namespace App\Services;

use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Collection;

class MemberUserLinkService
{
    /**
     * Fiches Member sans compte susceptibles de correspondre a $user.
     * Classees : correspondance email exacte d'abord, puis nom + prenom.
     */
    public function suggestionsFor(User $user, int $limit = 5): Collection
    {
        $emailMatches = Member::query()
            ->withoutAccount()
            ->where('anonymise', false)
            ->whereRaw('lower(email) = ?', [mb_strtolower((string) $user->email)])
            ->get();

        $name = mb_strtolower(trim((string) $user->name));

        $nameMatches = collect();
        if ($name !== '') {
            $nameMatches = Member::query()
                ->withoutAccount()
                ->where('anonymise', false)
                ->where(function ($q) use ($name) {
                    $q->whereRaw("lower(trim(coalesce(first_name,'') || ' ' || coalesce(last_name,''))) = ?", [$name])
                      ->orWhereRaw("lower(trim(coalesce(last_name,'') || ' ' || coalesce(first_name,''))) = ?", [$name]);
                })
                ->get();
        }

        return $emailMatches->concat($nameMatches)
            ->unique('id')
            ->take($limit)
            ->values();
    }
}
