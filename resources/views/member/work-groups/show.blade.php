@extends('layouts.member')
@section('title', $workGroup->name)
@section('page-title', $workGroup->name)
@section('page-subtitle', 'Groupe de travail')

@section('content')
<div x-data="{ tab: new URLSearchParams(location.search).get('tab') || 'accueil' }">

    @if(session('success'))<div class="flash-success"><i data-lucide="check-circle"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error"><i data-lucide="alert-circle"></i>{{ session('error') }}</div>@endif

    <section class="welcome">
        <article class="welcome-main is-member">
            <div class="eyebrow eyebrow-member">
                <i data-lucide="users"></i>
                {{ $workGroup->join_policy === 'open' ? 'Groupe ouvert' : 'Groupe sur demande' }}
            </div>
            <h1>{{ $workGroup->name }}</h1>
            <p>{{ \Str::limit($workGroup->description, 220) ?: 'Espace d\'échange et de collaboration.' }}</p>
            <div class="quick-actions">
                @if($status === 'active')
                    <form method="POST" action="{{ route('member.work-groups.leave', $workGroup) }}" onsubmit="return confirm('Quitter ce groupe ?');">@csrf @method('DELETE')
                        <button class="btn btn-secondary"><i data-lucide="log-out"></i>Quitter le groupe</button>
                    </form>
                @elseif($status === 'pending')
                    <span class="badge gold" style="align-self:center;">Demande en attente</span>
                @elseif($workGroup->join_policy === 'open')
                    <form method="POST" action="{{ route('member.work-groups.join', $workGroup) }}">@csrf
                        <button class="btn btn-primary"><i data-lucide="plus"></i>Rejoindre</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('member.work-groups.join', $workGroup) }}">@csrf
                        <button class="btn btn-primary"><i data-lucide="send"></i>Demander à rejoindre</button>
                    </form>
                @endif
                @if($workGroup->has_collaborative_space && $workGroup->collaborative_space_url)
                    <a href="{{ $workGroup->collaborative_space_url }}" target="_blank" rel="noopener" class="btn btn-secondary"><i data-lucide="external-link"></i>Espace de travail collaboratif</a>
                @endif
            </div>
        </article>
        <div class="welcome-photo" style="{{ $workGroup->coverUrl() ? '' : 'background: '.($workGroup->color ?? '#85B79D').';display:grid;place-items:center;' }}">
            @if($workGroup->coverUrl())
                <img src="{{ $workGroup->coverUrl() }}" alt="{{ $workGroup->name }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                <i data-lucide="{{ $workGroup->icon ?? 'users' }}" style="width:72px;height:72px;color:white;opacity:0.85;"></i>
            @endif
        </div>
    </section>

    <section class="kpi-bar" style="margin-top:18px;">
        <div class="kpi-bar-stats" style="grid-template-columns: repeat(2, minmax(0,1fr)) !important;">
            <div class="kpi-item">
                <div class="kpi-item-icon" style="background:rgba(133,183,157,0.16);color:#2f694e;"><i data-lucide="users"></i></div>
                <div><strong>{{ $workGroup->active_members_count }}</strong><span>membres</span></div>
            </div>
            <div class="kpi-item">
                <div class="kpi-item-icon" style="background:rgba(53,107,138,0.10);color:var(--blue);"><i data-lucide="folder"></i></div>
                <div><strong>{{ $workGroup->has_resources ? $workGroup->resources()->count() : 0 }}</strong><span>ressources</span></div>
            </div>
        </div>
    </section>

    <div style="display:flex;gap:8px;margin:22px 0 18px;border-bottom:1px solid var(--border);flex-wrap:wrap;">
        <button @click="tab='accueil'" style="background:none;border:none;padding:10px 4px;cursor:pointer;font-weight:800;border-bottom:2px solid transparent;" :style="tab==='accueil' && 'border-bottom-color:var(--blue);'">Accueil</button>
        @if($canViewResources)
        <button @click="tab='ressources'" style="background:none;border:none;padding:10px 4px;cursor:pointer;font-weight:800;border-bottom:2px solid transparent;" :style="tab==='ressources' && 'border-bottom-color:var(--blue);'">Ressources</button>
        @endif
        @if($workGroup->has_forum)
        <button @click="tab='discussions'" style="background:none;border:none;padding:10px 4px;cursor:pointer;font-weight:800;border-bottom:2px solid transparent;" :style="tab==='discussions' && 'border-bottom-color:var(--blue);'">Discussions</button>
        @endif
        @if($canManage)
        <button @click="tab='manage'" style="background:none;border:none;padding:10px 4px;cursor:pointer;font-weight:800;border-bottom:2px solid transparent;" :style="tab==='manage' && 'border-bottom-color:var(--blue);'">Gérer @if($pending->count() > 0)<span class="badge gold" style="margin-left:6px;">{{ $pending->count() }}</span>@endif</button>
        @endif
    </div>

    <div x-show="tab==='accueil'">
        @include('member.work-groups.partials._about')
    </div>

    @if($canViewResources)
    <div x-show="tab==='ressources'" x-cloak>
        @includeIf('member.work-groups.partials._resources')
    </div>
    @endif

    @if($workGroup->has_forum)
    <div x-show="tab==='discussions'" x-cloak>
        @includeIf('member.work-groups.partials._forum')
    </div>
    @endif

    @if($canManage)
    <div x-show="tab==='manage'" x-cloak>
        @includeIf('member.work-groups.partials._manage')
    </div>
    @endif

</div>
@endsection
