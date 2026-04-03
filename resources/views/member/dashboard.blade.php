@extends('layouts.member')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord membre')
@section('page-subtitle', 'Un cockpit naturaliste simple, lisible et orienté action')

@section('topbar-actions')
    <button class="btn btn-secondary"><i data-lucide="database"></i>Explorer Artemisiae</button>
    <a href="{{ route('member.work-groups') }}" class="btn btn-primary"><i data-lucide="users"></i>Accès aux groupes</a>
@endsection

@section('content')

    {{-- ═══════════════════════════════════════════════════
         WELCOME SECTION  (1.35fr + 0.95fr)
    ═══════════════════════════════════════════════════ --}}
    <section class="welcome">

        {{-- Left: welcome-main --}}
        <article class="welcome-main">
            <div class="eyebrow">
                <i data-lucide="leaf"></i>
                @if($isCurrentMember)
                    Bienvenue sur votre espace OREINA
                @else
                    Votre adhésion a expiré
                @endif
            </div>

            <h1>Bonjour {{ $member?->first_name ?? $user->name }}, prêt à contribuer au réseau aujourd'hui&nbsp;?</h1>

            <p>
                Cet espace membre est pensé comme un outil d'action&nbsp;: suivre vos contributions,
                retrouver vos dernières activités, accéder rapidement à Artemisiae
                et rester connecté à la vie du réseau.
            </p>

            <div class="quick-actions">
                <a href="{{ route('member.work-groups') }}" class="btn btn-primary"><i data-lucide="users"></i>Accès aux groupes</a>
                <a href="{{ route('member.profile') }}" class="btn btn-secondary"><i data-lucide="user-round"></i>Compléter mon profil</a>
                <a href="{{ route('member.lepis') }}" class="btn btn-secondary"><i data-lucide="newspaper"></i>Lepis</a>
            </div>
        </article>

        {{-- Right: welcome-side (mini-cards) --}}
        <div class="welcome-side">
            {{-- Latest journal issue --}}
            {{-- Chersotis (revue scientifique) --}}
            <article class="mini-card blue">
                <div>
                    <strong>Chersotis</strong>
                    @if($isCurrentMember && $latestIssues->count() > 0)
                        <p>Dernier numéro disponible : Vol.&nbsp;{{ $latestIssues->first()->volume_number }} — N°{{ $latestIssues->first()->issue_number }}</p>
                    @else
                        <p>La revue scientifique d'OREINA. Articles, publications et soumissions.</p>
                    @endif
                </div>
                <a href="{{ route('member.journal') }}" class="text-link"><i data-lucide="book-open"></i>Consulter Chersotis</a>
            </article>

            {{-- Lepis (bulletin adhérents) --}}
            <article class="mini-card sage">
                <div>
                    <strong>Lepis</strong>
                    <p>Le bulletin trimestriel des adhérents. Actualités, synthèses et contributions.</p>
                </div>
                <a href="{{ route('member.lepis') }}" class="text-link"><i data-lucide="newspaper"></i>Consulter Lepis</a>
            </article>

            {{-- Soumettre --}}
            <article class="mini-card" style="background:var(--blue); color:white;">
                <div>
                    <strong style="color:white;">Soumettre</strong>
                    <p style="color:rgba(255,255,255,0.85);">Proposer un article pour Chersotis ou une contribution pour Lepis.</p>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="{{ route('journal.submit') }}" class="text-link" style="color:white;"><i data-lucide="file-plus"></i>Chersotis</a>
                    <a href="{{ route('member.lepis.suggest') }}" class="text-link" style="color:rgba(255,255,255,0.7);"><i data-lucide="newspaper"></i>Lepis</a>
                </div>
            </article>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════
         MAIN GRID  (1.15fr + 0.95fr)
    ═══════════════════════════════════════════════════ --}}
    <section class="grid">

        {{-- ── LEFT STACK ── --}}
        <div class="stack">

            {{-- Contributions panel --}}
            <article class="card panel">
                <div class="panel-head">
                    <div>
                        <h2>Mes contributions</h2>
                        <p>Un résumé de vos indicateurs : adhésion, dons, participations.</p>
                    </div>
                </div>

                <div class="contrib-grid">
                    <div class="stat">
                        <strong>{{ $stats['membership_years'] }}</strong>
                        <span>année(s) d'adhésion</span>
                    </div>
                    <div class="stat">
                        <strong>{{ number_format($stats['total_donations'], 0, ',', ' ') }}&nbsp;€</strong>
                        <span>total des dons</span>
                    </div>
                    <div class="stat">
                        <strong>{{ $stats['donation_count'] }}</strong>
                        <span>don(s) enregistré(s)</span>
                    </div>
                </div>
            </article>

            {{-- Activity feed panel --}}
            <article class="card panel">
                <div class="panel-head">
                    <div>
                        <h2>Activité récente</h2>
                        <p>Votre flux personnel et les dernières actions dans vos espaces.</p>
                    </div>
                    <a href="#" class="text-link"><i data-lucide="arrow-right"></i>Tout voir</a>
                </div>

                @if($member)
                    @livewire('member.activity-feed', ['memberId' => $member->id, 'isCurrentMember' => $isCurrentMember])
                @else
                    <div class="activity-list">
                        <article class="activity-item">
                            <div class="bullet"><i data-lucide="user-round"></i></div>
                            <div>
                                <strong>Complétez votre profil</strong>
                                <p>Renseignez vos informations pour accéder à toutes les fonctionnalités de votre espace membre.</p>
                            </div>
                            <div class="time">Maintenant</div>
                        </article>
                    </div>
                @endif
            </article>

            {{-- Recent donations panel --}}
            @if($recentDonations->count() > 0)
            <article class="card panel">
                <div class="panel-head">
                    <div>
                        <h2>Derniers dons</h2>
                        <p>Vos dons récents et reçus fiscaux disponibles.</p>
                    </div>
                    <a href="{{ route('member.documents') }}" class="text-link"><i data-lucide="arrow-right"></i>Mes documents</a>
                </div>

                <div class="activity-list">
                    @foreach($recentDonations as $donation)
                    <article class="activity-item">
                        <div class="bullet gold"><i data-lucide="heart"></i></div>
                        <div>
                            <strong>{{ number_format($donation->amount, 2, ',', ' ') }} €</strong>
                            <p>Don du {{ $donation->donation_date->format('d/m/Y') }}</p>
                        </div>
                        <a href="{{ route('member.documents.cerfa', $donation) }}" class="text-link"><i data-lucide="download"></i>Reçu fiscal</a>
                    </article>
                    @endforeach
                </div>
            </article>
            @endif
        </div>

        {{-- ── RIGHT STACK ── --}}
        <div class="stack">

            {{-- Réseau des adhérents (carte) --}}
            <article class="card panel" style="background:var(--forest); color:white; border:none;">
                <div class="panel-head">
                    <div>
                        <h2 style="color:white;">Réseau des adhérents</h2>
                        <p style="color:rgba(255,255,255,0.8);">Visualiser la répartition des membres et accéder à la carte interactive.</p>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:center;height:160px;border-radius:16px;background:rgba(255,255,255,0.08);margin-top:10px;position:relative;overflow:hidden;">
                    {{-- Mini silhouette France SVG --}}
                    <svg viewBox="0 0 200 200" style="width:140px;height:140px;opacity:0.25;" fill="white">
                        <path d="M100,10 L120,25 L140,20 L155,35 L165,55 L170,75 L160,85 L170,100 L165,115 L155,125 L145,140 L130,155 L120,170 L100,180 L85,175 L75,185 L60,175 L50,160 L40,145 L35,130 L30,115 L35,100 L30,85 L35,70 L45,55 L55,40 L70,30 L85,20 Z"/>
                    </svg>
                    <div style="position:absolute;text-align:center;">
                        <div style="font-size:28px;font-weight:800;letter-spacing:-0.04em;">
                            @php
                                $totalActiveMembers = \App\Models\Member::where('is_active', true)->count();
                            @endphp
                            {{ $totalActiveMembers }}
                        </div>
                        <div style="font-size:13px;color:rgba(255,255,255,0.7);">membres actifs</div>
                    </div>
                </div>

                <div style="margin-top:16px;">
                    <a href="{{ route('member.map') }}" class="btn btn-primary"><i data-lucide="map"></i>Explorer la carte</a>
                </div>
            </article>

            {{-- Todo / Actions panel --}}
            <article class="card panel">
                <div class="panel-head">
                    <div>
                        <h2>À faire</h2>
                        <p>Les actions qui doivent ressortir tout de suite.</p>
                    </div>
                </div>

                <div class="todo-list">
                    @if(!$member || !$member->biography)
                    <article class="todo-item">
                        <div>
                            <strong>Compléter votre présentation membre</strong>
                            <p>Ajoutez une courte biographie et vos centres d'intérêt pour mieux apparaître dans le réseau.</p>
                        </div>
                        <span class="status gold">Prioritaire</span>
                    </article>
                    @endif

                    @if(!$isCurrentMember)
                    <article class="todo-item">
                        <div>
                            <strong>Mettre à jour votre adhésion</strong>
                            <p>Votre cotisation a expiré. Renouvelez pour continuer à accéder à tous les services.</p>
                        </div>
                        <span class="status blue">À traiter</span>
                    </article>
                    @endif

                    @if($workGroups->count() > 0 && count($myGroupIds) === 0)
                    <article class="todo-item">
                        <div>
                            <strong>Rejoindre un groupe de travail</strong>
                            <p>Choisissez vos thématiques ou espaces de contribution pour personnaliser votre tableau de bord.</p>
                        </div>
                        <span class="status sage">Suggestion</span>
                    </article>
                    @endif
                </div>
            </article>

            {{-- Espaces collaboratifs / GT panel --}}
            @if($workGroups->count() > 0)
            <article class="card panel">
                <div class="panel-head">
                    <div>
                        <h2>Mes espaces</h2>
                        <p>Les groupes de travail et espaces collaboratifs auxquels vous participez.</p>
                    </div>
                    <a href="{{ route('member.work-groups') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir tous mes espaces</a>
                </div>

                <div class="news-list">
                    @foreach($workGroups->take(3) as $index => $gt)
                    <article class="news-item" @if($index === 0) style="background:#EEF4F8;" @elseif($index === 1) style="background:#EEF6F1;" @else style="background:#FAF8F5;" @endif>
                        <strong>{{ $gt->name }}</strong>
                        <p>{{ Str::limit($gt->description, 120) }}</p>
                        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
                            @if(in_array($gt->id, $myGroupIds))
                                <span class="badge sage">Membre</span>
                            @endif
                            <span class="badge">{{ $gt->members_count }} membres</span>
                        </div>
                    </article>
                    @endforeach
                </div>
            </article>
            @endif

            {{-- Journal issues panel --}}
            @if($isCurrentMember && $latestIssues->count() > 0)
            <article class="card panel">
                <div class="panel-head">
                    <div>
                        <h2>Derniers numéros</h2>
                        <p>Les publications récentes de Chersotis, la revue scientifique d'OREINA.</p>
                    </div>
                    <a href="{{ route('member.journal') }}" class="text-link"><i data-lucide="arrow-right"></i>Tous les numéros</a>
                </div>

                <div class="news-list">
                    @foreach($latestIssues as $issue)
                    <article class="news-item">
                        <strong>{{ $issue->title ?? 'OREINA' }} — Vol.&nbsp;{{ $issue->volume_number }} N°{{ $issue->issue_number }}</strong>
                        <p>{{ $issue->publication_date?->translatedFormat('F Y') }}</p>
                        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
                            @if($issue->pdf_file)
                                <a href="{{ route('member.journal.download', $issue) }}" class="badge blue">Télécharger le PDF</a>
                            @endif
                            <span class="badge">Publié</span>
                        </div>
                    </article>
                    @endforeach
                </div>
            </article>
            @endif

            {{-- Vie du réseau --}}
            @if($upcomingEvents->count() > 0)
            <article class="card panel">
                <div class="panel-head">
                    <div>
                        <h2>Vie du réseau</h2>
                        <p>Prochains événements et actualités de la communauté OREINA.</p>
                    </div>
                    <a href="{{ route('member.community') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir l'agenda</a>
                </div>

                <div class="news-list">
                    @foreach($upcomingEvents->take(3) as $event)
                    <article class="news-item">
                        <strong>{{ $event->title }}</strong>
                        <p>{{ $event->start_date->translatedFormat('l j F Y') }} @if($event->location_city)· {{ $event->location_city }}@endif</p>
                        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
                            <span class="badge blue">{{ ucfirst($event->event_type ?? 'Événement') }}</span>
                            @if($event->start_date->diffInDays(now()) < 7)
                                <span class="badge gold">Bientôt</span>
                            @endif
                        </div>
                    </article>
                    @endforeach
                </div>
            </article>
            @endif

            {{-- Quick access panel --}}
            <article class="card panel">
                <div class="panel-head">
                    <div>
                        <h2>Accès rapides</h2>
                        <p>Trois entrées simples vers vos outils essentiels.</p>
                    </div>
                </div>

                <div class="todo-list">
                    <article class="todo-item">
                        <div>
                            <strong>Explorer Artemisiae</strong>
                            <p>Retrouvez le portail national, vos filtres et vos espaces de consultation.</p>
                        </div>
                        <span class="status blue">Portail</span>
                    </article>

                    <article class="todo-item">
                        <div>
                            <strong>Mes ressources</strong>
                            <p>Guides, documents, revue et contenus utiles à vos activités naturalistes.</p>
                        </div>
                        <span class="status sage">Documents</span>
                    </article>

                    <article class="todo-item">
                        <div>
                            <strong>Mon profil public</strong>
                            <p>Vérifiez les informations visibles et la manière dont vous apparaissez dans le réseau.</p>
                        </div>
                        <span class="status gold">Profil</span>
                    </article>
                </div>
            </article>
        </div>
    </section>

@endsection
