<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@oreina.org')->first();

        if (!$admin) {
            return;
        }

        $events = [
            [
                'title' => 'Sortie Papillons de Nuit - Soirée Piège Lumineux',
                'event_type' => 'sortie',
                'description' => 'Venez découvrir le monde fascinant des papillons nocturnes lors d\'une soirée piège lumineux.',
                'content' => '<p>Rejoignez-nous pour une soirée d\'observation des papillons de nuit dans la forêt de Fontainebleau.</p><p>Nous installerons un piège lumineux et observerons ensemble les différentes espèces attirées par la lumière.</p><p>Prévoir une lampe frontale et des vêtements chauds.</p>',
                'start_date' => now()->addDays(15)->setTime(21, 0),
                'end_date' => now()->addDays(16)->setTime(1, 0),
                'location_name' => 'Parking des Gorges de Franchard',
                'location_city' => 'Fontainebleau',
                'registration_required' => true,
                'max_participants' => 20,
                'price' => 0,
                'status' => 'published',
            ],
            [
                'title' => 'Conférence : Les migrations des Lépidoptères',
                'event_type' => 'conference',
                'description' => 'Conférence sur les phénomènes migratoires chez les papillons, présentée par le Dr. Martin.',
                'content' => '<p>Le Dr. Jean Martin, spécialiste des migrations animales, nous présentera les dernières découvertes sur les déplacements des papillons.</p><p>De la Belle-Dame au Monarque, découvrez les incroyables voyages de ces insectes fragiles.</p>',
                'start_date' => now()->addDays(30)->setTime(18, 30),
                'end_date' => now()->addDays(30)->setTime(20, 30),
                'location_name' => 'Salle des conférences - Muséum',
                'location_address' => '57 rue Cuvier',
                'location_city' => 'Paris',
                'registration_required' => true,
                'max_participants' => 80,
                'price' => 5,
                'status' => 'published',
            ],
            [
                'title' => 'Atelier Élevage de Chenilles',
                'event_type' => 'atelier',
                'description' => 'Apprenez les bases de l\'élevage de chenilles avec nos spécialistes.',
                'content' => '<p>Cet atelier pratique vous initiera aux techniques d\'élevage des chenilles de papillons.</p><p>Vous apprendrez à reconnaître les plantes hôtes, à créer un élevage adapté, et à suivre le développement de la chrysalide jusqu\'à l\'émergence.</p>',
                'start_date' => now()->addDays(45)->setTime(14, 0),
                'end_date' => now()->addDays(45)->setTime(17, 0),
                'location_name' => 'Maison de la Nature',
                'location_city' => 'Lyon',
                'registration_required' => true,
                'max_participants' => 15,
                'price' => 10,
                'status' => 'published',
            ],
            [
                'title' => 'Assemblée Générale OREINA 2026',
                'event_type' => 'reunion',
                'description' => 'Assemblée Générale annuelle de l\'association OREINA.',
                'content' => '<p>Tous les membres sont conviés à l\'Assemblée Générale annuelle de l\'association.</p><p>Ordre du jour : rapport moral, rapport financier, élection du bureau, projets 2026.</p>',
                'start_date' => now()->addMonths(2)->setTime(9, 30),
                'end_date' => now()->addMonths(2)->setTime(17, 0),
                'location_name' => 'Université Paul Valéry',
                'location_city' => 'Montpellier',
                'registration_required' => true,
                'max_participants' => 100,
                'price' => 0,
                'status' => 'draft',
            ],
            [
                'title' => 'Prospection Pyrénées - Zygènes et Apollons',
                'event_type' => 'sortie',
                'description' => 'Week-end de prospection dans les Pyrénées-Orientales.',
                'content' => '<p>Trois jours de terrain dans les Pyrénées pour observer les espèces montagnardes : Apollons, Zygènes, Moirés...</p><p>Hébergement en gîte. Prévoir de bonnes chaussures de marche.</p>',
                'start_date' => now()->addMonths(3)->setTime(9, 0),
                'end_date' => now()->addMonths(3)->addDays(2)->setTime(18, 0),
                'location_name' => 'Gîte de Font-Romeu',
                'location_city' => 'Font-Romeu',
                'registration_required' => true,
                'max_participants' => 12,
                'price' => 150,
                'status' => 'published',
            ],
        ];

        foreach ($events as $eventData) {
            Event::updateOrCreate(
                ['slug' => Str::slug($eventData['title'])],
                array_merge($eventData, [
                    'organizer_id' => $admin->id,
                    'slug' => Str::slug($eventData['title']),
                    'published_at' => $eventData['status'] === 'published' ? now() : null,
                ])
            );
        }
    }
}
