<?php

namespace Database\Seeders;

use App\Models\JournalIssue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JournalIssueSeeder extends Seeder
{
    public function run(): void
    {
        $issues = [
            [
                'volume_number' => 60,
                'issue_number' => 1,
                'title' => 'Oreina n°60 - Printemps 2025',
                'description' => 'Au sommaire : Les Lycaenidae de Corse, Nouvelles données sur Zerynthia polyxena, Tribune libre.',
                'publication_date' => '2025-03-15',
                'status' => 'published',
                'page_count' => 48,
                'cover_image' => 'issues/issue-60-1.jpg',
            ],
            [
                'volume_number' => 59,
                'issue_number' => 4,
                'title' => 'Oreina n°59 - Hiver 2024',
                'description' => 'Au sommaire : Bilan du programme Atlas, Les Sphingidae migrateurs, Index annuel 2024.',
                'publication_date' => '2024-12-15',
                'status' => 'published',
                'page_count' => 52,
                'cover_image' => 'issues/issue-59-4.jpg',
            ],
            [
                'volume_number' => 59,
                'issue_number' => 3,
                'title' => 'Oreina n°59 - Automne 2024',
                'description' => 'Au sommaire : Spécial Zygaenidae, Élevage de Saturnia pyri, Actualités entomologiques.',
                'publication_date' => '2024-09-15',
                'status' => 'published',
                'page_count' => 44,
                'cover_image' => 'issues/issue-60-1.jpg',
            ],
            [
                'volume_number' => 59,
                'issue_number' => 2,
                'title' => 'Oreina n°59 - Été 2024',
                'description' => 'Au sommaire : Les papillons des Alpes du Sud, Compte-rendu sortie terrain, Bibliographie.',
                'publication_date' => '2024-06-15',
                'status' => 'published',
                'page_count' => 48,
                'cover_image' => 'issues/issue-59-4.jpg',
            ],
            [
                'volume_number' => 60,
                'issue_number' => 2,
                'title' => 'Oreina n°60 - Été 2025',
                'description' => 'Numéro en préparation.',
                'publication_date' => null,
                'status' => 'draft',
                'page_count' => null,
            ],
        ];

        foreach ($issues as $issueData) {
            JournalIssue::updateOrCreate(
                [
                    'volume_number' => $issueData['volume_number'],
                    'issue_number' => $issueData['issue_number'],
                ],
                array_merge($issueData, [
                    'slug' => Str::slug($issueData['title']),
                ])
            );
        }
    }
}
