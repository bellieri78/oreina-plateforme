<?php

namespace App\Services;

use App\Models\LepisBulletin;
use App\Models\LepisBulletinRecipient;
use App\Models\Member;
use App\Models\Membership;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LepisBulletinRecipientSnapshotter
{
    public function snapshot(LepisBulletin $bulletin): SnapshotResult
    {
        $referenceDate = $bulletin->published_to_members_at ?? Carbon::now();

        $paperCount = 0;
        $digitalCount = 0;
        $skipped = [];

        $members = Member::query()
            ->whereHas('memberships', function ($q) use ($referenceDate) {
                $q->where('status', 'active')
                    ->where('start_date', '<=', $referenceDate)
                    ->where('end_date', '>=', $referenceDate);
            })
            ->with(['memberships' => function ($q) use ($referenceDate) {
                $q->where('status', 'active')
                    ->where('start_date', '<=', $referenceDate)
                    ->where('end_date', '>=', $referenceDate)
                    ->orderByDesc('end_date');
            }])
            ->get();

        DB::transaction(function () use ($members, $bulletin, &$paperCount, &$digitalCount, &$skipped) {
            foreach ($members as $member) {
                $membership = $member->memberships->first();
                if (! $membership) {
                    continue;
                }
                $format = $membership->lepis_format ?: Membership::LEPIS_FORMAT_PAPER;

                if ($format === Membership::LEPIS_FORMAT_DIGITAL) {
                    if (empty($member->email)) {
                        $skipped[] = ['member_id' => $member->id, 'reason' => 'digital format but email missing'];
                        Log::channel('daily')->warning('Lepis snapshot skip: digital without email', [
                            'bulletin_id' => $bulletin->id, 'member_id' => $member->id,
                        ]);
                        continue;
                    }
                } else {
                    if (! $this->hasFullAddress($member)) {
                        $skipped[] = ['member_id' => $member->id, 'reason' => 'paper format but postal address incomplete'];
                        Log::channel('daily')->warning('Lepis snapshot skip: paper without address', [
                            'bulletin_id' => $bulletin->id, 'member_id' => $member->id,
                        ]);
                        continue;
                    }
                }

                LepisBulletinRecipient::updateOrCreate(
                    ['lepis_bulletin_id' => $bulletin->id, 'member_id' => $member->id],
                    [
                        'membership_id' => $membership->id,
                        'format' => $format,
                        'email_at_snapshot' => $member->email,
                        'postal_address_at_snapshot' => $format === Membership::LEPIS_FORMAT_PAPER
                            ? [
                                'address' => $member->address,
                                'postal_code' => $member->postal_code,
                                'city' => $member->city,
                                'country' => $member->country,
                            ]
                            : null,
                        'included_at' => Carbon::now(),
                    ]
                );

                if ($format === Membership::LEPIS_FORMAT_PAPER) {
                    $paperCount++;
                } else {
                    $digitalCount++;
                }
            }
        });

        return new SnapshotResult($paperCount, $digitalCount, $skipped);
    }

    private function hasFullAddress(Member $member): bool
    {
        return ! empty($member->address)
            && ! empty($member->postal_code)
            && ! empty($member->city);
    }
}
