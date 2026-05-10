@extends('layouts.hub')

@section('title', 'Référentiel taxonomique (TAXREF)')
@section('meta_description', 'oreina est partenaire référent de PatriNat pour la mise à jour de TAXREF, le référentiel taxonomique national, sur le groupe des Lépidoptères de France métropolitaine.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="flex-1">
                    <div class="eyebrow blue mb-4 inline-flex">
                        <i class="icon icon-blue" data-lucide="layers"></i>
                        Projet 1 / 5
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Référentiel taxonomique</h1>
                    <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl">
                        Maintenir à jour la liste des noms scientifiques des Lépidoptères de France
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm bg-white p-5 rounded-2xl border border-slate-200 lg:min-w-[420px]">
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Programme</p>
                        <p class="font-bold text-oreina-dark">TAXREF</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Partenaire</p>
                        <p class="font-bold text-oreina-dark">PatriNat (MNHN)</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Convention</p>
                        <p class="font-bold text-oreina-dark">OFB 2026, 2028</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Coordination</p>
                        <p class="font-bold text-oreina-dark">4 experts bénévoles</p>
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
                    <div class="rounded-3xl shadow-lg flex items-center justify-center bg-gradient-to-br from-oreina-blue/10 to-oreina-green/10 relative overflow-hidden" style="min-height: 340px;">
                        <i data-lucide="layers" style="width:140px;height:140px;color:var(--blue);opacity:0.85"></i>
                        <i data-lucide="tag" style="width:36px;height:36px;color:#2f694e;opacity:0.55;position:absolute;top:24px;right:32px"></i>
                        <i data-lucide="book-marked" style="width:36px;height:36px;color:#8b6c05;opacity:0.55;position:absolute;bottom:32px;left:28px"></i>
                        <i data-lucide="dna" style="width:32px;height:32px;color:var(--coral);opacity:0.55;position:absolute;bottom:36px;right:36px"></i>
                    </div>
                </div>
                <div class="lg:col-span-3 text-slate-600 space-y-6">
                    <p class="text-xl leading-relaxed">
                        Toute donnée d'observation a besoin d'un nom. Et pour que ce nom ait un sens pour tous, il doit être partagé, validé, mis à jour. C'est précisément le rôle d'un <strong>référentiel taxonomique</strong> : fournir à l'ensemble des acteurs (chercheurs, gestionnaires, naturalistes, institutions) une liste structurée, à jour, des noms scientifiques utilisés en France.
                    </p>
                    <p class="leading-relaxed">
                        Pour les Lépidoptères de France métropolitaine, c'est oreina qui assure ce travail, en partenariat avec PatriNat (MNHN, OFB, CNRS), dans le cadre du référentiel national <strong>TAXREF</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pourquoi --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="lightbulb"></i>
                    L'enjeu
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Pourquoi un référentiel taxonomique ?</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    Une espèce de papillon, ce n'est pas qu'un nom : c'est un nom <strong>et</strong> une définition scientifique. Cette définition évolue continuellement, avec l'avancée des connaissances. Une espèce peut être divisée en plusieurs (lorsqu'on découvre que ce qu'on prenait pour une seule espèce regroupe en réalité des entités distinctes), regroupée avec une autre, déplacée d'un genre à un autre, ou simplement renommée pour des raisons de priorité nomenclaturale. Avec l'avènement des <strong>outils moléculaires</strong>, ces évolutions s'accélèrent : le barcoding et les analyses phylogénétiques révèlent fréquemment des espèces cryptiques, des hybrides ou des erreurs de circonscription.
                </p>
                <p>
                    Sans référentiel partagé, ces évolutions deviennent un casse-tête. Une donnée saisie en 2010 sous un nom devenu invalide en 2024 doit pouvoir être reliée au nom actuel. Une carte de répartition réalisée à partir de plusieurs sources doit pouvoir agréger des données saisies sous des noms différents pour la même espèce. Une politique de conservation (Liste rouge, espèce protégée) doit pouvoir suivre une espèce même si son nom change.
                </p>
                <p>
                    <strong>TAXREF</strong> est le référentiel qui rend tout cela possible à l'échelle nationale. Il est porté par PatriNat (Office français de la biodiversité, Muséum national d'Histoire naturelle, CNRS) et publié en versions annuelles. C'est l'épine dorsale du Système d'Information sur la Biodiversité (SIB), qui irrigue l'INPN, le SINP, les politiques de conservation et l'ensemble des bases naturalistes nationales.
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
                <h2 class="text-3xl font-bold text-oreina-dark">Partenaire référent de PatriNat sur les Lépidoptères</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    Depuis plusieurs années, oreina est partenaire référent de PatriNat pour la mise à jour de TAXREF concernant les Lépidoptères de métropole. Concrètement, cela signifie qu'une <strong>équipe de quatre experts bénévoles</strong>, spécialistes de différents groupes (Rhopalocères, Géométridés, Noctuoidés, Microlépidoptères), assure :
                </p>
                <ul class="list-disc pl-8 space-y-3 marker:text-oreina-green">
                    <li><span class="pl-2 inline-block">une <strong>veille bibliographique continue</strong> sur les publications scientifiques mondiales décrivant de nouvelles espèces, de nouvelles combinaisons ou apportant de nouvelles données génétiques pertinentes pour la faune de France ;</span></li>
                    <li><span class="pl-2 inline-block">l'<strong>analyse des publications</strong> au regard du référentiel français existant : telle synonymie est-elle robuste, telle révision est-elle suffisamment documentée pour être intégrée, tel changement de combinaison concerne-t-il bien des taxons français ;</span></li>
                    <li><span class="pl-2 inline-block">la <strong>proposition de mises à jour</strong> via l'interface en ligne TAXREF-Web développée par PatriNat ;</span></li>
                    <li><span class="pl-2 inline-block">la <strong>rédaction de notes explicatives</strong> qui accompagnent chaque modification, pour que les utilisateurs du référentiel puissent comprendre les implications, et notamment savoir comment traiter leurs données antérieures.</span></li>
                </ul>
                <p>
                    Ce travail, essentiellement bénévole, est appuyé depuis 2024 par la coordinatrice scientifique salariée de l'association, qui assure le lien avec PatriNat et l'animation du groupe d'experts.
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
                <h2 class="text-3xl font-bold text-oreina-dark">TAXREF en chiffres (Lépidoptères de France métropole)</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">État du référentiel et bilan d'activité oreina, exercice 2024.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-green mb-2">44 649</p>
                    <p class="text-sm text-slate-600 leading-tight">noms scientifiques recensés dans TAXREF (tous Lépidoptères)</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-green mb-2">20 945</p>
                    <p class="text-sm text-slate-600 leading-tight">noms recensés pour la France métropolitaine</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-green mb-2">5 683</p>
                    <p class="text-sm text-slate-600 leading-tight">espèces valides documentées en métropole</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">286</p>
                    <p class="text-sm text-slate-600 leading-tight">nouveaux noms intégrés en 2024 (dont 228 à l'échelle spécifique)</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">15</p>
                    <p class="text-sm text-slate-600 leading-tight">espèces nouvelles signalées en France en 2024</p>
                </div>
                <div class="card p-6 text-center">
                    <p class="text-4xl font-bold text-oreina-coral mb-2">~400</p>
                    <p class="text-sm text-slate-600 leading-tight">nouveaux noms prévus sur 2026, 2028 (cible convention OFB)</p>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">Source : Rapport d'activité OFB 2024 d'oreina.</p>
        </div>
    </section>

    {{-- Méthodologie --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="git-branch"></i>
                    Méthodologie
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">De la publication à TAXREF</h2>
                <p class="text-slate-500 mt-3 max-w-2xl">Chaque modification de TAXREF suit un processus formalisé qui garantit la traçabilité scientifique de chaque changement.</p>
            </div>

            <div class="grid lg:grid-cols-5 gap-4">
                <div class="card p-5">
                    <div class="w-10 h-10 rounded-full bg-oreina-blue text-white flex items-center justify-center font-bold mb-3">1</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Détection</h3>
                    <p class="text-sm text-slate-600">Un membre de l'équipe repère, dans une publication récente, une information taxonomique pertinente.</p>
                </div>
                <div class="card p-5">
                    <div class="w-10 h-10 rounded-full bg-oreina-blue text-white flex items-center justify-center font-bold mb-3">2</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Analyse</h3>
                    <p class="text-sm text-slate-600">L'équipe évalue la robustesse de la proposition : qualité du journal, argumentation, pertinence pour la faune française.</p>
                </div>
                <div class="card p-5">
                    <div class="w-10 h-10 rounded-full bg-oreina-blue text-white flex items-center justify-center font-bold mb-3">3</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Documentation</h3>
                    <p class="text-sm text-slate-600">Les références sont intégrées dans DOC-Web, l'outil de gestion bibliographique de PatriNat. En 2024, 440 liens nom-référence ont été établis.</p>
                </div>
                <div class="card p-5">
                    <div class="w-10 h-10 rounded-full bg-oreina-blue text-white flex items-center justify-center font-bold mb-3">4</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Proposition</h3>
                    <p class="text-sm text-slate-600">Le changement est proposé via TAXREF-Web. Pour les modifications à fort impact, une note explicative est rédigée.</p>
                </div>
                <div class="card p-5">
                    <div class="w-10 h-10 rounded-full bg-oreina-blue text-white flex items-center justify-center font-bold mb-3">5</div>
                    <h3 class="font-bold text-oreina-dark mb-2">Diffusion</h3>
                    <p class="text-sm text-slate-600">Répercussion immédiate sur <em>Artemisiae</em> via Systema, intégration dans la version annuelle suivante de TAXREF, relais dans <em>Lepis</em> et article de synthèse annuel dans <em>Chersotis</em>.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Systema --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="book-marked"></i>
                    Outil associé
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Systema : la gestion du référentiel en continu</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">TAXREF est publié en versions stabilisées annuelles. Or l'expertise taxonomique, elle, ne s'arrête pas à un calendrier de versionnage : une publication scientifique paraît, une révision s'impose, et il faut pouvoir en tirer les conséquences sans attendre douze mois. C'est précisément ce que permet <strong>Systema</strong>, le module de gestion taxonomique développé par oreina sur la plateforme <em>Artemisiae</em>.</p>
            </div>

            <div class="grid lg:grid-cols-5 gap-8 items-start">
                <div class="lg:col-span-3">
                    <h3 class="text-xl font-bold text-oreina-dark mb-4">Une connexion temps réel à TAXREF-Web</h3>
                    <p class="text-slate-600 mb-4">Systema repose sur une <strong>API qui se connecte en temps réel à TAXREF-Web</strong>, l'outil applicatif de PatriNat. Cette architecture permet à oreina de gérer le référentiel des Lépidoptères de France <strong>en continu</strong>, sans dépendre du rythme des versions stabilisées annuelles. Une décision taxonomique prise par l'équipe d'experts est immédiatement répercutée dans Systema, et donc visible par les utilisateurs d'<em>Artemisiae</em>, qu'il s'agisse de saisie de données ou de consultation des fiches taxons.</p>
                    <p class="text-slate-600 mb-4">Cette gestion en continu se traduit par trois fonctionnalités structurantes.</p>

                    <div class="space-y-5 mt-6">
                        <div class="flex gap-4">
                            <div class="pub-card-icon gold flex-shrink-0">
                                <i class="icon icon-gold" data-lucide="refresh-cw"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-oreina-dark mb-1">Suivi nomenclatural en temps réel</h4>
                                <p class="text-sm text-slate-600">Liste systématique complète des Lépidoptères de France, organisée en familles, sous-familles, tribus et genres. Pour chaque taxon, une fiche indique le nom valide, l'auteur et l'année de description, les synonymes et l'historique nomenclatural, immédiatement à jour.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="pub-card-icon coral flex-shrink-0">
                                <i class="icon icon-coral" data-lucide="list-ordered"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-oreina-dark mb-1">Gestion de l'ordination des taxons</h4>
                                <p class="text-sm text-slate-600">L'ordre dans lequel les taxons sont présentés (au sein d'un genre, d'une tribu, d'une famille) reflète la classification scientifique du moment. Avec l'avancée des travaux phylogénétiques, cet ordre évolue. Systema permet de <strong>réordonner les taxons selon les usages scientifiques actuels</strong>, en suivant les publications de référence, sans attendre que ces évolutions soient reprises dans la version officielle suivante de TAXREF.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="pub-card-icon sage flex-shrink-0">
                                <i class="icon icon-sage" data-lucide="sticky-note"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-oreina-dark mb-1">Notes taxonomiques sourcées</h4>
                                <p class="text-sm text-slate-600">Chaque prise de position de l'équipe (acceptation d'une révision, refus d'une synonymie proposée, choix entre plusieurs combinaisons concurrentes) fait l'objet d'une <strong>note taxonomique</strong> argumentée. Ces notes sont rattachées aux <strong>références scientifiques de l'index bibliographique d'<em>Artemisiae</em></strong>, garantissant la traçabilité de chaque décision et permettant à l'utilisateur de remonter à la publication source.</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-slate-600 mt-6">L'ensemble de ces informations est consultable à la fois sur la <strong>liste systématique générale</strong> et directement sur les <strong>fiches taxons</strong> d'<em>Artemisiae</em>. Systema est librement accessible, sans inscription préalable.</p>

                    <a href="https://oreina.org/artemisiae/index.php?module=systema&action=liste" target="_blank" rel="noopener" class="btn btn-primary mt-6">
                        <i class="icon icon-sage" data-lucide="external-link"></i>
                        Consulter Systema sur Artemisiae
                    </a>
                </div>
                <div class="lg:col-span-2">
                    <div class="card p-0 overflow-hidden bg-gradient-to-br from-oreina-yellow/10 to-oreina-coral/10 aspect-[4/5] flex items-center justify-center">
                        <img src="/images/systema.png" alt="Capture d'écran de l'interface Systema sur Artemisiae" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center\'><p class=\'text-slate-500 text-sm font-bold\'>Aperçu Systema</p><p class=\'text-slate-400 text-xs mt-1\'>Liste systématique des Lépidoptères de France</p></div>'">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Pour aller plus loin --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-oreina-dark">Pour aller plus loin</h2>
                <p class="text-slate-500 mt-2">Ressources externes et publications associées au projet TAXREF.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="https://inpn.mnhn.fr/programme/referentiel-taxonomique-taxref" target="_blank" rel="noopener" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-3">
                        <i class="icon icon-sage" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-green transition">TAXREF officiel</h3>
                    <p class="text-xs text-slate-500">Présentation du référentiel sur le site de l'INPN.</p>
                </a>
                <a href="https://taxref.mnhn.fr/" target="_blank" rel="noopener" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon blue mb-3">
                        <i class="icon icon-blue" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-blue transition">TAXREF-Web</h3>
                    <p class="text-xs text-slate-500">Interface de consultation et de proposition par PatriNat.</p>
                </a>
                <a href="{{ route('journal.home') }}" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon coral mb-3">
                        <i class="icon icon-coral" data-lucide="book-open"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-coral transition">Articles de synthèse</h3>
                    <p class="text-xs text-slate-500">Synthèses annuelles publiées dans la revue <em>Chersotis</em>.</p>
                </a>
                <a href="/documents/rapport-activite-2024.pdf" class="card p-5 hover:shadow-lg transition group">
                    <div class="pub-card-icon gold mb-3">
                        <i class="icon icon-gold" data-lucide="file-text"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-1 group-hover:text-oreina-blue transition">Rapport d'activité</h3>
                    <p class="text-xs text-slate-500">Rapport TAXREF 2024 d'oreina à l'OFB.</p>
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
                <p class="text-slate-500 mt-3 max-w-2xl">Le travail taxonomique d'oreina repose entièrement sur l'expertise bénévole. Plusieurs voies de contribution sont possibles, selon votre niveau de spécialisation.</p>
            </div>

            <div class="space-y-4">
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon sage flex-shrink-0">
                        <i class="icon icon-sage" data-lucide="book-open"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Vous êtes lépidoptériste expérimenté ?</h3>
                        <p class="text-slate-600 text-sm">Si vous suivez régulièrement une famille ou un groupe (Microlépidoptères, Géomètres, Noctuelles…) et que vous lisez la littérature scientifique récente, votre contribution à la veille bibliographique nous serait précieuse.</p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon coral flex-shrink-0">
                        <i class="icon icon-coral" data-lucide="message-square"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Vous avez identifié une publication récente importante ?</h3>
                        <p class="text-slate-600 text-sm">Signalez-nous l'article via la page Contact, en précisant les taxons concernés. Nous l'intégrerons à notre veille.</p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4">
                    <div class="pub-card-icon gold flex-shrink-0">
                        <i class="icon icon-gold" data-lucide="alert-circle"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-oreina-dark mb-1">Vous avez détecté une incohérence dans Systema ?</h3>
                        <p class="text-slate-600 text-sm">Toutes les remontées d'utilisateurs sont précieuses : elles permettent d'améliorer la qualité du référentiel.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA bandeau --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="message-circle"></i>Une question ou une idée ?</div>
                <h2>Participer à TAXREF</h2>
                <p>Que vous souhaitiez signaler une publication, contribuer à la veille bibliographique ou rejoindre l'association, nous sommes à votre écoute.</p>
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
                <p class="text-slate-500 mt-3 max-w-3xl mx-auto">oreina conduit cinq projets scientifiques structurants dans le cadre de sa convention 2026-2028 avec l'OFB. TAXREF est l'un d'entre eux. Les quatre autres lui sont étroitement articulés.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
