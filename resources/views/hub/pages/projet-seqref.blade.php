@extends('layouts.hub')

@section('title', 'Barcoding moléculaire (SEQREF)')
@section('meta_description', 'oreina contribue, en partenariat avec le MNHN, à la constitution d\'une bibliothèque de séquences ADN de référence pour les Lépidoptères de France métropolitaine.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="flex-1">
                    <div class="eyebrow blue mb-4 inline-flex">
                        <i class="icon icon-blue" data-lucide="dna"></i>
                        Projet 2 / 5
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Barcoding moléculaire</h1>
                    <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl">
                        Constituer une bibliothèque de séquences ADN de référence pour les papillons de France
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm bg-white p-5 rounded-2xl border border-slate-200 lg:min-w-[420px]">
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Programme</p>
                        <p class="font-bold text-oreina-dark">SEQREF</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Pilotage scientifique</p>
                        <p class="font-bold text-oreina-dark">ISYEB (MNHN)</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Convention</p>
                        <p class="font-bold text-oreina-dark">OFB 2026, 2028</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Coordination</p>
                        <p class="font-bold text-oreina-dark">2 bénévoles + salariée</p>
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
                    <div class="rounded-3xl shadow-lg flex items-center justify-center bg-gradient-to-br from-oreina-blue/10 to-oreina-turquoise/10 relative overflow-hidden" style="min-height: 340px;">
                        <i data-lucide="dna" style="width:140px;height:140px;color:var(--blue);opacity:0.85"></i>
                        <i data-lucide="microscope" style="width:36px;height:36px;color:#2f694e;opacity:0.55;position:absolute;top:24px;right:32px"></i>
                        <i data-lucide="database" style="width:36px;height:36px;color:var(--coral);opacity:0.55;position:absolute;bottom:32px;left:28px"></i>
                        <i data-lucide="flask-conical" style="width:32px;height:32px;color:#8b6c05;opacity:0.55;position:absolute;bottom:36px;right:36px"></i>
                    </div>
                </div>
                <div class="lg:col-span-3 text-slate-600 space-y-6">
                    <p class="text-xl leading-relaxed">
                        Identifier une espèce à partir d'un fragment d'ADN, retrouver une espèce dans un échantillon de pollen ou dans un piège à ADN environnemental, détecter des espèces cryptiques que la morphologie ne permet pas de distinguer : ces usages, encore récents, deviennent centraux pour le suivi de la biodiversité.
                    </p>
                    <p class="leading-relaxed">
                        Tous reposent sur une condition préalable : disposer d'une <strong>bibliothèque de séquences de référence</strong> fiable et complète. C'est l'objet du projet <strong>SEQREF</strong>, qu'oreina conduit en partenariat avec le Muséum national d'Histoire naturelle.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Documents à télécharger - section mise en avant --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="download"></i>
                    Documents à télécharger
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Pour comprendre, pour participer</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">Trois documents pour découvrir le projet, comprendre votre rôle et participer concrètement.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                {{-- Document 1 : C'est quoi le barcoding --}}
                <div class="card p-6 flex flex-col text-center hover:shadow-lg transition border-2 border-oreina-coral/20">
                    <div class="pub-card-icon blue mx-auto mb-4" style="width: 64px; height: 64px;">
                        <i class="icon icon-blue" data-lucide="book-open" style="width:28px;height:28px"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark text-lg mb-2">Le barcoding</h3>
                    <p class="text-slate-600 text-sm mb-6 flex-grow">C'est quoi ? Pourquoi c'est important ? Notre rôle ? Le fascicule pédagogique pour découvrir le projet.</p>
                    <div class="flex items-center justify-center gap-2 text-xs text-slate-400 mb-4">
                        <i data-lucide="file-text" style="width:14px;height:14px"></i>
                        <span>PDF · Fascicule</span>
                    </div>
                    <a href="https://oreina.org/docs/cestkoi_barcoding.pdf" target="_blank" rel="noopener" class="btn btn-primary justify-center">
                        <i class="icon icon-sage" data-lucide="download"></i>
                        Télécharger
                    </a>
                </div>

                {{-- Document 2 : Protocole simple --}}
                <div class="card p-6 flex flex-col text-center hover:shadow-lg transition border-2 border-oreina-coral/20">
                    <div class="pub-card-icon sage mx-auto mb-4" style="width: 64px; height: 64px;">
                        <i class="icon icon-sage" data-lucide="list-checks" style="width:28px;height:28px"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark text-lg mb-2">Protocole simple</h3>
                    <p class="text-slate-600 text-sm mb-6 flex-grow">Comment participer au programme barcoding en 7 étapes simples. Idéal pour démarrer.</p>
                    <div class="flex items-center justify-center gap-2 text-xs text-slate-400 mb-4">
                        <i data-lucide="file-text" style="width:14px;height:14px"></i>
                        <span>PDF · Protocole 7 étapes</span>
                    </div>
                    <a href="https://oreina.org/docs/protocol_barcoding_simple.pdf" target="_blank" rel="noopener" class="btn btn-primary justify-center">
                        <i class="icon icon-sage" data-lucide="download"></i>
                        Télécharger
                    </a>
                </div>

                {{-- Document 3 : Protocole détaillé --}}
                <div class="card p-6 flex flex-col text-center hover:shadow-lg transition border-2 border-oreina-coral/20">
                    <div class="pub-card-icon coral mx-auto mb-4" style="width: 64px; height: 64px;">
                        <i class="icon icon-coral" data-lucide="book-marked" style="width:28px;height:28px"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark text-lg mb-2">Protocole détaillé</h3>
                    <p class="text-slate-600 text-sm mb-6 flex-grow">Tous les détails de votre rôle dans le programme : préparation, conservation, étiquetage, envoi.</p>
                    <div class="flex items-center justify-center gap-2 text-xs text-slate-400 mb-4">
                        <i data-lucide="file-text" style="width:14px;height:14px"></i>
                        <span>PDF · Manuel complet</span>
                    </div>
                    <a href="https://oreina.org/docs/protocol_barcoding_detail.pdf" target="_blank" rel="noopener" class="btn btn-primary justify-center">
                        <i class="icon icon-sage" data-lucide="download"></i>
                        Télécharger
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Le barcoding, c'est quoi --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="lightbulb"></i>
                    Pour comprendre
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Le barcoding, c'est quoi ?</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    Le <strong>barcoding moléculaire</strong>, ou « code-barres ADN », est une technique scientifique qui permet d'identifier un organisme à partir d'une courte séquence d'ADN, appelée <em>marqueur</em>. Le principe est simple : à chaque espèce correspond, dans son génome, des régions dont la séquence est suffisamment stable au sein de l'espèce et suffisamment différente entre espèces pour servir de signature génétique. Comme un code-barres en supermarché, mais pour les êtres vivants.
                </p>
                <p>
                    Pour les Lépidoptères, le marqueur principal est le gène mitochondrial <strong>CO1</strong> (cytochrome oxydase, sous-unité 1). Ce gène est devenu le standard international pour le barcoding animal : il est suffisamment variable pour discriminer la majorité des espèces, et son séquençage est aujourd'hui rapide et économique. Pour certains groupes complexes (espèces d'apparition récente, hybrides, lignées en cours de spéciation), CO1 ne suffit pas et il devient nécessaire d'associer des <strong>marqueurs nucléaires</strong> complémentaires.
                </p>
                <p>
                    Pour qu'un barcoding soit utile, il faut comparer la séquence obtenue à une <strong>base de données de référence</strong> dans laquelle figurent des séquences attribuées à des espèces correctement identifiées et géoréférencées. C'est cette base de référence qu'il s'agit de construire et de compléter.
                </p>
            </div>
        </div>
    </section>

    {{-- Pourquoi c'est important --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="target"></i>
                    L'enjeu
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Pourquoi c'est important</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    Au-delà de l'identification individuelle, une bibliothèque de séquences de référence robuste ouvre plusieurs usages structurants pour la connaissance et le suivi de la biodiversité.
                </p>
                <p>
                    Elle permet le <strong>suivi par ADN environnemental</strong> (eDNA), méthode en plein essor qui consiste à détecter la présence d'espèces à partir de traces d'ADN laissées dans le milieu : eau, sol, ou même nectar et pollen pour les pollinisateurs. C'est précisément ce qu'expérimente le réseau thématique CNRS Pollinéco sur les insectes floricoles. Sans bibliothèque de référence complète, ces analyses produisent des résultats partiels ou erronés.
                </p>
                <p>
                    Elle alimente aussi la <strong>taxonomie elle-même</strong>. La confrontation de spécimens issus de toute l'aire de répartition d'une espèce révèle souvent des <strong>lignées phylogénétiques distinctes</strong>, parfois associées à des caractéristiques biogéographiques particulières. L'analyse des distances génétiques entre ces lignées peut conduire à la description de nouvelles espèces, ou à la confirmation de séparations longtemps suspectées sur des bases morphologiques.
                </p>
                <p>
                    Elle servira enfin, à terme, le <strong>dispositif européen EU-PoMS</strong> (European Pollinator Monitoring Scheme), dans lequel oreina est désignée comme structure de référence pour les Lépidoptères nocturnes. Le schéma central d'EU-PoMS s'appuie aujourd'hui sur des relevés visuels, mais des modules génétiques complémentaires sont envisagés pour le futur, et nécessiteront une couverture de référence aussi exhaustive que possible.
                </p>
            </div>
        </div>
    </section>

    {{-- Le rôle d'oreina --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow sage mb-4 inline-flex">
                    <i class="icon icon-sage" data-lucide="handshake"></i>
                    Le rôle d'oreina
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Un partenariat avec le MNHN, un réseau national de collecte</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    oreina s'est associée dès 2024 à PatriNat pour contribuer à la complétion d'un référentiel de séquences moléculaires pour les Lépidoptères de France métropolitaine. Le projet est piloté scientifiquement par l'<strong>Institut de Systématique, Évolution, Biodiversité</strong> (ISYEB, UMR 7205 CNRS/MNHN), et plus précisément par l'équipe de Rodolphe Rougerie, dans le cadre de l'infrastructure internationale <strong>BOLD</strong> (Barcode of Life Data System).
                </p>
                <p>
                    Le rôle d'oreina dans ce dispositif est double :
                </p>
                <ul class="list-disc pl-8 space-y-3 marker:text-oreina-green">
                    <li><span class="pl-2 inline-block">une <strong>expertise d'identification et de priorisation</strong>. Sur les 5 615 espèces de Lépidoptères recensées en France métropolitaine, l'équipe d'oreina a expertisé chaque taxon au regard des données déjà disponibles dans BOLD, de la variabilité génétique connue, des cas d'hybridation, et de la présence de localités-types en France. Cette analyse a permis de classer les espèces selon leur priorité de récolte, et d'orienter les efforts de collecte vers ce qui manque réellement à la base ;</span></li>
                    <li><span class="pl-2 inline-block">l'<strong>animation d'un réseau national de collecte</strong>. La collecte des spécimens repose sur les adhérents d'oreina et sur un réseau de référents régionaux et de contributeurs ponctuels, qui prélèvent les espèces prioritaires dans leur région, les préparent selon le protocole, et les transmettent au MNHN pour séquençage.</span></li>
                </ul>
                <p>
                    Le projet est piloté au sein de l'association par deux bénévoles experts et la coordinatrice scientifique salariée, qui assure la liaison opérationnelle avec le MNHN et l'animation du réseau.
                </p>
            </div>
        </div>
    </section>

    {{-- Chiffres-clés --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="bar-chart-3"></i>
                    Chiffres-clés
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">SEQREF en chiffres</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">État de la couverture moléculaire des Lépidoptères de France et objectifs de la convention 2026, 2028.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-turquoise mb-2">5 615</p>
                    <p class="text-sm text-slate-600 leading-tight">espèces de Lépidoptères recensées en France métropolitaine</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-turquoise mb-2">3 620</p>
                    <p class="text-sm text-slate-600 leading-tight">espèces classées comme prioritaires pour la collecte</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-turquoise mb-2">594</p>
                    <p class="text-sm text-slate-600 leading-tight">taxons d'importance moyenne pour la complétion de la base</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">17</p>
                    <p class="text-sm text-slate-600 leading-tight">référents régionaux pour organiser la collecte dans les territoires</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">1 000</p>
                    <p class="text-sm text-slate-600 leading-tight">spécimens à transmettre au MNHN par an : c'est l'objectif</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">~70 %</p>
                    <p class="text-sm text-slate-600 leading-tight">de couverture cible des espèces françaises à l'horizon 2028</p>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">Sources : fiche projet SEQREF 2026, 2028 et rapport d'activité OFB 2024 d'oreina.</p>
        </div>
    </section>

    {{-- Cinq axes de travail --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="git-branch"></i>
                    Méthodologie
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Cinq axes de travail interdépendants</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Le projet SEQREF s'organise autour de cinq axes complémentaires, qui structurent l'effort collectif d'oreina et de ses partenaires.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card p-6">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="list-checks"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Suivi dynamique des priorités</h3>
                    <p class="text-sm text-slate-600">Maintien à jour de la liste des espèces prioritaires, en fonction des séquences déjà obtenues, des publications scientifiques nouvelles et des besoins du dispositif EU-PoMS.</p>
                </div>

                <div class="card p-6">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="database"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Complétude de la banque de séquences</h3>
                    <p class="text-sm text-slate-600">Acquisition progressive de séquences pour les espèces prioritaires, en couvrant l'aire de répartition française pour identifier les éventuelles lignées intra-spécifiques.</p>
                </div>

                <div class="card p-6">
                    <div class="pub-card-icon gold mb-4">
                        <i class="icon icon-gold" data-lucide="flask-conical"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Séquençage génomique</h3>
                    <p class="text-sm text-slate-600">Pris en charge par l'équipe ISYEB du MNHN. Marqueur principal CO1, avec marqueurs nucléaires complémentaires pour les espèces complexes.</p>
                </div>

                <div class="card p-6">
                    <div class="pub-card-icon blue mb-4">
                        <i class="icon icon-blue" data-lucide="users-round"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Animation du réseau</h3>
                    <p class="text-sm text-slate-600">Coordination des référents régionaux, accompagnement des contributeurs, organisation de webinaires et de sessions de formation, valorisation des contributeurs dans les publications.</p>
                </div>

                <div class="card p-6">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="git-merge"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Intégration des données</h3>
                    <p class="text-sm text-slate-600">Articulation entre SEQREF et les autres référentiels d'oreina, notamment TAXREF (les changements taxonomiques détectés par barcoding alimentent la mise à jour du référentiel).</p>
                </div>

                <div class="card p-6 border-2 border-oreina-green/30">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="globe-2"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Articulation avec EU-PoMS</h3>
                    <p class="text-sm text-slate-600">Priorisation des espèces relevant d'EU-PoMS dans les efforts de séquençage, pour anticiper les besoins futurs en modules génétiques du dispositif européen.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Comment ça marche concrètement --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="route"></i>
                    Concrètement
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Participer en quatre étapes</h2>
                <p class="text-slate-500 mt-3 max-w-2xl">Toute la chaîne de contribution s'organise sur la plateforme <em>Artemisiae</em>, qui centralise les priorités d'espèces, l'enregistrement des dons de spécimens et le suivi des envois vers le MNHN.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="card p-6">
                    <div class="w-10 h-10 rounded-full bg-oreina-yellow text-oreina-dark flex items-center justify-center font-bold mb-3">1</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Priorisez</h3>
                    <p class="text-sm text-slate-600">Consultez la liste des espèces recherchées sur <em>Artemisiae</em>, ou l'onglet « Barcoding » sur chaque fiche espèce.</p>
                </div>
                <div class="card p-6">
                    <div class="w-10 h-10 rounded-full bg-oreina-yellow text-oreina-dark flex items-center justify-center font-bold mb-3">2</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Récoltez</h3>
                    <p class="text-sm text-slate-600">Récoltez des spécimens dans vos collections existantes, ou lors de nouvelles collectes, dans le respect de la réglementation en vigueur.</p>
                </div>
                <div class="card p-6">
                    <div class="w-10 h-10 rounded-full bg-oreina-yellow text-oreina-dark flex items-center justify-center font-bold mb-3">3</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Signalez</h3>
                    <p class="text-sm text-slate-600">Enregistrez votre observation et signalez-la comme un don pour le projet barcoding sur <em>Artemisiae</em>. Un numéro d'identification unique vous est attribué.</p>
                </div>
                <div class="card p-6">
                    <div class="w-10 h-10 rounded-full bg-oreina-yellow text-oreina-dark flex items-center justify-center font-bold mb-3">4</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Préparez</h3>
                    <p class="text-sm text-slate-600">Étalage classique, détermination certaine (genitalia si nécessaire), étiquetage avec date, lieu et coordonnées géographiques. Envoi groupé au MNHN.</p>
                </div>
            </div>

            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="https://oreina.org/artemisiae/index.php?module=barcode&action=barcode" target="_blank" rel="noopener" class="btn btn-primary">
                    <i class="icon icon-sage" data-lucide="external-link"></i>
                    Consulter le tableau des espèces
                </a>
                <a href="https://oreina.org/artemisiae/" target="_blank" rel="noopener" class="btn btn-secondary">
                    <i class="icon icon-blue" data-lucide="database"></i>
                    Accéder à <em class="not-italic">&nbsp;Artemisiae</em>
                </a>
            </div>
        </div>
    </section>

    {{-- Pour aller plus loin --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-oreina-dark">Pour aller plus loin</h2>
                <p class="text-slate-500 mt-2">Ressources externes, protocoles et publications associées au projet SEQREF.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="https://www.boldsystems.org/" target="_blank" rel="noopener" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon blue mb-3">
                        <i class="icon icon-blue" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-blue transition">BOLD Systems</h3>
                    <p class="text-xs text-slate-500">L'infrastructure internationale de référence pour le barcoding.</p>
                </a>
                <a href="https://isyeb.mnhn.fr/" target="_blank" rel="noopener" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-3">
                        <i class="icon icon-sage" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-green transition">Institut ISYEB</h3>
                    <p class="text-xs text-slate-500">Institut de Systématique, Évolution, Biodiversité (UMR 7205 CNRS, MNHN).</p>
                </a>
                <a href="/documents/protocole-barcoding-oreina.pdf" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon coral mb-3">
                        <i class="icon icon-coral" data-lucide="file-text"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-coral transition">Protocole de collecte</h3>
                    <p class="text-xs text-slate-500">Fascicule pédagogique et protocole détaillé pour les contributeurs.</p>
                </a>
                <a href="/documents/rapport-activite-2024.pdf" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon gold mb-3">
                        <i class="icon icon-gold" data-lucide="file-bar-chart"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-blue transition">Rapport d'activité</h3>
                    <p class="text-xs text-slate-500">Rapport SEQREF 2024 d'oreina à l'OFB.</p>
                </a>
            </div>
        </div>
    </section>

    {{-- Trois voies de contribution --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow sage mb-4 inline-flex">
                    <i class="icon icon-sage" data-lucide="helping-hand"></i>
                    Contribuer
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Vous pouvez contribuer</h2>
                <p class="text-slate-500 mt-3 max-w-2xl">SEQREF est un projet collectif, ouvert à tous : adhérents et non-adhérents, lépidoptéristes confirmés ou naturalistes motivés. Plus le réseau est large, plus la couverture géographique est complète.</p>
            </div>

            <div class="space-y-4">
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon sage flex-shrink-0">
                        <i class="icon icon-sage" data-lucide="search"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Devenir collecteur</h3>
                        <p class="text-slate-600 text-sm">Vous êtes lépidoptériste actif sur le terrain ? Consultez la liste des espèces prioritaires sur <em>Artemisiae</em>, prélevez selon le protocole et transmettez vos spécimens au MNHN. Chaque spécimen compte.</p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon coral flex-shrink-0">
                        <i class="icon icon-coral" data-lucide="map-pin"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Devenir référent régional</h3>
                        <p class="text-slate-600 text-sm">Si vous souhaitez animer la collecte dans votre région, coordonner les contributeurs locaux et faire le lien avec l'équipe nationale, contactez-nous. La formation et l'appui sont assurés par la coordinatrice scientifique.</p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon gold flex-shrink-0">
                        <i class="icon icon-gold" data-lucide="building-2"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Vous représentez une association ou une structure de gestion ?</h3>
                        <p class="text-slate-600 text-sm">Le projet SEQREF est ouvert aux partenariats avec les associations naturalistes régionales et les gestionnaires d'espaces naturels. Une contribution territoriale ciblée peut renforcer significativement la couverture sur des secteurs sous-échantillonnés.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA bandeau --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="message-circle"></i>Rejoindre le projet</div>
                <h2>Participer à SEQREF</h2>
                <p>Que vous soyez collecteur de terrain, référent régional ou structure partenaire, votre contribution renforce la couverture moléculaire des Lépidoptères de France. Contactez-nous pour démarrer.</p>
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
                <p class="text-slate-500 mt-3 max-w-3xl mx-auto">SEQREF s'inscrit dans la convention pluriannuelle 2026, 2028 d'oreina avec l'OFB, qui structure cinq projets scientifiques complémentaires.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('hub.projets.taxref') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="layers"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-green transition">TAXREF</h3>
                    <p class="text-xs text-slate-500">Référentiel taxonomique national des Lépidoptères de France.</p>
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
                <a href="{{ route('hub.projets.qualif') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="badge-check"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-green transition">QUALIF</h3>
                    <p class="text-xs text-slate-500">Qualification et validation des données d'observation.</p>
                </a>
            </div>
        </div>
    </section>
@endsection
