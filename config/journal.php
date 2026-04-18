<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Journal Information
    |--------------------------------------------------------------------------
    |
    | Basic information about the scientific journal
    |
    */

    'name' => env('JOURNAL_NAME', 'Chersotis'),
    'abbreviation' => env('JOURNAL_ABBREVIATION', 'Chersotis'),
    'publisher' => env('JOURNAL_PUBLISHER', 'OREINA'),

    /*
    |--------------------------------------------------------------------------
    | ISSN Numbers
    |--------------------------------------------------------------------------
    |
    | International Standard Serial Number for print and electronic versions
    |
    */

    'issn_print' => env('JOURNAL_ISSN_PRINT', ''),
    'issn_electronic' => env('JOURNAL_ISSN_ELECTRONIC', ''),

    /*
    |--------------------------------------------------------------------------
    | DOI Configuration
    |--------------------------------------------------------------------------
    |
    | DOI prefix assigned by Crossref or other DOI registration agency
    |
    */

    'doi_prefix' => env('JOURNAL_DOI_PREFIX', '10.24349'),

    /*
    |--------------------------------------------------------------------------
    | Review Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the peer review process
    |
    */

    'min_reviewers' => env('JOURNAL_MIN_REVIEWERS', 2),
    'max_reviewers' => env('JOURNAL_MAX_REVIEWERS', 3),
    'review_deadline_days' => env('JOURNAL_REVIEW_DEADLINE_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | License
    |--------------------------------------------------------------------------
    |
    | Default license for published articles
    |
    */

    'license' => env('JOURNAL_LICENSE', 'CC-BY-4.0'),
    'license_url' => env('JOURNAL_LICENSE_URL', 'https://creativecommons.org/licenses/by/4.0/'),

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    */
    'tagline' => env('JOURNAL_TAGLINE', 'Revue scientifique d\'Oreina'),
    'contact_email' => env('JOURNAL_CONTACT_EMAIL', 'revue@oreina.org'),

    'show_orcid' => env('JOURNAL_SHOW_ORCID', false),

    /*
    |--------------------------------------------------------------------------
    | Upload presets (utilisés par la rule App\Rules\SafeUpload)
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'manuscript' => [
            'exts'   => ['doc', 'docx', 'odt'],
            'mimes'  => [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.oasis.opendocument.text', // ODT (MIME nominal)
                'application/zip', // fallback : finfo renvoie application/zip pour ODT/DOCX sur certains systèmes (Windows notamment)
            ],
            'max_kb' => 30720, // 30 Mo
        ],
        'supplementary' => [
            'exts'   => ['xls', 'xlsx', 'pdf', 'zip'],
            'mimes'  => [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/pdf',
                'application/zip',
            ],
            'max_kb' => 51200, // 50 Mo
        ],
        'image' => [
            'exts'   => ['png', 'jpg', 'jpeg', 'tiff'],
            'mimes'  => ['image/png', 'image/jpeg', 'image/tiff'],
            'max_kb' => 20480, // 20 Mo (hi-res 300/600 DPI)
        ],
    ],

];
