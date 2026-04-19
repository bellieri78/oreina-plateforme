<?php

/**
 * Configuration du rendu PDF LaTeX
 *
 * Modifiez ces valeurs pour personnaliser l'apparence des articles PDF
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Polices
    |--------------------------------------------------------------------------
    */
    'fonts' => [
        // Police principale (lmodern = Latin Modern, similaire à Computer Modern)
        // Autres options: 'times' (Times New Roman), 'palatino', 'helvetica'
        'main' => env('LATEX_FONT_MAIN', 'helvetica'),

        // Taille de base du document (10pt, 11pt, 12pt)
        'base_size' => env('LATEX_FONT_SIZE', '10pt'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Couleurs (format HTML sans #)
    |--------------------------------------------------------------------------
    */
    'colors' => [
        'primary' => env('LATEX_COLOR_PRIMARY', 'EA580C'),      // Orange Chersotis (titres)
        'secondary' => env('LATEX_COLOR_SECONDARY', '0D9488'),  // Teal (sections, liens)
        'text' => env('LATEX_COLOR_TEXT', '333333'),            // Texte principal
        'gray' => env('LATEX_COLOR_GRAY', '555555'),            // Texte secondaire
        'light_gray' => env('LATEX_COLOR_LIGHT', 'F7F7F7'),     // Fond résumé
    ],

    /*
    |--------------------------------------------------------------------------
    | Marges (en mm)
    |--------------------------------------------------------------------------
    */
    'margins' => [
        'top' => env('LATEX_MARGIN_TOP', 22),
        'bottom' => env('LATEX_MARGIN_BOTTOM', 28),
        'left' => env('LATEX_MARGIN_LEFT', 18),
        'right' => env('LATEX_MARGIN_RIGHT', 18),
    ],

    /*
    |--------------------------------------------------------------------------
    | Marges du corps (pages 2+)
    |--------------------------------------------------------------------------
    |
    | Décision réunion Chersotis 2026-04-16 (section 10) : marge gauche élargie
    | pour obtenir ~130 mm de largeur utile au lieu des 160 mm précédents.
    | Sur A4 (210 mm), left=60 + right=20 → 130 mm utile.
    */
    'main_content_margins' => [
        'top' => env('LATEX_BODY_MARGIN_TOP', 22),
        'bottom' => env('LATEX_BODY_MARGIN_BOTTOM', 28),
        'left' => env('LATEX_BODY_MARGIN_LEFT', 60),
        'right' => env('LATEX_BODY_MARGIN_RIGHT', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Justification du corps de texte
    |--------------------------------------------------------------------------
    |
    | Décision réunion Chersotis 2026-04-16 (section 10) : aligné à gauche
    | (non justifié) pour une meilleure lisibilité.
    | - 'ragged' : \RaggedRight (recommandé, meilleure lisibilité dyslexie)
    | - 'justified' : justifié classique
    */
    'body_alignment' => env('LATEX_BODY_ALIGNMENT', 'ragged'),

    /*
    |--------------------------------------------------------------------------
    | Tailles de police (relatives)
    |--------------------------------------------------------------------------
    */
    'sizes' => [
        // Titre du journal (première page)
        'journal_title' => 22,

        // Titre de l'article
        'article_title' => 15,

        // Sections
        'section' => 'large',        // large, Large, LARGE, huge
        'subsection' => 'normalsize',

        // Résumé
        'abstract' => 'small',

        // Métadonnées (dates, affiliations)
        'metadata' => 'small',

        // Pied de page
        'footer' => 'footnotesize',
    ],

    /*
    |--------------------------------------------------------------------------
    | Espacements (en pt)
    |--------------------------------------------------------------------------
    */
    'spacing' => [
        // Espace avant/après les sections
        'section_before' => 18,
        'section_after' => 8,

        // Espace avant/après les sous-sections
        'subsection_before' => 12,
        'subsection_after' => 6,

        // Espace entre paragraphes
        'paragraph' => 6,
    ],

    /*
    |--------------------------------------------------------------------------
    | En-tête et pied de page
    |--------------------------------------------------------------------------
    */
    'header' => [
        'left' => 'Chersotis',
        'right' => config('journal.tagline'),
        'rule_width' => '0.5pt',
    ],

    'footer' => [
        'rule_width' => '0.3pt',
    ],

    /*
    |--------------------------------------------------------------------------
    | Première page
    |--------------------------------------------------------------------------
    */
    'first_page' => [
        // Largeur de la colonne gauche (métadonnées)
        'left_column_width' => 0.28,

        // Largeur de la colonne droite (titre, résumé)
        'right_column_width' => 0.68,
    ],

    /*
    |--------------------------------------------------------------------------
    | Images
    |--------------------------------------------------------------------------
    */
    'images' => [
        'max_width' => 0.9,      // Fraction de \textwidth
        'max_height' => 0.4,     // Fraction de \textheight
    ],

    /*
    |--------------------------------------------------------------------------
    | Informations du journal
    |--------------------------------------------------------------------------
    */
    'journal' => [
        'name' => 'Chersotis',
        'subtitle' => 'Par oreina',
        'issn_print' => '0044-586X',
        'issn_electronic' => '2107-7207',
        'license' => 'Creative Commons CC-BY 4.0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets (logos, badges) — chemins absolus pour \includegraphics
    |--------------------------------------------------------------------------
    */
    'assets' => [
        'papillon_logo' => storage_path('app/public/latex/assets/oreina-papillon.png'),
        'oreina_logo' => storage_path('app/public/latex/assets/oreina-noir-ligne.png'),
        'open_access' => storage_path('app/public/latex/assets/open-access.png'),
        'cc_by' => storage_path('app/public/latex/assets/cc-by-4.0.png'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Couleurs additionnelles (ajoutées pour refonte Biology Letters)
    |--------------------------------------------------------------------------
    */
    'colors_extra' => [
        'dark_teal' => env('LATEX_COLOR_DARK_TEAL', '0F766E'),  // Citation sidebar, ancien titre — back-compat
        // Vert Chersotis (charte OREINA) pour les titres H1/article
        // Décision réunion 2026-04-16 section 10 : préféré au bleu/teal
        'title_green' => env('LATEX_COLOR_TITLE_GREEN', '2C5F2D'),
    ],

];
