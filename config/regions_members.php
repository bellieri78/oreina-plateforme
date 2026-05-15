<?php

/**
 * Mapping département -> région métropolitaine pour les clusters de la carte
 * "Réseau des adhérents" du dashboard membre.
 *
 * Les coordonnées x/y sont relatives au viewBox SVG 200x200 utilisé dans
 * resources/views/member/partials/_reseau_map.blade.php.
 * Les DOM-TOM ne sont pas mappés (comptés dans le total mais hors carte).
 */

return [

    'regions' => [
        'HDF' => ['label' => 'Hauts-de-France',     'x' => 105, 'y' => 30],
        'IDF' => ['label' => 'Île-de-France',       'x' => 105, 'y' => 60],
        'GE'  => ['label' => 'Grand Est',           'x' => 150, 'y' => 55],
        'BRE' => ['label' => 'Bretagne',            'x' => 35,  'y' => 75],
        'PDL' => ['label' => 'Pays de la Loire',    'x' => 60,  'y' => 90],
        'NA'  => ['label' => 'Nouvelle-Aquitaine',  'x' => 75,  'y' => 130],
        'OCC' => ['label' => 'Occitanie',           'x' => 110, 'y' => 160],
        'AURA'=> ['label' => 'AURA + PACA',         'x' => 155, 'y' => 130],
    ],

    'departments' => [
        // Auvergne-Rhône-Alpes + PACA (combinés)
        '01' => 'AURA', '03' => 'AURA', '07' => 'AURA', '15' => 'AURA',
        '26' => 'AURA', '38' => 'AURA', '42' => 'AURA', '43' => 'AURA',
        '63' => 'AURA', '69' => 'AURA', '73' => 'AURA', '74' => 'AURA',
        '04' => 'AURA', '05' => 'AURA', '06' => 'AURA', '13' => 'AURA',
        '83' => 'AURA', '84' => 'AURA', '20' => 'AURA', '2A' => 'AURA', '2B' => 'AURA',

        // Bretagne
        '22' => 'BRE', '29' => 'BRE', '35' => 'BRE', '56' => 'BRE',

        // Grand Est
        '08' => 'GE', '10' => 'GE', '51' => 'GE', '52' => 'GE',
        '54' => 'GE', '55' => 'GE', '57' => 'GE', '67' => 'GE',
        '68' => 'GE', '88' => 'GE',

        // Hauts-de-France
        '02' => 'HDF', '59' => 'HDF', '60' => 'HDF', '62' => 'HDF', '80' => 'HDF',

        // Île-de-France
        '75' => 'IDF', '77' => 'IDF', '78' => 'IDF', '91' => 'IDF',
        '92' => 'IDF', '93' => 'IDF', '94' => 'IDF', '95' => 'IDF',

        // Nouvelle-Aquitaine
        '16' => 'NA', '17' => 'NA', '19' => 'NA', '23' => 'NA',
        '24' => 'NA', '33' => 'NA', '40' => 'NA', '47' => 'NA',
        '64' => 'NA', '79' => 'NA', '86' => 'NA', '87' => 'NA',

        // Occitanie
        '09' => 'OCC', '11' => 'OCC', '12' => 'OCC', '30' => 'OCC',
        '31' => 'OCC', '32' => 'OCC', '34' => 'OCC', '46' => 'OCC',
        '48' => 'OCC', '65' => 'OCC', '66' => 'OCC', '81' => 'OCC',
        '82' => 'OCC',

        // Pays de la Loire + Normandie + Centre (regroupés sur PDL pour simplifier la carte)
        '14' => 'PDL', '27' => 'PDL', '50' => 'PDL', '61' => 'PDL', '76' => 'PDL',
        '18' => 'PDL', '28' => 'PDL', '36' => 'PDL', '37' => 'PDL', '41' => 'PDL', '45' => 'PDL',
        '44' => 'PDL', '49' => 'PDL', '53' => 'PDL', '72' => 'PDL', '85' => 'PDL',

        // Bourgogne-Franche-Comté (regroupé sur Grand Est)
        '21' => 'GE', '25' => 'GE', '39' => 'GE', '58' => 'GE',
        '70' => 'GE', '71' => 'GE', '89' => 'GE', '90' => 'GE',
    ],
];
