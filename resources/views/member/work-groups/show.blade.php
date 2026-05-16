@extends('layouts.member')
@section('title', $workGroup->name)
@section('page-title', $workGroup->name)
@section('page-subtitle', 'Groupe de travail')

@section('content')
<style>
    .wg-header{display:grid;grid-template-columns:200px 1fr;gap:20px;align-items:stretch;background:var(--surface-sage);border:1px solid rgba(133,183,157,0.30);border-radius:20px;padding:20px;box-shadow:var(--shadow);}
    .wg-header-cover{border-radius:14px;overflow:hidden;background:var(--surface-soft);min-height:150px;display:grid;place-items:center;}
    .wg-header-cover img{width:100%;height:100%;object-fit:cover;display:block;}
    .wg-header-body{display:flex;flex-direction:column;justify-content:center;min-width:0;}
    .wg-header-body h1{margin:8px 0 0;font-size:clamp(22px,2.4vw,28px);font-weight:700;line-height:1.12;letter-spacing:-0.02em;}
    .wg-header-desc{margin:8px 0 0;color:var(--muted);font-size:14px;line-height:1.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
    .wg-kpis{display:flex;flex-wrap:wrap;gap:20px;margin-top:14px;}
    .wg-kpi{display:flex;align-items:center;gap:8px;font-size:14px;color:var(--text);}
    .wg-kpi i{width:16px;height:16px;color:var(--blue);}
    .wg-kpi strong{font-size:17px;}
    .wg-header .quick-actions{margin-top:16px;}
    .wg-tabs{display:inline-flex;gap:4px;background:var(--surface-soft);border:1px solid var(--border);padding:4px;border-radius:12px;margin:20px 0 18px;flex-wrap:wrap;}
    .wg-tab{background:none;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-weight:700;font-size:14px;color:var(--muted);display:inline-flex;align-items:center;gap:6px;transition:background .12s,color .12s;}
    .wg-tab:hover{background:rgba(0,0,0,0.04);color:var(--text);}
    .wg-tab.is-active{background:#fff;color:var(--blue);box-shadow:var(--shadow);}
    @media (max-width:900px){
        .wg-header{grid-template-columns:1fr;}
        .wg-header-cover{min-height:120px;}
    }
</style>

<div x-data="{ tab: new URLSearchParams(location.search).get('tab') || 'accueil' }">

    @if(session('success'))<div class="flash-success"><i data-lucide="check-circle"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error"><i data-lucide="alert-circle"></i>{{ session('error') }}</div>@endif

    <section class="wg-header">
        <div class="wg-header-cover" style="{{ $workGroup->coverUrl() ? '' : 'background: '.($workGroup->color ?? '#85B79D').';' }}">
            @if($workGroup->coverUrl())
                <img src="{{ $workGroup->coverUrl() }}" alt="{{ $workGroup->name }}">
            @else
                <i data-lucide="{{ $workGroup->icon ?? 'users' }}" style="width:54px;height:54px;color:white;opacity:0.85;"></i>
            @endif
        </div>
        <div class="wg-header-body">
            <div class="eyebrow eyebrow-member">
                <i data-lucide="users"></i>
                {{ $workGroup->join_policy === 'open' ? 'Groupe ouvert' : 'Groupe sur demande' }}
            </div>
            <h1>{{ $workGroup->name }}</h1>
            <p class="wg-header-desc">{{ \Str::limit($workGroup->description, 180) ?: 'Espace d\'échange et de collaboration.' }}</p>

            <div class="wg-kpis">
                <span class="wg-kpi"><i data-lucide="users"></i><strong>{{ $workGroup->active_members_count }}</strong> membres</span>
                <span class="wg-kpi"><i data-lucide="folder"></i><strong>{{ $workGroup->has_resources ? $workGroup->resources()->count() : 0 }}</strong> ressources</span>
            </div>

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
        </div>
    </section>

    <div class="wg-tabs">
        <button @click="tab='accueil'" class="wg-tab" :class="{ 'is-active': tab==='accueil' }">Accueil</button>
        @if($canViewResources)
        <button @click="tab='ressources'" class="wg-tab" :class="{ 'is-active': tab==='ressources' }">Ressources</button>
        @endif
        @if($workGroup->has_forum)
        <button @click="tab='discussions'" class="wg-tab" :class="{ 'is-active': tab==='discussions' }">Discussions</button>
        @endif
        @if($canManage)
        <button @click="tab='manage'" class="wg-tab" :class="{ 'is-active': tab==='manage' }">Gérer @if($pending->count() > 0)<span class="badge gold" style="margin-left:2px;">{{ $pending->count() }}</span>@endif</button>
        @endif
    </div>

    <div x-show="tab==='accueil'" class="grid-3" style="margin-top:18px;align-items:start;">
        <div style="display:flex;flex-direction:column;gap:18px;">
            @include('member.work-groups.partials._about')
            @if($workGroup->has_forum)
                @include('member.work-groups.partials._recent_discussions')
            @endif
        </div>
        <div style="display:flex;flex-direction:column;gap:18px;">
            @if($canViewResources)
                @include('member.work-groups.partials._recent_resources')
            @endif
            @include('member.work-groups.partials._activity')
        </div>
        <div style="display:flex;flex-direction:column;gap:18px;">
            @include('member.work-groups.partials._coordinators')
            @include('member.work-groups.partials._projects')
        </div>
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
