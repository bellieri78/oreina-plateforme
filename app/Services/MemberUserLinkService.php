<?php

namespace App\Services;

use App\Models\AuditLog;
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

    /**
     * Rattache $member a $user de facon atomique.
     * Retourne false si le user a deja une fiche, ou si la fiche est deja prise.
     */
    public function link(User $user, Member $member): bool
    {
        if ($user->member()->exists()) {
            return false;
        }

        $affected = Member::whereKey($member->id)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        if ($affected === 0) {
            return false;
        }

        AuditLog::log(
            'members',
            $member->id,
            AuditLog::ACTION_UPDATE,
            ['user_id' => null],
            ['user_id' => $user->id],
            "Rattachement manuel au compte #{$user->id} ({$user->email})"
        );

        return true;
    }

    /**
     * Detache la fiche $member de son compte.
     */
    public function unlink(Member $member): void
    {
        $oldUserId = $member->user_id;

        Member::whereKey($member->id)->update(['user_id' => null]);

        AuditLog::log(
            'members',
            $member->id,
            AuditLog::ACTION_UPDATE,
            ['user_id' => $oldUserId],
            ['user_id' => null],
            "Detachement manuel du compte #{$oldUserId}"
        );
    }
}
