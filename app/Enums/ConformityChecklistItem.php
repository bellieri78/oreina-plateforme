<?php

namespace App\Enums;

enum ConformityChecklistItem: string
{
    case BiblioFormat         = 'biblio_format';
    case AuthorAffiliations   = 'author_affiliations';
    case Correspondence       = 'correspondence';
    case FiguresNumbered      = 'figures_numbered';
    case Acknowledgements     = 'acknowledgements';
    case AbstractsKeywords    = 'abstracts_keywords';
    case ImageRights          = 'image_rights';
    case ConflictsOfInterest  = 'conflicts_of_interest';
    case SupplementaryData    = 'supplementary_data';

    public function label(): string
    {
        return match ($this) {
            self::BiblioFormat        => 'Format bibliographique',
            self::AuthorAffiliations  => 'Affiliations complètes',
            self::Correspondence      => 'Coordonnées de correspondance',
            self::FiguresNumbered     => 'Figures numérotées et légendées',
            self::Acknowledgements    => 'Remerciements présents',
            self::AbstractsKeywords   => 'Résumé FR + EN + mots-clés',
            self::ImageRights         => 'Droits images / copyright',
            self::ConflictsOfInterest => 'Conflits d\'intérêt déclarés',
            self::SupplementaryData   => 'Données supplémentaires identifiées',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::BiblioFormat        => 'Harvard, ordre alphabétique, cohérence citations/biblio',
            self::AuthorAffiliations  => 'Institution + adresse postale pour chaque auteur',
            self::Correspondence      => 'Email de l\'auteur référent renseigné',
            self::FiguresNumbered     => 'Figure 1, 2a, 2b… avec légendes sous l\'image',
            self::Acknowledgements    => 'Financements, aide terrain, permissions de capture',
            self::AbstractsKeywords   => 'Les 3 champs remplis, ≥ 100 caractères',
            self::ImageRights         => 'Images originales, CC, ou autorisation écrite',
            self::ConflictsOfInterest => 'Déclaration présente (même si « aucun »)',
            self::SupplementaryData   => 'Fichiers supplémentaires bien séparés du corps',
        };
    }
}
