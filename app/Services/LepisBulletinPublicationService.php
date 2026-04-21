<?php

namespace App\Services;

use App\Exceptions\Lepis\InvalidTransitionException;
use App\Exceptions\Lepis\MissingPdfException;
use App\Jobs\SyncLepisBulletinToBrevoList;
use App\Models\LepisBulletin;
use Illuminate\Support\Facades\DB;

class LepisBulletinPublicationService
{
    public function publishToMembers(LepisBulletin $bulletin): void
    {
        if (! $bulletin->isDraft()) {
            throw InvalidTransitionException::from($bulletin->status, LepisBulletin::STATUS_MEMBERS);
        }

        if (blank($bulletin->pdf_path)) {
            throw MissingPdfException::forBulletin($bulletin->id);
        }

        DB::transaction(function () use ($bulletin) {
            $bulletin->update([
                'status' => LepisBulletin::STATUS_MEMBERS,
                'published_to_members_at' => now(),
                'brevo_sync_failed' => false,
            ]);
        });

        SyncLepisBulletinToBrevoList::dispatch($bulletin);
    }

    public function makePublic(LepisBulletin $bulletin): void
    {
        if (! $bulletin->isInMembersPhase()) {
            throw InvalidTransitionException::from($bulletin->status, LepisBulletin::STATUS_PUBLIC);
        }

        $bulletin->update([
            'status' => LepisBulletin::STATUS_PUBLIC,
            'published_public_at' => now(),
        ]);
    }

    public function revertToDraft(LepisBulletin $bulletin): void
    {
        if (! $bulletin->isInMembersPhase()) {
            throw InvalidTransitionException::from($bulletin->status, LepisBulletin::STATUS_DRAFT);
        }

        if ($bulletin->brevo_synced_at !== null) {
            throw new InvalidTransitionException(
                "Impossible de revenir en brouillon : la liste Brevo a déjà été synchronisée."
            );
        }

        $bulletin->update([
            'status' => LepisBulletin::STATUS_DRAFT,
            'published_to_members_at' => null,
            'brevo_list_id' => null,
            'brevo_list_name' => null,
            'brevo_sync_failed' => false,
        ]);
    }

    public function resyncBrevo(LepisBulletin $bulletin): void
    {
        if (! $bulletin->isInMembersPhase() && ! $bulletin->isPublic()) {
            throw InvalidTransitionException::from($bulletin->status, 'resync');
        }

        $bulletin->update(['brevo_sync_failed' => false]);
        SyncLepisBulletinToBrevoList::dispatch($bulletin);
    }
}
