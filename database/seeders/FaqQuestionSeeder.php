<?php

namespace Database\Seeders;

use App\Models\FaqQuestion;
use Illuminate\Database\Seeder;

class FaqQuestionSeeder extends Seeder
{
    public function run(): void
    {
        if (FaqQuestion::count() > 0) {
            $this->command->info('FAQ questions already seeded, skipping.');
            return;
        }

        $data = $this->data();

        foreach ($data as $section => $questions) {
            foreach ($questions as $i => $q) {
                FaqQuestion::create([
                    'section'    => $section,
                    'question'   => $q['q'],
                    'answer'     => $q['a'],
                    'is_visible' => true,
                    'sort_order' => $i + 1,
                ]);
            }
        }
    }

    private function data(): array
    {
        return [
            'association' => [
                ['q' => "Qu'est-ce qu'oreina ?",
                 'a' => "<p>oreina est une association loi 1901 fondée en 2007, dédiée à l'étude scientifique, à la vulgarisation et à la protection des Lépidoptères de France. Elle réunit aujourd'hui plusieurs centaines d'adhérents : amateurs aguerris, professionnels, enseignants-chercheurs et structures naturalistes. L'association porte plusieurs publications (le bulletin <em>Lepis</em>, la revue scientifique <em>Chersotis</em>), anime la plateforme de gestion de données <em>Artemisiae</em>, et conduit des projets scientifiques sous convention avec l'Office français de la biodiversité (OFB) et le Muséum national d'Histoire naturelle (MNHN).</p>"],
                ['q' => "Pourquoi ce nom, <em>oreina</em> ?",
                 'a' => "<p>Le nom est emprunté à <em>Chersotis oreina</em> (Dufay, 1984), une noctuelle des pelouses subalpines de France. C'est un double hommage : à un papillon des montagnes françaises, et à Claude Dufay, lépidoptériste majeur du XX<sup>e</sup> siècle qui l'a décrit. Cette histoire est racontée plus en détail sur la page <a href=\"/pourquoi-oreina\" class=\"text-oreina-green font-bold\">Pourquoi oreina</a>.</p>"],
                ['q' => "Qui dirige l'association ?",
                 'a' => "<p>oreina est administrée par un conseil d'administration de 16 membres élus en assemblée générale, qui élit en son sein un bureau (président, vice-président, trésorier, trésorière adjointe, secrétaire, secrétaire adjointe). Depuis 2024, une coordinatrice salariée appuie l'animation du réseau scientifique. La composition complète est disponible sur la page <a href=\"/equipe\" class=\"text-oreina-green font-bold\">L'équipe</a>.</p>"],
                ['q' => "oreina est-elle reconnue d'intérêt général ?",
                 'a' => "<p>Oui. oreina dispose de la reconnaissance d'intérêt général au sens de l'article 200 du Code général des impôts, attribuée par la Direction générale des finances publiques (DGFIP). Cette reconnaissance permet aux particuliers et aux entreprises qui font un don à l'association de bénéficier d'une réduction d'impôt sur le revenu (66 % pour les particuliers) ou sur les sociétés (60 %).</p>"],
                ['q' => "Quel est le rôle d'oreina dans le paysage entomologique français ?",
                 'a' => "<p>oreina est aujourd'hui l'une des principales structures associatives françaises spécialisées sur les Lépidoptères. Elle entretient des relations de coopération avec l'Office pour les insectes et leur environnement (OPIE), Réserves naturelles de France (RNF), Arthropologia, ainsi qu'avec de nombreuses associations naturalistes régionales. À l'échelle européenne, oreina est désignée comme structure de référence pour les Lépidoptères nocturnes au sein du dispositif EU-PoMS (European Pollinator Monitoring Scheme).</p>"],
                ['q' => "Comment oreina est-elle financée ?",
                 'a' => "<p>oreina vit principalement de trois sources : les <strong>cotisations des adhérents</strong>, les <strong>subventions publiques</strong> dans le cadre de conventions pluriannuelles (OFB, MNHN), et les <strong>dons</strong> ouverts à tous. Les comptes annuels sont présentés en assemblée générale et accessibles aux adhérents.</p>"],
            ],
            'adherer' => [
                ['q' => "Comment devenir adhérent ?",
                 'a' => "<p>L'adhésion se fait en ligne via la plateforme HelloAsso, depuis la page <a href=\"/adhesion\" class=\"text-oreina-coral font-bold\">Devenir membre</a>. Plusieurs formules sont disponibles selon votre profil (individuel, étudiant, soutien). L'adhésion est annuelle et donne accès au bulletin <em>Lepis</em>, à l'extranet de l'association, aux rencontres annuelles et aux groupes de travail.</p>"],
                ['q' => "Quel est le tarif de l'adhésion ?",
                 'a' => "<p>Les tarifs sont fixés annuellement par le conseil d'administration et présentés sur la page <a href=\"/adhesion\" class=\"text-oreina-coral font-bold\">Devenir membre</a>. Une formule à tarif réduit est prévue pour les étudiants, les demandeurs d'emploi et les bénéficiaires de minima sociaux.</p>"],
                ['q' => "Faut-il être lépidoptériste pour adhérer ?",
                 'a' => "<p>Non. oreina accueille toutes les personnes intéressées par les papillons, du débutant curieux au spécialiste. La diversité des profils est une richesse pour l'association : amateurs, photographes, gestionnaires d'espaces naturels, chercheurs, enseignants, illustrateurs naturalistes. Chacun y trouve sa place.</p>"],
                ['q' => "Comment contribuer en tant que bénévole ?",
                 'a' => "<p>Plusieurs portes d'entrée existent. La plus accessible est la <strong>saisie de données d'observation</strong> sur la plateforme <em>Artemisiae</em>, ouverte à tous. Pour aller plus loin, vous pouvez rejoindre l'un des <strong>groupes de travail</strong> thématiques de l'association : validation des données, barcoding moléculaire, traits de vie, Zygènes, comités éditoriaux. Contactez-nous via la page <a href=\"/contact\" class=\"text-oreina-coral font-bold\">Contact</a> pour en discuter.</p>"],
                ['q' => "Puis-je faire un don à oreina ?",
                 'a' => "<p>Oui, oreina accepte les dons (par virement, chèque ou paiement en ligne via HelloAsso) et délivre un reçu fiscal permettant la déduction d'impôt. Pour les structures, des conventions de mécénat peuvent être mises en place pour soutenir des projets scientifiques spécifiques.</p>"],
                ['q' => "Une entreprise ou une collectivité peut-elle adhérer ?",
                 'a' => "<p>Oui. Les statuts d'oreina prévoient l'adhésion de personnes morales (associations, collectivités, entreprises, structures de gestion). Cette adhésion peut s'inscrire dans une démarche de partenariat scientifique ou pédagogique. Contactez le bureau pour discuter des modalités.</p>"],
            ],
            'papillons' => [
                ['q' => "Combien y a-t-il d'espèces de papillons en France ?",
                 'a' => "<p>La faune française compte environ <strong>5 200 espèces</strong> de Lépidoptères répertoriées : un peu moins de 260 espèces de Rhopalocères (les papillons « de jour ») et environ 4 950 espèces d'Hétérocères (les papillons « de nuit »). Cette diversité, l'une des plus riches d'Europe, s'explique par la variété des climats et des habitats français, des Alpes aux côtes méditerranéennes.</p>"],
                ['q' => "Quelle est la différence entre un papillon « de jour » et un papillon « de nuit » ?",
                 'a' => "<p>La distinction « papillons de jour / papillons de nuit » est commode mais scientifiquement imparfaite. Elle recouvre la séparation entre <strong>Rhopalocères</strong> (papillons aux antennes en massue, principalement diurnes) et <strong>Hétérocères</strong> (papillons aux antennes variées, plumeuses ou filiformes, majoritairement nocturnes mais avec de nombreuses exceptions diurnes). Cette dichotomie ne correspond pas à un classement phylogénétique strict : les Rhopalocères forment un sous-groupe au sein des Hétérocères. De nombreuses espèces dites « nocturnes » volent en plein jour : Zygènes, certaines Géomètres, Sphinx de jour.</p>"],
                ['q' => "Tous les papillons sont-ils en déclin ?",
                 'a' => "<p>Le constat global est préoccupant mais nuancé. De nombreuses espèces, notamment celles inféodées aux milieux ouverts (prairies maigres, pelouses calcaires, lisières) et aux habitats spécialisés, sont en déclin marqué. D'autres espèces, plus généralistes ou favorisées par le réchauffement climatique, sont stables ou en expansion vers le nord. Cette diversité de trajectoires est précisément ce que cherchent à documenter les programmes de suivi auxquels oreina contribue, notamment dans le cadre du dispositif EU-PoMS.</p>"],
                ['q' => "Combien de temps vit un papillon ?",
                 'a' => "<p>Cela dépend de l'espèce et du stade. La durée totale du cycle de vie (œuf, chenille, chrysalide, adulte) varie de quelques semaines à plusieurs années pour certaines espèces de montagne. La vie de l'adulte (l'imago, le « papillon » au sens commun) est en général courte : de quelques jours à quelques semaines. Certaines espèces hivernantes (Vulcain, Citron, Petite Tortue) peuvent vivre plusieurs mois sous forme adulte en passant l'hiver en diapause.</p>"],
                ['q' => "Les papillons piquent-ils ? Sont-ils dangereux ?",
                 'a' => "<p>Non. Aucun Lépidoptère adulte ne pique en France : les papillons n'ont ni dard ni venin. Quelques <strong>chenilles</strong> peuvent en revanche provoquer des réactions cutanées par contact avec leurs poils urticants : c'est notamment le cas de la Processionnaire du pin (<em>Thaumetopoea pityocampa</em>) et de la Processionnaire du chêne (<em>Thaumetopoea processionea</em>), responsables d'enjeux sanitaires dans certaines régions. Les autres chenilles sont inoffensives.</p>"],
                ['q' => "Comment attirer les papillons dans mon jardin ?",
                 'a' => "<p>Trois principes simples. <strong>Diversifier la végétation</strong> en plantant des espèces locales mellifères et des plantes-hôtes pour les chenilles : orties pour les Vanessa, fenouil pour le Machaon, lierre pour l'Argus bleu. <strong>Ne pas tondre</strong> systématiquement la pelouse : laisser des zones de prairie fleurie, des bandes herbacées ou un coin de friche. <strong>Bannir les pesticides</strong>, notamment les insecticides et les herbicides systémiques. Un jardin sans pesticides, avec une diversité botanique simple, accueille naturellement plusieurs dizaines d'espèces de papillons.</p>"],
            ],
            'identifier' => [
                ['q' => "Comment identifier un papillon ?",
                 'a' => "<p>L'identification dépend du groupe et de l'espèce. Pour les Rhopalocères, plusieurs guides de référence existent : Lafranchis, Tolman &amp; Lewington, Tshikolovets. Pour les Hétérocères, qui sont beaucoup plus nombreux, l'identification fait souvent appel à des critères fins (motifs alaires, examen des armures génitales) et de plus en plus au <strong>barcoding moléculaire</strong> pour les espèces cryptiques. La plateforme <em>Artemisiae</em> d'oreina propose des outils d'aide à l'identification, et l'association anime des groupes de travail thématiques sur la difficulté typologique des identifications.</p>"],
                ['q' => "Faut-il capturer un papillon pour l'identifier ?",
                 'a' => "<p>Pas systématiquement. Pour la majorité des Rhopalocères et pour de nombreux Hétérocères, l'identification sur photo de bonne qualité est possible. Pour certaines espèces difficiles ou cryptiques, l'examen morphologique direct ou l'analyse génétique peut être nécessaire. oreina promeut une <strong>éthique de l'observation</strong> privilégiant les méthodes non invasives lorsque c'est possible, et encadrant la capture par des protocoles scientifiques rigoureux lorsqu'elle est nécessaire.</p>"],
                ['q' => "Comment bien photographier un papillon ?",
                 'a' => "<p>Quelques principes : approcher lentement, par la face latérale ou ventrale (cela évite l'ombre projetée qui fait fuir l'insecte) ; viser les heures fraîches (matin tôt, soir) où les papillons sont moins mobiles ; cadrer suffisamment large pour saisir les motifs alaires et le contexte (plante hôte, habitat). Pour la diffusion sur <em>Artemisiae</em>, une photo nette du dessus et du dessous des ailes est idéale.</p>"],
                ['q' => "Quelle est la meilleure période pour observer les papillons ?",
                 'a' => "<p>Cela dépend des espèces. La période <strong>mai à septembre</strong> concentre la plus grande diversité de Rhopalocères en France. Les Hétérocères sont actifs toute l'année, avec des pics au printemps et en fin d'été. Certaines espèces sont strictement printanières (Aurore, Citron) ou automnales (Sylvain azuré, plusieurs Noctuelles). Les milieux d'altitude offrent souvent leur meilleure période en juillet et août, alors que les milieux méditerranéens sont riches dès mars et avril.</p>"],
                ['q' => "Comment attirer les papillons nocturnes pour les observer ?",
                 'a' => "<p>Deux méthodes principales sont utilisées par les lépidoptéristes : le <strong>piégeage lumineux</strong> (lampes UV ou actiniques) qui attire de nombreux Hétérocères, et le <strong>miellage</strong> (mélange sucré-alcoolisé appliqué sur les troncs) qui attire certaines Noctuelles. Ces méthodes sont encadrées par des protocoles scientifiques et leur usage à des fins d'observation suit des règles éthiques précises : durée d'attraction limitée, libération des individus, respect des habitats.</p>"],
                ['q' => "Existe-t-il des applications d'identification automatique ?",
                 'a' => "<p>Oui, plusieurs applications grand public proposent l'identification de papillons par intelligence artificielle. Leur fiabilité dépend toutefois fortement <strong>des données d'entraînement</strong> sur lesquelles elles ont été développées. La plupart des modèles disponibles aujourd'hui ont été entraînés sur des jeux de données dominés par les contributions <strong>nord-européennes</strong> (Pays-Bas, Royaume-Uni, Allemagne, Scandinavie). Ils sont donc particulièrement performants sur les espèces communes du nord de l'Europe, présentes en France septentrionale, mais beaucoup moins fiables sur les <strong>faunes méridionales, méditerranéennes, alpines ou pyrénéennes</strong>, sous-représentées dans les corpus d'apprentissage.</p><p>oreina recommande donc d'utiliser ces outils comme <strong>première aide</strong>, en gardant à l'esprit qu'une suggestion automatique n'est pas une identification certaine, et de soumettre les observations sensibles ou les cas d'identification difficile à des <strong>validateurs humains</strong> via <em>Artemisiae</em> ou par sollicitation directe d'un spécialiste.</p>"],
            ],
            'donnees' => [
                ['q' => "Qu'est-ce qu'<em>Artemisiae</em> ?",
                 'a' => "<p><em>Artemisiae</em> est la plateforme web de gestion des données d'observation des Lépidoptères animée par oreina. Elle permet à toute personne intéressée de <strong>saisir ses observations</strong> (espèce, date, lieu, photo), de <strong>consulter</strong> les données existantes, et de bénéficier d'une <strong>validation</strong> par un réseau de spécialistes. La plateforme est accessible à tous, adhérents ou non.</p>"],
                ['q' => "Comment saisir une observation sur <em>Artemisiae</em> ?",
                 'a' => "<p>Il suffit de créer un compte (gratuit) sur la plateforme <a href=\"https://oreina.org/artemisiae/\" class=\"text-oreina-green font-bold\"><em>Artemisiae</em></a>, puis de renseigner vos observations via le formulaire de saisie. Les champs essentiels sont l'espèce identifiée (ou la mention « à identifier »), la date et le lieu (commune ou coordonnées GPS). Une photo est vivement recommandée pour faciliter la validation.</p>"],
                ['q' => "Qui valide les observations ?",
                 'a' => "<p>Les observations sont examinées par un <strong>réseau de validateurs bénévoles</strong>, lépidoptéristes expérimentés répartis par groupes taxonomiques et régions. Le processus de validation suit un protocole formalisé qui distingue plusieurs niveaux de fiabilité : observation très probable, probable, ou nécessitant des éléments complémentaires. Cette validation est l'un des cœurs opérationnels de l'association, dans le cadre du projet QUALIF.</p>"],
                ['q' => "Mes données m'appartiennent-elles ?",
                 'a' => "<p>Oui. Les données saisies sur <em>Artemisiae</em> restent la propriété intellectuelle des observateurs. oreina assure leur archivage, leur qualification et leur partage selon les modalités consenties par chaque utilisateur. Les données validées peuvent être transmises à l'INPN (Inventaire national du patrimoine naturel) dans le cadre du dispositif SINP (Système d'information sur la biodiversité), avec mention explicite de la source.</p>"],
                ['q' => "Qui peut consulter les données ?",
                 'a' => "<p>L'accès aux données dépend de leur statut. Les <strong>données validées</strong> sont consultables par tous les utilisateurs d'<em>Artemisiae</em>. Pour certaines espèces sensibles (espèces protégées soumises à pression de prélèvement, par exemple), un système de <strong>maille de précision dégradée</strong> est appliqué pour protéger les stations vulnérables tout en garantissant la disponibilité scientifique de l'information.</p>"],
                ['q' => "Comment oreina contribue-t-elle aux référentiels nationaux ?",
                 'a' => "<p>oreina est partie prenante de plusieurs référentiels structurants. Elle contribue activement à <strong>TAXREF</strong>, le référentiel taxonomique national porté par PatriNat (MNHN). Elle développe en parallèle plusieurs référentiels complémentaires : <strong>SEQREF</strong> pour le barcoding moléculaire, <strong>BDC</strong> pour les traits de vie, <strong>IDENT</strong> pour les guides d'identification, <strong>QUALIF</strong> pour la qualification de la donnée. Ces cinq projets s'inscrivent dans la convention pluriannuelle 2026-2028 avec l'Office français de la biodiversité.</p>"],
            ],
            'ethique' => [
                ['q' => "La capture de papillons est-elle légale en France ?",
                 'a' => "<p>Oui pour la majorité des espèces, dans un cadre scientifique ou amateur. Mais <strong>certaines espèces sont strictement protégées</strong> par l'arrêté ministériel du 23 avril 2007 (révisé) : leur capture, leur transport, leur naturalisation et leur commerce sont interdits sans autorisation préfectorale. La liste comprend notamment l'Apollon, le Semi-Apollon, l'Azuré du serpolet, plusieurs <em>Maculinea</em>, plusieurs espèces de zones humides. Avant toute capture, vérifiez le statut de l'espèce sur l'INPN.</p>"],
                ['q' => "Que pense oreina de la collection de papillons ?",
                 'a' => "<p>La constitution de <strong>collections scientifiques</strong> reste un outil indispensable de la lépidoptérologie : elle permet l'examen morphologique fin, l'extraction d'ADN pour le barcoding, la conservation de spécimens-types et la documentation pérenne de la biodiversité. oreina valorise les collections déposées dans des institutions publiques (Muséum, universités, sociétés savantes) et encourage les lépidoptéristes amateurs à prévoir le devenir de leurs collections personnelles. La constitution de collections <strong>purement décoratives</strong> ou commerciales, en revanche, n'a plus de pertinence aujourd'hui : la photographie suffit pour ces usages, et le prélèvement crée une pression inutile sur les populations.</p>"],
                ['q' => "Le piégeage lumineux est-il nuisible aux papillons ?",
                 'a' => "<p>C'est une question légitime que la communauté scientifique prend au sérieux. Le piégeage lumineux nocturne attire effectivement des centaines à des milliers d'individus en une nuit. Les protocoles éthiques actuels visent à <strong>minimiser l'impact</strong> : durée d'éclairage limitée (quelques heures, pas toute la nuit), distance respectée des sites sensibles, <strong>libération immédiate</strong> des individus dès l'identification, choix raisonné des longueurs d'onde. La pollution lumineuse permanente (éclairage public, illumination des bâtiments) constitue un problème conservationniste <strong>bien plus grave</strong> que le piégeage scientifique ponctuel. oreina contribue activement à la formalisation et à la diffusion de bonnes pratiques.</p>"],
                ['q' => "Les papillons sont-ils des pollinisateurs importants ?",
                 'a' => "<p>Oui, mais leur rôle reste sous-évalué dans les politiques publiques. Si les abeilles concentrent l'essentiel de l'attention médiatique, <strong>les papillons (notamment de nuit) jouent un rôle de pollinisation significatif</strong>, en particulier pour les plantes à corolle profonde et pour les espèces à floraison nocturne. La participation d'oreina au dispositif <strong>EU-PoMS</strong> (European Pollinator Monitoring Scheme), en tant que structure de référence pour les Lépidoptères nocturnes, vise précisément à mieux quantifier cette contribution à l'échelle européenne.</p>"],
                ['q' => "Comment oreina contribue-t-elle aux Listes rouges ?",
                 'a' => "<p>oreina est associée aux travaux de révision des Listes rouges des Lépidoptères de France métropolitaine. L'expertise apportée par l'association porte à la fois sur la <strong>taxonomie</strong> (clarification de la liste des taxons à évaluer), sur la <strong>chorologie</strong> (cartographie de la répartition actuelle et historique), et sur l'<strong>état de conservation</strong> (tendances populationnelles, vulnérabilité des habitats). La révision en cours concerne particulièrement les Rhopalocères et les Zygaenidae.</p>"],
                ['q' => "Peut-on faire confiance à l'IA pour identifier les papillons ?",
                 'a' => "<p>Avec discernement, et en gardant à l'esprit deux limites importantes.</p><p>La première limite est <strong>géographique</strong>. Les modèles d'IA disponibles aujourd'hui ont été entraînés sur des jeux de données dominés par les contributions <strong>nord-européennes</strong> (Pays-Bas, Royaume-Uni, Allemagne, Scandinavie), où les communautés de naturalistes amateurs sont nombreuses et structurées de longue date. Ils sont donc particulièrement performants sur les espèces communes du nord de l'Europe, présentes également en France septentrionale, mais leur fiabilité chute sensiblement sur les <strong>faunes méridionales, méditerranéennes, alpines, pyrénéennes ou corses</strong>, sous-représentées dans les corpus d'apprentissage. Une espèce typiquement endémique des Alpes du sud ou des garrigues languedociennes a toutes les chances d'être mal identifiée par un modèle entraîné majoritairement sur des observations néerlandaises.</p><p>La seconde limite est <strong>épistémologique</strong>. L'IA opère sur des <strong>représentations internes opaques</strong> qui ne correspondent pas toujours aux mêmes critères qu'un taxonomiste humain : deux espèces peuvent être correctement séparées par des indices qui n'ont pas de pertinence biologique réelle, ce qui pose problème dès qu'on sort du domaine sur lequel le modèle a été entraîné.</p><p>oreina considère donc que l'IA est un <strong>outil d'assistance précieux</strong>, mais pas un substitut à l'expertise humaine, surtout pour les usages réglementaires (évaluations environnementales, listes rouges, données d'engagement public) et pour les faunes peu représentées dans les modèles. Cette articulation entre IA et expertise humaine est l'un des sujets de réflexion actuels du projet QUALIF.</p>"],
            ],
        ];
    }
}
