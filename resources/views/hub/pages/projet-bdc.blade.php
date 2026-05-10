@extends('layouts.hub')

@section('title', 'Traits de vie (BDC)')
@section('meta_description', 'oreina coordonne, en partenariat avec PatriNat (MNHN) et Arthropologia, la constitution d\'une base de données nationale sur les traits de vie des Lépidoptères de France métropolitaine.')

@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="flex-1">
                    <div class="eyebrow coral mb-4 inline-flex">
                        <i class="icon icon-coral" data-lucide="list-checks"></i>
                        Projet 3 / 5
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Traits de vie</h1>
                    <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl">
                        Structurer la connaissance écologique et biologique des Lépidoptères de France
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm bg-white p-5 rounded-2xl border border-slate-200 lg:min-w-[420px]">
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Programme</p>
                        <p class="font-bold text-oreina-dark">BDC</p>
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
                        <p class="font-bold text-oreina-dark">3 bénévoles + salariée</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Chapô --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-5 gap-12 items-center">
                <div class="lg:col-span-3 text-slate-600 space-y-6 order-2 lg:order-1">
                    <p class="text-xl leading-relaxed">
                        Quand vous observez un Gazé voleter au-dessus des haies en mai, ou que vous débusquez une chenille de Sphinx du troène sur votre lilas, vous collectez ce que les scientifiques appellent des <strong>traits de vie</strong>. Ces informations, aussi simples soient-elles, sont aujourd'hui éparpillées dans des dizaines d'ouvrages, d'articles scientifiques, de monographies régionales, et dans les observations accumulées sur <em>Artemisiae</em>.
                    </p>
                    <p class="leading-relaxed">
                        Le projet <strong>BDC</strong> vise à les compiler, les vérifier, les sourcer et les mettre à disposition de tous, dans une base de données nationale structurée et interrogeable, en partenariat avec <strong>PatriNat (MNHN)</strong> et l'association <strong>Arthropologia</strong>.
                    </p>
                </div>
                <div class="lg:col-span-2 order-1 lg:order-2" x-data="{ open: false }">
                    <figure class="rounded-3xl bg-white p-4 sm:p-6 shadow-lg border border-oreina-beige/40">
                        <button
                            type="button"
                            @click="open = true"
                            class="group block w-full relative overflow-hidden rounded-2xl cursor-zoom-in focus:outline-none focus:ring-2 focus:ring-oreina-coral focus:ring-offset-2"
                            aria-label="Agrandir le schéma des catégories de traits de vie"
                        >
                            <img
                                src="/images/bdc-traits-de-vie.png"
                                alt="Schéma des neuf grandes catégories de traits de vie d'un papillon : Reproduction, Alimentation, Développement, Phénologie, Mobilité, Morphologie, Comportement, Biogéographie et Habitat."
                                class="w-full h-auto rounded-2xl transition-transform duration-300 group-hover:scale-[1.02]"
                                loading="lazy"
                            >
                            <span class="absolute top-3 right-3 inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/90 text-oreina-dark shadow-md opacity-0 group-hover:opacity-100 transition" aria-hidden="true">
                                <i data-lucide="maximize-2" style="width:18px;height:18px"></i>
                            </span>
                        </button>
                        <figcaption class="text-xs text-slate-500 mt-3 text-center italic">Les 9 grandes catégories de traits de vie autour de l'espèce. <span class="not-italic text-oreina-coral font-semibold">Cliquez pour agrandir.</span></figcaption>
                    </figure>

                    {{-- Lightbox --}}
                    <div
                        x-show="open"
                        x-cloak
                        x-transition.opacity
                        @keydown.escape.window="open = false"
                        @click="open = false"
                        class="fixed inset-0 z-[100] bg-oreina-dark/90 backdrop-blur-sm flex items-center justify-center p-4 sm:p-8"
                        role="dialog"
                        aria-modal="true"
                        aria-label="Schéma agrandi des catégories de traits de vie"
                    >
                        <button
                            type="button"
                            @click.stop="open = false"
                            class="absolute top-4 right-4 sm:top-6 sm:right-6 inline-flex items-center justify-center w-11 h-11 rounded-full bg-white/95 hover:bg-white text-oreina-dark shadow-lg transition"
                            aria-label="Fermer"
                        >
                            <i data-lucide="x" style="width:22px;height:22px"></i>
                        </button>
                        <img
                            @click.stop
                            src="/images/bdc-traits-de-vie.png"
                            alt="Schéma agrandi des neuf grandes catégories de traits de vie d'un papillon."
                            class="max-w-full max-h-full w-auto h-auto rounded-2xl shadow-2xl"
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Qu'est-ce qu'un trait de vie --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="lightbulb"></i>
                    Pour comprendre
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Qu'est-ce qu'un trait de vie ?</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    Un trait de vie, c'est une caractéristique biologique ou écologique propre à une espèce. Pour un papillon, cela peut être : à quelle période vole-t-il ? Combien de générations produit-il dans l'année ? Sur quelle plante sa chenille se nourrit-elle ? À quel stade passe-t-il l'hiver, et où exactement ? Dans quel type de milieu vit-il, et jusqu'à quelle altitude ?
                </p>
                <p>
                    Ces caractéristiques ne sont pas des détails anecdotiques. Elles déterminent la manière dont chaque espèce répond à son environnement, et donc sa vulnérabilité aux changements en cours.
                </p>
                <p>
                    Un Grand Nègre des bois hiverne au stade chenille, niché dans les touffes de graminées. Une Petite Tortue passe l'hiver sous forme d'imago, réfugiée dans une grange ou un grenier. Cette différence de stratégie explique pourquoi ces deux espèces réagissent très différemment à un hiver doux, à la fauche d'une prairie en automne, ou à la fermeture d'un bâtiment agricole. Sans cette information précise, impossible d'interpréter correctement les variations observées sur le terrain.
                </p>
            </div>

            {{-- Encart : Damier de la succise --}}
            <div class="mt-10 p-6 sm:p-8 rounded-2xl border-l-4 border-oreina-yellow" style="background: rgba(237, 196, 66, 0.08);">
                <div class="flex items-start gap-4">
                    <div class="pub-card-icon gold flex-shrink-0">
                        <i class="icon icon-gold" data-lucide="microscope"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-3 text-lg">Des traits qui varient — et c'est là que ça devient intéressant</h3>
                        <p class="text-slate-600 mb-3">
                            L'une des particularités fascinantes des papillons, c'est que leurs traits de vie ne sont pas figés dans le marbre.
                        </p>
                        <p class="text-slate-600 mb-3">
                            Prenons le <strong>Damier de la succise</strong>. La bibliographie de référence cite <em>Succisa pratensis</em> comme plante dominante, avec quelques plantes secondaires selon les régions. Or, des observations de terrain ont récemment documenté des chenilles se nourrissant de <em>Valeriana officinalis</em>, une plante jusqu'alors inconnue dans le régime de l'espèce sur notre territoire. Cette découverte, recoupée avec des observations similaires en Bavière et en Suède, suggère une stratégie alimentaire plus souple qu'on ne le pensait.
                        </p>
                        <p class="text-slate-600">
                            Sans l'observation précise d'un naturaliste sur le terrain, cette information n'existerait pas. C'est pourquoi parler de traits de vie sans parler de géographie, c'est passer à côté de l'essentiel : un trait <strong>régionalisé</strong>, ancré dans une région biogéographique atlantique, continentale, méditerranéenne ou alpine, vaut infiniment plus qu'une moyenne nationale.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Pourquoi capitaliser dans une base de données --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="target"></i>
                    L'enjeu
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Pourquoi capitaliser dans une base de données</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    Compilées, vérifiées, sourcées et mises à disposition, les connaissances sur les traits de vie deviennent un outil puissant pour des usages très concrets, à plusieurs échelles.
                </p>
                <p>
                    <strong>Pour les lépidoptéristes</strong>, c'est d'abord un référentiel de confiance, évolutif, qu'on peut interroger, enrichir et corriger collectivement. Mais c'est surtout un outil qui transforme la pratique de terrain : connaître la phénologie régionalisée d'une espèce, sa plante-hôte locale, son stade d'hivernage, c'est savoir <strong>quand chercher, où chercher, et quoi chercher</strong>. Pour la prospection ciblée d'une espèce méconnue dans son département, pour la recherche chorologique, pour la confirmation d'une donnée historique douteuse, l'accès à une base de traits de vie consolidée fait gagner un temps considérable et démultiplie l'efficacité du travail naturaliste.
                </p>
                <p>
                    <strong>Pour les chercheurs</strong>, c'est une matière première indispensable pour modéliser les réponses des espèces au changement climatique, analyser les extinctions locales, ou identifier les groupes les plus vulnérables.
                </p>
                <p>
                    <strong>Pour les politiques publiques</strong>, c'est ce qui permet de répondre à des questions précises : combien de papillons diurnes liés aux prairies sèches ont plusieurs générations par an ? Quelles espèces de papillons nocturnes pollinisent les orchidées ? Quels papillons de jour sont univoltins, et donc plus vulnérables aux aléas saisonniers ? Toutes ces questions trouvent leur réponse dans une base structurée. Aucune n'est accessible si l'information reste éparpillée dans des dizaines de publications.
                </p>
                <p>
                    Le projet BDC répond directement aux besoins du <strong>Plan National d'Action Pollinisateurs</strong> et du dispositif européen <strong>EU-PoMS</strong>, qui nécessitent de caractériser précisément les espèces suivies.
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
                <h2 class="text-3xl font-bold text-oreina-dark">Une coordination scientifique nationale</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    PatriNat a développé une <strong>base de connaissance unifiée</strong> adossée à TAXREF (pour les espèces) et à HABREF (pour les habitats). Cette base sémantique permet de stocker et d'interroger trois grandes catégories de traits, dans un cadre ontologique cohérent à l'échelle nationale.
                </p>
                <p>
                    oreina s'est associée dès 2024 à PatriNat pour contribuer à l'alimentation de cette base sur les Lépidoptères. Le travail est mené en lien étroit avec l'<strong>association Arthropologia</strong>, dont le projet jumeau <em>BeeFunc</em>, lancé en 2026, structure les données équivalentes pour les 980 espèces d'abeilles sauvages de France. Les deux associations alignent leurs définitions et leurs méthodologies, pour que les deux bases puissent dialoguer dans la base de connaissance de PatriNat.
                </p>
                <p>
                    La méthodologie d'oreina repose sur trois principes structurants :
                </p>
                <ul class="list-disc pl-8 space-y-3 marker:text-oreina-green">
                    <li><span class="pl-2 inline-block">chaque <strong>valeur de trait est obligatoirement reliée à sa source bibliographique</strong>, via l'index bibliographique d'<em>Artemisiae</em>, et assortie d'un <strong>niveau de confiance</strong> ;</span></li>
                    <li><span class="pl-2 inline-block">les traits peuvent être <strong>régionalisés</strong> selon les grandes régions biogéographiques françaises (atlantique, continentale, méditerranéenne, alpine), pour rendre compte de la variabilité réelle ;</span></li>
                    <li><span class="pl-2 inline-block">une distinction est faite entre <strong>traits primaires</strong> (renseignés directement à partir de sources bibliographiques) et <strong>traits secondaires</strong> (calculés à partir des données disponibles, comme l'index de polyphagie HPI calculé sur le nombre d'espèces, de genres et de familles de plantes-hôtes).</span></li>
                </ul>
                <p>
                    Le projet est piloté au sein de l'association par trois bénévoles experts et la coordinatrice scientifique salariée.
                </p>
            </div>
        </div>
    </section>

    {{-- Chiffres-clés --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="bar-chart-3"></i>
                    Chiffres-clés
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">BDC en chiffres</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">L'ambition du projet et la structure de la base de connaissance.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-yellow/5 to-oreina-coral/5">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">5</p>
                    <p class="text-sm text-slate-600 leading-tight">grands traits prioritaires en cours de renseignement (phase 1)</p>
                </div>
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-yellow/5 to-oreina-coral/5">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">54</p>
                    <p class="text-sm text-slate-600 leading-tight">traits prévus à terme, organisés en 9 grandes catégories</p>
                </div>
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-yellow/5 to-oreina-coral/5">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">3</p>
                    <p class="text-sm text-slate-600 leading-tight">familles de traits dans l'ontologie PatriNat (intrinsèques, interactions biotiques, espèce-habitat)</p>
                </div>
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-green/5 to-oreina-turquoise/5">
                    <p class="text-4xl font-bold text-oreina-green mb-2">100 %</p>
                    <p class="text-sm text-slate-600 leading-tight">des espèces suivies par EU-PoMS à renseigner d'ici 2028</p>
                </div>
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-green/5 to-oreina-turquoise/5">
                    <p class="text-4xl font-bold text-oreina-green mb-2">2</p>
                    <p class="text-sm text-slate-600 leading-tight">bases jumelles dans la sphère pollinisateurs : BDC (papillons) et BeeFunc (abeilles)</p>
                </div>
                <div class="card p-6 text-center bg-gradient-to-br from-oreina-green/5 to-oreina-turquoise/5">
                    <p class="text-4xl font-bold text-oreina-green mb-2">980</p>
                    <p class="text-sm text-slate-600 leading-tight">espèces d'abeilles sauvages dans BeeFunc (Arthropologia), avec définitions alignées sur BDC</p>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">Sources : fiche projet BDC 2026, 2028 ; bulletin <em>Lepis</em> n°1 (avril 2026).</p>
        </div>
    </section>

    {{-- Trois familles de traits --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="layers"></i>
                    Architecture
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Trois familles de traits</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">L'ontologie développée par PatriNat structure les traits de vie en trois grandes familles complémentaires, qui couvrent l'essentiel de l'écologie d'une espèce.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="card p-6">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="zap"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-3">Traits intrinsèques</h3>
                    <p class="text-slate-600 text-sm mb-4">Caractéristiques propres à l'espèce, indépendantes de son environnement immédiat.</p>
                    <ul class="text-sm text-slate-500 space-y-1.5 list-disc pl-5 marker:text-oreina-green">
                        <li>Phénologie (période de vol, voltinisme)</li>
                        <li>Stade et lieu d'hivernage</li>
                        <li>Régime alimentaire larvaire et adulte</li>
                        <li>Comportement migratoire</li>
                        <li>Rythme circadien</li>
                        <li>Mode de reproduction</li>
                    </ul>
                </div>

                <div class="card p-6">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="users-round"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-3">Interactions biotiques</h3>
                    <p class="text-slate-600 text-sm mb-4">Relations entre l'espèce et les autres organismes vivants.</p>
                    <ul class="text-sm text-slate-500 space-y-1.5 list-disc pl-5 marker:text-oreina-coral">
                        <li>Plantes-hôtes des chenilles</li>
                        <li>Plantes butinées par les adultes</li>
                        <li>Relations avec les parasitoïdes</li>
                        <li>Myrmécophilie</li>
                        <li>Cannibalisme larvaire (cas particuliers)</li>
                        <li>Index de polyphagie (HPI)</li>
                    </ul>
                </div>

                <div class="card p-6">
                    <div class="pub-card-icon blue mb-4">
                        <i class="icon icon-blue" data-lucide="map-pin"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-3">Interactions espèce-habitat</h3>
                    <p class="text-slate-600 text-sm mb-4">Préférences et exigences écologiques de l'espèce.</p>
                    <ul class="text-sm text-slate-500 space-y-1.5 list-disc pl-5" style="--tw-marker-color: var(--blue);">
                        <li>Types de milieux fréquentés</li>
                        <li>Structure de l'habitat (complexe / simple)</li>
                        <li>Acidité, humidité du milieu</li>
                        <li>Amplitude altitudinale</li>
                        <li>Type d'aire de répartition</li>
                        <li>Capacités de dispersion</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Cinq traits prioritaires --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="target"></i>
                    Phase actuelle
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Cinq traits prioritaires</h2>
                <p class="text-slate-500 mt-3 max-w-2xl">La priorité de la phase 2026, 2028 porte sur cinq domaines fondamentaux, choisis pour leur valeur immédiate dans le cadre du Plan National d'Action Pollinisateurs et d'EU-PoMS.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="card p-5">
                    <div class="w-10 h-10 rounded-full bg-oreina-yellow text-oreina-dark flex items-center justify-center font-bold mb-3">1</div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Plantes-hôtes</h3>
                    <p class="text-xs text-slate-600">Espèces, genres, familles de plantes consommées par les chenilles. Index de polyphagie HPI.</p>
                </div>
                <div class="card p-5">
                    <div class="w-10 h-10 rounded-full bg-oreina-yellow text-oreina-dark flex items-center justify-center font-bold mb-3">2</div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Période de vol</h3>
                    <p class="text-xs text-slate-600">Phénologie de l'imago, par décade ou par mois. Régionalisable selon les régions biogéographiques.</p>
                </div>
                <div class="card p-5">
                    <div class="w-10 h-10 rounded-full bg-oreina-yellow text-oreina-dark flex items-center justify-center font-bold mb-3">3</div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Voltinisme</h3>
                    <p class="text-xs text-slate-600">Nombre de générations annuelles. Univoltin, bivoltin, multivoltin, semivoltin. Souvent variable selon la région.</p>
                </div>
                <div class="card p-5">
                    <div class="w-10 h-10 rounded-full bg-oreina-yellow text-oreina-dark flex items-center justify-center font-bold mb-3">4</div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Hivernage</h3>
                    <p class="text-xs text-slate-600">Stade auquel l'espèce passe l'hiver (œuf, chenille, chrysalide, imago) et lieu (litière, sous-bois, granges...).</p>
                </div>
                <div class="card p-5">
                    <div class="w-10 h-10 rounded-full bg-oreina-yellow text-oreina-dark flex items-center justify-center font-bold mb-3">5</div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Habitats et altitude</h3>
                    <p class="text-xs text-slate-600">Types de milieux fréquentés et amplitude altitudinale. Référentiel HABREF pour les habitats.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Comment se construit la base --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow sage mb-4 inline-flex">
                    <i class="icon icon-sage" data-lucide="route"></i>
                    Concrètement
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Comment se construit la base</h2>
                <p class="text-slate-500 mt-3 max-w-2xl">Chaque valeur de trait passe par un cycle de validation rigoureux, garantissant traçabilité et fiabilité.</p>
            </div>

            <div class="grid lg:grid-cols-4 gap-4">
                <div class="card p-6">
                    <div class="w-12 h-12 rounded-full bg-oreina-green text-white flex items-center justify-center font-bold mb-4 text-lg">1</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Sourcer</h3>
                    <p class="text-sm text-slate-600">Toute valeur de trait est obligatoirement reliée à une source : publication scientifique, monographie régionale, observation documentée sur <em>Artemisiae</em>. La source est ajoutée à l'index bibliographique.</p>
                </div>
                <div class="card p-6">
                    <div class="w-12 h-12 rounded-full bg-oreina-green text-white flex items-center justify-center font-bold mb-4 text-lg">2</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Renseigner</h3>
                    <p class="text-sm text-slate-600">La valeur est saisie dans la base de PatriNat avec mention de la source, du niveau de confiance et de la région biogéographique concernée le cas échéant.</p>
                </div>
                <div class="card p-6">
                    <div class="w-12 h-12 rounded-full bg-oreina-green text-white flex items-center justify-center font-bold mb-4 text-lg">3</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Valider</h3>
                    <p class="text-sm text-slate-600">Le réseau d'experts d'oreina expertise les valeurs renseignées, signale les contradictions entre sources et arbitre les choix de référence.</p>
                </div>
                <div class="card p-6">
                    <div class="w-12 h-12 rounded-full bg-oreina-green text-white flex items-center justify-center font-bold mb-4 text-lg">4</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Diffuser</h3>
                    <p class="text-sm text-slate-600">La base est interrogeable dans la base de connaissance unifiée de PatriNat, avec accès aux sources et niveaux de confiance pour chaque valeur.</p>
                </div>
            </div>
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
                <h2 class="text-3xl font-bold text-oreina-dark">Une boucle vertueuse pour les lépidoptéristes</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">L'intérêt opérationnel de la base BDC pour les utilisateurs d'<em>Artemisiae</em> tient à un cycle qui relie directement vos observations, l'enrichissement des fiches taxons et la prospection ciblée sur le terrain.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 items-start">
                <div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-4">Vos observations enrichissent les fiches taxons</h3>
                    <p class="text-slate-600 mb-4">
                        Chaque observation précisément documentée sur <em>Artemisiae</em> (chenille avec sa plante, adulte en train de butiner, station d'hivernage, individu observé hors période de vol connue) est susceptible d'alimenter la base BDC : soit en confirmant une valeur existante avec un appui géographique supplémentaire, soit en signalant une variation régionale, soit en révélant un trait jusqu'alors méconnu.
                    </p>
                    <p class="text-slate-600">
                        Une fois validées par le réseau d'experts, ces informations remontent dans les <strong>fiches taxons d'<em>Artemisiae</em></strong>, où chaque espèce dispose d'un onglet « traits de vie » consultable par tous : phénologie locale, plantes-hôtes documentées, voltinisme régionalisé, stade et lieu d'hivernage.
                    </p>
                </div>

                <div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-4">Et orientent vos prospections futures</h3>
                    <p class="text-slate-600 mb-4">
                        À l'inverse, les fiches taxons enrichies deviennent un <strong>outil de prospection ciblée</strong>. Pour rechercher une espèce méconnue dans votre département, l'accès aux traits consolidés vous indique précisément quand sortir, sur quels milieux orienter vos efforts, sur quelles plantes-hôtes inspecter les chenilles, à quelle altitude prospecter.
                    </p>
                    <p class="text-slate-600">
                        Pour la <strong>recherche chorologique</strong>, c'est un levier décisif : confirmer ou infirmer la présence d'une espèce dans une région où elle est attendue, documenter une expansion d'aire, vérifier une donnée historique douteuse, tout cela devient possible avec une base de référence à laquelle se confronter.
                    </p>
                </div>
            </div>

            {{-- Schéma du cycle --}}
            <div class="mt-12 card p-8 bg-white">
                <h3 class="text-lg font-bold text-oreina-dark mb-6 text-center">Le cycle vertueux entre observations, traits et terrain</h3>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-oreina-green to-oreina-teal rounded-2xl flex items-center justify-center">
                            <i data-lucide="camera" style="width:30px;height:30px;color:#fff"></i>
                        </div>
                        <h4 class="font-bold text-oreina-dark text-sm mb-1">1. Observation terrain</h4>
                        <p class="text-xs text-slate-500">Saisie sur <em>Artemisiae</em> avec contexte (plante, date, altitude, milieu).</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-oreina-coral to-oreina-yellow rounded-2xl flex items-center justify-center">
                            <i data-lucide="badge-check" style="width:30px;height:30px;color:#fff"></i>
                        </div>
                        <h4 class="font-bold text-oreina-dark text-sm mb-1">2. Trait validé</h4>
                        <p class="text-xs text-slate-500">Validation par le réseau d'experts, intégration dans la base BDC sourcée.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-oreina-turquoise to-oreina-blue rounded-2xl flex items-center justify-center">
                            <i data-lucide="file-text" style="width:30px;height:30px;color:#fff"></i>
                        </div>
                        <h4 class="font-bold text-oreina-dark text-sm mb-1">3. Fiche taxon enrichie</h4>
                        <p class="text-xs text-slate-500">Affichage dans l'onglet « traits de vie » de la fiche espèce d'<em>Artemisiae</em>.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-oreina-yellow to-oreina-coral rounded-2xl flex items-center justify-center">
                            <i data-lucide="search" style="width:30px;height:30px;color:#fff"></i>
                        </div>
                        <h4 class="font-bold text-oreina-dark text-sm mb-1">4. Prospection ciblée</h4>
                        <p class="text-xs text-slate-500">Recherche orientée et chorologie : nouvelles observations, et le cycle recommence.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Pour aller plus loin --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-oreina-dark">Pour aller plus loin</h2>
                <p class="text-slate-500 mt-2">Ressources externes, partenariats et publications associées au projet BDC.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="https://inpn.mnhn.fr/programme/referentiel-habitats-habref" target="_blank" rel="noopener" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-3">
                        <i class="icon icon-sage" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-green transition">HABREF</h3>
                    <p class="text-xs text-slate-500">Référentiel national des habitats, complémentaire de TAXREF.</p>
                </a>
                <a href="https://www.arthropologia.org/" target="_blank" rel="noopener" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon coral mb-3">
                        <i class="icon icon-coral" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-coral transition">Arthropologia</h3>
                    <p class="text-xs text-slate-500">Partenaire associatif du projet, porteur de la base BeeFunc sur les abeilles.</p>
                </a>
                <a href="{{ route('hub.lepis') }}" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon gold mb-3">
                        <i class="icon icon-gold" data-lucide="book-open"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-coral transition">Lepis n°1</h3>
                    <p class="text-xs text-slate-500">Article de présentation du projet dans le bulletin trimestriel d'oreina.</p>
                </a>
                <a href="/documents/rapport-activite-2024.pdf" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon blue mb-3">
                        <i class="icon icon-blue" data-lucide="file-bar-chart"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-blue transition">Rapport d'activité</h3>
                    <p class="text-xs text-slate-500">Rapport BDC 2024 d'oreina à l'OFB.</p>
                </a>
            </div>
        </div>
    </section>

    {{-- Vous contribuez peut-être déjà sans le savoir --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="helping-hand"></i>
                    Contribuer
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Vous contribuez peut-être déjà sans le savoir</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">
                    Une chenille photographiée en train de consommer les feuilles d'une plante, c'est une donnée plante-hôte. Un adulte observé en train de butiner, c'est une donnée phénologique et une contribution à la connaissance du rôle pollinisateur de l'espèce. Un imago photographié en janvier dans un grenier, c'est un stade et un lieu d'hivernage documentés. Chaque observation précise est un élément de plus à intégrer dans le tableau.
                </p>
            </div>

            <div class="space-y-4">
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon sage flex-shrink-0">
                        <i class="icon icon-sage" data-lucide="camera"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Documentez vos observations</h3>
                        <p class="text-slate-600 text-sm">Photo de chenille avec sa plante, observation phénologique précise, station d'hivernage : chaque détail compte. Saisissez vos observations sur <em>Artemisiae</em> avec le maximum d'informations contextuelles.</p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon coral flex-shrink-0">
                        <i class="icon icon-coral" data-lucide="book-marked"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Saisissez des valeurs bibliographiques</h3>
                        <p class="text-slate-600 text-sm">Pour celles et ceux qui souhaitent aller plus loin, une interface simplifiée permettra prochainement la saisie directe de valeurs issues d'ouvrages ou d'articles, accessible même sans maîtrise des standards scientifiques.</p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon gold flex-shrink-0">
                        <i class="icon icon-gold" data-lucide="lightbulb"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Apportez votre expertise</h3>
                        <p class="text-slate-600 text-sm">Vous êtes spécialiste d'un groupe taxonomique, d'une région ou d'un trait particulier (phénologie, plantes-hôtes, hivernage) ? Rejoignez le réseau d'experts validateurs.</p>
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
                <h2>Participer à BDC</h2>
                <p>Que vous soyez naturaliste de terrain, contributeur bibliographique ou expert validateur, votre apport renforce la couverture des traits de vie des Lépidoptères de France. Contactez-nous pour échanger.</p>
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
                <p class="text-slate-500 mt-3 max-w-3xl mx-auto">BDC s'inscrit dans la convention pluriannuelle 2026, 2028 d'oreina avec l'OFB, qui structure cinq projets scientifiques complémentaires.</p>
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
