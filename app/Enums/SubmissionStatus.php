<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Draft               = 'draft';
    case Submitted           = 'submitted';
    case UnderInitialReview  = 'under_initial_review';
    case RevisionRequested   = 'revision_requested';
    case UnderPeerReview     = 'under_peer_review';
    case RevisionAfterReview = 'revision_after_review';
    case Accepted            = 'accepted';
    case InProduction        = 'in_production';
    case Published           = 'published';
    case Rejected            = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Draft               => 'Brouillon',
            self::Submitted           => 'Soumis',
            self::UnderInitialReview  => 'Évaluation initiale',
            self::RevisionRequested   => 'Retour auteur (avant relecture)',
            self::UnderPeerReview     => 'En relecture',
            self::RevisionAfterReview => 'Révision demandée (après relecture)',
            self::Accepted            => 'Accepté',
            self::InProduction        => 'En maquettage',
            self::Published           => 'Publié',
            self::Rejected            => 'Rejeté',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft               => 'gray',
            self::Submitted           => 'blue',
            self::UnderInitialReview  => 'amber',
            self::RevisionRequested   => 'orange',
            self::UnderPeerReview     => 'indigo',
            self::RevisionAfterReview => 'orange',
            self::Accepted            => 'green',
            self::InProduction        => 'teal',
            self::Published           => 'emerald',
            self::Rejected            => 'red',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Published, self::Rejected], true);
    }

    public function isEditorial(): bool
    {
        return in_array($this, [
            self::UnderInitialReview,
            self::RevisionRequested,
            self::UnderPeerReview,
            self::RevisionAfterReview,
        ], true);
    }

    public static function labels(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }
        return $out;
    }
}
