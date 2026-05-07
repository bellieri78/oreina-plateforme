@extends('layouts.hub')

@section('title', 'Pourquoi oreina')
@section('meta_description', 'De Claude Dufay à Chersotis oreina, du Guide des papillons nocturnes à l\'Office français de la biodiversité : récit de la genèse d\'oreina, association des lépidoptéristes de France.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-16 bg-warm">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="eyebrow blue mb-6">
                <i class="icon icon-blue" data-lucide="feather"></i>
                Histoire & sens
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Pourquoi oreina</h1>
            <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl mx-auto">
                Récit d'une genèse, sens d'un nom
            </p>
        </div>
    </section>

    {{-- Chapô --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="prose prose-lg text-slate-600">
                    <p class="text-xl leading-relaxed">
                        Une association porte toujours plus que sa définition statutaire. Derrière oreina, il y a une intuition de départ, un nom choisi avec soin, et une succession de paris éditoriaux et scientifiques qui dessinent, en presque vingt ans, une certaine idée de la lépidoptérologie française : exigeante, conviviale, ancrée dans le terrain, ouverte aux institutions.
                    </p>
                    <p>
                        Cette page revient sur cette histoire : celle d'une association, celle d'un papillon, et celle de l'homme qui l'a décrit.
                    </p>
                </div>
                <div class="rounded-3xl overflow-hidden shadow-lg" style="min-height: 320px; background: url('/images/about-mission.jpg') center/cover no-repeat;"></div>
            </div>
        </div>
    </section>

    {{-- Le contexte fondateur --}}
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow sage mb-4 inline-flex">
                    <i class="icon icon-sage" data-lucide="history"></i>
                    2007 : un manque, un élan
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Le contexte fondateur</h2>
            </div>

            {{-- 3 dates clés --}}
            <div class="grid grid-cols-3 gap-4 mb-10">
                <div class="text-center p-5 rounded-2xl bg-oreina-green/5 border border-oreina-green/15">
                    <div class="text-2xl sm:text-3xl font-bold text-oreina-green">2007</div>
                    <div class="text-xs text-slate-500 mt-1 leading-tight">Déclaration<br>(10 janvier)</div>
                </div>
                <div class="text-center p-5 rounded-2xl bg-oreina-yellow/10 border border-oreina-yellow/30">
                    <div class="text-2xl sm:text-3xl font-bold" style="color: #8b6c05;">2008</div>
                    <div class="text-xs text-slate-500 mt-1 leading-tight">Premier numéro<br>du magazine</div>
                </div>
                <div class="text-center p-5 rounded-2xl bg-oreina-blue/10 border border-oreina-blue/20">
                    <div class="text-2xl sm:text-3xl font-bold" style="color: var(--blue);">~20 ans</div>
                    <div class="text-xs text-slate-500 mt-1 leading-tight">D'engagement<br>associatif</div>
                </div>
            </div>

            <div class="prose prose-lg text-slate-600 max-w-none">
                <p>
                    Au milieu des années 2000, la communauté française des lépidoptéristes traverse une période de fragilité. <em>Alexanor</em>, revue historique de référence depuis 1959, est en pause. Les espaces de mise en relation entre lépidoptéristes amateurs et professionnels se sont raréfiés. Il manque un lieu, éditorial, associatif, fédérateur, où la diversité des passions, des pratiques et des savoirs naturalistes puisse se retrouver.
                </p>
                <p>
                    C'est dans ce contexte que paraît, chez Delachaux et Niestlé, le <em>Guide des papillons nocturnes de France</em>, ouvrage qui marque durablement le paysage entomologique francophone et donne envie, à toute une génération de naturalistes, de s'investir davantage dans l'étude des hétérocères. Cet élan ne se contentera pas d'être éditorial : quelques lépidoptéristes y voient le moment de relancer une dynamique associative à la hauteur de l'ambition du livre.
                </p>
                <p>
                    L'association <strong>oreina</strong> est officiellement déclarée le <strong>10 janvier 2007</strong>, sous le régime de la loi de 1901, par David Demergès et Roland Robineau. Son objet, tel que rédigé à l'article 3 de ses statuts fondateurs, est « <em>l'étude à caractère scientifique, au niveau amateur, des Lépidoptères de France, sa vulgarisation et leur protection par tous moyens appropriés</em> ».
                </p>
                <p>
                    Le premier numéro du magazine <em>oreina</em> paraît en <strong>2008</strong>. Trimestriel, généreusement illustré, il s'impose rapidement comme un rendez-vous attendu des lépidoptéristes francophones. Autour de lui, et autour des rencontres annuelles qui s'organisent dans la foulée, se constitue progressivement la communauté qui fait aujourd'hui la force de l'association.
                </p>
            </div>
        </div>
    </section>

    {{-- Le choix du nom --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="tag"></i>
                    Le choix du nom
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark"><em>oreina</em>, double symbole de l'entomologie française</h2>
            </div>

            {{-- Illustrations Chersotis oreina + Claude Dufay --}}
            <div class="grid sm:grid-cols-2 gap-6 mb-10">
                <figure class="card p-0 overflow-hidden">
                    <div class="aspect-[4/3] bg-gradient-to-br from-oreina-green/10 to-oreina-turquoise/10 flex items-center justify-center">
                        <img src="/images/pourquoi/chersotis-oreina.jpg" alt="Chersotis oreina, Dufay 1984, sur pelouse subalpine" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><p class=\'text-slate-500 text-sm font-bold\'>Chersotis oreina</p><p class=\'text-slate-400 text-xs italic\'>(Dufay, 1984)</p></div>'">
                    </div>
                    <figcaption class="p-4 text-sm text-slate-500">
                        <em>Chersotis oreina</em> (Dufay, 1984), Noctuidae, Noctuinae. Pelouses subalpines des Alpes, des Pyrénées et du sud du Jura.
                    </figcaption>
                </figure>

                <figure class="card p-0 overflow-hidden">
                    <div class="aspect-[4/3] bg-gradient-to-br from-oreina-yellow/10 to-oreina-coral/10 flex items-center justify-center">
                        <img src="/images/pourquoi/claude-dufay.jpg" alt="Claude Dufay, lépidoptériste français (1926-2001)" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><p class=\'text-slate-500 text-sm font-bold\'>Claude Dufay</p><p class=\'text-slate-400 text-xs\'>1926, 2001</p></div>'">
                    </div>
                    <figcaption class="p-4 text-sm text-slate-500">
                        Claude Dufay (1926, 2001), entomologiste français, auteur de la description originale de <em>Chersotis oreina</em>.
                    </figcaption>
                </figure>
            </div>

            <div class="prose prose-lg text-slate-600 max-w-none">
                <p>
                    Le nom de l'association n'a pas été choisi par hasard. Il est emprunté à <em>Chersotis oreina</em> (Dufay, 1984), une noctuelle (Lepidoptera, Noctuidae, Noctuinae) qui occupe à elle seule deux places significatives dans l'histoire entomologique du XX<sup>e</sup> siècle.
                </p>
                <p>
                    C'est d'abord <strong>un taxon décrit en France</strong>, à partir d'un exemplaire des Hautes-Alpes, et dont l'aire de répartition s'étend des Alpes occidentales aux Pyrénées en passant par le sud du Jura. Papillon orophile inféodé aux pelouses subalpines fleuries, <em>Chersotis oreina</em> fréquente les milieux d'altitude que parcourent les naturalistes français depuis plus d'un siècle. Son épithète spécifique vient du grec <em>ὀρεινός</em> (<em>oreinós</em>), signifiant « de la montagne », « montagnard », référence directe à son écologie.
                </p>
                <p>
                    C'est ensuite <strong>un taxon décrit par Claude Dufay</strong> (1926, 2001), figure majeure de la lépidoptérologie française et internationale. Élève de Pierre-Paul Grassé, entré au CNRS en 1954, Claude Dufay a partagé sa carrière entre la station biologique des Eyzies, le laboratoire de zoologie et d'écologie de l'université de Lyon, et le Muséum national d'Histoire naturelle. Ses travaux sur le phototactisme des Noctuidae, sujet de sa thèse d'État soutenue en 1964, restent aujourd'hui une référence sur la réponse des hétérocères aux sources lumineuses. Mais c'est surtout par son œuvre taxonomique que sa portée s'est révélée mondiale : 161 publications scientifiques et près de 150 espèces décrites, des Plusiinae indo-australiens et africains à la faune des Comores, de Madagascar et de l'Europe méditerranéenne. Sa collection est aujourd'hui conservée au Centre de Conservation et d'Étude des Collections du Musée des Confluences à Lyon ; sa bibliothèque, à la Société linnéenne de Lyon.
                </p>
                <p>
                    En <strong>1984</strong>, Claude Dufay publie dans <em>Nota lepidopterologica</em> la description originale de <em>Chersotis oreina</em>, « <em>noctuelle méconnue des montagnes de l'Europe occidentale</em> ». L'espèce, longtemps confondue avec sa proche parente <em>Chersotis alpestris</em>, illustre exactement ce qui fait le sel de la systématique des Noctuinae : un travail patient de comparaison morphologique, d'examen des armures génitales, de prise en compte de la variabilité géographique, jusqu'à ce qu'une espèce méconnue soit enfin reconnue comme telle.
                </p>
                <p>
                    Choisir <em>oreina</em> comme nom d'association, c'était donc poser un double hommage : à un papillon <strong>français des hautes altitudes</strong>, et à un lépidoptériste dont les travaux ont façonné la connaissance des Noctuidae bien au-delà des frontières nationales.
                </p>
            </div>

            {{-- Encart filiation --}}
            <div class="mt-10 p-6 bg-oreina-yellow/5 border-l-4 border-oreina-yellow rounded-r-xl">
                <h3 class="font-bold text-oreina-dark mb-3">Une filiation assumée</h3>
                <p class="text-slate-600">
                    En 2010, dans le numéro 7 du magazine <em>oreina</em>, David Demergès consacrait une fiche technique au groupe des <em>Chersotis larixia / anatolica / elegans</em>, prolongement direct des travaux de Dufay sur ce genre complexe. Cette filiation intellectuelle, modeste mais explicite, dit l'esprit dans lequel l'association inscrit son nom : continuer, à hauteur d'amateurs avertis et de spécialistes, le travail entomologique français.
                </p>
            </div>

            {{-- Référence bibliographique --}}
            <div class="mt-6 text-xs text-slate-500 italic border-t border-slate-200 pt-4">
                Référence : Dufay, C., 1984. <em>Chersotis oreina</em> n. sp., noctuelle méconnue des montagnes de l'Europe occidentale (Noctuidae, Noctuinae). <em>Nota lepidopterologica</em>, 7(1) : 8, 20.
            </div>
        </div>
    </section>

    {{-- Du nom au logo --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="palette"></i>
                    Du nom au logo
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Le papillon dans le cercle</h2>
            </div>

            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="rounded-3xl bg-gradient-to-br from-oreina-yellow/10 to-oreina-coral/10 p-12 flex items-center justify-center" style="min-height: 360px;">
                    <img src="/images/logo-papillon.png" alt="Logo oreina, papillon Chersotis oreina dans un cercle ouvert" style="max-width: 280px; width: 100%; height: auto;">
                </div>
                <div class="prose prose-lg text-slate-600 max-w-none">
                    <p>
                        Pendant près de vingt ans, <em>Chersotis oreina</em> est resté un nom, un référent savant que les adhérents partageaient sans qu'il soit nécessairement visible dans la communication de l'association.
                    </p>
                    <p>
                        C'est en <strong>2025</strong>, dans le cadre de la refonte de l'identité visuelle conduite avec la graphiste Isabelle, que le choix a été fait d'<strong>assumer pleinement le nom et de lui donner forme</strong>. Le nouveau logo représente <em>Chersotis oreina</em>, posé dans un cercle ouvert. Le cercle, c'est la dimension associative : le partage, le réseau, l'horizon collectif. Son ouverture, c'est le geste vers l'extérieur : vers les partenaires, vers les communautés naturalistes voisines, vers les non-initiés qu'oreina entend accueillir.
                    </p>
                    <p>
                        Le papillon n'est plus un symbole abstrait : c'est <em>Chersotis oreina</em>, identifiable, situé, vivant, comme l'ensemble des Lépidoptères que l'association s'attache à mieux faire connaître.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Famille de noms --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="network"></i>
                    Une famille de noms
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Quatre noms, une cohérence</h2>
            </div>

            <div class="prose prose-lg text-slate-600 max-w-none">
                <p>
                    À mesure qu'oreina s'est dotée de nouveaux outils et de nouvelles publications, le nom de l'association a essaimé. Chaque grand chantier a reçu son propre nom, choisi dans le vocabulaire scientifique des Lépidoptères. Cette cohérence n'est pas anecdotique : elle signe l'identité naturaliste et savante de l'association.
                </p>

                <h3 class="text-2xl font-bold text-oreina-dark mt-10 flex flex-wrap items-center gap-3">
                    <em>Artemisiae</em>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-oreina-green/10 text-oreina-green not-italic">2018</span>
                </h3>
                <p>
                    <em>Artemisiae</em> est la plateforme technique d'oreina, dédiée à la saisie, à l'archivage, à la qualification et à la visualisation des données d'observation. Le nom est emprunté à <em>Cucullia artemisiae</em> (Denis &amp; Schiffermüller, 1775), noctuelle dont la chenille se nourrit d'armoises (<em>Artemisia</em> spp.), plantes elles-mêmes nommées en hommage à la déesse grecque Artémis, divinité de la chasse, de la nature sauvage et de l'abondance. Une façon de placer la plateforme sous le signe de la richesse de la donnée naturaliste.
                </p>

                <h3 class="text-2xl font-bold text-oreina-dark mt-10 flex flex-wrap items-center gap-3">
                    <em>Lepis</em>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-oreina-coral/10 text-oreina-coral not-italic">2026</span>
                </h3>
                <p>
                    <em>Lepis</em> est le bulletin trimestriel de la vie associative et naturaliste. Le nom vient du grec <em>λεπίς</em> (<em>lepís</em>), signifiant « écaille », racine éponyme des Lépidoptères, littéralement les « porteurs d'écailles ». Un nom volontairement bref, qui inscrit le bulletin dans la matérialité même du papillon.
                </p>

                <h3 class="text-2xl font-bold text-oreina-dark mt-10 flex flex-wrap items-center gap-3">
                    <em>Chersotis</em>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-oreina-blue/10 text-oreina-blue not-italic">2026</span>
                </h3>
                <p>
                    <em>Chersotis</em> est la revue scientifique en accès ouvert d'oreina, publiée en flux continu, à comité de lecture, avec attribution de DOI via Crossref. Le nom reprend celui du genre auquel appartient <em>Chersotis oreina</em> : boucle élégante avec le nom de l'association. Le genre <em>Chersotis</em> (Boisduval, 1840) regroupe une cinquantaine d'espèces de noctuelles principalement orophiles ou xérophiles, distribuées à travers l'Eurasie, dont l'épithète vient du grec <em>χερσότης</em> (<em>chersótēs</em>), désignant l'aridité des milieux qu'affectionnent ces papillons.
                </p>
            </div>

            {{-- Note typographique --}}
            <div class="mt-10 p-6 bg-white border border-slate-200 rounded-xl">
                <p class="text-sm text-slate-600">
                    <strong class="text-oreina-dark">Note typographique.</strong> Par convention partagée par les comités éditoriaux d'oreina, les noms de revues, magazines et plateformes (<em>oreina</em>, <em>Artemisiae</em>, <em>Lepis</em>, <em>Chersotis</em>) sont écrits en minuscules et en italique, sauf en début de phrase. Cette convention contribue à la cohérence visuelle de l'écosystème.
                </p>
            </div>
        </div>
    </section>

    {{-- Trajectoire continue --}}
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow sage mb-4 inline-flex">
                    <i class="icon icon-sage" data-lucide="trending-up"></i>
                    Une trajectoire continue
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">De la revue à l'acteur scientifique national</h2>
            </div>

            <div class="prose prose-lg text-slate-600 max-w-none">
                <p>
                    En presque vingt ans, oreina a connu une transformation importante. D'une association centrée sur l'édition d'un magazine, elle est devenue un acteur scientifique national, sous convention avec les principaux établissements publics (Office français de la biodiversité, Muséum national d'Histoire naturelle, PatriNat) et partie prenante du dispositif européen de suivi des pollinisateurs (EU-PoMS).
                </p>
                <p>
                    Cette évolution n'a rien renié de l'esprit fondateur. Les rencontres annuelles continuent d'être un moment-clé de la vie associative. Le bénévolat reste la colonne vertébrale de l'association. La vulgarisation et le partage des connaissances restent au cœur du projet, comme l'a réaffirmé l'assemblée générale extraordinaire du 20 décembre 2025, qui a structuré la séparation entre publication associative (<em>Lepis</em>) et revue scientifique (<em>Chersotis</em>) précisément pour mieux servir ces deux missions complémentaires.
                </p>
                <p>
                    Si oreina continue de porter avec assurance le nom d'un papillon discret des hautes altitudes, c'est aussi parce qu'elle assume cette identité à la fois savante et accessible, rigoureuse et conviviale, ancrée et ouverte, qui caractérise depuis l'origine la communauté française des lépidoptéristes.
                </p>
            </div>
        </div>
    </section>

    {{-- CTA final --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="compass"></i>Et maintenant ?</div>
                <h2>Et maintenant ?</h2>
                <p>Trois portes d'entrée selon ce qui vous a parlé dans cette histoire.</p>
                <div class="content-actions">
                    <a href="{{ route('hub.membership') }}" class="btn btn-primary">
                        <i class="icon icon-sage" data-lucide="heart-plus"></i>
                        Devenir membre
                    </a>
                    <a href="{{ route('hub.about') }}" class="btn btn-ghost-light">
                        <i class="icon icon-white" data-lucide="info"></i>
                        En savoir plus sur l'association
                    </a>
                    <a href="{{ route('hub.equipe') }}" class="btn btn-ghost-light">
                        <i class="icon icon-white" data-lucide="users-round"></i>
                        Rencontrer l'équipe
                    </a>
                </div>
            </article>
        </div>
    </section>
@endsection
