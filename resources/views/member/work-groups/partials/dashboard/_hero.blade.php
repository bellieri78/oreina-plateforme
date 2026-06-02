<div class="gt-logo">
    @if($workGroup->coverUrl())
        <img src="{{ $workGroup->coverUrl() }}" alt="{{ $workGroup->name }}">
    @else
        <i data-lucide="{{ $workGroup->icon ?? 'users' }}"></i>
    @endif
</div>
<div class="gt-banner-hero">
    <h1 class="gt-hero-title">
        {{ $workGroup->name }}
        <span class="gt-pill">{{ $workGroup->join_policy === 'open' ? 'Groupe ouvert' : 'Sur demande' }}</span>
    </h1>
    <p class="gt-hero-desc">{{ $workGroup->description ?: "Espace d'échange et de collaboration." }}</p>

    <div class="gt-chips">
        <span class="gt-chip"><i data-lucide="users"></i>{{ $workGroup->active_members_count }} membres</span>
        <span class="gt-chip"><i data-lucide="calendar"></i>Créé en {{ $workGroup->created_at?->format('Y') }}</span>
    </div>

    <div class="gt-hero-actions">
        @if($status === 'active')
            @if($workGroup->has_forum)
                <button type="button" class="btn wg-btn-gold" @click="tab='discussions'; newThread=true">
                    <i data-lucide="megaphone"></i>Publier une actualité
                </button>
                <button type="button" class="btn btn-ghost-light" @click="tab='discussions'">
                    <i data-lucide="messages-square"></i>Rejoindre la discussion
                </button>
            @endif
            @if($canManage)
                <button type="button" class="btn btn-ghost-light" @click="tab='manage'">
                    <i data-lucide="settings"></i>Paramètres
                </button>
            @endif
            <form method="POST" action="{{ route('member.work-groups.leave', $workGroup) }}" onsubmit="return confirm('Quitter ce groupe ?');">
                @csrf @method('DELETE')
                <button class="btn btn-ghost-light"><i data-lucide="log-out"></i>Quitter</button>
            </form>
        @elseif($status === 'pending')
            <span class="badge gold">Demande en attente</span>
        @else
            <form method="POST" action="{{ route('member.work-groups.join', $workGroup) }}">
                @csrf
                <button class="btn wg-btn-gold">
                    <i data-lucide="{{ $workGroup->join_policy === 'open' ? 'plus' : 'send' }}"></i>{{ $workGroup->join_policy === 'open' ? 'Rejoindre le groupe' : 'Demander à rejoindre' }}
                </button>
            </form>
        @endif
    </div>
</div>
