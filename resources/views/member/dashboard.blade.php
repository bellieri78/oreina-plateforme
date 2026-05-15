@extends('layouts.member')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord membre')
@section('page-subtitle', 'Votre espace personnel')

@section('topbar-actions')
    <button class="btn btn-secondary"><i data-lucide="database"></i>Explorer Artemisiae</button>
    <a href="{{ route('member.work-groups') }}" class="btn btn-primary"><i data-lucide="users"></i>Accès aux groupes</a>
@endsection

@section('content')

    {{-- ═══════════════════════════════════════════════════
         HERO — 2 colonnes (texte + photo papillon)
    ═══════════════════════════════════════════════════ --}}
    <section class="welcome">

        @if($isCurrentMember)
        <article class="welcome-main is-member">
            <div class="eyebrow eyebrow-member">
                <i data-lucide="check-circle"></i>
                Adhérent actif · {{ now()->year }}
            </div>
            <h1>Bonjour {{ $member?->first_name ?? $user->name }}&nbsp;!</h1>
            <p>
                Cet espace est pensé comme un outil d'action : suivre vos contributions,
                retrouver vos dernières activités et rester connecté à la vie du réseau.
            </p>
            <div class="quick-actions">
                <a href="{{ route('member.work-groups') }}" class="btn btn-primary"><i data-lucide="users"></i>Accès aux groupes</a>
                <a href="{{ route('member.profile') }}" class="btn btn-secondary"><i data-lucide="user-round"></i>Compléter mon profil</a>
                <a href="{{ route('hub.lepis.bulletins.index') }}" class="btn btn-secondary"><i data-lucide="newspaper"></i>Lepis</a>
            </div>
        </article>
        @else
        <article class="welcome-main is-visitor">
            <div class="eyebrow eyebrow-visitor">
                <i data-lucide="sparkles"></i>
                Compte visiteur
            </div>
            <h1>Bonjour {{ $member?->first_name ?? $user->name }}&nbsp;!</h1>
            <p>
                Vous avez un compte OREINA. Depuis cet espace, vous pouvez soumettre des articles
                à Chersotis, notre revue scientifique. Pour accéder à toutes les fonctionnalités
                (Lepis, groupes de travail, annuaire, chat), rejoignez l'association.
            </p>
            <div class="quick-actions">
                <a href="{{ route('journal.submissions.create') }}" class="btn btn-primary"><i data-lucide="file-plus"></i>Soumettre un article</a>
                <a href="{{ route('hub.membership') }}" class="btn btn-secondary"><i data-lucide="heart-plus"></i>Adhérer à OREINA</a>
            </div>
        </article>
        @endif

        @include('member.partials._hero_carousel')

    </section>

    @include('member.partials._kpi_bar')

    @if($isCurrentMember)
    {{-- ═══════════════════════════════════════════════════
         ZONE B — Priorité immédiate (À faire + Mes soumissions)
    ═══════════════════════════════════════════════════ --}}
    <section class="grid">
        <article class="card panel">
            <div class="panel-head">
                <div>
                    <h2>À faire</h2>
                    <p>Les actions qui doivent ressortir tout de suite.</p>
                </div>
            </div>

            <div class="todo-list">
                @if(!$member->biography)
                <article class="todo-item">
                    <div>
                        <strong>Compléter votre présentation membre</strong>
                        <p>Ajoutez une courte biographie et vos centres d'intérêt pour mieux apparaître dans le réseau.</p>
                    </div>
                    <span class="status gold">Prioritaire</span>
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

        <article class="card panel">
            <div class="panel-head">
                <div>
                    <h2>Mes soumissions Chersotis</h2>
                    <p>Vos articles soumis à la revue scientifique et leur statut.</p>
                </div>
                <a href="{{ route('journal.submissions.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Toutes</a>
            </div>

            @if($mySubmissions->count() > 0)
            <div class="activity-list">
                @foreach($mySubmissions as $sub)
                @php
                    $displayStatus = $sub->publicStatus()->value;
                    $statusLabels = [
                        'submitted' => ['Soumis', 'blue'],
                        'under_initial_review' => ['Évaluation initiale', 'gold'],
                        'revision_requested' => ['Révision demandée', 'coral'],
                        'under_peer_review' => ['En relecture', 'gold'],
                        'revision_after_review' => ['Révision demandée', 'coral'],
                        'accepted' => ['Accepté', 'sage'],
                        'in_production' => ['En maquettage', 'blue'],
                        'awaiting_author_approval' => ['Approbation demandée', 'approval'],
                        'rejected' => ['Refusé', 'coral'],
                        'published' => ['Publié', 'sage'],
                        'redirected_to_lepis' => ['Transmis au bulletin Lepis', 'sage'],
                    ];
                    $label = $statusLabels[$displayStatus] ?? ['Inconnu', 'blue'];
                @endphp
                <article class="activity-item">
                    <div class="bullet {{ $displayStatus === 'published' ? '' : ($displayStatus === 'accepted' ? 'gold' : 'blue') }}">
                        @if($displayStatus === 'published')
                            <i data-lucide="check-circle" style="width:18px;height:18px;color:#2f694e;"></i>
                        @elseif($displayStatus === 'accepted')
                            <i data-lucide="badge-check" style="width:18px;height:18px;color:#8b6c05;"></i>
                        @elseif($displayStatus === 'under_peer_review' || $displayStatus === 'under_initial_review')
                            <i data-lucide="clock" style="width:18px;height:18px;color:var(--blue);"></i>
                        @elseif($displayStatus === 'revision_requested' || $displayStatus === 'revision_after_review')
                            <i data-lucide="pen-line" style="width:18px;height:18px;color:var(--coral);"></i>
                        @else
                            <i data-lucide="file-text" style="width:18px;height:18px;color:var(--blue);"></i>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('journal.submissions.show', $sub) }}" class="text-link">
                            <strong>{{ $sub->title }}</strong>
                        </a>
                        <p>
                            @if($sub->doi)DOI : {{ $sub->doi }} · @endif
                            Soumis le {{ $sub->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <span class="status {{ $label[1] }}">{{ $label[0] }}</span>
                    </div>
                </article>
                @endforeach
            </div>
            @else
            <div style="text-align:center; padding:24px; color:var(--muted);">
                <i data-lucide="file-text" style="width:32px;height:32px;margin:0 auto 12px;display:block;opacity:0.4;"></i>
                <p style="font-size:14px;">Aucune soumission pour le moment.</p>
                <a href="{{ route('journal.submissions.create') }}" class="btn btn-primary" style="margin-top:12px;"><i data-lucide="file-plus"></i>Soumettre un article</a>
            </div>
            @endif
        </article>
    </section>

    {{-- ═══════════════════════════════════════════════════
         ZONE C — Vie du réseau (Activité + Événements)
    ═══════════════════════════════════════════════════ --}}
    <section class="grid">
        <article class="card panel">
            <div class="panel-head">
                <div>
                    <h2>Activité récente</h2>
                    <p>Votre flux personnel et les dernières actions dans vos espaces.</p>
                </div>
            </div>

            @livewire('member.activity-feed', ['memberId' => $member->id, 'isCurrentMember' => $isCurrentMember])
        </article>

        @if($upcomingEvents->count() > 0)
        <article class="card panel">
            <div class="panel-head">
                <div>
                    <h2>Vie du réseau</h2>
                    <p>Prochains événements et actualités de la communauté OREINA.</p>
                </div>
                <a href="{{ route('hub.events.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir l'agenda</a>
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
        @else
        <article class="card panel">
            <div class="panel-head">
                <div>
                    <h2>Vie du réseau</h2>
                    <p>Aucun événement à venir pour le moment.</p>
                </div>
                <a href="{{ route('hub.events.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir l'agenda</a>
            </div>
        </article>
        @endif
    </section>

    {{-- ═══════════════════════════════════════════════════
         ZONE D — Mes espaces & lectures
    ═══════════════════════════════════════════════════ --}}
    <section class="grid">
        <div class="stack">
            @if($workGroups->count() > 0)
            <article class="card panel">
                <div class="panel-head">
                    <div>
                        <h2>Mes espaces</h2>
                        <p>Les groupes de travail et espaces collaboratifs auxquels vous participez.</p>
                    </div>
                    <a href="{{ route('member.work-groups') }}" class="text-link"><i data-lucide="arrow-right"></i>Tous mes espaces</a>
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

            <article class="card panel" style="background:var(--forest); color:white; border:none;">
                <div class="panel-head">
                    <div>
                        <h2 style="color:white;">Réseau des adhérents</h2>
                        <p style="color:rgba(255,255,255,0.8);">Visualiser la répartition des membres et accéder à la carte interactive.</p>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:center;height:160px;border-radius:16px;background:rgba(255,255,255,0.08);margin-top:10px;position:relative;overflow:hidden;">
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
                    <a href="{{ route('member.directory.index') }}" class="btn btn-primary"><i data-lucide="map"></i>Voir l'annuaire</a>
                </div>
            </article>
        </div>

        <div class="stack">
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
    </section>

    @else
    {{-- ═══════════════════════════════════════════════════
         NON-ADHÉRENT : section simplifiée
    ═══════════════════════════════════════════════════ --}}
    <section>
        <div class="card panel">
            <div class="panel-head">
                <div>
                    <h2>Votre compte OREINA</h2>
                    <p>Vous n'êtes pas encore adhérent. Voici ce que vous pouvez faire avec votre compte gratuit.</p>
                </div>
            </div>

            <div class="todo-list">
                <article class="todo-item">
                    <div>
                        <strong>Soumettre un article à Chersotis</strong>
                        <p>La revue scientifique d'OREINA est ouverte à tous. Soumettez vos travaux sur les Lépidoptères de France.</p>
                    </div>
                    <a href="{{ route('journal.submissions.create') }}" class="btn btn-primary" style="height:auto;padding:8px 14px;font-size:13px;"><i data-lucide="file-plus"></i>Soumettre</a>
                </article>

                <article class="todo-item">
                    <div>
                        <strong>Consulter les articles publiés</strong>
                        <p>Chersotis est en accès libre. Parcourez les articles scientifiques disponibles.</p>
                    </div>
                    <a href="{{ route('journal.articles.index') }}" class="btn btn-secondary" style="height:auto;padding:8px 14px;font-size:13px;"><i data-lucide="book-open"></i>Articles</a>
                </article>

                <article class="todo-item" style="background:var(--surface-sage);">
                    <div>
                        <strong>Rejoindre OREINA</strong>
                        <p>L'adhésion donne accès au bulletin Lepis, aux groupes de travail, au chat entre adhérents, à la carte des membres et aux documents personnels.</p>
                    </div>
                    <a href="{{ route('hub.membership') }}" class="btn btn-primary" style="height:auto;padding:8px 14px;font-size:13px;"><i data-lucide="heart-plus"></i>Adhérer</a>
                </article>
            </div>
        </div>
    </section>

    {{-- Mes soumissions Chersotis (pour non-adhérents — pour les adhérents, c'est en Zone B) --}}
    <section>
        <div class="card panel">
            <div class="panel-head">
                <div>
                    <h2>Mes soumissions à Chersotis</h2>
                    <p>Vos articles soumis à la revue scientifique et leur statut.</p>
                </div>
                <a href="{{ route('journal.submissions.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Toutes mes soumissions</a>
            </div>

            @if($mySubmissions->count() > 0)
            <div class="activity-list">
                @foreach($mySubmissions as $sub)
                @php
                    $displayStatus = $sub->publicStatus()->value;
                    $statusLabels = [
                        'submitted' => ['Soumis', 'blue'],
                        'under_initial_review' => ['Évaluation initiale', 'gold'],
                        'revision_requested' => ['Révision demandée', 'coral'],
                        'under_peer_review' => ['En relecture', 'gold'],
                        'revision_after_review' => ['Révision demandée', 'coral'],
                        'accepted' => ['Accepté', 'sage'],
                        'in_production' => ['En maquettage', 'blue'],
                        'awaiting_author_approval' => ['Approbation demandée', 'approval'],
                        'rejected' => ['Refusé', 'coral'],
                        'published' => ['Publié', 'sage'],
                        'redirected_to_lepis' => ['Transmis au bulletin Lepis', 'sage'],
                    ];
                    $label = $statusLabels[$displayStatus] ?? ['Inconnu', 'blue'];
                @endphp
                <article class="activity-item">
                    <div class="bullet {{ $displayStatus === 'published' ? '' : ($displayStatus === 'accepted' ? 'gold' : 'blue') }}">
                        @if($displayStatus === 'published')
                            <i data-lucide="check-circle" style="width:18px;height:18px;color:#2f694e;"></i>
                        @elseif($displayStatus === 'accepted')
                            <i data-lucide="badge-check" style="width:18px;height:18px;color:#8b6c05;"></i>
                        @elseif($displayStatus === 'under_peer_review' || $displayStatus === 'under_initial_review')
                            <i data-lucide="clock" style="width:18px;height:18px;color:var(--blue);"></i>
                        @elseif($displayStatus === 'revision_requested' || $displayStatus === 'revision_after_review')
                            <i data-lucide="pen-line" style="width:18px;height:18px;color:var(--coral);"></i>
                        @else
                            <i data-lucide="file-text" style="width:18px;height:18px;color:var(--blue);"></i>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('journal.submissions.show', $sub) }}" class="text-link">
                            <strong>{{ $sub->title }}</strong>
                        </a>
                        <p>
                            @if($sub->doi)DOI : {{ $sub->doi }} · @endif
                            Soumis le {{ $sub->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <span class="status {{ $label[1] }}">{{ $label[0] }}</span>
                    </div>
                </article>
                @endforeach
            </div>
            @else
            <div style="text-align:center; padding:24px; color:var(--muted);">
                <i data-lucide="file-text" style="width:32px;height:32px;margin:0 auto 12px;display:block;opacity:0.4;"></i>
                <p style="font-size:14px;">Aucune soumission pour le moment.</p>
                <a href="{{ route('journal.submissions.create') }}" class="btn btn-primary" style="margin-top:12px;"><i data-lucide="file-plus"></i>Soumettre un article</a>
            </div>
            @endif
        </div>
    </section>
    @endif


@endsection
