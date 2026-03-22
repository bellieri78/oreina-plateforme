<?php

namespace Database\Seeders;

use App\Models\JournalIssue;
use App\Models\Review;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        // Créer des auteurs
        $author1 = User::firstOrCreate(
            ['email' => 'auteur1@example.com'],
            [
                'name' => 'Dr. Marie Dupont',
                'password' => bcrypt('auteur123'),
            ]
        );

        $author2 = User::firstOrCreate(
            ['email' => 'auteur2@example.com'],
            [
                'name' => 'Prof. Jean Martin',
                'password' => bcrypt('auteur123'),
            ]
        );

        $author3 = User::firstOrCreate(
            ['email' => 'auteur3@example.com'],
            [
                'name' => 'Dr. Sophie Leclerc',
                'password' => bcrypt('auteur123'),
            ]
        );

        // Créer des évaluateurs
        $reviewer1 = User::firstOrCreate(
            ['email' => 'evaluateur1@example.com'],
            [
                'name' => 'Dr. Pierre Bernard',
                'password' => bcrypt('evaluateur123'),
            ]
        );

        $reviewer2 = User::firstOrCreate(
            ['email' => 'evaluateur2@example.com'],
            [
                'name' => 'Prof. Claire Moreau',
                'password' => bcrypt('evaluateur123'),
            ]
        );

        // Récupérer l'éditeur
        $editor = User::where('email', 'editeur@oreina.org')->first();

        // Récupérer un numéro publié
        $publishedIssue = JournalIssue::where('status', 'published')
            ->orderBy('volume_number', 'desc')
            ->first();

        $submissions = [
            // Article publié
            [
                'author_id' => $author1->id,
                'journal_issue_id' => $publishedIssue?->id,
                'title' => 'Distribution et écologie de Parnassius apollo dans les Pyrénées françaises',
                'abstract' => 'Cette étude présente les résultats d\'un inventaire exhaustif de Parnassius apollo (Linnaeus, 1758) réalisé dans les Pyrénées françaises entre 2020 et 2024. Les données collectées révèlent une distribution altitudinale comprise entre 1200 et 2400 mètres, avec une préférence marquée pour les pelouses subalpines à Sedum. La comparaison avec les données historiques suggère un déplacement de l\'aire de répartition vers des altitudes plus élevées, potentiellement lié au changement climatique. Des recommandations pour la conservation de l\'espèce sont proposées.',
                'keywords' => 'Parnassius apollo, Pyrénées, distribution, écologie, conservation, changement climatique',
                'manuscript_file' => 'submissions/parnassius-apollo-pyrenees.pdf',
                'co_authors' => [
                    ['name' => 'Paul Roux', 'email' => 'proux@univ-toulouse.fr', 'affiliation' => 'Université de Toulouse'],
                    ['name' => 'Isabelle Faure', 'email' => '', 'affiliation' => 'CNRS'],
                ],
                'status' => Submission::STATUS_PUBLISHED,
                'editor_id' => $editor?->id,
                'decision' => Submission::DECISION_ACCEPT,
                'decision_at' => now()->subMonths(3),
                'doi' => '10.12345/oreina.2025.001',
                'start_page' => 12,
                'end_page' => 24,
                'submitted_at' => now()->subMonths(6),
                'published_at' => now()->subMonths(2),
            ],

            // Article accepté (en attente de publication)
            [
                'author_id' => $author2->id,
                'title' => 'Nouvelles données sur les Zygènes (Zygaenidae) du Massif Central',
                'abstract' => 'Nous présentons ici de nouvelles données sur la distribution des Zygaenidae dans le Massif Central français. Au cours de nos prospections menées entre 2022 et 2024, nous avons pu confirmer la présence de Zygaena ephialtes dans trois nouvelles localités et documenter pour la première fois Zygaena fausta dans le département du Cantal. Les habitats et plantes-hôtes sont décrits pour chaque observation.',
                'keywords' => 'Zygaenidae, Zygaena, Massif Central, Cantal, nouvelles données, distribution',
                'manuscript_file' => 'submissions/zygaenidae-massif-central.pdf',
                'co_authors' => [],
                'status' => Submission::STATUS_ACCEPTED,
                'editor_id' => $editor?->id,
                'decision' => Submission::DECISION_ACCEPT,
                'decision_at' => now()->subWeeks(2),
                'editor_notes' => 'Excellent travail de terrain. L\'article sera publié dans le prochain numéro.',
                'submitted_at' => now()->subMonths(4),
            ],

            // Article en révision (révision demandée)
            [
                'author_id' => $author3->id,
                'title' => 'Impact des pratiques agricoles sur les populations de Maculinea arion en Bourgogne',
                'abstract' => 'Cette étude examine l\'impact des différentes pratiques agricoles sur les populations de Maculinea arion (Linnaeus, 1758) dans la région Bourgogne-Franche-Comté. Les résultats montrent une corrélation négative entre l\'intensification agricole et la densité des populations. Les parcelles gérées en agriculture biologique présentent des densités significativement plus élevées. Des recommandations pour une gestion favorable à l\'espèce sont formulées.',
                'keywords' => 'Maculinea arion, Azuré du serpolet, agriculture, conservation, pratiques agricoles, Bourgogne',
                'manuscript_file' => 'submissions/maculinea-arion-bourgogne.pdf',
                'co_authors' => [
                    ['name' => 'Marc Petit', 'email' => 'mpetit@example.com', 'affiliation' => 'Association Bourgogne Nature'],
                ],
                'status' => Submission::STATUS_REVISION,
                'editor_id' => $editor?->id,
                'decision' => Submission::DECISION_MINOR,
                'decision_at' => now()->subWeeks(1),
                'editor_notes' => 'Article intéressant mais quelques points méritent d\'être clarifiés : 1) Préciser la méthodologie d\'échantillonnage. 2) Ajouter les données brutes en annexe. 3) Discuter les biais potentiels liés à la taille de l\'échantillon.',
                'submitted_at' => now()->subMonths(2),
            ],

            // Article en cours d'évaluation
            [
                'author_id' => $author1->id,
                'title' => 'Premier signalement de Colias palaeno dans les Vosges depuis 1950',
                'abstract' => 'Nous rapportons ici l\'observation de Colias palaeno (Linnaeus, 1761), le Solitaire, dans le massif des Vosges en juillet 2024. Cette observation constitue le premier signalement de l\'espèce dans ce massif depuis plus de 70 ans. Le contexte de l\'observation, l\'habitat et les implications pour la conservation sont discutés.',
                'keywords' => 'Colias palaeno, Vosges, redécouverte, conservation, tourbières',
                'manuscript_file' => 'submissions/colias-palaeno-vosges.pdf',
                'co_authors' => [],
                'status' => Submission::STATUS_IN_REVIEW,
                'editor_id' => $editor?->id,
                'submitted_at' => now()->subWeeks(3),
            ],

            // Article soumis (en attente)
            [
                'author_id' => $author2->id,
                'title' => 'Inventaire des Hétérocères nocturnes d\'un jardin urbain de Lyon',
                'abstract' => 'Cet article présente les résultats d\'un suivi de trois ans (2022-2024) des Hétérocères nocturnes dans un jardin urbain de 500 m² situé dans l\'agglomération lyonnaise. Au total, 287 espèces ont été recensées, dont plusieurs considérées comme rares à l\'échelle régionale. Cette étude démontre l\'intérêt des espaces verts privés pour la conservation de la biodiversité en milieu urbain.',
                'keywords' => 'Hétérocères, papillons de nuit, biodiversité urbaine, Lyon, inventaire',
                'manuscript_file' => 'submissions/heteroceres-jardin-lyon.pdf',
                'co_authors' => [
                    ['name' => 'Anne Beaumont', 'email' => 'abeaumont@gmail.com', 'affiliation' => ''],
                ],
                'status' => Submission::STATUS_SUBMITTED,
                'submitted_at' => now()->subDays(5),
            ],

            // Article rejeté
            [
                'author_id' => $author3->id,
                'title' => 'Observations de papillons dans mon jardin',
                'abstract' => 'J\'ai observé plein de papillons dans mon jardin cet été. Il y avait des blancs, des jaunes et des marrons. Je les ai photographiés et j\'aimerais partager ces photos avec la communauté.',
                'keywords' => 'papillons, jardin, observations',
                'manuscript_file' => 'submissions/observations-jardin.pdf',
                'co_authors' => [],
                'status' => Submission::STATUS_REJECTED,
                'editor_id' => $editor?->id,
                'decision' => Submission::DECISION_REJECT,
                'decision_at' => now()->subMonths(1),
                'editor_notes' => 'Malheureusement, cette soumission ne correspond pas aux standards de publication de la revue OREINA. Un article scientifique doit comporter une identification précise des espèces, une méthodologie rigoureuse, et une analyse contextualisant les observations. Nous vous encourageons à consulter nos instructions aux auteurs et à nous resoumettre un manuscrit plus complet.',
                'submitted_at' => now()->subMonths(1)->subDays(10),
            ],
        ];

        foreach ($submissions as $data) {
            $submission = Submission::updateOrCreate(
                ['title' => $data['title']],
                $data
            );

            // Ajouter des évaluations pour l'article en cours de révision
            if ($submission->status === Submission::STATUS_IN_REVIEW) {
                // Évaluation complétée
                Review::updateOrCreate(
                    [
                        'submission_id' => $submission->id,
                        'reviewer_id' => $reviewer1->id,
                    ],
                    [
                        'assigned_by' => $editor?->id,
                        'status' => Review::STATUS_COMPLETED,
                        'invited_at' => now()->subWeeks(2),
                        'responded_at' => now()->subWeeks(2)->addDays(1),
                        'due_date' => now()->addWeeks(2),
                        'completed_at' => now()->subDays(3),
                        'recommendation' => Review::RECOMMENDATION_MINOR,
                        'comments_to_editor' => 'Article intéressant sur une observation importante. Quelques corrections mineures à apporter concernant la nomenclature et les références bibliographiques.',
                        'comments_to_author' => 'Observation très intéressante. Je suggère d\'ajouter plus de détails sur l\'habitat exact et de citer les travaux de Lafranchis (2000) concernant l\'historique de l\'espèce dans les Vosges.',
                        'score_originality' => 5,
                        'score_methodology' => 3,
                        'score_clarity' => 4,
                        'score_significance' => 5,
                        'score_references' => 3,
                    ]
                );

                // Évaluation en cours
                Review::updateOrCreate(
                    [
                        'submission_id' => $submission->id,
                        'reviewer_id' => $reviewer2->id,
                    ],
                    [
                        'assigned_by' => $editor?->id,
                        'status' => Review::STATUS_ACCEPTED,
                        'invited_at' => now()->subWeeks(2),
                        'responded_at' => now()->subWeeks(2)->addDays(2),
                        'due_date' => now()->addWeeks(1),
                    ]
                );
            }

            // Ajouter des évaluations pour l'article accepté
            if ($submission->status === Submission::STATUS_ACCEPTED) {
                Review::updateOrCreate(
                    [
                        'submission_id' => $submission->id,
                        'reviewer_id' => $reviewer1->id,
                    ],
                    [
                        'assigned_by' => $editor?->id,
                        'status' => Review::STATUS_COMPLETED,
                        'invited_at' => now()->subMonths(3),
                        'responded_at' => now()->subMonths(3)->addDays(1),
                        'completed_at' => now()->subMonths(2)->subWeeks(2),
                        'recommendation' => Review::RECOMMENDATION_ACCEPT,
                        'comments_to_editor' => 'Excellent travail de terrain, données solides, rédaction de qualité.',
                        'comments_to_author' => 'Article bien rédigé avec des données de qualité. Félicitations pour ce travail rigoureux.',
                        'score_originality' => 4,
                        'score_methodology' => 5,
                        'score_clarity' => 5,
                        'score_significance' => 4,
                        'score_references' => 4,
                    ]
                );

                Review::updateOrCreate(
                    [
                        'submission_id' => $submission->id,
                        'reviewer_id' => $reviewer2->id,
                    ],
                    [
                        'assigned_by' => $editor?->id,
                        'status' => Review::STATUS_COMPLETED,
                        'invited_at' => now()->subMonths(3),
                        'responded_at' => now()->subMonths(3)->addDays(3),
                        'completed_at' => now()->subMonths(2)->subWeeks(1),
                        'recommendation' => Review::RECOMMENDATION_ACCEPT,
                        'comments_to_editor' => 'Très bon article, prêt pour publication.',
                        'comments_to_author' => 'Contribution importante à la connaissance des Zygaenidae du Massif Central. Données bien présentées.',
                        'score_originality' => 4,
                        'score_methodology' => 4,
                        'score_clarity' => 5,
                        'score_significance' => 4,
                        'score_references' => 5,
                    ]
                );
            }

            // Ajouter des évaluations pour l'article en révision
            if ($submission->status === Submission::STATUS_REVISION) {
                Review::updateOrCreate(
                    [
                        'submission_id' => $submission->id,
                        'reviewer_id' => $reviewer1->id,
                    ],
                    [
                        'assigned_by' => $editor?->id,
                        'status' => Review::STATUS_COMPLETED,
                        'invited_at' => now()->subMonths(1)->subWeeks(2),
                        'responded_at' => now()->subMonths(1)->subWeeks(2)->addDays(1),
                        'completed_at' => now()->subWeeks(2),
                        'recommendation' => Review::RECOMMENDATION_MINOR,
                        'comments_to_editor' => 'Sujet intéressant mais méthodologie à renforcer.',
                        'comments_to_author' => 'Le sujet est très pertinent. Cependant, la méthodologie d\'échantillonnage mériterait d\'être précisée. Comment avez-vous sélectionné les parcelles ? Quelle était la période de prospection ?',
                        'score_originality' => 4,
                        'score_methodology' => 2,
                        'score_clarity' => 4,
                        'score_significance' => 4,
                        'score_references' => 3,
                    ]
                );
            }
        }
    }
}
