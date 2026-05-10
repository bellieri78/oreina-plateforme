@extends('layouts.hub')

@section('title', 'Artemisiae - Le portail naturaliste des Lépidoptères de France')
@section('meta_description', 'Artemisiae est le portail naturaliste d\'oreina pour la saisie, la consultation et la qualification des données d\'observation des Lépidoptères de France. Fiches espèces, cartes, application mobile (PWA), référentiel taxonomique Systema et barcoding intégré.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('hub.home') }}" class="text-sm text-slate-500 hover:text-oreina-coral inline-flex items-center gap-2 transition">
                    <i data-lucide="arrow-left" style="width:14px;height:14px"></i>
                    Retour à l'accueil
                </a>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="flex-1">
                    <div class="eyebrow gold mb-4 inline-flex">
                        <i class="icon icon-gold" data-lucide="globe-2"></i>
                        Portail naturaliste
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Artemisiae</h1>
                    <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl">
                        L'observatoire des Lépidoptères de France : saisir, consulter, qualifier — sous licence libre
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm bg-white p-5 rounded-2xl border border-slate-200 lg:min-w-[420px]">
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Consultation</p>
                        <p class="font-bold text-oreina-dark">Libre, sans inscription</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Saisie</p>
                        <p class="font-bold text-oreina-dark">Compte gratuit</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Logiciel</p>
                        <p class="font-bold text-oreina-dark">Libre (licence AGPL)</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Cadre</p>
                        <p class="font-bold text-oreina-dark">Standard SINP</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="https://oreina.org/artemisiae/" target="_blank" rel="noopener" class="btn btn-primary">
                    <i class="icon icon-sage" data-lucide="external-link"></i>
                    Ouvrir Artemisiae
                </a>
                <a href="https://oreina.org/artemisiae/index.php?module=info&action=faq" target="_blank" rel="noopener" class="btn btn-secondary">
                    <i class="icon icon-coral" data-lucide="help-circle"></i>
                    Tutoriels &amp; FAQ
                </a>
            </div>
        </div>
    </section>

    {{-- C'est quoi --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-5 gap-12 items-start">
                <div class="lg:col-span-2">
                    <div class="eyebrow blue mb-4 inline-flex">
                        <i class="icon icon-blue" data-lucide="lightbulb"></i>
                        C'est quoi&nbsp;?
                    </div>
                    <h2 class="text-3xl font-bold text-oreina-dark mb-4">Un observatoire, pas seulement une base de données</h2>
                    <p class="text-slate-600 leading-relaxed mb-4">
                        <em>Artemisiae</em> est l'observatoire des Lépidoptères de France développé et maintenu par oreina. Saisie en ligne, application mobile, fiches taxons, cartes de répartition, référentiel taxonomique en continu (Systema), Portail bibliographique, séquences de barcoding : tous ces outils sont intégrés dans une même interface, articulée autour des projets scientifiques de l'association.
                    </p>
                    <p class="text-slate-600 leading-relaxed mb-4">
                        Le portail repose sur un <strong>logiciel libre sous licence AGPL</strong>, développé par <strong>Denis Vandromme</strong>, et hébergé en France (o2switch, Clermont-Ferrand). L'organisation des données est conforme au standard <strong>SINP</strong>.
                    </p>
                    <p class="text-slate-600 leading-relaxed">
                        L'esprit du portail est résolument <strong>ouvert</strong> : la consultation des fiches taxons, des cartes, du référentiel et du Portail bibliographique est libre. Seule la saisie de données nécessite un compte personnel, gratuit.
                    </p>
                </div>

                <div class="lg:col-span-3">
                    {{-- Imprim'écran principal : page d'accueil Artemisiae --}}
                    <div class="card p-0 overflow-hidden bg-gradient-to-br from-oreina-yellow/10 to-oreina-coral/10 aspect-[16/10] flex items-center justify-center">
                        <img src="/images/artemisiae/accueil.png" alt="Capture d'écran de la page d'accueil du portail Artemisiae" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><i data-lucide=\'image\' style=\'width:48px;height:48px;color:#cbd5e1;margin:0 auto 12px\'></i><p class=\'text-slate-500 text-sm font-bold\'>Imprim\'écran : page d\'accueil Artemisiae</p><p class=\'text-slate-400 text-xs mt-1\'>/images/artemisiae/accueil.png</p></div>'; if (typeof lucide !== \'undefined\') lucide.createIcons();">
                    </div>
                    <p class="text-xs text-slate-400 mt-2 italic">Page d'accueil — accès aux observatoires, à la saisie, à la consultation et aux outils.</p>
                </div>
            </div>

            {{-- Principes structurants --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-12">
                <div class="card p-5 bg-white text-center">
                    <i data-lucide="unlock" style="width:28px;height:28px;color:var(--coral);margin:0 auto 8px"></i>
                    <p class="font-bold text-oreina-dark text-sm mb-1">Diffusion libre</p>
                    <p class="text-xs text-slate-500">de la connaissance, dans le respect des producteurs</p>
                </div>
                <div class="card p-5 bg-white text-center">
                    <i data-lucide="users" style="width:28px;height:28px;color:#8b6c05;margin:0 auto 8px"></i>
                    <p class="font-bold text-oreina-dark text-sm mb-1">Participation volontaire</p>
                    <p class="text-xs text-slate-500">animation et développement du réseau d'observateurs</p>
                </div>
                <div class="card p-5 bg-white text-center">
                    <i data-lucide="microscope" style="width:28px;height:28px;color:var(--blue);margin:0 auto 8px"></i>
                    <p class="font-bold text-oreina-dark text-sm mb-1">Rigueur scientifique</p>
                    <p class="text-xs text-slate-500">et entomologique, dans le respect de la réglementation</p>
                </div>
                <div class="card p-5 bg-white text-center">
                    <i data-lucide="share-2" style="width:28px;height:28px;color:var(--sage);margin:0 auto 8px"></i>
                    <p class="font-bold text-oreina-dark text-sm mb-1">Échange &amp; partage</p>
                    <p class="text-xs text-slate-500">à toutes les échelles, du local au national</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Comment l'utiliser --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="play-circle"></i>
                    Comment l'utiliser
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Trois usages, une même interface</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Selon que vous veniez consulter, saisir ou contribuer à la qualification, le portail propose des entrées dédiées. La logique est la même : ouvrir l'interface, choisir l'observatoire ou le module, et se laisser guider par les fiches et formulaires.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-12">
                <div class="card p-6">
                    <div class="pub-card-icon blue mb-4">
                        <i class="icon icon-blue" data-lucide="book-open"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">1. Consulter</h3>
                    <p class="text-sm text-slate-600">Fiches taxons, cartes de répartition, photothèque, Portail bibliographique, référentiel Systema. Libre et sans inscription. Parfait pour préparer une sortie ou identifier une donnée.</p>
                </div>
                <div class="card p-6">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="edit-3"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">2. Saisir</h3>
                    <p class="text-sm text-slate-600">Compte gratuit, saisie via un relevé localisé sur un site ou un point. Possibilité d'ajouter des co-observateurs, un déterminateur tiers, des photos. Saisie en ligne ou via l'application mobile (hors-ligne possible).</p>
                </div>
                <div class="card p-6">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="badge-check"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">3. Qualifier</h3>
                    <p class="text-sm text-slate-600">Validation des données selon trois modes (à la saisie, automatique, manuelle par un expert) — voir la section dédiée plus bas.</p>
                </div>
            </div>

            {{-- Imprim'écran : interface de saisie --}}
            <div class="grid lg:grid-cols-2 gap-6">
                <div>
                    <div class="card p-0 overflow-hidden bg-gradient-to-br from-oreina-coral/10 to-oreina-yellow/10 aspect-[16/10] flex items-center justify-center">
                        <img src="/images/artemisiae/saisie.png" alt="Capture d'écran du formulaire de saisie d'un relevé sur Artemisiae" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><i data-lucide=\'image\' style=\'width:40px;height:40px;color:#cbd5e1;margin:0 auto 10px\'></i><p class=\'text-slate-500 text-xs font-bold\'>Imprim\'écran : formulaire de saisie</p><p class=\'text-slate-400 text-xs mt-1\'>/images/artemisiae/saisie.png</p></div>'; if (typeof lucide !== \'undefined\') lucide.createIcons();">
                    </div>
                    <p class="text-xs text-slate-400 mt-2 italic">Saisie d'un relevé : localisation, date, co-observateurs, observations par espèce et par stade.</p>
                </div>
                <div>
                    <div class="card p-0 overflow-hidden bg-gradient-to-br from-oreina-blue/10 to-oreina-green/10 aspect-[16/10] flex items-center justify-center">
                        <img src="/images/artemisiae/tableau-bord.png" alt="Capture d'écran du tableau de bord personnel d'un membre Artemisiae" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><i data-lucide=\'image\' style=\'width:40px;height:40px;color:#cbd5e1;margin:0 auto 10px\'></i><p class=\'text-slate-500 text-xs font-bold\'>Imprim\'écran : tableau de bord membre</p><p class=\'text-slate-400 text-xs mt-1\'>/images/artemisiae/tableau-bord.png</p></div>'; if (typeof lucide !== \'undefined\') lucide.createIcons();">
                    </div>
                    <p class="text-xs text-slate-400 mt-2 italic">Tableau de bord du membre : suivi des relevés, statut de validation, préférences.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Fiches espèces et cartes --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow sage mb-4 inline-flex">
                    <i class="icon icon-sage" data-lucide="map-pin"></i>
                    Fiches taxons &amp; cartes
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Une fiche taxon par espèce, articulée avec les cartes</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Chaque taxon référencé dispose de sa propre fiche, qui agrège l'ensemble des connaissances qu'oreina rassemble : statut taxonomique, photothèque, traits de vie (BDC), niveau de difficulté d'identification (IDENT), bibliographie, et carte de répartition issue du programme ABDSM de PatriNat.</p>
            </div>

            <div class="grid lg:grid-cols-5 gap-8 items-start">
                <div class="lg:col-span-3">
                    <div class="card p-0 overflow-hidden bg-gradient-to-br from-oreina-green/10 to-oreina-turquoise/10 aspect-[16/11] flex items-center justify-center">
                        <img src="/images/artemisiae/fiche-espece.png" alt="Capture d'écran d'une fiche taxon sur Artemisiae" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><i data-lucide=\'image\' style=\'width:48px;height:48px;color:#cbd5e1;margin:0 auto 12px\'></i><p class=\'text-slate-500 text-sm font-bold\'>Imprim\'écran : fiche taxon</p><p class=\'text-slate-400 text-xs mt-1\'>/images/artemisiae/fiche-espece.png</p></div>'; if (typeof lucide !== \'undefined\') lucide.createIcons();">
                    </div>
                    <p class="text-xs text-slate-400 mt-2 italic">Fiche taxon : statut taxonomique, photothèque, traits de vie, difficulté d'identification, bibliographie et répartition.</p>
                </div>
                <div class="lg:col-span-2 space-y-4">
                    <div class="card p-5 bg-white">
                        <h3 class="font-bold text-oreina-dark mb-2 text-sm flex items-center gap-2">
                            <i data-lucide="layers" style="width:18px;height:18px;color:var(--sage)"></i>
                            Statut taxonomique
                        </h3>
                        <p class="text-xs text-slate-600">Nom valide, synonymies, ordination, mises à jour Systema en temps réel.</p>
                    </div>
                    <div class="card p-5 bg-white">
                        <h3 class="font-bold text-oreina-dark mb-2 text-sm flex items-center gap-2">
                            <i data-lucide="image" style="width:18px;height:18px;color:var(--coral)"></i>
                            Photothèque
                        </h3>
                        <p class="text-xs text-slate-600">Imagos, chenilles, mines, chrysalides — avec mention systématique des auteurs.</p>
                    </div>
                    <div class="card p-5 bg-white">
                        <h3 class="font-bold text-oreina-dark mb-2 text-sm flex items-center gap-2">
                            <i data-lucide="list-checks" style="width:18px;height:18px;color:#8b6c05"></i>
                            Traits de vie (BDC)
                        </h3>
                        <p class="text-xs text-slate-600">Plantes-hôtes, phénologie, voltinisme, écologie, statut de protection.</p>
                    </div>
                    <div class="card p-5 bg-white">
                        <h3 class="font-bold text-oreina-dark mb-2 text-sm flex items-center gap-2">
                            <i data-lucide="search" style="width:18px;height:18px;color:var(--blue)"></i>
                            Difficulté d'identification (IDENT)
                        </h3>
                        <p class="text-xs text-slate-600">Typologie T1-T5 par stade biologique, agrégats associés, guides liés.</p>
                    </div>
                </div>
            </div>

            {{-- Cartes de répartition --}}
            <div class="grid lg:grid-cols-2 gap-6 mt-12">
                <div>
                    <div class="card p-0 overflow-hidden bg-gradient-to-br from-oreina-green/10 to-oreina-turquoise/10 aspect-[16/10] flex items-center justify-center">
                        <img src="/images/artemisiae/carte-repartition.png" alt="Capture d'écran d'une carte de répartition départementale sur Artemisiae" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><i data-lucide=\'image\' style=\'width:40px;height:40px;color:#cbd5e1;margin:0 auto 10px\'></i><p class=\'text-slate-500 text-xs font-bold\'>Imprim\'écran : carte de répartition</p><p class=\'text-slate-400 text-xs mt-1\'>/images/artemisiae/carte-repartition.png</p></div>'; if (typeof lucide !== \'undefined\') lucide.createIcons();">
                    </div>
                    <p class="text-xs text-slate-400 mt-2 italic">Carte de répartition : maillage départemental issu directement des observations validées dans la base.</p>
                </div>
                <div class="space-y-4">
                    <h3 class="text-xl font-bold text-oreina-dark">Les cartes de répartition</h3>
                    <p class="text-sm text-slate-600">Les cartes affichées sur <em>Artemisiae</em> sont <strong>issues directement des observations validées dans la base</strong>. Pour chaque taxon, les mailles départementales reflètent en temps réel l'état des connaissances accumulées par le réseau d'observateurs. Articulées avec le projet IDENT, elles permettent d'identifier les <strong>zones de sympatrie</strong> où une espèce, par ailleurs inconfondable, devient difficile à distinguer d'une espèce-sœur.</p>
                    <p class="text-sm text-slate-600">En parallèle, oreina <strong>alimente le programme ABDSM</strong> (Atlas de la Biodiversité Départementale et des Secteurs Marins) de PatriNat à partir de ces cartes — une contribution structurelle à l'inventaire national des Lépidoptères, dans le cadre de la convention OFB.</p>
                    <p class="text-xs text-slate-500"><strong>Contribution ABDSM 2024 :</strong> 839 cartes versées au programme, dont 115 mises à jour en 2024 (Rhopalocères et Zygènes principalement).</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Application mobile (PWA) --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-5 gap-10 items-start">
                <div class="lg:col-span-2 order-2 lg:order-1">
                    <div class="card p-0 overflow-hidden bg-gradient-to-br from-oreina-blue/10 to-oreina-coral/10 aspect-[9/16] flex items-center justify-center max-w-xs mx-auto">
                        <img src="/images/artemisiae/app-mobile.png" alt="Capture d'écran de l'application mobile Artemisiae (PWA)" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><i data-lucide=\'smartphone\' style=\'width:48px;height:48px;color:#cbd5e1;margin:0 auto 12px\'></i><p class=\'text-slate-500 text-sm font-bold\'>Imprim\'écran : application mobile</p><p class=\'text-slate-400 text-xs mt-1\'>/images/artemisiae/app-mobile.png</p></div>'; if (typeof lucide !== \'undefined\') lucide.createIcons();">
                    </div>
                    <p class="text-xs text-slate-400 mt-3 italic text-center">Saisie sur le terrain, en mode hors-ligne.</p>
                </div>
                <div class="lg:col-span-3 order-1 lg:order-2">
                    <div class="eyebrow coral mb-4 inline-flex">
                        <i class="icon icon-coral" data-lucide="smartphone"></i>
                        Application mobile
                    </div>
                    <h2 class="text-3xl font-bold text-oreina-dark mb-4">Saisir sur le terrain, même hors connexion</h2>

                    <div class="bg-oreina-blue/5 border-l-4 border-oreina-blue p-4 rounded-r-lg mb-5">
                        <p class="text-sm text-slate-700"><strong class="text-oreina-dark">Une PWA, pas une app store.</strong> L'application mobile d'Artemisiae est une <em>Progressive Web App</em> : elle s'installe directement depuis votre navigateur mobile, sans passer par les stores. Connectez-vous à la version mobile du portail, puis utilisez l'icône d'installation pour l'ajouter à votre écran d'accueil.</p>
                    </div>

                    <p class="text-slate-600 leading-relaxed mb-4">
                        L'application permet de saisir vos relevés directement depuis le terrain, avec géolocalisation GPS automatique. Les observations sont enregistrées localement et synchronisées avec le portail dès que le réseau est disponible.
                    </p>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Les comptes sont les mêmes que sur le portail : tableau de bord, relevés, sites, points et préférences sont partagés entre les deux interfaces.
                    </p>

                    <div class="grid sm:grid-cols-2 gap-3 mb-6">
                        <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg">
                            <i data-lucide="map-pin" style="width:20px;height:20px;color:var(--coral);flex-shrink:0;margin-top:2px"></i>
                            <div>
                                <p class="font-bold text-oreina-dark text-sm">Géolocalisation GPS</p>
                                <p class="text-xs text-slate-600">Positionnement automatique, ajustable par toucher sur la carte.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg">
                            <i data-lucide="wifi-off" style="width:20px;height:20px;color:var(--blue);flex-shrink:0;margin-top:2px"></i>
                            <div>
                                <p class="font-bold text-oreina-dark text-sm">Mode hors-ligne</p>
                                <p class="text-xs text-slate-600">Saisie complète sans réseau, synchronisation différée.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg">
                            <i data-lucide="map" style="width:20px;height:20px;color:var(--sage);flex-shrink:0;margin-top:2px"></i>
                            <div>
                                <p class="font-bold text-oreina-dark text-sm">Fonds de carte hors-ligne</p>
                                <p class="text-xs text-slate-600">Format pmtiles, à télécharger ou à créer (QGIS, MOBAC).</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg">
                            <i data-lucide="list" style="width:20px;height:20px;color:#8b6c05;flex-shrink:0;margin-top:2px"></i>
                            <div>
                                <p class="font-bold text-oreina-dark text-sm">Listes d'espèces</p>
                                <p class="text-xs text-slate-600">Téléchargement par observatoire, saisie hors-ligne accélérée.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg">
                            <i data-lucide="camera" style="width:20px;height:20px;color:var(--coral);flex-shrink:0;margin-top:2px"></i>
                            <div>
                                <p class="font-bold text-oreina-dark text-sm">Photo intégrée</p>
                                <p class="text-xs text-slate-600">Une photo associable à chaque observation.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg">
                            <i data-lucide="refresh-cw" style="width:20px;height:20px;color:var(--blue);flex-shrink:0;margin-top:2px"></i>
                            <div>
                                <p class="font-bold text-oreina-dark text-sm">Synchronisation</p>
                                <p class="text-xs text-slate-600">Compteur de relevés à synchroniser, déclenchement manuel.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="https://oreina.org/artemisiae/" target="_blank" rel="noopener" class="btn btn-primary">
                            <i class="icon icon-sage" data-lucide="download"></i>
                            Installer depuis le mobile
                        </a>
                        <a href="https://oreina.org/artemisiae/index.php?module=info&action=faq" target="_blank" rel="noopener" class="btn btn-secondary">
                            <i class="icon icon-coral" data-lucide="book-open"></i>
                            Aide à l'installation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Portail bibliographique --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="library"></i>
                    Portail bibliographique
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Le Portail bibliographique des papillons de France</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Index et fonds documentaire de référence : un outil à part entière, intégré au portail <em>Artemisiae</em>, qui rassemble la littérature lépidoptérologique française et donne accès, lorsque c'est possible, aux ressources en texte intégral.</p>
            </div>

            {{-- Imprim'écran pleine largeur --}}
            <div class="card p-0 overflow-hidden bg-gradient-to-br from-oreina-yellow/15 to-oreina-coral/10 aspect-[16/9] flex items-center justify-center mb-10">
                <img src="/images/artemisiae/biblio.png" alt="Capture d'écran du Portail bibliographique des papillons de France sur Artemisiae" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><i data-lucide=\'image\' style=\'width:48px;height:48px;color:#cbd5e1;margin:0 auto 12px\'></i><p class=\'text-slate-500 text-sm font-bold\'>Imprim\'écran : Portail bibliographique</p><p class=\'text-slate-400 text-xs mt-1\'>/images/artemisiae/biblio.png</p></div>'; if (typeof lucide !== \'undefined\') lucide.createIcons();">
            </div>

            {{-- Chiffres-clés --}}
            <div class="grid grid-cols-3 gap-4 mb-10">
                <div class="card p-6 text-center bg-white">
                    <p class="text-4xl sm:text-5xl font-bold text-oreina-coral mb-2">23&nbsp;715</p>
                    <p class="text-sm text-slate-600 leading-tight">références bibliographiques</p>
                </div>
                <div class="card p-6 text-center bg-white">
                    <p class="text-4xl sm:text-5xl font-bold text-oreina-green mb-2">3&nbsp;705</p>
                    <p class="text-sm text-slate-600 leading-tight">taxons couverts par les références</p>
                </div>
                <div class="card p-6 text-center bg-white">
                    <p class="text-4xl sm:text-5xl font-bold text-oreina-blue mb-2">6&nbsp;917</p>
                    <p class="text-sm text-slate-600 leading-tight">auteurs référencés</p>
                </div>
            </div>
            <p class="text-center text-xs text-slate-400 mb-10">Chiffres au début 2026.</p>

            {{-- Descriptif officiel + caractéristiques --}}
            <div class="grid lg:grid-cols-5 gap-10 items-start">
                <div class="lg:col-span-3">
                    <div class="bg-white p-6 rounded-2xl border-l-4 border-oreina-yellow mb-6">
                        <p class="text-slate-600 leading-relaxed italic">
                            «&nbsp;Le Portail bibliographique des papillons de France vous permet d'accéder à de nombreuses notices bibliographiques traitant des lépidoptères en France. L'association oreina a pour objectif de constituer un index bibliographique mais également un fond documentaire de référence sur les Lépidoptères de France métropolitaine et de Corse. Articles scientifiques, rapports d'études, ouvrages... Vous trouverez ainsi de nombreuses références en lien direct avec la thématique du portail.&nbsp;»
                        </p>
                    </div>

                    <p class="text-slate-600 leading-relaxed mb-4">
                        Au-delà du simple index, le portail bibliographique vise à constituer un véritable <strong>fonds documentaire</strong> : plusieurs centaines de références donnent accès au texte intégral, soit via un PDF directement hébergé sur Artemisiae, soit via un lien vers la ressource en accès libre chez l'éditeur. Cet effort est continu : chaque ajout de référence est l'occasion de chercher, quand cela est possible, à donner accès au document.
                    </p>
                    <p class="text-slate-600 leading-relaxed">
                        Les articles publiés dans <em>Chersotis</em>, la revue scientifique d'oreina, y sont automatiquement référencés avec leur DOI dès leur publication. Les nouvelles parutions signalées par la communauté sont ajoutées en continu et reliées aux taxons traités.
                    </p>
                </div>

                <div class="lg:col-span-2 space-y-3">
                    <div class="card p-4 flex items-start gap-3 bg-white">
                        <i data-lucide="file-text" style="width:24px;height:24px;color:#8b6c05;flex-shrink:0;margin-top:2px"></i>
                        <div>
                            <p class="font-bold text-oreina-dark text-sm mb-1">Articles, rapports, ouvrages</p>
                            <p class="text-xs text-slate-600">Articles scientifiques, rapports d'études, monographies, faunes régionales, thèses.</p>
                        </div>
                    </div>
                    <div class="card p-4 flex items-start gap-3 bg-white">
                        <i data-lucide="file-down" style="width:24px;height:24px;color:var(--coral);flex-shrink:0;margin-top:2px"></i>
                        <div>
                            <p class="font-bold text-oreina-dark text-sm mb-1">PDF et liens externes</p>
                            <p class="text-xs text-slate-600">Plusieurs centaines de références donnent accès au texte intégral.</p>
                        </div>
                    </div>
                    <div class="card p-4 flex items-start gap-3 bg-white">
                        <i data-lucide="search" style="width:24px;height:24px;color:var(--blue);flex-shrink:0;margin-top:2px"></i>
                        <div>
                            <p class="font-bold text-oreina-dark text-sm mb-1">Recherche multicritère</p>
                            <p class="text-xs text-slate-600">Par auteur, taxon, année, journal, mots-clés.</p>
                        </div>
                    </div>
                    <div class="card p-4 flex items-start gap-3 bg-white">
                        <i data-lucide="link" style="width:24px;height:24px;color:var(--sage);flex-shrink:0;margin-top:2px"></i>
                        <div>
                            <p class="font-bold text-oreina-dark text-sm mb-1">Liens taxon ↔ référence</p>
                            <p class="text-xs text-slate-600">Depuis la fiche d'une espèce, accès à toutes les publications qui en parlent.</p>
                        </div>
                    </div>
                    <div class="card p-4 flex items-start gap-3 bg-white">
                        <i data-lucide="file-plus" style="width:24px;height:24px;color:var(--coral);flex-shrink:0;margin-top:2px"></i>
                        <div>
                            <p class="font-bold text-oreina-dark text-sm mb-1">Contributions ouvertes</p>
                            <p class="text-xs text-slate-600">Signaler une référence manquante via un formulaire dédié.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CTA portail biblio --}}
            <div class="mt-10 flex flex-wrap gap-3">
                <a href="https://oreina.org/artemisiae/index.php?module=biblio&action=accueil" target="_blank" rel="noopener" class="btn btn-primary">
                    <i class="icon icon-sage" data-lucide="external-link"></i>
                    Ouvrir le Portail bibliographique
                </a>
                <a href="{{ route('hub.contact') }}" class="btn btn-secondary">
                    <i class="icon icon-coral" data-lucide="file-plus"></i>
                    Signaler une référence
                </a>
            </div>
        </div>
    </section>

    {{-- Systema --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-5 gap-10 items-start">
                <div class="lg:col-span-2 order-2 lg:order-1">
                    <div class="card p-0 overflow-hidden bg-gradient-to-br from-oreina-green/10 to-oreina-blue/10 aspect-[4/5] flex items-center justify-center">
                        <img src="/images/artemisiae/systema.png" alt="Capture d'écran du module Systema sur Artemisiae" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><i data-lucide=\'image\' style=\'width:40px;height:40px;color:#cbd5e1;margin:0 auto 10px\'></i><p class=\'text-slate-500 text-xs font-bold\'>Imprim\'écran : module Systema</p><p class=\'text-slate-400 text-xs mt-1\'>/images/artemisiae/systema.png</p></div>'; if (typeof lucide !== \'undefined\') lucide.createIcons();">
                    </div>
                </div>
                <div class="lg:col-span-3 order-1 lg:order-2">
                    <div class="eyebrow sage mb-4 inline-flex">
                        <i class="icon icon-sage" data-lucide="layers"></i>
                        Module Systema
                    </div>
                    <h2 class="text-3xl font-bold text-oreina-dark mb-4">Le suivi du référentiel national, en continu</h2>
                    <p class="text-slate-600 leading-relaxed mb-4">
                        <strong>Systema</strong> est le module de gestion taxonomique d'<em>Artemisiae</em>, connecté en temps réel à TAXREF-Web (PatriNat). Il permet à oreina de gérer le référentiel des Lépidoptères de France <strong>en continu</strong>, sans dépendre du rythme des versions stabilisées annuelles de TAXREF.
                    </p>
                    <p class="text-slate-600 leading-relaxed mb-4">
                        Concrètement, dès qu'une décision taxonomique est validée par les experts d'oreina (description d'une nouvelle espèce, restauration d'un taxon synonymisé, mise en synonymie, réordination), elle est répercutée immédiatement dans Systema et donc visible par tous les utilisateurs du portail.
                    </p>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Le module donne accès à la liste systématique générale des Lépidoptères de France, à la traçabilité des actes nomenclaturaux et à l'historique des modifications. Il est librement consultable, sans inscription.
                    </p>

                    <div class="flex flex-wrap gap-3">
                        <a href="https://oreina.org/artemisiae/index.php?module=systema&action=liste" target="_blank" rel="noopener" class="btn btn-primary">
                            <i class="icon icon-sage" data-lucide="external-link"></i>
                            Consulter Systema
                        </a>
                        <a href="{{ route('hub.projets.taxref') }}" class="btn btn-secondary">
                            <i class="icon icon-coral" data-lucide="layers"></i>
                            Le projet TAXREF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Barcoding intégré --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-5 gap-10 items-start">
                <div class="lg:col-span-3">
                    <div class="eyebrow coral mb-4 inline-flex">
                        <i class="icon icon-coral" data-lucide="dna"></i>
                        Module Barcode
                    </div>
                    <h2 class="text-3xl font-bold text-oreina-dark mb-4">Le projet barcoding intégré au portail</h2>
                    <p class="text-slate-600 leading-relaxed mb-4">
                        Le projet <strong>SEQREF</strong> d'oreina vise à constituer une bibliothèque de séquences moléculaires de référence pour les Lépidoptères de France, en priorité pour les agrégats critiques où l'identification morphologique atteint ses limites. Les séquences validées sont directement intégrées au portail et accessibles depuis les fiches taxons.
                    </p>
                    <p class="text-slate-600 leading-relaxed mb-4">
                        Le module <strong>Barcode</strong> d'<em>Artemisiae</em> permet de consulter les séquences disponibles par espèce, leur région de référence, leur niveau de validation et leur correspondance avec les bases internationales (BOLD, GenBank). Pour les espèces des complexes documentés par IDENT, le barcoding constitue le niveau d'investigation T5 — le recours moléculaire après les critères externes et l'examen des armures génitales.
                    </p>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        L'intégration du barcoding au portail évite la dispersion des données : un naturaliste qui consulte la fiche d'une espèce d'un agrégat critique trouve dans la même interface la difficulté typologique, les guides d'identification, et l'état des références moléculaires.
                    </p>

                    <div class="flex flex-wrap gap-3">
                        <a href="https://oreina.org/artemisiae/index.php?module=barcode&action=barcode" target="_blank" rel="noopener" class="btn btn-primary">
                            <i class="icon icon-sage" data-lucide="external-link"></i>
                            Consulter le module Barcode
                        </a>
                        <a href="{{ route('hub.projets.seqref') }}" class="btn btn-secondary">
                            <i class="icon icon-coral" data-lucide="dna"></i>
                            Le projet SEQREF
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="card p-0 overflow-hidden bg-gradient-to-br from-oreina-coral/10 to-oreina-blue/10 aspect-[4/5] flex items-center justify-center">
                        <img src="/images/artemisiae/barcode.png" alt="Capture d'écran du module Barcode sur Artemisiae" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><i data-lucide=\'image\' style=\'width:40px;height:40px;color:#cbd5e1;margin:0 auto 10px\'></i><p class=\'text-slate-500 text-xs font-bold\'>Imprim\'écran : module Barcode</p><p class=\'text-slate-400 text-xs mt-1\'>/images/artemisiae/barcode.png</p></div>'; if (typeof lucide !== \'undefined\') lucide.createIcons();">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Les validateurs --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="shield-check"></i>
                    Les validateurs
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Trois modes de validation, une seule exigence : la traçabilité</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Toute donnée saisie sur Artemisiae passe par un processus de qualification. Selon l'espèce et le contexte, la validation est effectuée à la saisie, par algorithme, ou manuellement par un expert. Ce processus est le pendant opérationnel du projet QUALIF.</p>
            </div>

            <div class="grid sm:grid-cols-1 lg:grid-cols-3 gap-4 mb-10">
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-oreina-green/15 text-oreina-green font-bold text-lg">1</span>
                        <i data-lucide="zap" style="width:22px;height:22px;color:var(--sage);opacity:0.65"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Validée à la saisie</h3>
                    <p class="text-sm text-slate-600">Pour les espèces très courantes et dont l'identification est aisée. La donnée est qualifiée immédiatement, avec possibilité de contrôle a posteriori par un validateur.</p>
                </div>
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-oreina-blue/15 text-oreina-blue font-bold text-lg">2</span>
                        <i data-lucide="cpu" style="width:22px;height:22px;color:var(--blue);opacity:0.65"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Validation automatique</h3>
                    <p class="text-sm text-slate-600">Un algorithme croise localisation, période de vol, amplitude altitudinale et expérience de l'observateur (espèces déjà saisies). La finesse augmente avec la masse de données collectées.</p>
                </div>
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-oreina-coral/15 text-oreina-coral font-bold text-lg">3</span>
                        <i data-lucide="user-check" style="width:22px;height:22px;color:var(--coral);opacity:0.65"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Validation manuelle</h3>
                    <p class="text-sm text-slate-600">Pour les espèces définies a priori comme nécessitant une expertise. Un validateur expert peut interroger l'observateur, demander des précisions, et qualifier la donnée. Le nom des validateurs est indiqué pour chaque observatoire.</p>
                </div>
            </div>

            <div class="card p-6 bg-slate-50 border-l-4 border-oreina-blue">
                <h3 class="font-bold text-oreina-dark mb-3 flex items-center gap-2">
                    <i data-lucide="info" style="width:20px;height:20px;color:var(--blue)"></i>
                    Le principe de l'échange validateur ↔ observateur
                </h3>
                <p class="text-sm text-slate-600 mb-3">
                    Le gestionnaire ne peut pas modifier une donnée sans l'accord de l'observateur. En cas d'ambiguïté, le validateur sollicite l'observateur par e-mail pour demander des précisions. C'est seulement en l'absence de réponse, ou en cas de désaccord persistant après échange, que le validateur peut affecter le niveau de validation de son choix.
                </p>
                <p class="text-sm text-slate-600">
                    Cette règle protège la <strong>propriété intellectuelle inaliénable de l'observateur sur sa donnée</strong>, tout en garantissant la qualité du jeu de données global.
                </p>
            </div>

            <div class="mt-6 grid sm:grid-cols-2 gap-4">
                <a href="{{ route('hub.projets.qualif') }}" class="card p-5 hover:shadow-lg transition group flex items-center gap-4">
                    <div class="pub-card-icon sage flex-shrink-0">
                        <i class="icon icon-sage" data-lucide="badge-check"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-green transition">Le projet QUALIF</h3>
                        <p class="text-xs text-slate-500">Le cadre méthodologique de la qualification des données.</p>
                    </div>
                </a>
                <a href="{{ route('hub.contact') }}" class="card p-5 hover:shadow-lg transition group flex items-center gap-4">
                    <div class="pub-card-icon coral flex-shrink-0">
                        <i class="icon icon-coral" data-lucide="user-plus"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-coral transition">Devenir validateur</h3>
                        <p class="text-xs text-slate-500">Spécialiste d'un groupe ? Rejoignez le réseau de validation.</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- Vos données, vos droits --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="shield"></i>
                    Vos données, vos droits
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Une éthique de la donnée naturaliste</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Confier ses observations à <em>Artemisiae</em>, c'est rejoindre un dispositif qui garantit le respect de la propriété intellectuelle, la maîtrise du niveau de diffusion, et la conformité au RGPD comme au standard SINP.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="card p-6 bg-white">
                    <div class="pub-card-icon sage mb-3">
                        <i class="icon icon-sage" data-lucide="eye"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Statut privé ou public</h3>
                    <p class="text-xs text-slate-600">Vous choisissez, observation par observation, le statut de vos données. Le statut privé permet de dégrader la précision visualisée et de limiter l'extraction par des tiers.</p>
                </div>
                <div class="card p-6 bg-white">
                    <div class="pub-card-icon coral mb-3">
                        <i class="icon icon-coral" data-lucide="zoom-out"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Échelle de visualisation</h3>
                    <p class="text-xs text-slate-600">Quatre niveaux disponibles : point précis, commune, maille 10×10, département. Vos données sensibles sont automatiquement dégradées selon les guides nationaux.</p>
                </div>
                <div class="card p-6 bg-white">
                    <div class="pub-card-icon blue mb-3">
                        <i class="icon icon-blue" data-lucide="award"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Propriété intellectuelle inaliénable</h3>
                    <p class="text-xs text-slate-600">La donnée reste votre propriété. Toute citation utilise la forme : <em>Artemisiae/oreina — nom de l'observateur — date</em>.</p>
                </div>
                <div class="card p-6 bg-white">
                    <div class="pub-card-icon gold mb-3">
                        <i class="icon icon-gold" data-lucide="trash-2"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Droit de retrait</h3>
                    <p class="text-xs text-slate-600">À tout moment, vous pouvez supprimer une donnée ou l'ensemble de votre compte. La suppression est immédiate et définitive.</p>
                </div>
                <div class="card p-6 bg-white">
                    <div class="pub-card-icon sage mb-3">
                        <i class="icon icon-sage" data-lucide="building-2"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Rattachement à un organisme</h3>
                    <p class="text-xs text-slate-600">Vos données peuvent être liées à un organisme (employeur, partenaire, étude conventionnée), avec un cadre juridique adapté pour les données acquises sur fonds publics.</p>
                </div>
                <div class="card p-6 bg-white">
                    <div class="pub-card-icon coral mb-3">
                        <i class="icon icon-coral" data-lucide="server"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Hébergement en France</h3>
                    <p class="text-xs text-slate-600">Données stockées sur les serveurs o2switch (Clermont-Ferrand). Conformité RGPD, code source sous licence libre AGPL.</p>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="https://oreina.org/artemisiae/index.php?module=info&action=cgu" target="_blank" rel="noopener" class="text-sm text-oreina-coral font-bold hover:underline inline-flex items-center gap-1">
                    Lire les conditions générales d'utilisation
                    <i data-lucide="external-link" style="width:14px;height:14px"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Tutoriels & FAQ --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="help-circle"></i>
                    Tutoriels &amp; FAQ
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Apprendre à utiliser le portail</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">La FAQ d'<em>Artemisiae</em> rassemble les guides pratiques et les réponses aux questions les plus fréquentes : création de compte, premiers pas en saisie, gestion des préférences, utilisation de l'application mobile, validation des données.</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-4">
                <a href="https://oreina.org/artemisiae/index.php?module=info&action=faq" target="_blank" rel="noopener" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon blue mb-4">
                        <i class="icon icon-blue" data-lucide="book-marked"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-blue transition">FAQ complète</h3>
                    <p class="text-sm text-slate-600 mb-3">Toutes les réponses pratiques sur l'utilisation du portail web et de l'application.</p>
                    <span class="text-xs text-oreina-blue font-bold inline-flex items-center gap-1">Ouvrir la FAQ <i data-lucide="external-link" style="width:12px;height:12px"></i></span>
                </a>
                <a href="https://oreina.org/artemisiae/" target="_blank" rel="noopener" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="user-plus"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-coral transition">Créer un compte</h3>
                    <p class="text-sm text-slate-600 mb-3">Inscription gratuite et immédiate, accès à la saisie et au tableau de bord personnel.</p>
                    <span class="text-xs text-oreina-coral font-bold inline-flex items-center gap-1">Aller sur Artemisiae <i data-lucide="external-link" style="width:12px;height:12px"></i></span>
                </a>
                <a href="{{ route('hub.contact') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="life-buoy"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-green transition">Une question particulière&nbsp;?</h3>
                    <p class="text-sm text-slate-600 mb-3">Si la FAQ ne couvre pas votre cas, l'équipe oreina vous répond directement.</p>
                    <span class="text-xs text-oreina-green font-bold inline-flex items-center gap-1">Nous contacter <i data-lucide="arrow-right" style="width:12px;height:12px"></i></span>
                </a>
            </div>
        </div>
    </section>

    {{-- CTA bandeau --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="globe-2"></i>Rejoindre l'observatoire</div>
                <h2>Saisir, c'est faire vivre la connaissance</h2>
                <p>Chaque observation saisie sur Artemisiae enrichit la base de connaissance des Lépidoptères de France et alimente, via le SINP, les politiques publiques de préservation. La saisie est gratuite, l'inscription immédiate, et vous gardez à tout moment la main sur la diffusion de vos données.</p>
                <div class="content-actions">
                    <a href="https://oreina.org/artemisiae/" target="_blank" rel="noopener" class="btn btn-primary">
                        <i class="icon icon-sage" data-lucide="external-link"></i>
                        Ouvrir Artemisiae
                    </a>
                    <a href="{{ route('hub.membership') }}" class="btn btn-ghost-light">
                        <i class="icon icon-white" data-lucide="heart-plus"></i>
                        Adhérer à oreina
                    </a>
                </div>
            </article>
        </div>
    </section>

    {{-- Articulation avec les projets --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-oreina-dark">Artemisiae et les projets scientifiques d'oreina</h2>
                <p class="text-slate-500 mt-3 max-w-3xl mx-auto">Le portail est l'interface utilisateur des cinq projets de la convention OFB 2026, 2028. Chaque module en est une déclinaison concrète, accessible aux observateurs.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <a href="{{ route('hub.projets.taxref') }}" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-3">
                        <i class="icon icon-sage" data-lucide="layers"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 text-sm group-hover:text-oreina-green transition">TAXREF</h3>
                    <p class="text-xs text-slate-500">→ Module Systema</p>
                </a>
                <a href="{{ route('hub.projets.seqref') }}" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon coral mb-3">
                        <i class="icon icon-coral" data-lucide="dna"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 text-sm group-hover:text-oreina-coral transition">SEQREF</h3>
                    <p class="text-xs text-slate-500">→ Module Barcode</p>
                </a>
                <a href="{{ route('hub.projets.bdc') }}" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon gold mb-3">
                        <i class="icon icon-gold" data-lucide="list-checks"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 text-sm group-hover:text-oreina-coral transition">BDC</h3>
                    <p class="text-xs text-slate-500">→ Traits de vie sur fiche taxon</p>
                </a>
                <a href="{{ route('hub.projets.ident') }}" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon blue mb-3">
                        <i class="icon icon-blue" data-lucide="search"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 text-sm group-hover:text-oreina-blue transition">IDENT</h3>
                    <p class="text-xs text-slate-500">→ Niveaux T1-T5, Labo Lépidos</p>
                </a>
                <a href="{{ route('hub.projets.qualif') }}" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-3">
                        <i class="icon icon-sage" data-lucide="badge-check"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 text-sm group-hover:text-oreina-green transition">QUALIF</h3>
                    <p class="text-xs text-slate-500">→ Validateurs &amp; modes de validation</p>
                </a>
            </div>
        </div>
    </section>
@endsection
