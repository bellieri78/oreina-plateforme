@extends('layouts.hub')

@section('title', 'Labo Lépidos - Outil pédagogique d\'identification')
@section('meta_description', 'Les Labo Lépidos d\'oreina : webinaires courts dédiés aux complexes d\'espèces de Lépidoptères. Replays, supports téléchargeables, et propositions ouvertes à la communauté.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('hub.projets.ident') }}" class="text-sm text-slate-500 hover:text-oreina-coral inline-flex items-center gap-2 transition">
                    <i data-lucide="arrow-left" style="width:14px;height:14px"></i>
                    Retour au projet IDENT
                </a>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="flex-1">
                    <div class="eyebrow coral mb-4 inline-flex">
                        <i class="icon icon-coral" data-lucide="flask-conical"></i>
                        Outil IDENT
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Labo Lépidos</h1>
                    <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl">
                        Décortiquer les complexes d'espèces, en direct, avec un spécialiste — et garder la trace pour s'y replonger
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm bg-white p-5 rounded-2xl border border-slate-200 lg:min-w-[420px]">
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Format</p>
                        <p class="font-bold text-oreina-dark">Webinaire 30-40 min</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Cible</p>
                        <p class="font-bold text-oreina-dark">Observateurs &amp; validateurs</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Accès</p>
                        <p class="font-bold text-oreina-dark">Libre, replays compris</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Rattachement</p>
                        <p class="font-bold text-oreina-dark">Projet IDENT</p>
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
                    <div class="rounded-3xl shadow-lg flex items-center justify-center bg-gradient-to-br from-oreina-coral/15 to-oreina-yellow/10 relative overflow-hidden" style="min-height: 340px;">
                        <i data-lucide="flask-conical" style="width:140px;height:140px;color:var(--coral);opacity:0.85"></i>
                        <i data-lucide="play-circle" style="width:36px;height:36px;color:var(--blue);opacity:0.55;position:absolute;top:24px;right:32px"></i>
                        <i data-lucide="file-down" style="width:36px;height:36px;color:#8b6c05;opacity:0.55;position:absolute;bottom:32px;left:28px"></i>
                        <i data-lucide="users" style="width:32px;height:32px;color:#2f694e;opacity:0.55;position:absolute;bottom:36px;right:36px"></i>
                    </div>
                </div>
                <div class="lg:col-span-3 text-slate-600 space-y-6">
                    <p class="text-xl leading-relaxed">
                        Les <strong>Labo Lépidos</strong> sont une déclinaison opérationnelle du projet IDENT : à intervalles réguliers, oreina ouvre la salle à un spécialiste qui prend en charge un agrégat précis et le décortique pas à pas, en direct, avec questions du public.
                    </p>
                    <p class="leading-relaxed">
                        L'enjeu est double : transmettre les critères de détermination de manière vivante, et constituer un <strong>corpus de supports réutilisables</strong> — diaporama, replay, synthèse PDF — directement mobilisable lors de la saisie sur <em>Artemisiae</em> ou pendant les sessions de validation.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Comment ça marche --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="lightbulb"></i>
                    Le concept
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Un format court, dense, et qui laisse une trace</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Chaque session suit la même charpente : un agrégat ou un complexe d'espèces, un cadrage taxonomique clair, les critères externes, les critères internes quand ils sont nécessaires, l'apport éventuel du barcoding, et des clés pratiques pour la saisie sur <em>Artemisiae</em>.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="card p-6 bg-gradient-to-br from-oreina-coral/5 to-white border-t-4 border-oreina-coral">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl font-bold text-oreina-coral">1</span>
                        <i data-lucide="target" style="width:24px;height:24px;color:var(--coral);opacity:0.6"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Cadrer le complexe</h3>
                    <p class="text-xs text-slate-600">Vue d'ensemble des espèces, statut de fréquence, contexte taxonomique, révisions récentes.</p>
                </div>
                <div class="card p-6 bg-gradient-to-br from-oreina-yellow/10 to-white border-t-4 border-oreina-yellow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl font-bold" style="color:#8b6c05">2</span>
                        <i data-lucide="scan-eye" style="width:24px;height:24px;color:#8b6c05;opacity:0.6"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Critères externes</h3>
                    <p class="text-xs text-slate-600">Habitus, dessins, dimorphisme, variations géographiques. Ce qui se voit sans manipulation.</p>
                </div>
                <div class="card p-6 bg-gradient-to-br from-oreina-blue/5 to-white border-t-4 border-oreina-blue">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl font-bold text-oreina-blue">3</span>
                        <i data-lucide="microscope" style="width:24px;height:24px;color:var(--blue);opacity:0.6"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Examens internes</h3>
                    <p class="text-xs text-slate-600">Quand et comment passer aux genitalia. Apport du barcoding ADN sur les cas-limites.</p>
                </div>
                <div class="card p-6 bg-gradient-to-br from-oreina-green/5 to-white border-t-4 border-oreina-green">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl font-bold text-oreina-green">4</span>
                        <i data-lucide="wand-sparkles" style="width:24px;height:24px;color:var(--sage);opacity:0.6"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Clés pour Artemisiae</h3>
                    <p class="text-xs text-slate-600">Synthèse opérationnelle pour la saisie et la validation : ce qu'il faut renseigner, ce qu'il faut vérifier.</p>
                </div>
            </div>

            <div class="mt-8 p-6 bg-slate-50 rounded-xl border border-slate-200">
                <p class="text-sm text-slate-600">
                    <strong class="text-oreina-dark">Trois livrables systématiques.</strong> Chaque Labo Lépido produit (1) un webinaire en direct avec replay, (2) un diaporama téléchargeable au format PDF, et (3) une synthèse-clé d'identification utilisable comme aide-mémoire de terrain ou de saisie. L'ensemble est versé au corpus documentaire IDENT et reste librement accessible, sans inscription.
                </p>
            </div>
        </div>
    </section>

    {{-- Catalogue des Labo Lépidos --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="library"></i>
                    Catalogue
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Les Labo Lépidos disponibles</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">La série démarre en 2026 avec le complexe <em>Hoplodrina</em>, choisi pour son taux d'erreur élevé sur <em>Artemisiae</em> et la révision taxonomique récente qui en fait un cas pédagogique idéal. D'autres sessions sont en préparation.</p>
            </div>

            {{-- Labo disponible --}}
            <div class="card p-8 bg-white border-l-4 border-oreina-coral mb-6">
                <div class="grid lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="text-xs font-bold uppercase tracking-wide bg-oreina-green/15 text-oreina-green px-3 py-1 rounded-full">Disponible</span>
                            <span class="text-xs text-slate-400">N° 01 — Macrohétérocères / Noctuidae</span>
                        </div>
                        <h3 class="text-2xl font-bold text-oreina-dark mb-2">Le complexe <em>Hoplodrina</em> en France</h3>
                        <p class="text-sm text-slate-500 mb-4">Détermination et validation des données sur Artemisiae</p>
                        <p class="text-slate-600 leading-relaxed mb-4">
                            Sept espèces du genre <em>Hoplodrina</em> sont aujourd'hui reconnues en France, dont une — <em>H. alsinides</em> — n'a été restaurée comme bonne espèce qu'en 2020 après près d'un siècle de synonymie. Le taux d'erreur d'identification estimé sur <em>Artemisiae</em> dépasse 30 %, ce qui en fait un complexe prioritaire pour la qualification des données nocturnes, et notamment pour le suivi EU-PoMS.
                        </p>
                        <p class="text-slate-600 leading-relaxed mb-6">
                            Cette session passe en revue les sept espèces, les caractères externes utilisables, les cas où la dissection est indispensable, et l'apport du barcoding pour les cas-limites. Une synthèse opérationnelle clôt la séance avec les bons réflexes à avoir au moment de la saisie.
                        </p>

                        <div class="flex flex-wrap gap-3">
                            <a href="/labo-lepidos/hoplodrina/replay" class="btn btn-primary">
                                <i class="icon icon-sage" data-lucide="play-circle"></i>
                                Voir le replay
                            </a>
                            <a href="/labo-lepidos/hoplodrina/diaporama.pdf" class="btn btn-secondary">
                                <i class="icon icon-coral" data-lucide="file-down"></i>
                                Diaporama (PDF)
                            </a>
                            <a href="/labo-lepidos/hoplodrina/synthese.pdf" class="btn btn-secondary">
                                <i class="icon icon-coral" data-lucide="clipboard-list"></i>
                                Synthèse-clé
                            </a>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-xl">
                        <p class="text-xs text-slate-400 uppercase tracking-wide mb-3">Fiche technique</p>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-slate-400 text-xs">Animation</dt>
                                <dd class="font-bold text-oreina-dark">Référent du COTECH IDENT</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 text-xs">Durée</dt>
                                <dd class="font-bold text-oreina-dark">35 min + questions</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 text-xs">Niveau</dt>
                                <dd class="font-bold text-oreina-dark">Intermédiaire à confirmé</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 text-xs">Difficulté typologique</dt>
                                <dd class="font-bold text-oreina-dark">T3 à T5 selon les paires</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 text-xs">Stade biologique</dt>
                                <dd class="font-bold text-oreina-dark">Imago</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Labos à venir --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="card p-6 bg-white/60 border border-dashed border-slate-300">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wide bg-oreina-yellow/30 px-2 py-1 rounded-full" style="color:#8b6c05">En préparation</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Sujets candidats</h3>
                    <p class="text-xs text-slate-600">Plusieurs complexes prioritaires sont identifiés par le COTECH IDENT : Hespéries du genre <em>Pyrgus</em>, Mélitées, <em>Euxoa</em> spp., géomètres du genre <em>Eupithecia</em>… Le calendrier 2026-2028 se construit au fil des disponibilités d'expertise au sein du réseau.</p>
                </div>
                <div class="card p-6 bg-white/60 border border-dashed border-slate-300">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wide bg-oreina-blue/15 text-oreina-blue px-2 py-1 rounded-full">Appel ouvert</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Et le vôtre ?</h3>
                    <p class="text-xs text-slate-600">Le catalogue est <strong>co-construit avec la communauté</strong>. Vous pouvez proposer un complexe à traiter, ou animer une session sur un groupe que vous maîtrisez. Voir les modalités plus bas.</p>
                </div>
                <div class="card p-6 bg-white/60 border border-dashed border-slate-300">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wide bg-oreina-green/15 text-oreina-green px-2 py-1 rounded-full">Calendrier</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Une session par trimestre</h3>
                    <p class="text-xs text-slate-600">Le rythme cible est d'environ un Labo Lépido par trimestre. Les dates sont annoncées dans la lettre d'information et sur la page d'accueil d'oreina.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Animer un Labo Lépido --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-5 gap-10">
                <div class="lg:col-span-2">
                    <div class="eyebrow sage mb-4 inline-flex">
                        <i class="icon icon-sage" data-lucide="presentation"></i>
                        Pour les spécialistes
                    </div>
                    <h2 class="text-3xl font-bold text-oreina-dark mb-4">Animer une session</h2>
                    <p class="text-slate-600 leading-relaxed">
                        Vous identifiez régulièrement un agrégat critique, ou vous avez publié récemment sur un groupe difficile : votre expertise a sa place dans les Labo Lépidos. L'objectif n'est pas un exposé académique, mais un transfert opérationnel vers les observateurs et validateurs d'<em>Artemisiae</em>.
                    </p>
                </div>
                <div class="lg:col-span-3 space-y-4">
                    <div class="card p-5 flex gap-4">
                        <div class="pub-card-icon sage flex-shrink-0">
                            <i class="icon icon-sage" data-lucide="layout-template"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-1 text-sm">Une trame fournie</h3>
                            <p class="text-slate-600 text-xs">oreina met à disposition un canevas de présentation et un gabarit graphique. Vous vous concentrez sur le contenu scientifique ; nous prenons en charge la mise en forme et la régie technique.</p>
                        </div>
                    </div>
                    <div class="card p-5 flex gap-4">
                        <div class="pub-card-icon coral flex-shrink-0">
                            <i class="icon icon-coral" data-lucide="image"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-1 text-sm">Iconographie mutualisée</h3>
                            <p class="text-slate-600 text-xs">Vous pouvez puiser dans la base photographique d'oreina, ou apporter vos propres clichés (avec mention systématique de l'auteur sur chaque support).</p>
                        </div>
                    </div>
                    <div class="card p-5 flex gap-4">
                        <div class="pub-card-icon gold flex-shrink-0">
                            <i class="icon icon-gold" data-lucide="message-square"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-1 text-sm">Un binôme d'animation</h3>
                            <p class="text-slate-600 text-xs">Un membre du COTECH IDENT vous accompagne pour la préparation et tient la modération des questions pendant la session, pour que vous puissiez vous concentrer sur le propos.</p>
                        </div>
                    </div>
                    <div class="card p-5 flex gap-4">
                        <div class="pub-card-icon blue flex-shrink-0">
                            <i class="icon icon-blue" data-lucide="award"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-oreina-dark mb-1 text-sm">Une trace pérenne</h3>
                            <p class="text-slate-600 text-xs">Le replay et les supports portent votre nom. Ils restent référencés dans le corpus IDENT et peuvent être cités à part entière dans vos travaux.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Proposer un sujet --}}
    <section id="proposer" class="py-16 bg-slate-50 scroll-mt-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="message-circle-plus"></i>
                    Proposer
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Proposer un sujet ou une animation</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">Vous voulez animer une session, ou simplement signaler un agrégat qui mériterait d'être traité ? Quelques lignes suffisent. Le COTECH IDENT examine les propositions et revient vers vous.</p>
            </div>

            @if(session('labo_success'))
                <div class="mb-6 p-5 rounded-xl bg-oreina-green/10 border border-oreina-green/30 flex items-start gap-3">
                    <i data-lucide="check-circle-2" style="width:22px;height:22px;color:#2f694e;flex-shrink:0;margin-top:2px"></i>
                    <div class="text-sm text-oreina-dark">
                        <strong>Merci !</strong> {{ session('labo_success') }}
                    </div>
                </div>
            @endif

            <div class="card p-8 bg-white">
                <form action="{{ route('hub.outils.labo-lepidos.proposer') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="nom" class="block text-sm font-bold text-oreina-dark mb-2">Nom et prénom <span class="text-oreina-coral">*</span></label>
                            <input type="text" id="nom" name="nom" required value="{{ old('nom') }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-oreina-coral focus:border-oreina-coral outline-none transition">
                            @error('nom')<p class="mt-1 text-xs text-oreina-coral">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-bold text-oreina-dark mb-2">Adresse e-mail <span class="text-oreina-coral">*</span></label>
                            <input type="email" id="email" name="email" required value="{{ old('email') }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-oreina-coral focus:border-oreina-coral outline-none transition">
                            @error('email')<p class="mt-1 text-xs text-oreina-coral">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-oreina-dark mb-2">Type de proposition <span class="text-oreina-coral">*</span></label>
                        <div class="grid sm:grid-cols-2 gap-3">
                            <label class="flex items-start gap-3 p-4 border border-slate-300 rounded-lg cursor-pointer hover:border-oreina-coral hover:bg-slate-50 transition">
                                <input type="radio" name="type_proposition" value="animer" required {{ old('type_proposition') === 'animer' ? 'checked' : '' }} class="mt-1">
                                <div>
                                    <p class="font-bold text-oreina-dark text-sm">Je propose d'animer un Labo Lépido</p>
                                    <p class="text-xs text-slate-500 mt-1">Sur un groupe que je maîtrise.</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 p-4 border border-slate-300 rounded-lg cursor-pointer hover:border-oreina-coral hover:bg-slate-50 transition">
                                <input type="radio" name="type_proposition" value="suggerer" required {{ old('type_proposition') === 'suggerer' ? 'checked' : '' }} class="mt-1">
                                <div>
                                    <p class="font-bold text-oreina-dark text-sm">Je suggère un sujet à traiter</p>
                                    <p class="text-xs text-slate-500 mt-1">Sans m'engager à l'animer.</p>
                                </div>
                            </label>
                        </div>
                        @error('type_proposition')<p class="mt-2 text-xs text-oreina-coral">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="sujet" class="block text-sm font-bold text-oreina-dark mb-2">Complexe ou agrégat concerné <span class="text-oreina-coral">*</span></label>
                        <input type="text" id="sujet" name="sujet" required value="{{ old('sujet') }}" placeholder="Ex. : Hespéries du genre Pyrgus, Euxoa spp., Mélitées du complexe athalia…" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-oreina-coral focus:border-oreina-coral outline-none transition">
                        @error('sujet')<p class="mt-1 text-xs text-oreina-coral">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="motivation" class="block text-sm font-bold text-oreina-dark mb-2">Motivation et contexte <span class="text-oreina-coral">*</span></label>
                        <textarea id="motivation" name="motivation" rows="5" required placeholder="Pourquoi ce complexe vous semble-t-il prioritaire ? Quels critères vous paraissent les plus discriminants ? Avez-vous une expérience d'identification ou de validation sur ce groupe ?" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-oreina-coral focus:border-oreina-coral outline-none transition">{{ old('motivation') }}</textarea>
                        @error('motivation')<p class="mt-1 text-xs text-oreina-coral">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="ressources" class="block text-sm font-bold text-oreina-dark mb-2">Ressources disponibles <span class="text-slate-400 font-normal">(facultatif)</span></label>
                        <textarea id="ressources" name="ressources" rows="3" placeholder="Photographies, publications, dissections déjà documentées, base de données personnelle, séquences génétiques, contacts utiles…" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-oreina-coral focus:border-oreina-coral outline-none transition">{{ old('ressources') }}</textarea>
                    </div>

                    <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg">
                        <input type="checkbox" id="rgpd" name="rgpd" required {{ old('rgpd') ? 'checked' : '' }} class="mt-1">
                        <label for="rgpd" class="text-xs text-slate-600">
                            J'accepte qu'oreina conserve les informations transmises pour traiter ma proposition. Mes coordonnées ne seront pas diffusées en dehors du COTECH IDENT et pourront être supprimées sur simple demande.
                        </label>
                    </div>
                    @error('rgpd')<p class="text-xs text-oreina-coral">{{ $message }}</p>@enderror

                    {{-- Anti-bot Cloudflare Turnstile --}}
                    @turnstile
                    @error('cf-turnstile-response')<p class="text-xs text-oreina-coral">{{ $message }}</p>@enderror

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon icon-sage" data-lucide="send"></i>
                            Envoyer ma proposition
                        </button>
                        <a href="{{ route('hub.contact') }}" class="btn btn-secondary">
                            <i class="icon icon-coral" data-lucide="mail"></i>
                            Préférer un contact direct
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="help-circle"></i>
                    Questions fréquentes
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">À savoir avant de proposer</h2>
            </div>

            <div class="space-y-4">
                <details class="card p-6 group">
                    <summary class="font-bold text-oreina-dark cursor-pointer flex items-center justify-between">
                        <span>Faut-il être adhérent d'oreina pour proposer ou animer&nbsp;?</span>
                        <i data-lucide="chevron-down" class="text-slate-400 group-open:rotate-180 transition"></i>
                    </summary>
                    <p class="mt-4 text-sm text-slate-600">Non. Les Labo Lépidos sont ouverts à toute la communauté lépidoptérologique francophone, adhérents et non-adhérents. L'adhésion reste évidemment un soutien apprécié, mais elle n'est pas un prérequis.</p>
                </details>
                <details class="card p-6 group">
                    <summary class="font-bold text-oreina-dark cursor-pointer flex items-center justify-between">
                        <span>Quels sujets ont le plus de chances d'être retenus&nbsp;?</span>
                        <i data-lucide="chevron-down" class="text-slate-400 group-open:rotate-180 transition"></i>
                    </summary>
                    <p class="mt-4 text-sm text-slate-600">Les agrégats à fort taux d'erreur sur <em>Artemisiae</em>, ceux qui concernent des espèces du suivi EU-PoMS, et les complexes affectés par une révision taxonomique récente sont en priorité. Mais un sujet plus pointu peut tout à fait être retenu s'il répond à un besoin de validation identifié, ou s'il documente un piège classique.</p>
                </details>
                <details class="card p-6 group">
                    <summary class="font-bold text-oreina-dark cursor-pointer flex items-center justify-between">
                        <span>Qui valide les contenus&nbsp;?</span>
                        <i data-lucide="chevron-down" class="text-slate-400 group-open:rotate-180 transition"></i>
                    </summary>
                    <p class="mt-4 text-sm text-slate-600">Le COTECH IDENT relit chaque support avant publication, et la mise en cohérence avec la typologie PatriNat (T1-T5) est systématiquement vérifiée. L'objectif n'est pas le filtre, mais l'alignement avec le reste du corpus IDENT — y compris les fiches d'<em>Artemisiae</em> et le projet QUALIF.</p>
                </details>
                <details class="card p-6 group">
                    <summary class="font-bold text-oreina-dark cursor-pointer flex items-center justify-between">
                        <span>Quelle est la différence avec un article de Chersotis ou de Lepis&nbsp;?</span>
                        <i data-lucide="chevron-down" class="text-slate-400 group-open:rotate-180 transition"></i>
                    </summary>
                    <p class="mt-4 text-sm text-slate-600">Un Labo Lépido est un format <strong>pédagogique</strong> et <strong>opérationnel</strong>, pas un format de publication scientifique. Il peut tout à fait s'appuyer sur un article publié, ou être l'occasion d'identifier un sujet qui mériterait ensuite un article dans <em>Chersotis</em>. Les deux formats sont complémentaires.</p>
                </details>
                <details class="card p-6 group">
                    <summary class="font-bold text-oreina-dark cursor-pointer flex items-center justify-between">
                        <span>Les supports sont-ils sous quelle licence&nbsp;?</span>
                        <i data-lucide="chevron-down" class="text-slate-400 group-open:rotate-180 transition"></i>
                    </summary>
                    <p class="mt-4 text-sm text-slate-600">Les supports produits dans le cadre des Labo Lépidos sont diffusés sous licence <strong>Creative Commons BY-NC-SA 4.0</strong> par défaut, avec attribution à l'auteur de la session et à oreina. Les iconographies tierces conservent la licence de leur auteur, indiquée au cas par cas.</p>
                </details>
            </div>
        </div>
    </section>

    {{-- CTA bandeau --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="flask-conical"></i>Rejoindre la dynamique</div>
                <h2>Faire grandir le catalogue</h2>
                <p>Chaque Labo Lépido renforce la qualité des identifications saisies sur <em>Artemisiae</em>, et donne aux validateurs des repères opérationnels. Plus le catalogue grandit, plus la donnée naturaliste française gagne en fiabilité. Votre expertise — et vos suggestions — sont attendues.</p>
                <div class="content-actions">
                    <a href="#proposer" class="btn btn-primary">
                        <i class="icon icon-sage" data-lucide="message-circle-plus"></i>
                        Faire une proposition
                    </a>
                    <a href="{{ route('hub.projets.ident') }}" class="btn btn-ghost-light">
                        <i class="icon icon-white" data-lucide="arrow-left"></i>
                        Retour au projet IDENT
                    </a>
                </div>
            </article>
        </div>
    </section>

    {{-- Pour aller plus loin --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-oreina-dark">Articulation avec les autres outils oreina</h2>
                <p class="text-slate-500 mt-3 max-w-3xl mx-auto">Les Labo Lépidos s'inscrivent dans le projet IDENT et dialoguent étroitement avec les autres briques de la convention OFB 2026, 2028.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('hub.projets.ident') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon gold mb-4">
                        <i class="icon icon-gold" data-lucide="search"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-coral transition">Projet IDENT</h3>
                    <p class="text-xs text-slate-500">Le cadre méthodologique : typologie de la difficulté, agrégats, sympatrie.</p>
                </a>
                <a href="{{ route('hub.projets.qualif') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="badge-check"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-green transition">Projet QUALIF</h3>
                    <p class="text-xs text-slate-500">La qualification des données d'observation, alimentée par les supports IDENT.</p>
                </a>
                <a href="https://oreina.org/artemisiae/" target="_blank" rel="noopener" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-coral transition">Artemisiae</h3>
                    <p class="text-xs text-slate-500">Le portail de saisie où les supports sont mobilisés au quotidien.</p>
                </a>
                <a href="{{ route('journal.home') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon blue mb-4">
                        <i class="icon icon-blue" data-lucide="book-open"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-blue transition">Chersotis</h3>
                    <p class="text-xs text-slate-500">La revue scientifique d'oreina, complémentaire pour les contenus de fond.</p>
                </a>
            </div>
        </div>
    </section>
@endsection
