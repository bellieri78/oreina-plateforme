@extends('layouts.member')
@section('title', $workGroup->name)
@section('page-title', $workGroup->name)
@section('page-subtitle', 'Groupe de travail')

@section('content')
<style>
    .wg-top{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px;}
    .wg-top h1{margin:0;font-size:clamp(24px,2.4vw,32px);font-weight:700;letter-spacing:-0.02em;display:inline-flex;align-items:center;gap:12px;flex-wrap:wrap;}
    .wg-pill{font-size:12px;font-weight:800;padding:4px 12px;border-radius:999px;background:rgba(133,183,157,0.20);color:#2f694e;}
    .wg-top-desc{margin:10px 0 0;color:var(--muted);font-size:15px;line-height:1.6;max-width:840px;}
    .wg-meta{margin:10px 0 0;display:flex;flex-wrap:wrap;gap:18px;color:var(--muted);font-size:13px;}
    .wg-meta span{display:inline-flex;align-items:center;gap:6px;}
    .wg-meta i{width:14px;height:14px;}
    .wg-headgrid{display:grid;grid-template-columns:1.6fr 1fr;gap:18px;align-items:stretch;}
    .wg-kpibar{display:grid;grid-template-columns:200px 1fr;gap:20px;background:var(--surface-sage);border:1px solid rgba(133,183,157,0.30);border-radius:18px;padding:18px;box-shadow:var(--shadow);}
    .wg-cover{border-radius:14px;overflow:hidden;background:var(--surface-soft);min-height:170px;display:grid;place-items:center;}
    .wg-cover img{width:100%;height:100%;object-fit:cover;display:block;}
    .wg-kpis{display:flex;flex-direction:column;justify-content:center;gap:18px;}
    .wg-krow{display:flex;flex-wrap:wrap;gap:16px 30px;}
    .wg-kpi{display:flex;align-items:center;gap:10px;}
    .wg-kpi-ic{width:38px;height:38px;border-radius:10px;display:grid;place-items:center;background:rgba(133,183,157,0.18);color:#2f694e;flex-shrink:0;}
    .wg-kpi-ic i{width:18px;height:18px;}
    .wg-kpi-tx{font-size:13px;color:var(--muted);}
    .wg-kpi-tx strong{font-size:20px;display:block;line-height:1;color:var(--text);}
    .wg-tabs-row{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin:20px 0 18px;}
    .wg-tabs{display:inline-flex;gap:4px;background:var(--surface-soft);border:1px solid var(--border);padding:4px;border-radius:12px;flex-wrap:wrap;}
    .wg-tab{background:none;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-weight:700;font-size:14px;color:var(--muted);display:inline-flex;align-items:center;gap:6px;transition:background .12s,color .12s;}
    .wg-tab:hover{background:rgba(0,0,0,0.04);color:var(--text);}
    .wg-tab.is-active{background:#fff;color:var(--blue);box-shadow:var(--shadow);}
    .wg-modal-ov{position:fixed;inset:0;background:rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;z-index:60;padding:20px;}
    .wg-modal{background:#fff;border-radius:16px;max-width:480px;width:100%;max-height:80vh;overflow:auto;padding:22px;box-shadow:var(--shadow);}
    @media (max-width:1240px){
        .wg-headgrid{grid-template-columns:1fr;}
        .wg-kpibar{grid-template-columns:1fr;}
        .wg-cover{min-height:150px;}
    }
</style>

<div x-data="{ tab: new URLSearchParams(location.search).get('tab') || 'accueil', membersOpen:false, newThread:false }">

    @if(session('success'))<div class="flash-success"><i data-lucide="check-circle"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error"><i data-lucide="alert-circle"></i>{{ session('error') }}</div>@endif

    <div class="wg-top">
        <div>
            <h1>{{ $workGroup->name }}@if($workGroup->is_active)<span class="wg-pill">Groupe actif</span>@endif</h1>
            <p class="wg-top-desc">{{ $workGroup->description ?: 'Espace d\'échange et de collaboration.' }}</p>
            <div class="wg-meta">
                <span><i data-lucide="calendar"></i>Créé en {{ $workGroup->created_at?->translatedFormat('F Y') }}</span>
                <span><i data-lucide="users"></i>{{ $workGroup->join_policy === 'open' ? 'Groupe ouvert' : 'Groupe sur demande' }}</span>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @if($canManage)
            <button type="button" @click="tab='manage'" class="btn btn-secondary"><i data-lucide="settings"></i>Paramètres du groupe</button>
            @endif
            @if($status === 'active')
                <form method="POST" action="{{ route('member.work-groups.leave', $workGroup) }}" onsubmit="return confirm('Quitter ce groupe ?');">@csrf @method('DELETE')
                    <button class="btn btn-secondary"><i data-lucide="log-out"></i>Quitter</button>
                </form>
            @elseif($status === 'pending')
                <span class="badge gold" style="align-self:center;">Demande en attente</span>
            @else
                <form method="POST" action="{{ route('member.work-groups.join', $workGroup) }}">@csrf
                    <button class="btn btn-primary"><i data-lucide="{{ $workGroup->join_policy === 'open' ? 'plus' : 'send' }}"></i>{{ $workGroup->join_policy === 'open' ? 'Rejoindre' : 'Demander à rejoindre' }}</button>
                </form>
            @endif
        </div>
    </div>

    <div class="wg-headgrid">
        <div class="wg-kpibar">
            <div class="wg-cover" style="{{ $workGroup->coverUrl() ? '' : 'background: '.($workGroup->color ?? '#85B79D').';' }}">
                @if($workGroup->coverUrl())
                    <img src="{{ $workGroup->coverUrl() }}" alt="{{ $workGroup->name }}">
                @else
                    <i data-lucide="{{ $workGroup->icon ?? 'users' }}" style="width:54px;height:54px;color:white;opacity:0.85;"></i>
                @endif
            </div>
            <div class="wg-kpis">
                <div class="wg-krow">
                    <div class="wg-kpi"><span class="wg-kpi-ic"><i data-lucide="users"></i></span><span class="wg-kpi-tx"><strong>{{ $workGroup->active_members_count }}</strong>membres</span></div>
                    <div class="wg-kpi"><span class="wg-kpi-ic"><i data-lucide="folder-kanban"></i></span><span class="wg-kpi-tx"><strong>{{ $projects->count() }}</strong>projets</span></div>
                    <div class="wg-kpi"><span class="wg-kpi-ic"><i data-lucide="file-text"></i></span><span class="wg-kpi-tx"><strong>{{ $documentsCount }}</strong>documents</span></div>
                    <div class="wg-kpi"><span class="wg-kpi-ic"><i data-lucide="messages-square"></i></span><span class="wg-kpi-tx"><strong>{{ $threadsCount }}</strong>discussions</span></div>
                </div>
                <div>
                    <button type="button" @click="membersOpen=true" class="btn btn-secondary" style="justify-self:start;"><i data-lucide="users"></i>Voir les membres</button>
                </div>
            </div>
        </div>
        @include('member.work-groups.partials._about')
    </div>

    <div class="wg-tabs-row">
        <div class="wg-tabs">
            <button @click="tab='accueil'" class="wg-tab" :class="{ 'is-active': tab==='accueil' }">Accueil</button>
            @if($workGroup->has_forum)
            <button @click="tab='discussions'" class="wg-tab" :class="{ 'is-active': tab==='discussions' }">Discussions @if($threadsCount)<span class="badge gold" style="margin-left:2px;">{{ $threadsCount }}</span>@endif</button>
            @endif
            @if($canViewResources)
            <button @click="tab='documents'" class="wg-tab" :class="{ 'is-active': tab==='documents' }">Documents</button>
            <button @click="tab='ressources'" class="wg-tab" :class="{ 'is-active': tab==='ressources' }">Ressources</button>
            @endif
            <button @click="tab='projets'" class="wg-tab" :class="{ 'is-active': tab==='projets' }">Projets</button>
            <button @click="tab='apropos'" class="wg-tab" :class="{ 'is-active': tab==='apropos' }">À propos</button>
        </div>
        @if($canParticipate && $workGroup->has_forum)
        <button type="button" @click="tab='discussions'; newThread=true" class="btn btn-primary"><i data-lucide="plus"></i>Nouvelle publication</button>
        @endif
    </div>

    <div x-show="tab==='accueil'" class="grid-3" style="margin-top:4px;align-items:start;">
        <div style="display:flex;flex-direction:column;gap:18px;">
            @include('member.work-groups.partials._welcome')
            @if($workGroup->has_forum)@include('member.work-groups.partials._recent_discussions')@endif
        </div>
        <div style="display:flex;flex-direction:column;gap:18px;">
            @if($canViewResources)@include('member.work-groups.partials._recent_documents')@endif
            @if($canViewResources)@include('member.work-groups.partials._recent_links')@endif
        </div>
        <div style="display:flex;flex-direction:column;gap:18px;">
            @include('member.work-groups.partials._projects')
            @include('member.work-groups.partials._events')
            @include('member.work-groups.partials._activity')
        </div>
    </div>

    @if($workGroup->has_forum)
    <div x-show="tab==='discussions'" x-cloak>@includeIf('member.work-groups.partials._forum')</div>
    @endif

    @if($canViewResources)
    <div x-show="tab==='documents'" x-cloak>@includeIf('member.work-groups.partials._documents')</div>
    <div x-show="tab==='ressources'" x-cloak>@includeIf('member.work-groups.partials._links')</div>
    @endif

    <div x-show="tab==='projets'" x-cloak>@include('member.work-groups.partials._projects')</div>

    <div x-show="tab==='apropos'" x-cloak>@include('member.work-groups.partials._about')</div>

    @if($canManage)
    <div x-show="tab==='manage'" x-cloak>@includeIf('member.work-groups.partials._manage')</div>
    @endif

    <template x-if="membersOpen">
        <div class="wg-modal-ov" @click.self="membersOpen=false" @keydown.escape.window="membersOpen=false">
            <div class="wg-modal">
                <div class="panel-head"><div><h2 style="font-size:20px;">Membres ({{ $members->count() }})</h2></div>
                    <button type="button" @click="membersOpen=false" class="text-link" style="background:none;border:none;cursor:pointer;"><i data-lucide="x"></i></button>
                </div>
                @forelse($members as $m)
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border);">
                    <div class="reseau-avatar" style="margin:0;">
                        @if($m->photo_path)<img src="{{ \Storage::url($m->photo_path) }}" alt="">@else{{ strtoupper(substr($m->first_name ?? '?',0,1)) }}@endif
                    </div>
                    <span><strong style="display:block;font-size:14px;">{{ $m->full_name ?? $m->first_name }}</strong>
                    @if($workGroup->isCoordinator($m))<small style="color:var(--muted);">Coordinateur</small>@endif</span>
                </div>
                @empty
                <p style="color:var(--muted);padding:10px 0;">Aucun membre actif.</p>
                @endforelse
            </div>
        </div>
    </template>

</div>
@endsection
