<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Association Information
    |--------------------------------------------------------------------------
    |
    | Informations légales de l'association OREINA utilisées pour les reçus
    | fiscaux et autres documents officiels.
    |
    */

    'association' => [
        'name' => env('OREINA_NAME', 'OREINA - Les Lépidoptères de France'),
        'address' => env('OREINA_ADDRESS', '123 rue des Papillons'),
        'postal_code' => env('OREINA_POSTAL_CODE', '75001'),
        'city' => env('OREINA_CITY', 'Paris'),
        'siret' => env('OREINA_SIRET', '123 456 789 00012'),
        'rna' => env('OREINA_RNA', 'W123456789'),
        'objet' => env('OREINA_OBJET', 'Étude et protection des lépidoptères de France'),
        'logo' => env('OREINA_LOGO', '/images/logo.jpg'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Receipt Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration des reçus fiscaux (Cerfa).
    |
    */

    'tax_receipts' => [
        // Préfixe du numéro de reçu
        'prefix' => env('OREINA_RECEIPT_PREFIX', 'RF'),

        // Articles du CGI applicables
        'cgi_articles' => '200 et 238 bis du Code général des impôts',

        // Type d'organisme (pour Cerfa)
        'organisme_type' => 'association',
    ],
];
