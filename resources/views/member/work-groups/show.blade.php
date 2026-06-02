@extends('layouts.member')
@section('title', $workGroup->name)
@section('page-title', $workGroup->name)
@section('page-subtitle', 'Groupe de travail')

@section('content')
<style>
    .gt-breadcrumb{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);margin-bottom:16px;}
    .gt-breadcrumb a{color:var(--muted);text-decoration:none;}
    .gt-breadcrumb a:hover{color:var(--forest);}
    .gt-row1{display:grid;grid-template-columns:2fr 1fr;gap:18px;align-items:start;margin-bottom:18px;}
    .gt-body{display:grid;grid-template-columns:2fr 1fr;gap:18px;align-items:start;}
    .gt-col{display:flex;flex-direction:column;gap:18px;}

    .gt-hero{display:flex;gap:20px;background:linear-gradient(135deg,#1f4a3f,#2C5F2D);border-radius:20px;padding:24px;color:#fff;box-shadow:var(--shadow);}
    .gt-hero-thumb{width:120px;height:120px;border-radius:16px;overflow:hidden;flex-shrink:0;display:grid;place-items:center;}
    .gt-hero-thumb img{width:100%;height:100%;object-fit:cover;}
    .gt-hero-thumb i{width:48px;height:48px;color:#fff;opacity:.85;}
    .gt-hero-body{flex:1;min-width:0;}
    .gt-hero-title{margin:0;font-size:clamp(22px,2.2vw,30px);font-weight:800;letter-spacing:-0.02em;display:inline-flex;align-items:center;gap:12px;flex-wrap:wrap;color:#fff;}
    .gt-pill{font-size:12px;font-weight:800;padding:4px 12px;border-radius:999px;background:rgba(255,255,255,0.18);color:#fff;}
    .gt-hero-desc{margin:10px 0 0;color:rgba(255,255,255,0.88);font-size:15px;line-height:1.6;max-width:640px;}
    .gt-meta{margin:14px 0 0;display:flex;flex-wrap:wrap;gap:18px;font-size:13px;color:rgba(255,255,255,0.82);}
    .gt-meta span{display:inline-flex;align-items:center;gap:6px;}
    .gt-meta i{width:14px;height:14px;}
    .gt-hero-actions{margin-top:18px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
    .wg-btn-gold{background:var(--gold);color:#3a2e05;border:none;font-weight:800;}
    .wg-btn-gold:hover{filter:brightness(1.05);}
    .btn-ghost-light{background:rgba(255,255,255,0.14);color:#fff;border:1px solid rgba(255,255,255,0.28);}
    .btn-ghost-light:hover{background:rgba(255,255,255,0.22);}

    .gt-avatar-row{display:flex;flex-wrap:wrap;gap:6px;}
    .gt-avatar-more{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;background:var(--surface-sage);color:#2f694e;font-weight:800;font-size:13px;}
    .gt-next{display:flex;gap:14px;align-items:flex-start;}
    .gt-next-date{width:64px;flex-shrink:0;text-align:center;background:var(--surface-sage);border-radius:14px;padding:10px 6px;color:#2f694e;}
    .gt-next-date small{display:block;font-size:12px;font-weight:800;text-transform:uppercase;}
    .gt-next-date strong{display:block;font-size:26px;line-height:1;}
    .gt-next-info{flex:1;min-width:0;}
    .gt-quick-item{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:11px 0;border-bottom:1px solid var(--border);text-decoration:none;color:var(--text);font-weight:600;}
    .gt-quick-item:last-child{border-bottom:none;}
    .gt-quick-item i[data-lucide]{width:18px;height:18px;color:var(--blue);}
    .gt-res-item{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);font-size:14px;}
    .gt-back{background:none;border:none;cursor:pointer;color:var(--blue);font-weight:700;display:inline-flex;align-items:center;gap:6px;margin-bottom:16px;font-size:14px;}

    .wg-modal-ov{position:fixed;inset:0;background:rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;z-index:60;padding:20px;}
    .wg-modal{background:#fff;border-radius:16px;max-width:480px;width:100%;max-height:80vh;overflow:auto;padding:22px;box-shadow:var(--shadow);}

    @media (max-width:1100px){
        .gt-row1,.gt-body{grid-template-columns:1fr;}
        .gt-hero{flex-direction:column;}
        .gt-hero-thumb{width:100%;height:160px;}
    }
</style>

<div x-data="{ tab: new URLSearchParams(location.search).get('tab') || 'accueil', membersOpen:false, newThread:false }">

    @if(session('success'))<div class="flash-success"><i data-lucide="check-circle"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error"><i data-lucide="alert-circle"></i>{{ session('error') }}</div>@endif

    <div class="gt-breadcrumb">
        <a href="{{ route('member.work-groups') }}">Groupes de travail</a>
        <i data-lucide="chevron-right" style="width:14px;height:14px;"></i>
        <span style="color:var(--text);font-weight:600;">{{ $workGroup->name }}</span>
    </div>

    {{-- ===================== TABLEAU DE BORD (accueil) ===================== --}}
    <div x-show="tab==='accueil'" x-cloak>
        <div class="gt-row1">
            @include('member.work-groups.partials.dashboard._hero')
            @include('member.work-groups.partials.dashboard._about_card')
        </div>

        <div class="gt-body">
            <div class="gt-col">
                @include('member.work-groups.partials._activity')
                @include('member.work-groups.partials.dashboard._projects_card')
                @if($workGroup->has_forum)@include('member.work-groups.partials._recent_discussions')@endif
            </div>
            <div class="gt-col">
                @include('member.work-groups.partials.dashboard._members_card')
                @if($nextEvent)@include('member.work-groups.partials.dashboard._next_meeting_card')@endif
                @if(count($quickLinks))@include('member.work-groups.partials.dashboard._quick_links_card')@endif
                @if($canViewResources)@include('member.work-groups.partials.dashboard._resources_card')@endif
            </div>
        </div>
    </div>

    {{-- ===================== VUES DÉTAILLÉES (Voir tous) ===================== --}}
    @if($workGroup->has_forum)
    <div x-show="tab==='discussions'" x-cloak>
        <button type="button" class="gt-back" @click="tab='accueil'"><i data-lucide="arrow-left"></i>Retour au tableau de bord</button>
        @includeIf('member.work-groups.partials._forum')
    </div>
    @endif

    @if($canViewResources)
    <div x-show="tab==='documents'" x-cloak>
        <button type="button" class="gt-back" @click="tab='accueil'"><i data-lucide="arrow-left"></i>Retour au tableau de bord</button>
        @includeIf('member.work-groups.partials._documents')
    </div>
    <div x-show="tab==='ressources'" x-cloak>
        <button type="button" class="gt-back" @click="tab='accueil'"><i data-lucide="arrow-left"></i>Retour au tableau de bord</button>
        @includeIf('member.work-groups.partials._links')
    </div>
    @endif

    <div x-show="tab==='projets'" x-cloak>
        <button type="button" class="gt-back" @click="tab='accueil'"><i data-lucide="arrow-left"></i>Retour au tableau de bord</button>
        @include('member.work-groups.partials._projects')
    </div>

    <div x-show="tab==='evenements'" x-cloak>
        <button type="button" class="gt-back" @click="tab='accueil'"><i data-lucide="arrow-left"></i>Retour au tableau de bord</button>
        @include('member.work-groups.partials._events')
    </div>

    <div x-show="tab==='apropos'" x-cloak>
        <button type="button" class="gt-back" @click="tab='accueil'"><i data-lucide="arrow-left"></i>Retour au tableau de bord</button>
        @include('member.work-groups.partials._about')
    </div>

    @if($canManage)
    <div x-show="tab==='manage'" x-cloak>
        <button type="button" class="gt-back" @click="tab='accueil'"><i data-lucide="arrow-left"></i>Retour au tableau de bord</button>
        @includeIf('member.work-groups.partials._manage')
    </div>
    @endif

    {{-- ===================== MODALE MEMBRES ===================== --}}
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
