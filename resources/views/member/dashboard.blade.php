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

    @include('member.partials._groups_carousel')

    @if($isCurrentMember)
    <section class="grid" style="grid-template-columns: 1.5fr 1fr;">
        @include('member.partials._actualites_demo')
        @include('member.partials._ressources_recentes')
    </section>
    @endif

    @if($isCurrentMember)
    <section class="grid-3">
        @include('member.partials._contributions_list')
        @include('member.partials._reseau_map')
        @include('member.partials._agenda')
    </section>
    @endif

    @include('member.partials._suggestions')

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
