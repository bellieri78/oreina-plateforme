<?php

namespace Database\Seeders;

use App\Models\Submission;
use Illuminate\Database\Seeder;

class SampleArticleSeeder extends Seeder
{
    /**
     * Seed sample article content for testing the layout system.
     */
    public function run(): void
    {
        $submission = Submission::find(3);

        if (!$submission) {
            $this->command->error('Submission ID 3 not found!');
            return;
        }

        // Update metadata
        $submission->title = 'Étude phylogénétique des Lycaenidae du massif alpin : nouvelles perspectives taxonomiques';

        $submission->abstract = "Cette étude présente une analyse phylogénétique complète des espèces de Lycaenidae présentes dans le massif alpin, révélant plusieurs lignées évolutives jusqu'alors méconnues. En utilisant des marqueurs moléculaires mitochondriaux (COI) et nucléaires (EF-1α, wingless), nous avons analysé 156 spécimens représentant 42 espèces alpines. Nos résultats indiquent que le genre Polyommatus tel qu'actuellement défini est polyphylétique, avec au moins trois clades distincts. Nous proposons une révision taxonomique basée sur ces données phylogénétiques, incluant la résurrection de deux genres historiques et la description d'un nouveau sous-genre. Cette étude souligne l'importance des refuges glaciaires alpins dans la diversification des Lycaenidae européens et fournit un cadre phylogénétique robuste pour de futures études taxonomiques et biogéographiques.";

        $submission->keywords = 'Lycaenidae, Phylogénie, Alpes, Taxonomie, Polyommatus, Biogéographie, COI, Refuges glaciaires';
        $submission->doi = '10.1234/oreina.2026.001';
        $submission->start_page = 1;
        $submission->end_page = 24;

        // Author affiliations
        $submission->author_affiliations = [
            '1. Laboratoire d\'Entomologie, Muséum National d\'Histoire Naturelle, 75005 Paris, France',
            '2. Institut de Systématique, Évolution, Biodiversité (ISYEB), Sorbonne Université, 75005 Paris, France',
            '3. Station Alpine Joseph Fourier, Université Grenoble Alpes, 38000 Grenoble, France',
        ];

        // References
        $submission->references = [
            'Fric, Z., Wahlberg, N., Pech, P., & Zrzavý, J. (2007). Phylogeny and classification of the Phengaris-Maculinea clade (Lepidoptera: Lycaenidae): total evidence and phylogenetic species concepts. Systematic Entomology, 32(3), 558-567.',
            'Hebert, P. D., Cywinska, A., Ball, S. L., & deWaard, J. R. (2003). Biological identifications through DNA barcodes. Proceedings of the Royal Society B: Biological Sciences, 270(1512), 313-321.',
            'Schmitt, T. (2009). Biogeographical and evolutionary importance of the European high mountain systems. Frontiers in Zoology, 6(1), 9.',
            'Settele, J., Kudrna, O., Harpke, A., Kühn, I., Van Swaay, C., Verovnik, R., ... & Schweiger, O. (2008). Climatic risk atlas of European butterflies. BioRisk, 1, 1-710.',
            'Talavera, G., Lukhtanov, V. A., Rieppel, L., Pierce, N. E., & Vila, R. (2013). In the shadow of phylogenetic uncertainty: the recent diversification of Lysandra butterflies through chromosomal change. Molecular Phylogenetics and Evolution, 69(3), 469-478.',
            'Vila, R., Bell, C. D., Macniven, R., Goldman-Huertas, B., Ree, R. H., Marshall, C. R., ... & Pierce, N. E. (2011). Phylogeny and palaeoecology of Polyommatus blue butterflies show Beringia was a climate-regulated gateway to the New World. Proceedings of the Royal Society B: Biological Sciences, 278(1719), 2737-2744.',
            'Wiemers, M., Chazot, N., Wheat, C. W., Schweiger, O., & Wahlberg, N. (2020). A complete time-calibrated multi-gene phylogeny of the European butterflies. ZooKeys, 938, 97-124.',
        ];

        // Acknowledgements
        $submission->acknowledgements = "Nous remercions chaleureusement les nombreux collègues qui ont contribué aux collectes de terrain : Dr. Pierre Fontaine, Dr. Anne Mercier, M. Luc Dardenne et l'équipe de la Station Alpine Joseph Fourier. Ce travail a été financé par l'Office Français de la Biodiversité (projet ECOBIO-LEPI 2020-2024) et le programme européen BiodivERsA (ALPIBIODIV, ANR-18-EBI3-0006).";

        // Content blocks
        $submission->content_blocks = [
            // Introduction
            ['type' => 'heading', 'content' => '1. Introduction'],
            ['type' => 'paragraph', 'content' => "Les Lycaenidae constituent l'une des familles de Lépidoptères les plus diversifiées avec plus de 6000 espèces décrites à travers le monde (Fric et al., 2007). En Europe, cette famille compte environ 150 espèces, dont une proportion significative est endémique aux régions montagneuses, particulièrement le massif alpin (Settele et al., 2008). Les Alpes européennes représentent un hotspot de biodiversité remarquable, ayant servi de refuge pendant les périodes glaciaires quaternaires et favorisant ainsi des processus de spéciation complexes (Schmitt, 2009)."],
            ['type' => 'paragraph', 'content' => "Le genre Polyommatus Latreille, 1804, constitue le groupe le plus diversifié de Lycaenidae paléarctiques avec plus de 200 espèces nominales. Cependant, la taxonomie de ce genre demeure controversée, plusieurs auteurs ayant proposé des subdivisions basées sur des caractères morphologiques, notamment la structure des genitalia (Talavera et al., 2013; Wiemers et al., 2020). L'absence de consensus taxonomique reflète les difficultés inhérentes à la délimitation d'espèces dans ce groupe, caractérisé par une homogénéité morphologique externe contrastant avec une diversité génétique importante."],
            ['type' => 'paragraph', 'content' => "Les approches moléculaires ont révolutionné notre compréhension de la phylogénie des Lycaenidae au cours des deux dernières décennies (Vila et al., 2011; Cong et al., 2016). L'utilisation de marqueurs mitochondriaux, notamment le gène Cytochrome c Oxydase I (COI), s'est généralisée dans les études de barcoding et de délimitation d'espèces (Hebert et al., 2003). Toutefois, l'utilisation exclusive de marqueurs mitochondriaux peut conduire à des artéfacts phylogénétiques, notamment en raison de phénomènes d'introgression ou de sélection (Toews & Brelsford, 2012). L'intégration de marqueurs nucléaires est donc essentielle pour obtenir une vision phylogénétique robuste."],
            ['type' => 'paragraph', 'content' => "Dans cette étude, nous proposons une analyse phylogénétique multigénique des Lycaenidae alpins, basée sur un échantillonnage taxonomique et géographique exhaustif. Nos objectifs spécifiques sont : (1) de reconstruire les relations phylogénétiques au sein du genre Polyommatus et des genres apparentés présents dans les Alpes ; (2) d'évaluer la monophylie des groupes taxonomiques actuellement reconnus ; (3) de proposer une révision taxonomique fondée sur les données moléculaires ; et (4) de discuter les implications biogéographiques de nos résultats dans le contexte de l'histoire glaciaire alpine."],

            // Matériel et Méthodes
            ['type' => 'heading', 'content' => '2. Matériel et Méthodes'],
            ['type' => 'subheading', 'content' => '2.1. Échantillonnage et collecte'],
            ['type' => 'paragraph', 'content' => "Un total de 156 spécimens de Lycaenidae ont été collectés dans le massif alpin entre 2020 et 2024, couvrant un gradient altitudinal de 800 à 3200 mètres. Les sites d'échantillonnage ont été sélectionnés pour maximiser la diversité taxonomique et géographique, incluant les principaux massifs des Alpes françaises, suisses, italiennes et autrichiennes. Les spécimens ont été capturés au filet entomologique, photographiés in vivo, puis conservés dans de l'éthanol absolu à -20°C. Les vouchers sont déposés dans les collections du Muséum National d'Histoire Naturelle (Paris) et de la Station Alpine Joseph Fourier (Grenoble)."],
            ['type' => 'subheading', 'content' => "2.2. Extraction d'ADN et amplification"],
            ['type' => 'paragraph', 'content' => "L'ADN génomique total a été extrait à partir du thorax ou d'une patte de chaque spécimen en utilisant le kit DNeasy Blood & Tissue (Qiagen) selon le protocole du fabricant. La qualité et la concentration de l'ADN ont été évaluées par spectrophotométrie (NanoDrop 2000). Trois marqueurs ont été amplifiés : le fragment barcoding du gène mitochondrial COI (658 pb), et deux gènes nucléaires, Elongation Factor-1 alpha (EF-1α, 1240 pb) et wingless (wg, 400 pb)."],
            ['type' => 'paragraph', 'content' => "Les amplifications PCR ont été réalisées dans un volume final de 25 µL contenant 2.5 µL de tampon 10X, 2 mM de MgCl₂, 0.2 mM de chaque dNTP, 0.4 µM de chaque amorce, 0.5 U de Taq polymérase (Invitrogen) et 1 µL d'ADN template. Les profils thermiques utilisés sont détaillés dans le Tableau S1 (Matériel supplémentaire). Les produits PCR ont été purifiés avec le kit QIAquick PCR Purification (Qiagen) et séquencés en Sanger bidirectionnel par la plateforme Genoscope (Évry, France)."],

            // Résultats
            ['type' => 'heading', 'content' => '3. Résultats'],
            ['type' => 'paragraph', 'content' => "Nous avons obtenu des séquences de qualité pour 154 spécimens sur les 156 initialement collectés (taux de succès : 98.7%). L'alignement final du COI comprend 658 positions avec 287 sites variables dont 253 parsimonieux. Pour EF-1α, l'alignement de 1240 pb contient 412 sites variables (341 parsimonieux), et pour wingless, 167 sites sur 400 sont variables (138 parsimonieux). Aucun codon stop ni insertion/délétion n'a été détecté dans les séquences codantes, suggérant l'absence de pseudogènes nucléaires (NUMTs)."],
            ['type' => 'paragraph', 'content' => "L'arbre phylogénétique multigénique obtenu par inférence bayésienne révèle plusieurs clades fortement soutenus. Le genre Polyommatus sensu lato apparaît polyphylétique, réparti en trois clades majeurs. Les distances génétiques interspécifiques au niveau du COI varient de 2.8% à 8.4% (moyenne : 5.2 ± 1.3%), bien supérieures aux distances intraspécifiques (0.1% à 1.2%, moyenne : 0.4 ± 0.3%)."],

            // Discussion
            ['type' => 'heading', 'content' => '4. Discussion'],
            ['type' => 'paragraph', 'content' => "Nos résultats confirment la polyphylie du genre Polyommatus tel qu'actuellement circonscrit, en accord avec plusieurs études récentes. La subdivision en trois clades majeurs non-frères suggère que l'homogénéité morphologique externe masque une diversification ancienne, probablement antérieure au Pliocène. Cette situation n'est pas inédite chez les Lycaenidae où la convergence morphologique, particulièrement des structures alaires, a été documentée."],
            ['type' => 'paragraph', 'content' => "La structure phylogéographique observée suggère un rôle majeur des cycles glaciaires quaternaires dans la diversification des Lycaenidae alpins. Les espèces restreintes aux habitats de haute altitude montrent une structure génétique en accord avec le modèle des refuges multiples dans les nunataks. Cette hypothèse est corroborée par la présence de lignées génétiques profondément divergentes dans des massifs géographiquement isolés."],
            ['type' => 'paragraph', 'content' => "L'identification de lignées évolutives distinctes au sein d'unités taxonomiques actuellement reconnues a des implications directes pour les stratégies de conservation. Plusieurs espèces alpines de Lycaenidae sont actuellement menacées par le changement climatique qui provoque une remontée altitudinale des habitats favorables. La reconnaissance de diversité cryptique augmente le nombre d'unités évolutives significatives nécessitant des mesures de protection spécifiques."],

            // Conclusions
            ['type' => 'heading', 'content' => '5. Conclusions'],
            ['type' => 'paragraph', 'content' => "Cette étude fournit le cadre phylogénétique le plus complet à ce jour pour les Lycaenidae du massif alpin. Nos principaux résultats sont :"],
            ['type' => 'list', 'content' => "La confirmation de la polyphylie du genre Polyommatus sensu lato, nécessitant une révision taxonomique substantielle\nLa proposition d'un arrangement taxonomique révisé incluant la restauration de deux genres et la description d'un nouveau sous-genre\nL'identification de trois lignées évolutives majeures présentant des patterns biogéographiques contrastés\nLa détection de diversité cryptique potentielle chez certaines espèces alpines de haute altitude"],
        ];

        // Generate HTML content from blocks
        $submission->content_html = $this->blocksToHtml($submission->content_blocks);

        $submission->save();

        $this->command->info('Sample article content added to submission #3');
        $this->command->info('Title: ' . $submission->title);
        $this->command->info('Content blocks: ' . count($submission->content_blocks));
    }

    /**
     * Convert content blocks to HTML
     */
    private function blocksToHtml(array $blocks): string
    {
        $html = '';

        foreach ($blocks as $block) {
            switch ($block['type']) {
                case 'heading':
                    $html .= '<h2>' . e($block['content']) . '</h2>';
                    break;
                case 'subheading':
                    $html .= '<h3>' . e($block['content']) . '</h3>';
                    break;
                case 'paragraph':
                    $html .= '<p>' . e($block['content']) . '</p>';
                    break;
                case 'list':
                    $items = explode("\n", $block['content']);
                    $html .= '<ul>';
                    foreach ($items as $item) {
                        if (trim($item)) {
                            $html .= '<li>' . e(trim($item)) . '</li>';
                        }
                    }
                    $html .= '</ul>';
                    break;
            }
        }

        return $html;
    }
}
