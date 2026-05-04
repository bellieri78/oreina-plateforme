<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MemberDirectoryService
{
    public function filter(array $params, Member $excluding): Collection
    {
        $query = Member::inDirectory()->where('id', '!=', $excluding->id);

        if (!empty($params['departments']) && is_array($params['departments'])) {
            $query->where(function ($q) use ($params) {
                foreach ($params['departments'] as $dept) {
                    $q->orWhere('postal_code', 'like', $dept . '%');
                }
            });
        }

        if (!empty($params['groups']) && is_array($params['groups'])) {
            $placeholders = implode(',', array_fill(0, count($params['groups']), '?'));
            $query->whereRaw(
                "jsonb_exists_any(directory_groups, ARRAY[$placeholders])",
                $params['groups']
            );
        }

        if (!empty($params['q'])) {
            $needle = '%' . str_replace(['%', '_'], ['\%', '\_'], $params['q']) . '%';
            $query->where(function ($w) use ($needle) {
                $w->where('first_name', 'ilike', $needle)
                  ->orWhere('last_name', 'ilike', $needle);
            });
        }

        return $query->orderBy('last_name')->orderBy('first_name')->get();
    }

    public function toJsonRow(Member $member): array
    {
        $phone = ($member->directory_phone_visible && !empty($member->mobile))
            ? $member->mobile
            : null;

        return [
            'id'         => $member->id,
            'first_name' => $member->first_name,
            'last_name'  => $member->last_name,
            'department' => $member->directoryDepartment(),
            'email'      => $member->email,
            'phone'      => $phone,
            'groups'     => $member->directory_groups ?? [],
            'photo_url'  => $member->photo_path ? Storage::disk('public')->url($member->photo_path) : null,
        ];
    }
}
