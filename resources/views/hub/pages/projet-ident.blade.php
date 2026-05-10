@extends('layouts.hub')

@section('title', 'Identification (IDENT)')
@section('meta_description', 'oreina structure une base de connaissance sur la difficulté d\'identification des Lépidoptères de France, en lien avec PatriNat et le dispositif européen EU-PoMS.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="flex-1">
                    <div class="eyebrow gold mb-4 inline-flex">
                        <i class="icon icon-gold" data-lucide="search"></i>
                        Projet 4 / 5
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Identification</h1>
                    <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl">
                        Cartographier la difficulté d'identification des Lépidoptères et produire les outils pour la lever
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm bg-white p-5 rounded-2xl border border-slate-200 lg:min-w-[420px]">
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Programme</p>
                        <p class="font-bold text-oreina-dark">IDENT</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Coordination scientifique</p>
                        <p class="font-bold text-oreina-dark">PatriNat (MNHN)</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Convention</p>
                        <p class="font-bold text-oreina-dark">OFB 2026, 2028</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Coordination</p>
                        <p class="font-bold text-oreina-dark">14 bénévoles + salariée</p>
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
                    <div class="rounded-3xl shadow-lg flex items-center justify-center bg-gradient-to-br from-oreina-yellow/15 to-oreina-coral/10 relative overflow-hidden" style="min-height: 340px;">
                        <i data-lucide="search" style="width:140px;height:140px;color:#8b6c05;opacity:0.85"></i>
                        <i data-lucide="scan-eye" style="width:36px;height:36px;color:var(--blue);opacity:0.55;position:absolute;top:24px;right:32px"></i>
                        <i data-lucide="dna" style="width:36px;height:36px;color:var(--coral);opacity:0.55;position:absolute;bottom:32px;left:28px"></i>
                        <i data-lucide="map" style="width:32px;height:32px;color:#2f694e;opacity:0.55;position:absolute;bottom:36px;right:36px"></i>
                    </div>
                </div>
                <div class="lg:col-span-3 text-slate-600 space-y-6">
                    <p class="text-xl leading-relaxed">
                        Toute donnée d'observation commence par une identification. Et toute identification n'a pas la même difficulté : certaines espèces se reconnaissent au premier coup d'œil, d'autres exigent un examen morphologique fin, d'autres encore ne sont distinguables que par séquençage génétique. Cette difficulté varie aussi dans l'espace : telle espèce est inconfondable dans un département, mais sympatrique avec une espèce-sœur dans un autre.
                    </p>
                    <p class="leading-relaxed">
                        Le projet <strong>IDENT</strong> vise à structurer toute cette information : identifier les complexes d'espèces problématiques, documenter les critères de distinction, produire les guides et tutoriels, et rendre cette connaissance accessible à tous les observateurs, dès la saisie de leurs données sur <em>Artemisiae</em>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pour comprendre : singletons, agrégats, sympatrie --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="lightbulb"></i>
                    Pour comprendre
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Singletons, agrégats, sympatrie</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    Pour structurer la difficulté d'identification, PatriNat a développé deux concepts complémentaires.
                </p>
                <p>
                    Un <strong>singleton</strong> est une espèce considérée comme inconfondable en France métropolitaine : aucune autre espèce ne lui ressemble suffisamment pour qu'il y ait risque de confusion. Le Machaon, le Citron, le Vulcain, le Sphinx tête-de-mort sont des exemples typiques. Pour ces espèces, l'identification est immédiate, accessible aux débutants, et ne nécessite aucune investigation particulière.
                </p>
                <p>
                    Un <strong>agrégat</strong>, à l'inverse, est un ensemble d'espèces susceptibles d'être confondues entre elles. Les Hespéries du genre <em>Pyrgus</em>, plusieurs Mélitées, de nombreuses Noctuelles du genre <em>Mythimna</em>, certains Géomètres des bois sont autant d'exemples d'agrégats où la distinction demande un examen attentif, voire la dissection des armures génitales ou le séquençage moléculaire.
                </p>
                <p>
                    Mais ce qui rend la question vraiment intéressante, c'est que <strong>la difficulté d'identification varie dans l'espace</strong>. Une espèce peut être inconfondable dans un département où elle est seule, et devenir difficile à distinguer dès qu'on entre dans la zone de sympatrie d'une espèce-sœur. C'est pourquoi le projet IDENT articule en permanence la documentation des agrégats avec les <strong>cartes de répartition départementales</strong>, mises à jour dans le cadre du programme ABDSM (Atlas de la Biodiversité Départementale et des Secteurs Marins) de PatriNat.
                </p>
            </div>
        </div>
    </section>

    {{-- Cinq niveaux de difficulté --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="layers-3"></i>
                    Architecture
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Cinq niveaux de difficulté d'identification</h2>
                <p class="text-slate-500 mt-3 max-w-3xl mx-auto">La typologie développée par PatriNat structure l'effort d'identification en cinq niveaux d'investigation croissants. Chaque espèce, à chacun de ses stades biologiques, peut être positionnée sur cette échelle.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-oreina-green/15 text-oreina-green font-bold text-lg">T1</span>
                        <span class="text-xs font-bold uppercase tracking-wider text-oreina-green">Inconfondable</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Aucune investigation</h3>
                    <p class="text-xs text-slate-600">L'espèce est évaluée comme inconfondable après expertise. Singleton.</p>
                </div>

                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-oreina-turquoise/15 text-oreina-turquoise font-bold text-lg">T2</span>
                        <span class="text-xs font-bold uppercase tracking-wider text-oreina-turquoise">Facile</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Investigation à vue, sans manipulation</h3>
                    <p class="text-xs text-slate-600">Distinction possible sur photo ou observation directe, sans capture.</p>
                </div>

                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-oreina-yellow/20 font-bold text-lg" style="color:#8b6c05">T3</span>
                        <span class="text-xs font-bold uppercase tracking-wider" style="color:#8b6c05">Modérée</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Investigation à vue, avec manipulation</h3>
                    <p class="text-xs text-slate-600">Capture nécessaire pour observer certains critères (face ventrale, dessous des ailes, etc.).</p>
                </div>

                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-oreina-coral/15 text-oreina-coral font-bold text-lg">T4</span>
                        <span class="text-xs font-bold uppercase tracking-wider text-oreina-coral">Difficile</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Examen de la morphologie interne</h3>
                    <p class="text-xs text-slate-600">Examen des armures génitales nécessaire (dissection). Exige une expertise spécialisée.</p>
                </div>

                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-oreina-blue/15 text-oreina-blue font-bold text-lg">T5</span>
                        <span class="text-xs font-bold uppercase tracking-wider text-oreina-blue">Moléculaire</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Séquençage génétique</h3>
                    <p class="text-xs text-slate-600">Identification par barcoding moléculaire (CO1) ou marqueurs nucléaires complémentaires.</p>
                </div>
            </div>

            <div class="mt-8 p-6 bg-white rounded-xl border border-slate-200">
                <p class="text-sm text-slate-600">
                    <strong class="text-oreina-dark">Une typologie appliquée à chaque stade biologique.</strong> La difficulté d'identification n'est pas la même selon que l'on observe un imago, une chenille, une chrysalide ou une mine foliaire. Le projet IDENT documente la difficulté pour chaque stade pertinent. À ce jour, les <strong>mines foliaires</strong> de 11 espèces de Lépidoptères sont par exemple considérées par expertise comme des singletons : elles permettent une identification certaine sans avoir besoin d'observer le papillon adulte.
                </p>
            </div>
        </div>
    </section>

    {{-- Pourquoi structurer cette information --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="target"></i>
                    L'enjeu
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Pourquoi structurer cette information</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    La qualité d'une donnée naturaliste se joue dès la phase d'identification. Une espèce mal identifiée à la saisie est, dans le meilleur des cas, signalée par les validateurs et corrigée. Dans le pire, elle reste dans la base et fausse les analyses ultérieures : cartes de répartition, suivis temporels, modèles de distribution. Structurer la connaissance sur la difficulté d'identification permet d'agir <strong>au moment de l'acquisition</strong>, pas seulement en aval.
                </p>
                <p>
                    Cet enjeu prend une importance particulière dans le cadre du dispositif <strong>EU-PoMS</strong> (European Pollinator Monitoring Scheme), lié au Règlement européen sur la Restauration de la Nature, qui intègre désormais les Lépidoptères nocturnes au suivi standardisé des pollinisateurs. Pour que les suivis s'appuient sur des données fiables, encore faut-il fournir aux observateurs les outils permettant une identification rigoureuse, contextualisée localement. C'est précisément l'objet du projet IDENT.
                </p>
                <p>
                    L'enjeu est aussi <strong>pédagogique</strong>. Distinguer clairement les espèces faciles (singletons, T1-T2) des complexes nécessitant une expertise permet d'orienter les programmes de science participative et les nouveaux bénévoles vers des espèces accessibles, sans les décourager par des identifications trop ardues. C'est un levier essentiel pour élargir et fidéliser le réseau d'observateurs.
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
                <h2 class="text-3xl font-bold text-oreina-dark">Un comité technique dédié, un corpus documentaire en construction</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    oreina pilote au sein de PatriNat le volet Lépidoptères de la base de connaissance sur la difficulté d'identification. Le travail est porté par un <strong>comité technique interne (COTECH IDENT)</strong> qui rassemble 14 bénévoles experts, spécialistes de différents groupes taxonomiques, appuyés par la coordinatrice scientifique salariée. Le projet est étroitement articulé avec le projet QUALIF, dont il est l'ingrédient méthodologique central pour la qualification des données acquises sur <em>Artemisiae</em>.
                </p>
                <p>
                    Le travail d'oreina s'organise autour de quatre volets complémentaires :
                </p>
                <ul class="list-disc pl-8 space-y-3 marker:text-oreina-green">
                    <li><span class="pl-2 inline-block">la <strong>cartographie des agrégats</strong> : recensement systématique des complexes d'espèces problématiques, en priorisant ceux qui concernent les espèces suivies par EU-PoMS et les Macrohétérocères ;</span></li>
                    <li><span class="pl-2 inline-block">la <strong>typologie de la difficulté</strong> : classement de chaque espèce, à chacun de ses stades biologiques, dans les cinq niveaux de la typologie PatriNat ;</span></li>
                    <li><span class="pl-2 inline-block">la <strong>production de guides et tutoriels</strong> : rédaction de fiches descriptives, clés d'identification illustrées, guides de dissection pour les groupes nécessitant un examen morphologique interne ;</span></li>
                    <li><span class="pl-2 inline-block">la <strong>mise à jour des cartes de répartition</strong> : actualisation des cartes départementales dans le cadre du programme ABDSM, pour identifier précisément les zones de sympatrie qui génèrent les vraies difficultés d'identification.</span></li>
                </ul>
                <p>
                    Toutes ces ressources sont diffusées en libre accès via le portail <em>Artemisiae</em>, qui est conçu comme le vecteur principal de ce corpus documentaire.
                </p>
            </div>
        </div>
    </section>

    {{-- Chiffres-clés --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="bar-chart-3"></i>
                    Chiffres-clés
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">IDENT en chiffres</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">État du chantier en cours et objectifs de la convention 2026, 2028.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">14</p>
                    <p class="text-sm text-slate-600 leading-tight">bénévoles experts mobilisés au sein du comité technique IDENT</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">5</p>
                    <p class="text-sm text-slate-600 leading-tight">niveaux de difficulté dans la typologie PatriNat (T1 à T5)</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">~10</p>
                    <p class="text-sm text-slate-600 leading-tight">guides et fiches d'identification à produire d'ici 2028</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-green mb-2">839</p>
                    <p class="text-sm text-slate-600 leading-tight">cartes ABDSM de répartition départementale déjà réalisées pour les Lépidoptères</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-green mb-2">115</p>
                    <p class="text-sm text-slate-600 leading-tight">cartes ABDSM mises à jour en 2024 (Rhopalocères et Zygènes principalement)</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-green mb-2">100 %</p>
                    <p class="text-sm text-slate-600 leading-tight">des agrégats d'espèces liés au suivi EU-PoMS à saisir d'ici 2028</p>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">Sources : fiche projet IDENT 2026, 2028 et rapport d'activité OFB 2024 d'oreina.</p>
        </div>
    </section>

    {{-- Sur Artemisiae : la boucle vertueuse --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="repeat"></i>
                    Sur Artemisiae
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Une aide à la saisie, une aide à l'apprentissage</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Le travail d'IDENT n'a pas vocation à rester confiné dans une base de données institutionnelle : il est conçu pour irriguer directement la pratique des observateurs sur <em>Artemisiae</em>.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 items-start">
                <div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-4">Au moment de la saisie</h3>
                    <p class="text-slate-600 mb-4">
                        Lorsqu'un observateur saisit une donnée sur <em>Artemisiae</em>, la plateforme peut désormais lui signaler que l'espèce déclarée appartient à un agrégat connu, et lui indiquer la nature des risques de confusion dans son département. Selon la difficulté typologique, elle peut suggérer une <strong>confirmation par photo de critères spécifiques</strong>, signaler la nécessité d'un examen morphologique, ou orienter vers un guide dédié.
                    </p>
                    <p class="text-slate-600">
                        Cette aide contextuelle évite des erreurs en amont et renforce la qualité de la donnée dès l'origine, sans alourdir le processus de saisie pour les espèces inconfondables.
                    </p>
                </div>

                <div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-4">Pour progresser dans son apprentissage</h3>
                    <p class="text-slate-600 mb-4">
                        Pour les naturalistes en formation continue, IDENT fournit une véritable <strong>cartographie progressive de la difficulté</strong>. Un débutant peut consulter la liste des singletons (T1) et des espèces faciles (T2) de sa région : autant d'objectifs accessibles pour acquérir une première compétence solide. Un naturaliste plus avancé peut s'attaquer aux agrégats T3-T4 avec les guides illustrés produits par l'association.
                    </p>
                    <p class="text-slate-600">
                        Les <strong>fiches taxons d'<em>Artemisiae</em></strong> intègrent désormais ces informations : niveau de difficulté, agrégats associés, critères distinctifs, lien vers les guides. C'est un outil unique en France pour comprendre, espèce par espèce, le degré de fiabilité qu'on peut accorder à une identification de terrain.
                    </p>
                </div>
            </div>

            {{-- Schéma du cycle --}}
            <div class="mt-12 card p-8 bg-white">
                <h3 class="text-lg font-bold text-oreina-dark mb-6 text-center">Le cycle vertueux entre identification, apprentissage et qualité de la donnée</h3>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 bg-oreina-teal rounded-2xl flex items-center justify-center shadow-md">
                            <i data-lucide="map" style="width:30px;height:30px;color:#fff"></i>
                        </div>
                        <h4 class="font-bold text-oreina-dark text-sm mb-1">1. Cartographie</h4>
                        <p class="text-xs text-slate-500">Recensement des agrégats et typologie de la difficulté par les experts du COTECH.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 bg-oreina-yellow rounded-2xl flex items-center justify-center shadow-md">
                            <i data-lucide="book-open" style="width:30px;height:30px;color:#16302B"></i>
                        </div>
                        <h4 class="font-bold text-oreina-dark text-sm mb-1">2. Production des guides</h4>
                        <p class="text-xs text-slate-500">Rédaction des fiches, clés et guides de dissection. Diffusion via <em>Artemisiae</em>.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 bg-oreina-coral rounded-2xl flex items-center justify-center shadow-md">
                            <i data-lucide="wand-sparkles" style="width:30px;height:30px;color:#fff"></i>
                        </div>
                        <h4 class="font-bold text-oreina-dark text-sm mb-1">3. Aide à la saisie</h4>
                        <p class="text-xs text-slate-500">Signalement automatique des risques de confusion lors de la saisie sur <em>Artemisiae</em>.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 bg-oreina-blue rounded-2xl flex items-center justify-center shadow-md">
                            <i data-lucide="badge-check" style="width:30px;height:30px;color:#fff"></i>
                        </div>
                        <h4 class="font-bold text-oreina-dark text-sm mb-1">4. Donnée qualifiée</h4>
                        <p class="text-xs text-slate-500">Données plus fiables, validation facilitée, et le cycle alimente en retour la cartographie.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Labo Lépidos : transmettre l'expertise --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-5 gap-10 items-start">
                <div class="lg:col-span-2">
                    <div class="eyebrow coral mb-4 inline-flex">
                        <i class="icon icon-coral" data-lucide="flask-conical"></i>
                        Outil phare
                    </div>
                    <h2 class="text-3xl font-bold text-oreina-dark mb-4">Les Labo Lépidos</h2>
                    <p class="text-slate-600 leading-relaxed mb-4">
                        Documenter un complexe d'espèces ne suffit pas : encore faut-il que les critères, les pièges et les cas-limites soient transmis aux observateurs et aux validateurs. Les <strong>Labo Lépidos</strong> sont le format pédagogique d'oreina pour cela : des sessions courtes, animées par un spécialiste, qui prennent un agrégat précis et le décortiquent pas à pas.
                    </p>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Chaque Labo Lépido associe un webinaire en direct (avec questions du public) et un support téléchargeable réutilisable, pensé comme un véritable outil de travail pour la saisie sur <em>Artemisiae</em>.
                    </p>
                    <div class="content-actions">
                        <a href="/outils/labo-lepidos" class="btn btn-primary">
                            <i class="icon icon-sage" data-lucide="flask-conical"></i>
                            Découvrir les Labo Lépidos
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-3 grid sm:grid-cols-2 gap-4">
                    <div class="card p-6">
                        <div class="pub-card-icon coral mb-3">
                            <i class="icon icon-coral" data-lucide="presentation"></i>
                        </div>
                        <h3 class="font-bold text-oreina-dark mb-2 text-sm">Webinaires courts</h3>
                        <p class="text-xs text-slate-600">30 à 40 minutes pour traiter un agrégat ou un complexe d'espèces, animé par un référent du COTECH.</p>
                    </div>
                    <div class="card p-6">
                        <div class="pub-card-icon gold mb-3">
                            <i class="icon icon-gold" data-lucide="download"></i>
                        </div>
                        <h3 class="font-bold text-oreina-dark mb-2 text-sm">Supports téléchargeables</h3>
                        <p class="text-xs text-slate-600">Diaporama et synthèse PDF, librement réutilisables pour la formation et la validation des données.</p>
                    </div>
                    <div class="card p-6">
                        <div class="pub-card-icon blue mb-3">
                            <i class="icon icon-blue" data-lucide="users"></i>
                        </div>
                        <h3 class="font-bold text-oreina-dark mb-2 text-sm">Ouverts à tous</h3>
                        <p class="text-xs text-slate-600">Adhérents et non-adhérents bienvenus. Les replays restent accessibles en libre accès après la session.</p>
                    </div>
                    <div class="card p-6">
                        <div class="pub-card-icon sage mb-3">
                            <i class="icon icon-sage" data-lucide="lightbulb"></i>
                        </div>
                        <h3 class="font-bold text-oreina-dark mb-2 text-sm">Vous proposez le sujet</h3>
                        <p class="text-xs text-slate-600">Spécialistes ou observateurs : la communauté propose les agrégats à traiter et peut animer ses propres sessions.</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 p-6 bg-slate-50 rounded-2xl border border-slate-200">
                <p class="text-sm text-slate-600">
                    <strong class="text-oreina-dark">Premier Labo Lépido en ligne : le complexe <em>Hoplodrina</em>.</strong> Sept espèces en France, dont une redécouverte en 2020, un taux d'erreur d'identification estimé supérieur à 30 % sur <em>Artemisiae</em> : un cas d'école pour ouvrir la série. Diaporama, replay et clés pratiques sont disponibles sur la <a href="/outils/labo-lepidos" class="text-oreina-coral font-bold hover:underline">page dédiée</a>.
                </p>
            </div>
        </div>
    </section>

    {{-- IDENT et l'intelligence artificielle --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="cpu"></i>
                    Vers de nouveaux outils
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">IDENT et l'intelligence artificielle</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    Les outils d'identification automatique par intelligence artificielle se développent rapidement. Leur fiabilité dépend toutefois des données d'entraînement : la plupart des modèles disponibles aujourd'hui ont été développés à partir de corpus dominés par les contributions nord-européennes, et leurs performances chutent sensiblement sur les faunes méridionales, alpines, pyrénéennes ou corses, sous-représentées dans les jeux de données.
                </p>
                <p>
                    oreina a fait le choix de <strong>contribuer activement à l'amélioration de ces outils</strong>, plutôt que de les rejeter. Le projet IDENT prévoit le partage des bases de données photographiques de l'association pour augmenter la performance des modèles sur la faune française, accompagné d'une <strong>expertise nationale d'évaluation</strong>. L'idée n'est pas de remplacer le validateur humain par un algorithme, mais de fournir aux développeurs d'IA les corpus représentatifs qui leur manquent, et de mettre en place les protocoles d'évaluation indépendants permettant de mesurer la fiabilité réelle des modèles, espèce par espèce et région par région.
                </p>
                <p>
                    À terme, l'articulation idéale est claire : l'IA pour les espèces inconfondables et pour suggérer une première identification, l'expertise humaine pour les agrégats critiques et la validation des cas sensibles.
                </p>
            </div>
        </div>
    </section>

    {{-- Pour aller plus loin --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-oreina-dark">Pour aller plus loin</h2>
                <p class="text-slate-500 mt-2">Ressources externes, outils dérivés et publications associées au projet IDENT.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="/outils/labo-lepidos" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon coral mb-3">
                        <i class="icon icon-coral" data-lucide="flask-conical"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-coral transition">Labo Lépidos</h3>
                    <p class="text-xs text-slate-500">Webinaires courts dédiés aux complexes d'espèces. Replays et supports en libre accès.</p>
                </a>
                <a href="https://oreina.org/artemisiae/" target="_blank" rel="noopener" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon gold mb-3">
                        <i class="icon icon-gold" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-coral transition">Guides sur Artemisiae</h3>
                    <p class="text-xs text-slate-500">Corpus documentaire d'identification accessible en libre accès.</p>
                </a>
                <a href="https://inpn.mnhn.fr/programme/atlas-biodiversite-departementale" target="_blank" rel="noopener" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-3">
                        <i class="icon icon-sage" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-green transition">Programme ABDSM</h3>
                    <p class="text-xs text-slate-500">Atlas de la Biodiversité Départementale et des Secteurs Marins (PatriNat).</p>
                </a>
                <a href="/documents/rapport-activite-2024.pdf" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon blue mb-3">
                        <i class="icon icon-blue" data-lucide="file-bar-chart"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-blue transition">Rapport d'activité</h3>
                    <p class="text-xs text-slate-500">Rapport IDENT 2024 d'oreina à l'OFB.</p>
                </a>
            </div>
        </div>
    </section>

    {{-- Vous pouvez contribuer --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="helping-hand"></i>
                    Contribuer
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Vous pouvez contribuer</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Le projet IDENT mobilise une diversité de compétences : expertise taxonomique, pédagogie, photographie, illustration. Plusieurs voies de contribution sont ouvertes.</p>
            </div>

            <div class="space-y-4">
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon sage flex-shrink-0">
                        <i class="icon icon-sage" data-lucide="microscope"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Apporter une expertise taxonomique</h3>
                        <p class="text-slate-600 text-sm">Vous êtes spécialiste d'un groupe (Géomètres, Noctuelles, Microlépidoptères, Hespéries...) et vous identifiez régulièrement les agrégats critiques de votre groupe ? Rejoignez le COTECH IDENT pour contribuer à la documentation des complexes d'espèces.</p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon coral flex-shrink-0">
                        <i class="icon icon-coral" data-lucide="camera"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Partager des illustrations</h3>
                        <p class="text-slate-600 text-sm">Photographies de critères distinctifs, schémas d'armures génitales, illustrations comparatives : tous les documents iconographiques de qualité enrichissent les guides. Une mention systématique de l'auteur est intégrée à chaque support.</p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon gold flex-shrink-0">
                        <i class="icon icon-gold" data-lucide="clipboard-check"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Tester les clés d'identification</h3>
                        <p class="text-slate-600 text-sm">Avant publication, les clés sont testées par des naturalistes de différents niveaux pour vérifier leur clarté et leur ergonomie. Si vous avez du temps pour quelques sessions de relecture critique, votre regard est précieux.</p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon blue flex-shrink-0">
                        <i class="icon icon-blue" data-lucide="flask-conical"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Animer un Labo Lépido ou proposer un sujet</h3>
                        <p class="text-slate-600 text-sm">Vous souhaitez animer une session sur un agrégat que vous maîtrisez, ou suggérer un complexe d'espèces qui mériterait d'être traité ? Tout est centralisé sur la <a href="/outils/labo-lepidos" class="text-oreina-coral font-bold hover:underline">page Labo Lépidos</a>, où vous pouvez consulter les sessions disponibles et déposer votre proposition.</p>
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
                <h2>Participer à IDENT</h2>
                <p>Que vous souhaitiez intégrer le COTECH, partager des illustrations, tester des clés ou participer aux ateliers, votre contribution renforce la qualité des identifications et l'autonomie des observateurs.</p>
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
                <p class="text-slate-500 mt-3 max-w-3xl mx-auto">IDENT s'inscrit dans la convention pluriannuelle 2026, 2028 d'oreina avec l'OFB. Il est étroitement articulé avec le projet QUALIF, dont il est l'ingrédient méthodologique central.</p>
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
