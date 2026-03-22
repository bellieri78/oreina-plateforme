<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@oreina.org')->first();

        if (!$admin) {
            return;
        }

        $articles = [
            [
                'title' => 'Les papillons de jour en France : état des lieux',
                'category' => 'actualites',
                'summary' => 'Un panorama complet de la situation des Rhopalocères en France métropolitaine, avec les tendances récentes et les enjeux de conservation.',
                'content' => '<p>Les papillons de jour constituent un groupe emblématique de la biodiversité française. Avec environ 260 espèces recensées en France métropolitaine, ils représentent un patrimoine naturel exceptionnel.</p><p>Cependant, les études récentes montrent un déclin préoccupant de nombreuses populations, notamment dans les zones agricoles intensives et les milieux périurbains.</p><p>OREINA s\'engage dans le suivi et la protection de ces espèces fascinantes.</p>',
                'status' => 'published',
                'is_featured' => true,
                'featured_image' => 'articles/actu1.jpg',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Sortie terrain : à la découverte des Zygènes des Pyrénées',
                'category' => 'observations',
                'summary' => 'Compte-rendu de notre sortie annuelle dans les Pyrénées-Orientales à la recherche des Zygènes endémiques.',
                'content' => '<p>Du 15 au 17 juillet, une quinzaine de membres d\'OREINA se sont retrouvés dans les Pyrénées-Orientales pour notre traditionnelle sortie estivale.</p><p>Au programme : prospection des prairies d\'altitude à la recherche des Zygènes, ces papillons aux couleurs vives caractéristiques des milieux montagnards.</p><p>Parmi les observations remarquables : <em>Zygaena anthyllidis</em> et <em>Zygaena exulans</em>.</p>',
                'status' => 'published',
                'is_featured' => false,
                'featured_image' => 'articles/actu2.jpg',
                'published_at' => now()->subDays(12),
            ],
            [
                'title' => 'Guide d\'identification : les Sphingidés de France',
                'category' => 'publications',
                'summary' => 'Présentation de notre nouveau guide pratique pour identifier les Sphinx de France métropolitaine.',
                'content' => '<p>OREINA est fière de présenter son nouveau guide d\'identification consacré aux Sphingidés.</p><p>Cette famille de papillons de nuit comprend certaines des plus grandes et spectaculaires espèces européennes, comme le Sphinx du liseron ou le Moro-Sphinx.</p><p>Le guide propose des clés d\'identification illustrées et des fiches espèces détaillées.</p>',
                'status' => 'published',
                'is_featured' => false,
                'featured_image' => 'articles/actu3.jpg',
                'published_at' => now()->subDays(20),
            ],
            [
                'title' => 'Programme de conservation : l\'Apollon en danger',
                'category' => 'conservation',
                'summary' => 'Lancement d\'un programme de suivi et de conservation du Parnassius apollo dans les Alpes françaises.',
                'content' => '<p>L\'Apollon (<em>Parnassius apollo</em>) est l\'un des papillons les plus emblématiques de la montagne européenne. Malheureusement, ses populations sont en déclin dans de nombreuses régions.</p><p>OREINA lance un programme de suivi participatif pour mieux comprendre la répartition actuelle de l\'espèce et identifier les menaces pesant sur ses habitats.</p><p>Tous les membres sont invités à participer !</p>',
                'status' => 'published',
                'is_featured' => true,
                'featured_image' => 'articles/actu1.jpg',
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Assemblée Générale 2026 : les dates sont fixées',
                'category' => 'actualites',
                'summary' => 'L\'Assemblée Générale annuelle d\'OREINA aura lieu le 15 mars 2026 à Montpellier.',
                'content' => '<p>Nous avons le plaisir de vous annoncer que l\'Assemblée Générale 2026 se tiendra le samedi 15 mars à Montpellier, dans les locaux de l\'Université.</p><p>Au programme : bilan de l\'année écoulée, projets pour 2026, et conférences sur les Lépidoptères méditerranéens.</p><p>Réservez votre date !</p>',
                'status' => 'draft',
                'is_featured' => false,
                'published_at' => null,
            ],
        ];

        foreach ($articles as $articleData) {
            Article::updateOrCreate(
                ['slug' => Str::slug($articleData['title'])],
                array_merge($articleData, [
                    'author_id' => $admin->id,
                    'slug' => Str::slug($articleData['title']),
                    'validated_by' => $articleData['status'] === 'published' ? $admin->id : null,
                    'validated_at' => $articleData['status'] === 'published' ? now() : null,
                    'views_count' => $articleData['status'] === 'published' ? rand(50, 500) : 0,
                ])
            );
        }
    }
}
