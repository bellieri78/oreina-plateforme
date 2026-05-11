@extends('layouts.hub')

@section('title', 'Qualification des données (QUALIF)')
@section('meta_description', 'oreina coordonne la qualification des données d\'observation des Macrohétérocères de France pour produire un jeu de données de référence librement réutilisable.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="flex-1">
                    <div class="eyebrow sage mb-4 inline-flex">
                        <i class="icon icon-sage" data-lucide="badge-check"></i>
                        Projet 5 / 5
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Qualification des données</h1>
                    <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl">
                        Construire une chaîne de qualification systématique pour produire un jeu de données de référence sur les Macrohétérocères
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm bg-white p-5 rounded-2xl border border-slate-200 lg:min-w-[420px]">
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Programme</p>
                        <p class="font-bold text-oreina-dark">QUALIF</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Cible prioritaire</p>
                        <p class="font-bold text-oreina-dark">Macrohétérocères</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Périmètre</p>
                        <p class="font-bold text-oreina-dark">Données Artemisiae</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Coordination</p>
                        <p class="font-bold text-oreina-dark">~30 bénévoles + salariée</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Chapô --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-5 gap-12 items-center">
                <div class="lg:col-span-2">
                    @include('hub.partials._hero_visual', [
                        'image'    => '/images/projets/qualif/catocala-fraxini.webp',
                        'fallback' => '/images/projets/qualif/catocala-fraxini.jpg',
                        'alt'      => 'Catocala fraxini (Linnaeus, 1758), au repos sur drap de chasse nocturne — une donnée d\'observation à qualifier',
                        'species'  => 'Catocala fraxini',
                        'author'   => '(Linnaeus, 1758)',
                        'caption'  => 'Massif central, VIII.2024',
                        'credit'   => 'D. Demergès',
                        'ramp'     => 'sage',
                    ])
                </div>
                <div class="lg:col-span-3 text-slate-600 space-y-6">
                    <p class="text-xl leading-relaxed">
                        Toute donnée naturaliste a une histoire : un observateur, un lieu, une date, une identification, un contexte. Mais cette histoire n'est pas toujours complète, ni toujours certaine. La <strong>qualification de la donnée</strong> est le travail méthodique qui permet d'expliciter ce qu'on sait, ce qu'on ignore, ce qu'on peut en faire, et avec quel niveau de confiance.
                    </p>
                    <p class="leading-relaxed">
                        Le projet <strong>QUALIF</strong> structure ce travail à grande échelle pour les Macrohétérocères de France. Il s'appuie sur le réseau de validateurs d'<em>Artemisiae</em>, mobilise les quatre autres référentiels d'oreina, et a pour finalité de mettre à disposition de la communauté un <strong>jeu de données de référence</strong> exploitable pour des usages aussi variés que la conservation, la recherche, la gestion des espaces naturels ou la science participative.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pour comprendre : exactitude et pertinence --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="lightbulb"></i>
                    Pour comprendre
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Qualifier la donnée : exactitude et pertinence</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    La qualification des données recouvre en réalité <strong>deux processus distincts mais complémentaires</strong>, qu'il est important de bien différencier.
                </p>
                <p>
                    Le premier processus s'engage <strong>au moment de l'acquisition</strong>. Il cherche à évaluer l'<strong>exactitude</strong> de la donnée : l'espèce est-elle correctement identifiée ? La date est-elle plausible compte tenu de la phénologie ? La localisation est-elle cohérente avec l'aire de répartition connue ? Y a-t-il une photo ou un spécimen pour étayer l'observation ? Ce travail relève à la fois d'algorithmes automatiques (détection d'anomalies, croisements avec les référentiels) et d'une validation experte manuelle pour les cas douteux. C'est le travail que les bénévoles d'oreina effectuent quotidiennement sur la plateforme <em>Artemisiae</em>.
                </p>
                <p>
                    Le second processus est différent : il s'agit de qualifier la donnée <strong>en termes d'usage</strong>. Une donnée parfaitement exacte n'est pas forcément pertinente pour tous les usages. Pour réaliser une liste rouge selon les critères UICN, par exemple, on ne s'intéressera qu'aux données récentes (souvent moins de 10 ou 15 ans), géoréférencées avec une précision suffisante, et issues de prospections suffisamment couvrantes pour que les absences puissent être interprétées. Une donnée historique de qualité, datant de 1985, peut être parfaitement exacte mais inutilisable pour caractériser la situation actuelle.
                </p>
                <p>
                    Le projet QUALIF mobilise ces deux processus de façon coordonnée, avec un objectif final précis : produire un <strong>jeu de données de référence</strong> exploitable pour l'évaluation de l'état de conservation des Macrohétérocères de France.
                </p>
            </div>
        </div>
    </section>

    {{-- Une donnée fiable, c'est ce qui rend tout le reste possible --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="target"></i>
                    L'enjeu
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Une donnée fiable, c'est ce qui rend tout le reste possible</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    Les Macrohétérocères représentent l'essentiel de la diversité lépidoptérologique française : plusieurs milliers d'espèces, contre moins de 260 pour les Rhopalocères. Mais leur connaissance est nettement moins structurée. La pratique du piégeage lumineux, les difficultés d'identification, la dispersion des observations entre une multitude d'observateurs et de bases régionales font que <strong>les données sur les papillons de nuit sont à la fois nombreuses et hétérogènes</strong>.
                </p>
                <p>
                    Sans qualification systématique, cette masse de données reste sous-utilisée. Une carte de répartition produite à partir de données non qualifiées peut donner l'illusion d'une connaissance qui n'existe pas. Une analyse temporelle peut faire apparaître des tendances qui ne reflètent que les variations de l'effort de prospection. Une donnée mal géoréférencée, ou rattachée à un nom devenu invalide, peut polluer durablement les analyses.
                </p>
                <p>
                    L'enjeu de QUALIF est donc d'abord <strong>scientifique et méthodologique</strong> : produire une matière qualifiée, documentée, sourcée, qui ouvre des possibilités d'usage que la donnée brute ne permet pas. Sur les Macrohétérocères de France, ce travail constitue à la fois un effort inédit par son ampleur et une condition préalable à beaucoup d'autres travaux.
                </p>
                <p>
                    Une fois ce socle constitué, les usages possibles sont nombreux : <strong>recherche</strong> (modélisation des dynamiques de populations, étude des réponses au changement climatique), <strong>gestion d'espaces naturels</strong> (élaboration de plans de gestion, identification d'enjeux locaux), <strong>science participative</strong> (alimentation de programmes de suivi standardisés), <strong>évaluations environnementales</strong>, et bien sûr <strong>évaluation de l'état de conservation</strong>, dont la perspective d'une future Liste rouge nationale des papillons de nuit, en complément de la liste rouge européenne en cours d'achèvement à laquelle oreina contribue.
                </p>
            </div>
        </div>
    </section>

    {{-- Le rôle d'oreina --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow sage mb-4 inline-flex">
                    <i class="icon icon-sage" data-lucide="handshake"></i>
                    Le rôle d'oreina
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Un réseau de validateurs au cœur du dispositif</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    QUALIF occupe une place particulière dans le dispositif scientifique d'oreina. Là où les quatre autres projets — TAXREF, SEQREF, BDC, IDENT — produisent et structurent des <strong>référentiels techniques</strong>, QUALIF mobilise ces référentiels au service de la <strong>donnée d'observation</strong>. Il en est l'aboutissement opérationnel : c'est dans QUALIF que se concrétise l'utilité des autres projets pour la conservation.
                </p>
                <p>
                    Au cœur du dispositif se trouve le <strong>réseau de validateurs d'<em>Artemisiae</em></strong>, qui est probablement l'une des forces les plus singulières d'oreina dans le paysage naturaliste français. Une trentaine d'experts bénévoles, chacun spécialisé sur un groupe taxonomique précis (Géomètres, Noctuelles, Microlépidoptères, Sphinx, Saturnies, Hespéries, Lycènes, etc.), examinent au quotidien les observations saisies sur la plateforme. Cette validation ne se limite pas à un simple contrôle de plausibilité : elle s'appuie sur la confrontation à un faisceau de référentiels (taxonomie, difficulté d'identification, traits de vie, séquences de référence) et permet d'aboutir à une donnée qualifiée, sourcée, réutilisable.
                </p>
                <p>
                    Ce réseau, animé en continu, est le dispositif technique et humain qui rend possible le projet QUALIF. Sans lui, aucune qualification systématique à l'échelle nationale ne serait envisageable.
                </p>
                <p>
                    Le projet QUALIF mobilise simultanément les ressources des quatre autres projets :
                </p>
                <ul class="list-disc pl-8 space-y-3 marker:text-oreina-green">
                    <li><span class="pl-2 inline-block">le référentiel <strong>TAXREF</strong> et son interface Systema, pour s'assurer que chaque donnée est rattachée au taxon valide actuel ;</span></li>
                    <li><span class="pl-2 inline-block">la typologie <strong>IDENT</strong> de difficulté d'identification, pour évaluer la fiabilité d'une identification au regard du stade observé, de la difficulté du groupe, de la sympatrie locale ;</span></li>
                    <li><span class="pl-2 inline-block">les données <strong>SEQREF</strong> de barcoding, pour trancher les cas où l'identification morphologique est incertaine ;</span></li>
                    <li><span class="pl-2 inline-block">la base <strong>BDC</strong> de traits de vie, pour vérifier la cohérence phénologique, écologique et altitudinale d'une donnée.</span></li>
                </ul>
                <p>
                    L'ensemble est piloté par un groupe projet de cinq bénévoles et appuyé par la coordinatrice scientifique salariée et un chargé d'étude dédié à temps partiel.
                </p>
            </div>
        </div>
    </section>

    {{-- Chiffres-clés --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="bar-chart-3"></i>
                    Chiffres-clés
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">QUALIF en chiffres</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">Mobilisation, livrables et horizon du projet 2026, 2028.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-green/5 to-oreina-turquoise/5">
                    <p class="text-4xl font-bold text-oreina-green mb-2">~30</p>
                    <p class="text-sm text-slate-600 leading-tight">experts validateurs bénévoles spécialisés par groupe taxonomique</p>
                </div>
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-green/5 to-oreina-turquoise/5">
                    <p class="text-4xl font-bold text-oreina-green mb-2">5</p>
                    <p class="text-sm text-slate-600 leading-tight">bénévoles du groupe projet pilotant la coordination scientifique</p>
                </div>
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-green/5 to-oreina-turquoise/5">
                    <p class="text-4xl font-bold text-oreina-green mb-2">~2</p>
                    <p class="text-sm text-slate-600 leading-tight">ETP bénévoles par an, soit ~50 000 € de bénévolat valorisé</p>
                </div>
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-coral/5 to-oreina-yellow/5">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">~5</p>
                    <p class="text-sm text-slate-600 leading-tight">rapports d'analyse de qualité à transmettre aux producteurs de données</p>
                </div>
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-coral/5 to-oreina-yellow/5">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">1</p>
                    <p class="text-sm text-slate-600 leading-tight">jeu de données de référence Macrohétérocères, format SINP/INPN, livré à l'horizon 2028</p>
                </div>
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-coral/5 to-oreina-yellow/5">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">~3 000</p>
                    <p class="text-sm text-slate-600 leading-tight">espèces de Macrohétérocères concernées par le travail de qualification</p>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">Source : fiche projet QUALIF 2026, 2028.</p>
        </div>
    </section>

    {{-- Une chaîne de qualification en 4 temps --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="git-branch"></i>
                    Méthodologie
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Une chaîne de qualification en quatre temps</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Du flux brut de données à l'évaluation Liste rouge, le projet QUALIF structure une chaîne complète qui combine algorithmes, expertise humaine et validation collective.</p>
            </div>

            <div class="grid lg:grid-cols-4 gap-4">
                <div class="card p-6">
                    <div class="w-12 h-12 rounded-full bg-oreina-blue text-white flex items-center justify-center font-bold mb-4 text-lg">1</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Validation automatique</h3>
                    <p class="text-sm text-slate-600 mb-3">Application systématique d'algorithmes de détection d'anomalies sur l'ensemble du flux de données : croisement avec les référentiels TAXREF, IDENT, BDC, contrôle de cohérence phénologique, géographique, altitudinale.</p>
                    <p class="text-xs text-slate-400 italic">Tous les jeux de données passent par cette première étape, qu'ils proviennent d'<em>Artemisiae</em> ou d'autres sources du SINP.</p>
                </div>
                <div class="card p-6">
                    <div class="w-12 h-12 rounded-full bg-oreina-blue text-white flex items-center justify-center font-bold mb-4 text-lg">2</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Validation experte manuelle</h3>
                    <p class="text-sm text-slate-600 mb-3">Examen approfondi des cas signalés comme douteux par la validation automatique, par le réseau d'experts validateurs spécialisés. Confrontation des avis sur les cas problématiques.</p>
                    <p class="text-xs text-slate-400 italic">Chaque expert se concentre sur le ou les groupes taxonomiques qu'il maîtrise.</p>
                </div>
                <div class="card p-6">
                    <div class="w-12 h-12 rounded-full bg-oreina-blue text-white flex items-center justify-center font-bold mb-4 text-lg">3</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Qualification d'usage</h3>
                    <p class="text-sm text-slate-600 mb-3">Sur les données validées comme exactes, identification de celles qui sont exploitables pour une évaluation Liste rouge : profondeur temporelle, précision géographique, représentativité de l'effort de prospection.</p>
                    <p class="text-xs text-slate-400 italic">Mise en évidence des biais et des lacunes : régions sous-prospectées, espèces sans donnée récente.</p>
                </div>
                <div class="card p-6">
                    <div class="w-12 h-12 rounded-full bg-oreina-blue text-white flex items-center justify-center font-bold mb-4 text-lg">4</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Diffusion qualifiée</h3>
                    <p class="text-sm text-slate-600 mb-3">Production du jeu de données de référence (format SINP/INPN), des rapports d'analyse adressés aux producteurs, et d'une note méthodologique transmise aux instances UICN et Liste rouge.</p>
                    <p class="text-xs text-slate-400 italic">Les producteurs reçoivent un retour personnalisé sur la qualité de leurs données.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- QUALIF orchestre les autres projets --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="git-merge"></i>
                    Articulation
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">QUALIF orchestre les autres projets</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Chacun des projets scientifiques d'oreina alimente QUALIF avec un type de référentiel ou de connaissance. C'est ce qui fait la cohérence d'ensemble du dispositif.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('hub.projets.taxref') }}" class="card p-6 bg-white hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="layers"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-green transition">TAXREF</h3>
                    <p class="text-xs text-slate-600 mb-3">Référentiel taxonomique national.</p>
                    <p class="text-xs text-oreina-green font-bold">→ Permet à QUALIF de rattacher chaque donnée au taxon valide actuel et de gérer les évolutions nomenclaturales.</p>
                </a>
                <a href="{{ route('hub.projets.seqref') }}" class="card p-6 bg-white hover:shadow-lg transition group">
                    <div class="pub-card-icon blue mb-4">
                        <i class="icon icon-blue" data-lucide="dna"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-blue transition">SEQREF</h3>
                    <p class="text-xs text-slate-600 mb-3">Bibliothèque de séquences de référence.</p>
                    <p class="text-xs text-oreina-blue font-bold">→ Permet à QUALIF de trancher les cas d'identification incertaine par recours au barcoding.</p>
                </a>
                <a href="{{ route('hub.projets.bdc') }}" class="card p-6 bg-white hover:shadow-lg transition group">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="list-checks"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-coral transition">BDC</h3>
                    <p class="text-xs text-slate-600 mb-3">Base de données de traits de vie.</p>
                    <p class="text-xs text-oreina-coral font-bold">→ Permet à QUALIF de vérifier la cohérence phénologique, altitudinale, écologique d'une donnée.</p>
                </a>
                <a href="{{ route('hub.projets.ident') }}" class="card p-6 bg-white hover:shadow-lg transition group">
                    <div class="pub-card-icon gold mb-4">
                        <i class="icon icon-gold" data-lucide="search"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-yellow transition">IDENT</h3>
                    <p class="text-xs text-slate-600 mb-3">Typologie de difficulté d'identification.</p>
                    <p class="text-xs font-bold" style="color:#8b6c05">→ Permet à QUALIF d'évaluer la fiabilité d'une identification selon stade, agrégat, sympatrie locale.</p>
                </a>
            </div>

            <div class="mt-10 p-6 bg-white rounded-xl border-l-4 border-oreina-green">
                <p class="text-slate-600">
                    <strong class="text-oreina-dark">Une cohérence d'ensemble.</strong> Chacun des cinq projets pris isolément constitue une contribution scientifique en soi. Mais c'est leur articulation, et la capacité d'oreina à les mobiliser conjointement au service de la qualification de la donnée, qui fait la valeur stratégique du dispositif. QUALIF est le projet où cette cohérence devient visible et opérationnelle.
                </p>
            </div>
        </div>
    </section>

    {{-- Des usages multiples --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="flag"></i>
                    Horizon 2028
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Des usages multiples</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    Le livrable principal du projet QUALIF est un <strong>jeu de données de référence Macrohétérocères</strong>, au format SINP/INPN, accompagné d'une note méthodologique détaillant les choix de qualification effectués, les biais identifiés et les régions ou groupes problématiques. Ce livrable n'a pas une seule destination, mais plusieurs usages possibles, en fonction des besoins de chaque utilisateur.
                </p>
                <p>
                    Pour les <strong>chercheurs</strong>, c'est une matière première solide pour modéliser les dynamiques de populations, étudier les réponses au changement climatique, ou analyser les facteurs de raréfaction des espèces. Pour les <strong>gestionnaires d'espaces naturels</strong>, c'est un référentiel auquel se confronter pour situer le patrimoine lépidoptérologique d'un site dans son contexte national, et identifier les enjeux locaux spécifiques. Pour les <strong>bureaux d'études</strong> intervenant dans le cadre d'évaluations environnementales, c'est un support pour interpréter les résultats d'inventaires ponctuels.
                </p>
                <p>
                    Pour les <strong>programmes de science participative</strong>, le jeu de données qualifié permet d'identifier les espèces sur lesquelles concentrer les efforts d'observation citoyenne, et celles qui demandent au contraire une expertise solide. Pour les <strong>associations naturalistes régionales</strong>, c'est un outil de comparaison et de mise en cohérence avec les autres bases régionales.
                </p>
                <p>
                    Le jeu de données pourra enfin servir à des <strong>évaluations de l'état de conservation</strong> : identification d'espèces dont la situation appelle une vigilance, alimentation du volet espèces rares du dispositif EU-PoMS, et le cas échéant, contribution à une future liste rouge nationale des Lépidoptères nocturnes, en complément de la liste rouge européenne des Macrohétérocères en cours d'achèvement à laquelle oreina contribue.
                </p>
                <p>
                    Cette diversité d'usages est précisément ce qui justifie l'ambition du projet : la qualification systématique a un coût élevé, mais le jeu de données qui en résulte est <strong>un investissement collectif</strong> qui ouvre de multiples possibilités de travail pour la communauté scientifique, les acteurs de la conservation et les naturalistes eux-mêmes.
                </p>
            </div>
        </div>
    </section>

    {{-- Un bien commun naturaliste --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="share-2"></i>
                    Une finalité structurante
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Mettre à disposition un jeu de données de référence</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Au-delà de la Liste rouge, l'un des objectifs structurants de QUALIF, étroitement lié au projet <em>Artemisiae</em>, est de produire et de mettre à disposition de la communauté un jeu de données de référence sur les Macrohétérocères de France. C'est une condition de l'utilité collective du travail de validation effectué par le réseau d'oreina.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 items-start">
                <div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-4">Pourquoi un bien commun</h3>
                    <p class="text-slate-600 mb-4">
                        Une donnée validée qui resterait confinée dans la base interne d'oreina perdrait l'essentiel de sa valeur. <em>Artemisiae</em> n'a pas vocation à être un silo : la qualité de la donnée n'a de sens que si elle bénéficie à l'ensemble de la communauté naturaliste, scientifique et institutionnelle.
                    </p>
                    <p class="text-slate-600">
                        Le projet QUALIF assume donc explicitement cette dimension : <strong>produire un jeu de données de référence librement accessible</strong>, dans les formats standards du Système d'Information sur la Biodiversité, est un livrable au même titre que la qualification elle-même.
                    </p>
                </div>

                <div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-4">À qui il sera utile</h3>
                    <p class="text-slate-600 mb-4">
                        Le jeu de données de référence Macrohétérocères pourra servir aux <strong>évaluations Liste rouge</strong> nationales et régionales, aux <strong>gestionnaires d'espaces naturels</strong> qui élaborent des plans de gestion, aux <strong>chercheurs</strong> qui modélisent les dynamiques de populations, aux <strong>bureaux d'études</strong> dans le cadre des évaluations environnementales, et aux <strong>associations naturalistes régionales</strong> qui souhaitent croiser leurs données avec un référentiel national qualifié.
                    </p>
                    <p class="text-slate-600">
                        À chaque utilisateur correspondent des besoins de précision et de profondeur temporelle différents, que la qualification d'usage permettra d'adresser.
                    </p>
                </div>
            </div>

            <div class="mt-10 grid md:grid-cols-3 gap-6">
                <div class="card p-6 bg-white">
                    <div class="w-12 h-12 bg-gradient-to-br from-oreina-green to-oreina-teal rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="bookmark" style="width:24px;height:24px;color:#fff"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Format SINP / INPN</h3>
                    <p class="text-sm text-slate-600">Le jeu de données est produit dans les formats standards interopérables du Système d'Information sur la Biodiversité, garantissant son intégration dans les écosystèmes de données existants.</p>
                </div>
                <div class="card p-6 bg-white">
                    <div class="w-12 h-12 bg-gradient-to-br from-oreina-coral to-oreina-yellow rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="file-text" style="width:24px;height:24px;color:#fff"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Méthodologie transparente</h3>
                    <p class="text-sm text-slate-600">Une note méthodologique accompagne le jeu de données et précise les choix de qualification, les biais identifiés, les régions et groupes problématiques. Chaque utilisateur peut juger de la pertinence pour son usage propre.</p>
                </div>
                <div class="card p-6 bg-white">
                    <div class="w-12 h-12 bg-gradient-to-br from-oreina-turquoise to-oreina-blue rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="refresh-cw" style="width:24px;height:24px;color:#fff"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Mises à jour régulières</h3>
                    <p class="text-sm text-slate-600">Le jeu de données vit. Il s'enrichit en continu des nouvelles validations effectuées par le réseau d'<em>Artemisiae</em> et fait l'objet de versions stabilisées périodiques accompagnées de leurs métadonnées.</p>
                </div>
            </div>

            <div class="mt-10 p-6 bg-white rounded-xl border-l-4 border-oreina-yellow">
                <p class="text-slate-600">
                    <strong class="text-oreina-dark">Une cohérence avec la philosophie d'<em>Artemisiae</em>.</strong> La plateforme <em>Artemisiae</em> est conçue depuis l'origine comme un commun, accessible à tous, adhérents ou non, pour la saisie comme pour la consultation. Le projet QUALIF prolonge naturellement cette philosophie en mettant à disposition non plus seulement la plateforme, mais le résultat collectif du travail de validation : la donnée qualifiée elle-même.
                </p>
            </div>
        </div>
    </section>

    {{-- Pour aller plus loin --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-oreina-dark">Pour aller plus loin</h2>
                <p class="text-slate-500 mt-2">Ressources externes, programmes connexes et publications associées au projet QUALIF.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="https://uicn.fr/listes-rouges-en-france/" target="_blank" rel="noopener" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon coral mb-3">
                        <i class="icon icon-coral" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-coral transition">Listes rouges UICN</h3>
                    <p class="text-xs text-slate-500">Méthodologie et catégories de l'UICN pour l'évaluation de l'état de conservation.</p>
                </a>
                <a href="https://inpn.mnhn.fr/programme/donnees-observations-especes/presentation" target="_blank" rel="noopener" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon blue mb-3">
                        <i class="icon icon-blue" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-blue transition">SINP / INPN</h3>
                    <p class="text-xs text-slate-500">Système d'Information sur la Biodiversité et Inventaire National du Patrimoine Naturel.</p>
                </a>
                <a href="{{ route('journal.home') }}" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-3">
                        <i class="icon icon-sage" data-lucide="book-open"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-green transition">Articles méthodologiques</h3>
                    <p class="text-xs text-slate-500">Travaux d'oreina sur la qualification publiés dans <em>Chersotis</em>.</p>
                </a>
                <a href="/documents/rapport-activite-2024.pdf" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon gold mb-3">
                        <i class="icon icon-gold" data-lucide="file-bar-chart"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-yellow transition">Rapport d'activité</h3>
                    <p class="text-xs text-slate-500">Bilan d'activité QUALIF 2024 d'oreina.</p>
                </a>
            </div>
        </div>
    </section>

    {{-- Vous pouvez contribuer --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow sage mb-4 inline-flex">
                    <i class="icon icon-sage" data-lucide="helping-hand"></i>
                    Contribuer
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Vous pouvez contribuer</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">QUALIF est un projet collectif qui ne peut pas se faire sans une mobilisation large : la qualification de centaines de milliers de données ne peut reposer sur quelques personnes. Plusieurs formes de contribution sont possibles, à différents niveaux d'engagement.</p>
            </div>

            <div class="space-y-4">
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon sage flex-shrink-0">
                        <i class="icon icon-sage" data-lucide="pencil"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Saisissez des observations documentées</h3>
                        <p class="text-slate-600 text-sm">La meilleure contribution à QUALIF commence par une saisie soigneuse sur <em>Artemisiae</em> : photo, géoréférencement précis, contexte d'observation, méthode utilisée. Une donnée bien documentée à la source économise un travail considérable de validation en aval.</p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon coral flex-shrink-0">
                        <i class="icon icon-coral" data-lucide="user-check"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Devenez expert validateur</h3>
                        <p class="text-slate-600 text-sm">Si vous avez une expertise solide sur un groupe (Géomètres, Noctuelles, Microlépidoptères, Sphinx, Saturnies...), rejoignez le réseau des validateurs. Le temps de validation est modulable selon vos disponibilités, et l'appui méthodologique est assuré par le groupe projet.</p>
                    </div>
                </div>
                {{-- Carte "Vous gérez une base de données régionale ou thématique ?" temporairement masquée --}}
                {{--
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon blue flex-shrink-0">
                        <i class="icon icon-blue" data-lucide="building-2"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Vous gérez une base de données régionale ou thématique ?</h3>
                        <p class="text-slate-600 text-sm">Le projet QUALIF est ouvert aux jeux de données externes au SINP. oreina peut effectuer une analyse de qualité sur votre base et vous transmettre un rapport d'expertise détaillé. Contactez-nous pour étudier les modalités.</p>
                    </div>
                </div>
                --}}
            </div>
        </div>
    </section>

    {{-- CTA bandeau --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="message-circle"></i>Rejoindre le projet</div>
                <h2>Participer à QUALIF</h2>
                <p>Que vous soyez observateur de terrain, expert validateur ou gestionnaire d'une base de données régionale, votre contribution alimente le jeu de données de référence sur les Macrohétérocères de France et démultiplie ses usages. Contactez-nous pour échanger.</p>
                <div class="content-actions">
                    <a href="{{ route('hub.contact') }}" class="btn btn-primary">
                        <i class="icon icon-sage" data-lucide="mail"></i>
                        Nous contacter
                    </a>
                    <a href="{{ route('hub.membership') }}" class="btn btn-ghost-light">
                        <i class="icon icon-white" data-lucide="heart-plus"></i>
                        Adhérer à OREINA
                    </a>
                </div>
            </article>
        </div>
    </section>

    {{-- Autres projets --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-oreina-dark">Découvrir les autres projets</h2>
                <p class="text-slate-500 mt-3 max-w-3xl mx-auto">QUALIF est l'aboutissement opérationnel du dispositif scientifique d'oreina : il mobilise les quatre projets de référence (TAXREF, SEQREF, BDC, IDENT) pour qualifier les données d'observation.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('hub.projets.taxref') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="layers"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-green transition">TAXREF</h3>
                    <p class="text-xs text-slate-500">Référentiel taxonomique national des Lépidoptères de France.</p>
                </a>
                <a href="{{ route('hub.projets.seqref') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon blue mb-4">
                        <i class="icon icon-blue" data-lucide="dna"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-blue transition">SEQREF</h3>
                    <p class="text-xs text-slate-500">Bibliothèque de séquences moléculaires de référence.</p>
                </a>
                <a href="{{ route('hub.projets.bdc') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="list-checks"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-coral transition">BDC</h3>
                    <p class="text-xs text-slate-500">Base de données de traits de vie des Lépidoptères.</p>
                </a>
                <a href="{{ route('hub.projets.ident') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon gold mb-4">
                        <i class="icon icon-gold" data-lucide="search"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-yellow transition">IDENT</h3>
                    <p class="text-xs text-slate-500">Critères d'identification et typologie de difficulté.</p>
                </a>
            </div>
        </div>
    </section>
@endsection
