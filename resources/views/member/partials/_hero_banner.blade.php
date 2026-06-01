@php
    $heroFirstName = $member?->first_name ?? $user->name;
    $heroMemberSince = $member?->created_at?->year;
    $heroGroupCount = count($myGroupIds);
@endphp

<section class="hero-banner">
    <div class="hero-banner-body">
        @if($isCurrentMember)
            <div class="eyebrow">
                <i data-lucide="check-circle" style="width:14px;height:14px;"></i>
                Adhérent actif · {{ now()->year }}
            </div>
            <h1>Bonjour {{ $heroFirstName }}&nbsp;!</h1>
            <p class="hero-lede">Bienvenue dans votre espace membre OREINA.</p>

            <div class="hero-stats">
                <div class="hero-stat">
                    <i data-lucide="calendar-days"></i>
                    <div class="hero-stat-text">
                        <small>Membre depuis</small>
                        <strong>{{ $heroMemberSince ?? '—' }}</strong>
                    </div>
                </div>
                <div class="hero-stat">
                    <i data-lucide="users"></i>
                    <div class="hero-stat-text">
                        <small>Groupes actifs</small>
                        <strong>{{ $heroGroupCount }}</strong>
                    </div>
                </div>
                <div class="hero-stat">
                    <i data-lucide="file-text"></i>
                    <div class="hero-stat-text">
                        <small>Article{{ $stats['articles_submitted'] > 1 ? 's' : '' }} soumis</small>
                        <strong>{{ $stats['articles_submitted'] }}</strong>
                    </div>
                </div>
            </div>

            <div class="hero-actions">
                <a href="{{ route('member.work-groups') }}" class="btn-hero btn-hero-solid">
                    <i data-lucide="users" style="width:18px;height:18px;"></i>Accès à mes groupes
                </a>
                <a href="{{ route('member.profile') }}" class="btn-hero btn-hero-ghost">
                    <i data-lucide="user-round" style="width:18px;height:18px;"></i>Voir mon profil
                </a>
            </div>
        @else
            <div class="eyebrow">
                <i data-lucide="sparkles" style="width:14px;height:14px;"></i>
                Compte visiteur
            </div>
            <h1>Bonjour {{ $heroFirstName }}&nbsp;!</h1>
            <p class="hero-lede">
                Depuis cet espace, vous pouvez soumettre des articles à Chersotis, notre revue
                scientifique. Rejoignez l'association pour accéder à Lepis, aux groupes de
                travail, à l'annuaire et au chat.
            </p>

            <div class="hero-actions">
                <a href="{{ route('journal.submissions.create') }}" class="btn-hero btn-hero-solid">
                    <i data-lucide="file-plus" style="width:18px;height:18px;"></i>Soumettre un article
                </a>
                <a href="{{ route('hub.membership') }}" class="btn-hero btn-hero-ghost">
                    <i data-lucide="heart-plus" style="width:18px;height:18px;"></i>Adhérer à OREINA
                </a>
            </div>
        @endif
    </div>

    <div class="hero-banner-media">
        <img src="{{ asset('images/espace-membre/papillon-hero2.jpg') }}" alt="Papillon — espace OREINA" loading="eager">
    </div>
</section>
