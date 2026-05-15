<?php

namespace Database\Seeders;

use App\Models\WorkGroup;
use Illuminate\Database\Seeder;

class WorkGroupSeeder extends Seeder
{
    public function run(): void
    {
        if (WorkGroup::count() > 0) {
            return;
        }

        WorkGroup::create([
            'name' => 'Groupe de travail validateurs',
            'description' => "Espace de validation collaborative : documents cadre, échanges sur les outils, ressources bibliographiques et banques de photos de genitalia pour aider à la détermination au quotidien.",
            'color' => '#2C5F2D',
            'icon' => 'shield-check',
            'is_active' => true,
            'has_resources' => true,
            'has_collaborative_space' => true,
            'collaborative_space_url' => 'https://framadrive.org/',
            'has_forum' => true,
            'join_policy' => 'request',
        ]);

        WorkGroup::create([
            'name' => 'Zygaenidae France',
            'description' => "Groupe thématique d'échange autour des Zygènes de France métropolitaine.",
            'color' => '#356B8A',
            'icon' => 'bug',
            'is_active' => true,
            'has_resources' => true,
            'has_collaborative_space' => false,
            'has_forum' => false,
            'join_policy' => 'open',
        ]);

        WorkGroup::create([
            'name' => 'Atlas Grand Est',
            'description' => "Inventaire participatif des Lépidoptères du Grand Est.",
            'color' => '#85B79D',
            'icon' => 'map',
            'is_active' => true,
            'has_resources' => true,
            'has_collaborative_space' => false,
            'has_forum' => false,
            'join_policy' => 'open',
        ]);

        $valid = \App\Models\WorkGroup::where('slug', 'groupe-de-travail-validateurs')->first();
        if ($valid && $valid->forumCategories()->count() === 0) {
            $cat = \App\Models\WorkGroupForumCategory::create([
                'work_group_id' => $valid->id,
                'name' => 'Détermination',
                'description' => 'Questions et entraide sur la détermination des Lépidoptères.',
                'position' => 0,
            ]);
            $thread = \App\Models\WorkGroupForumThread::create([
                'work_group_forum_category_id' => $cat->id,
                'member_id' => null,
                'title' => 'Bienvenue dans le forum du groupe',
                'last_posted_at' => now(),
            ]);
            \App\Models\WorkGroupForumPost::create([
                'work_group_forum_thread_id' => $thread->id,
                'member_id' => null,
                'content' => "Ce forum est l'espace d'échange du groupe. Créez un fil par sujet. Plus d'infos : https://oreina.org",
            ]);
        }
    }
}
