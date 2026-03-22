<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Brevo API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'integration avec Brevo (ex-Sendinblue).
    | Documentation API : https://developers.brevo.com/
    |
    */

    'api_key' => env('BREVO_API_KEY'),

    'webhook_secret' => env('BREVO_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Listes Brevo
    |--------------------------------------------------------------------------
    |
    | IDs des listes Brevo pour la synchronisation des contacts.
    | Creer ces listes dans Brevo et renseigner leurs IDs ici.
    |
    */

    'lists' => [
        'all_contacts' => env('BREVO_LIST_ALL', null),        // Tous les contacts
        'members' => env('BREVO_LIST_MEMBERS', null),          // Adherents actifs
        'newsletter' => env('BREVO_LIST_NEWSLETTER', null),    // Inscrits newsletter
        'donors' => env('BREVO_LIST_DONORS', null),            // Donateurs
    ],

    /*
    |--------------------------------------------------------------------------
    | Attributs personnalises
    |--------------------------------------------------------------------------
    |
    | Mapping des attributs Laravel vers les attributs Brevo.
    | Ces attributs doivent etre crees dans Brevo avant la sync.
    |
    */

    'attributes' => [
        'PRENOM' => 'first_name',
        'NOM' => 'last_name',
        'MEMBRE_NUMERO' => 'member_number',
        'VILLE' => 'city',
        'CODE_POSTAL' => 'postal_code',
        'DATE_ADHESION' => 'joined_at',
        'DATE_EXPIRATION' => 'membership_expires_at',
        'EST_ADHERENT' => 'is_member',
        'EST_DONATEUR' => 'is_donor',
        'NEWSLETTER' => 'newsletter_subscribed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Options de synchronisation
    |--------------------------------------------------------------------------
    */

    'sync' => [
        // Nombre de contacts par batch (max 150 pour Brevo)
        'batch_size' => 100,

        // Inclure les contacts inactifs
        'include_inactive' => false,

        // Mettre a jour les contacts existants
        'update_existing' => true,
    ],
];
