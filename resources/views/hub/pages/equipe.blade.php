@extends('layouts.hub')

@section('title', 'L\'équipe')
@section('meta_description', 'Bureau, conseil d\'administration, salariée et bénévoles : l\'équipe d\'oreina, association loi 1901 dédiée à l\'étude et à la protection des Lépidoptères de France.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-16 bg-warm">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="eyebrow sage mb-6">
                <i class="icon icon-sage" data-lucide="users-round"></i>
                Gouvernance & équipe
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">L'équipe d'oreina</h1>
            <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl mx-auto">
                Une gouvernance bénévole, une coordination salariée, un réseau d'adhérents engagés
            </p>
        </div>
    </section>

    {{-- Intro --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="prose prose-lg text-slate-600 mx-auto">
                <p>
                    oreina fonctionne comme une association loi 1901, animée par ses adhérents et administrée par un conseil d'administration élu en assemblée générale. Depuis 2024, une coordinatrice salariée vient appuyer la dynamique scientifique et l'animation du réseau, sans se substituer à l'engagement bénévole qui reste la colonne vertébrale de l'association.
                </p>
                <p>
                    L'équipe d'oreina, ce sont trois cercles complémentaires : un bureau et un conseil d'administration qui assurent la gouvernance et la stratégie, une salariée qui coordonne le réseau scientifique, et plusieurs centaines d'adhérents qui font vivre les projets sur le terrain — en validation, en collecte, en rédaction et en formation.
                </p>
            </div>
        </div>
    </section>

    {{-- Bureau --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="eyebrow sage mb-4 inline-flex">
                    <i class="icon icon-sage" data-lucide="landmark"></i>
                    Bureau
                </div>
                <h2 class="text-2xl font-bold text-oreina-dark">Le bureau</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">Élu par le conseil d'administration, le bureau assure la gestion des affaires ordinaires et la représentation de l'association.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Président --}}
                <div class="card p-6 text-center">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden bg-gradient-to-br from-oreina-green/20 to-oreina-turquoise/20 flex items-center justify-center">
                        <img src="/images/team/david-demerges.jpg" alt="David Demergès" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'text-3xl font-bold text-oreina-green/60\'>DD</span>'">
                    </div>
                    <h3 class="font-bold text-oreina-dark text-lg">David Demergès</h3>
                    <p class="text-oreina-green text-sm font-bold mb-3">Président</p>
                    <p class="text-slate-600 text-sm">Lépidoptériste, président d'oreina depuis 2024. Représente l'association vis-à-vis des partenaires institutionnels et coordonne la stratégie scientifique.</p>
                </div>

                {{-- Président adjoint --}}
                <div class="card p-6 text-center">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden bg-gradient-to-br from-oreina-green/20 to-oreina-turquoise/20 flex items-center justify-center">
                        <img src="/images/team/francois-mathieu.jpg" alt="François Mathieu" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'text-3xl font-bold text-oreina-green/60\'>FM</span>'">
                    </div>
                    <h3 class="font-bold text-oreina-dark text-lg">François Mathieu</h3>
                    <p class="text-oreina-green text-sm font-bold mb-3">Président adjoint</p>
                    <p class="text-slate-600 text-sm">Membre du conseil d'administration. Pilote l'évolution éditoriale du bulletin <em>Lepis</em> et participe aux groupes de travail thématiques.</p>
                </div>

                {{-- Trésorier --}}
                <div class="card p-6 text-center">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden bg-gradient-to-br from-oreina-turquoise/20 to-oreina-blue/20 flex items-center justify-center">
                        <img src="/images/team/pascal-dupont.jpg" alt="Pascal Dupont" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'text-3xl font-bold text-oreina-turquoise/70\'>PD</span>'">
                    </div>
                    <h3 class="font-bold text-oreina-dark text-lg">Pascal Dupont</h3>
                    <p class="text-oreina-turquoise text-sm font-bold mb-3">Trésorier</p>
                    <p class="text-slate-600 text-sm">Lépidoptériste expérimenté. Suivi de la trésorerie et des conventions financières (MNHN, OFB). Référent scientifique pour le dispositif EU-PoMS.</p>
                </div>

                {{-- Trésorière adjointe --}}
                <div class="card p-6 text-center">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden bg-gradient-to-br from-oreina-turquoise/20 to-oreina-blue/20 flex items-center justify-center">
                        <img src="/images/team/huguette-robineau.jpg" alt="Huguette Robineau" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'text-3xl font-bold text-oreina-turquoise/70\'>HR</span>'">
                    </div>
                    <h3 class="font-bold text-oreina-dark text-lg">Huguette Robineau</h3>
                    <p class="text-oreina-turquoise text-sm font-bold mb-3">Trésorière adjointe</p>
                    <p class="text-slate-600 text-sm">Cofondatrice de l'association. Appui à la gestion financière et continuité historique d'oreina.</p>
                </div>

                {{-- Secrétaire --}}
                <div class="card p-6 text-center">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden bg-gradient-to-br from-oreina-yellow/20 to-oreina-coral/20 flex items-center justify-center">
                        <img src="/images/team/nicolas-lemaire.jpg" alt="Nicolas Lemaire" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'text-3xl font-bold text-oreina-coral/70\'>NL</span>'">
                    </div>
                    <h3 class="font-bold text-oreina-dark text-lg">Nicolas Lemaire</h3>
                    <p class="text-oreina-coral text-sm font-bold mb-3">Secrétaire</p>
                    <p class="text-slate-600 text-sm">Secrétariat de l'association, gestion des mailings et suivi administratif.</p>
                </div>

                {{-- Secrétaire adjoint --}}
                <div class="card p-6 text-center">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden bg-gradient-to-br from-oreina-yellow/20 to-oreina-coral/20 flex items-center justify-center">
                        <img src="/images/team/philippe-hey.jpg" alt="Philippe Hey" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'text-3xl font-bold text-oreina-coral/70\'>PH</span>'">
                    </div>
                    <h3 class="font-bold text-oreina-dark text-lg">Philippe Hey</h3>
                    <p class="text-oreina-coral text-sm font-bold mb-3">Secrétaire adjoint</p>
                    <p class="text-slate-600 text-sm">Appui au secrétariat et au suivi administratif.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Conseil d'administration --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="users"></i>
                    Conseil d'administration
                </div>
                <h2 class="text-2xl font-bold text-oreina-dark">Le conseil d'administration</h2>
                <p class="text-slate-500 mt-3 max-w-3xl mx-auto">17 administrateurs élus pour 3 ans renouvelables, qui définissent les orientations de l'association, valident les conventions et arbitrent ses engagements. Le conseil se réunit mensuellement.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @php
                    $administrateurs = [
                        ['nom' => 'Sylvain Delmas', 'slug' => 'sylvain-delmas', 'initiales' => 'SD', 'fonction' => 'Administrateur'],
                        ['nom' => 'David Demergès', 'slug' => 'david-demerges', 'initiales' => 'DD', 'fonction' => 'Président'],
                        ['nom' => 'Éric Drouet', 'slug' => 'eric-drouet', 'initiales' => 'ÉD', 'fonction' => 'Administrateur'],
                        ['nom' => 'Pascal Dupont', 'slug' => 'pascal-dupont', 'initiales' => 'PD', 'fonction' => 'Trésorier'],
                        ['nom' => 'Pierre-Yves Gourvil', 'slug' => 'pierre-yves-gourvil', 'initiales' => 'PG', 'fonction' => 'Administrateur'],
                        ['nom' => 'Stéphane Grenier', 'slug' => 'stephane-grenier', 'initiales' => 'SG', 'fonction' => 'Administrateur'],
                        ['nom' => 'Philippe Hey', 'slug' => 'philippe-hey', 'initiales' => 'PH', 'fonction' => 'Secrétaire adjoint'],
                        ['nom' => 'Jean-Marc Iurettigh', 'slug' => 'jean-marc-iurettigh', 'initiales' => 'JI', 'fonction' => 'Administrateur'],
                        ['nom' => 'Adrien Jailloux', 'slug' => 'adrien-jailloux', 'initiales' => 'AJ', 'fonction' => 'Administrateur'],
                        ['nom' => 'Nicolas Lemaire', 'slug' => 'nicolas-lemaire', 'initiales' => 'NL', 'fonction' => 'Secrétaire'],
                        ['nom' => 'François Mathieu', 'slug' => 'francois-mathieu', 'initiales' => 'FM', 'fonction' => 'Président adjoint'],
                        ['nom' => 'Nathalie Merlet', 'slug' => 'nathalie-merlet', 'initiales' => 'NM', 'fonction' => 'Administratrice'],
                        ['nom' => 'Alain Migeon', 'slug' => 'alain-migeon', 'initiales' => 'AM', 'fonction' => 'Administrateur'],
                        ['nom' => 'Marc Nicolle', 'slug' => 'marc-nicolle', 'initiales' => 'MN', 'fonction' => 'Administrateur'],
                        ['nom' => 'Huguette Robineau', 'slug' => 'huguette-robineau', 'initiales' => 'HR', 'fonction' => 'Trésorière adjointe'],
                        ['nom' => 'Roland Robineau', 'slug' => 'roland-robineau', 'initiales' => 'RR', 'fonction' => 'Administrateur'],
                        ['nom' => 'Denis Vandromme', 'slug' => 'denis-vandromme', 'initiales' => 'DV', 'fonction' => 'Administrateur'],
                    ];
                @endphp

                @foreach($administrateurs as $admin)
                    <div class="card p-4 text-center">
                        <div class="w-20 h-20 mx-auto mb-3 rounded-full overflow-hidden bg-gradient-to-br from-oreina-green/15 to-oreina-turquoise/15 flex items-center justify-center">
                            <img src="/images/team/{{ $admin['slug'] }}.jpg" alt="{{ $admin['nom'] }}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'text-lg font-bold text-oreina-green/60\'>{{ $admin['initiales'] }}</span>'">
                        </div>
                        <h3 class="font-bold text-oreina-dark text-sm leading-tight">{{ $admin['nom'] }}</h3>
                        <p class="text-slate-500 text-xs mt-1">{{ $admin['fonction'] }}</p>
                    </div>
                @endforeach
            </div>

            <p class="text-center text-slate-400 text-xs mt-8">Composition du conseil d'administration issue de l'assemblée générale du 19 mai 2024.</p>
        </div>
    </section>

    {{-- Salariée --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="briefcase"></i>
                    Animation du réseau scientifique
                </div>
                <h2 class="text-2xl font-bold text-oreina-dark">Notre salariée</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">Depuis 2024, oreina s'est dotée d'une première salariée pour coordonner les projets scientifiques et animer la communauté des bénévoles.</p>
            </div>

            <div class="card p-8">
                <div class="grid md:grid-cols-3 gap-8 items-center">
                    <div class="md:col-span-1 flex justify-center">
                        <div class="w-48 h-48 rounded-full overflow-hidden bg-gradient-to-br from-oreina-yellow/20 to-oreina-coral/20 flex items-center justify-center">
                            <img src="/images/team/pieternel-verschuren.jpg" alt="Pieternel Verschuren" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'text-5xl font-bold text-oreina-coral/60\'>PV</span>'">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <h3 class="font-bold text-oreina-dark text-2xl">Pieternel Verschuren</h3>
                        <p class="text-oreina-coral font-bold mb-4">Coordinatrice du réseau scientifique</p>
                        <div class="prose text-slate-600">
                            <p>
                                Recrutée en 2024 comme première salariée d'oreina, Pieternel anime les groupes de travail dédiés aux projets scientifiques (TAXREF, SEQREF, BDC, IDENT, QUALIF), assure l'interface avec les partenaires institutionnels et accompagne les contributeurs bénévoles. Basée à Strasbourg, elle travaille en télétravail.
                            </p>
                            <p class="text-sm text-slate-500">
                                Son poste, à 0,8 ETP, est cofinancé dans le cadre de la convention pluriannuelle 2026–2028 avec l'OFB. Le conseil d'administration a affiché, dès le recrutement, sa volonté de pérenniser ce premier emploi salarié.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 p-6 bg-oreina-green/5 border-l-4 border-oreina-green rounded-r-xl">
                <p class="text-sm text-slate-600">
                    <strong class="text-oreina-dark">Une salariée en appui des bénévoles.</strong> L'embauche d'une coordinatrice ne marque pas un basculement vers un fonctionnement professionnel : oreina reste fondamentalement une association bénévole. Les statuts précisent d'ailleurs que les membres rétribués par l'association ne peuvent être ni électeurs ni éligibles au conseil d'administration. La salariée travaille en appui des bénévoles, jamais en remplacement.
                </p>
            </div>
        </div>
    </section>

    {{-- Bénévoles & GT --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="heart"></i>
                    Le cœur d'oreina
                </div>
                <h2 class="text-2xl font-bold text-oreina-dark">Les bénévoles, force vive de l'association</h2>
                <p class="text-slate-500 mt-3 max-w-3xl mx-auto">
                    Plusieurs centaines d'adhérents font vivre les projets scientifiques d'oreina, en validation de données, en collecte sur le terrain, en rédaction, en formation et en animation territoriale. Les projets ne reposent pas sur un état-major, mais sur un réseau distribué de naturalistes — amateurs aguerris, professionnels, enseignants-chercheurs — qui contribuent chacun à hauteur de leur disponibilité et de leur expertise.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- GT Validateurs --}}
                <div class="card p-6">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="shield-check"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">GT Validateurs <em>Artemisiae</em></h3>
                    <p class="text-slate-600 text-sm">Cœur opérationnel de la qualification des données. Le réseau de validateurs bénévoles examine les observations saisies sur la plateforme <em>Artemisiae</em> selon le protocole de validation d'oreina, dans le cadre du projet QUALIF.</p>
                </div>

                {{-- GT Barcoding --}}
                <div class="card p-6">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="dna"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">GT Barcoding (SEQREF)</h3>
                    <p class="text-slate-600 text-sm">Réseau de référents régionaux qui collectent, identifient et préparent les spécimens transmis au MNHN pour séquençage ADN. Travail conjoint avec le programme PSYCHE (Wellcome Sanger Institute) et les associations régionales partenaires.</p>
                </div>

                {{-- GT Traits de vie --}}
                <div class="card p-6">
                    <div class="pub-card-icon gold mb-4">
                        <i class="icon icon-gold" data-lucide="list-checks"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">GT Traits de vie (BDC)</h3>
                    <p class="text-slate-600 text-sm">Structuration d'une base de données de traits biologiques et écologiques des Lépidoptères de France, en partenariat avec Arthropologia pour le volet pollinisation. Couplage avec les besoins du dispositif EU-PoMS.</p>
                </div>

                {{-- GT Zygènes --}}
                <div class="card p-6">
                    <div class="pub-card-icon blue mb-4">
                        <i class="icon icon-blue" data-lucide="sparkles"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">GT Zygènes</h3>
                    <p class="text-slate-600 text-sm">Créé en 2025 à la suite de la dissolution du GIRAZ-Zygaena, ce groupe rassemble les spécialistes français des Zygaenidae autour d'enjeux taxonomiques, chorologiques et de conservation, dans la perspective d'une révision de la Liste rouge.</p>
                </div>

                {{-- Comité Lepis --}}
                <div class="card p-6">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="book-open"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Comité de lecture <em>Lepis</em></h3>
                    <p class="text-slate-600 text-sm">Anime la production éditoriale du bulletin trimestriel des adhérents : sollicitation d'auteurs, relecture, choix iconographiques, vie associative.</p>
                </div>

                {{-- Comité Chersotis --}}
                <div class="card p-6">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="book-marked"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Comité de rédaction <em>Chersotis</em></h3>
                    <p class="text-slate-600 text-sm">Pilote la revue scientifique en accès ouvert : politique éditoriale, peer-review, conventions typographiques, intégration LaTeX/PDF, enregistrement DOI via Crossref.</p>
                </div>

                {{-- Référents régionaux --}}
                <div class="card p-6 md:col-span-2 lg:col-span-3">
                    <div class="flex items-start gap-4">
                        <div class="pub-card-icon gold flex-shrink-0">
                            <i class="icon icon-gold" data-lucide="map-pin"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-2">Référents régionaux</h3>
                            <p class="text-slate-600 text-sm">Adhérents qui assurent l'ancrage territorial d'oreina, animent les contributions locales et représentent l'association auprès des structures naturalistes régionales et des gestionnaires d'espaces naturels. Ce maillage régional est en consolidation, en lien avec le projet SEQREF qui mobilise des référents régionaux pour la collecte de spécimens.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Encart rejoindre --}}
            <div class="mt-12 p-8 bg-white rounded-2xl border border-slate-200">
                <div class="grid md:grid-cols-3 gap-6 items-center">
                    <div class="md:col-span-2">
                        <h3 class="text-xl font-bold text-oreina-dark mb-2">Vous souhaitez contribuer ?</h3>
                        <p class="text-slate-600">
                            Que vous soyez débutant ou expérimenté, plusieurs portes d'entrée existent : saisie de données sur <em>Artemisiae</em>, participation à un groupe de travail, contribution à un article, collecte de spécimens dans le cadre du barcoding. Toute contribution compte.
                        </p>
                    </div>
                    <div class="content-actions md:flex-col md:items-stretch">
                        <a href="{{ route('hub.contact') }}" class="btn btn-primary">
                            <i class="icon icon-sage" data-lucide="mail"></i>
                            Nous contacter
                        </a>
                        <a href="{{ route('hub.membership') }}" class="btn btn-secondary">
                            <i class="icon icon-blue" data-lucide="heart-plus"></i>
                            Adhérer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Vie statutaire --}}
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-oreina-dark">Vie statutaire et transparence</h2>
                <p class="text-slate-500 mt-3">
                    oreina rend compte de son fonctionnement à ses adhérents et à ses partenaires. Les comptes rendus de conseils d'administration, les procès-verbaux d'assemblées générales et les rapports d'activité sont accessibles aux membres dans l'extranet de l'association.
                </p>
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
                <a href="/documents/statuts-oreina.pdf" class="card p-5 text-center hover:shadow-lg transition">
                    <div class="pub-card-icon sage mx-auto mb-3">
                        <i class="icon icon-sage" data-lucide="file-text"></i>
                    </div>
                    <p class="font-bold text-oreina-dark text-sm">Statuts (PDF)</p>
                </a>
                <a href="/documents/rapport-activite-2024.pdf" class="card p-5 text-center hover:shadow-lg transition">
                    <div class="pub-card-icon coral mx-auto mb-3">
                        <i class="icon icon-coral" data-lucide="file-bar-chart"></i>
                    </div>
                    <p class="font-bold text-oreina-dark text-sm">Rapport d'activité 2024</p>
                </a>
                <a href="{{ route('member.dashboard') }}" class="card p-5 text-center hover:shadow-lg transition">
                    <div class="pub-card-icon gold mx-auto mb-3">
                        <i class="icon icon-gold" data-lucide="user-circle"></i>
                    </div>
                    <p class="font-bold text-oreina-dark text-sm">Espace adhérent</p>
                </a>
            </div>
        </div>
    </section>

    {{-- CTA final --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="users-round"></i>Rejoindre l'équipe</div>
                <h2>Rejoindre l'équipe d'oreina</h2>
                <p>oreina vit grâce à ses adhérents. Devenir membre, c'est rejoindre un réseau actif de naturalistes engagés dans la connaissance et la protection des Lépidoptères de France.</p>
                <div class="content-actions">
                    <a href="{{ route('hub.membership') }}" class="btn btn-primary">
                        <i class="icon icon-sage" data-lucide="heart-plus"></i>
                        Devenir membre
                    </a>
                    <a href="{{ route('hub.about') }}" class="btn btn-ghost-light">
                        <i class="icon icon-white" data-lucide="compass"></i>
                        Découvrir nos missions
                    </a>
                </div>
            </article>
        </div>
    </section>
@endsection
