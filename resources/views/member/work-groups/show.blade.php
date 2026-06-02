@extends('layouts.member')
@section('title', $workGroup->name)
@section('page-title', $workGroup->name)
@section('page-subtitle', 'Groupe de travail')

@section('content')
<style>
    .gt-page .panel-head{margin-bottom:14px;}
    .gt-page .panel-head h2{font-size:16px;line-height:1.3;letter-spacing:-0.01em;}
    .gt-page .card.panel{padding:18px;}
    .gt-page h3{font-size:14px;}

    /* Champs de formulaire (alignes sur les autres formulaires) */
    .gt-page .wg-field-label{display:block;font-size:13px;font-weight:700;margin-bottom:5px;color:var(--text);}
    .gt-page .wg-field{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:10px;font-size:14px;font-family:inherit;background:#fff;color:var(--text);}
    .gt-page .wg-field:focus{outline:none;border-color:var(--sage);box-shadow:0 0 0 3px rgba(133,183,157,0.25);}

    .gt-breadcrumb{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);margin-bottom:14px;}
    .gt-breadcrumb a{color:var(--muted);text-decoration:none;}
    .gt-breadcrumb a:hover{color:var(--forest);}

    /* ===== Bandeau header (hero vert + carte À propos inséré) ===== */
    .gt-banner{display:flex;align-items:flex-start;gap:22px;background:linear-gradient(135deg,#16302B 0%,#2C5F2D 70%);border-radius:18px;padding:24px;color:#fff;box-shadow:var(--shadow);margin-bottom:20px;}
    .gt-logo{width:96px;height:96px;border-radius:50%;background:#fff;overflow:hidden;display:grid;place-items:center;flex-shrink:0;}
    .gt-logo img{width:100%;height:100%;object-fit:cover;}
    .gt-logo i{width:42px;height:42px;color:var(--forest);}
    .gt-banner-hero{flex:1;min-width:0;}
    .gt-hero-title{margin:0;font-size:clamp(24px,2.4vw,34px);font-weight:800;letter-spacing:-0.02em;display:inline-flex;align-items:center;gap:12px;flex-wrap:wrap;color:#fff;}
    .gt-pill{font-size:12px;font-weight:700;padding:4px 12px;border-radius:999px;background:rgba(133,183,157,0.32);color:#e4f4ea;}
    .gt-hero-desc{margin:10px 0 0;color:rgba(255,255,255,0.85);font-size:14.5px;line-height:1.55;max-width:560px;}
    .gt-chips{margin:16px 0 0;display:flex;flex-wrap:wrap;gap:10px;}
    .gt-chip{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,0.10);border:1px solid rgba(255,255,255,0.18);padding:7px 13px;border-radius:999px;font-size:13px;font-weight:600;color:#eaf3ee;}
    .gt-chip i{width:15px;height:15px;}
    .gt-hero-actions{margin-top:18px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
    .wg-btn-gold{background:var(--gold);color:#3a2e05;border:none;font-weight:800;}
    .wg-btn-gold:hover{filter:brightness(1.05);}
    .btn-ghost-light{background:rgba(255,255,255,0.10);color:#fff;border:1px solid rgba(255,255,255,0.30);}
    .btn-ghost-light:hover{background:rgba(255,255,255,0.20);}
    .gt-banner-about{width:300px;flex-shrink:0;background:#fff;border-radius:14px;padding:18px;color:var(--text);box-shadow:0 10px 28px rgba(0,0,0,0.20);}
    .gt-coord-pill{font-size:11px;font-weight:700;padding:3px 10px;border-radius:999px;background:var(--surface-sage);color:#2f694e;white-space:nowrap;}

    /* ===== Corps 3 colonnes ===== */
    .gt-body{display:grid;grid-template-columns:1.6fr 1fr 1fr;gap:18px;align-items:start;}
    .gt-col{display:flex;flex-direction:column;gap:18px;}

    /* Activité */
    .gt-feed-item{display:flex;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);}
    .gt-feed-item:last-child{border-bottom:none;}
    .gt-feed-ic{width:34px;height:34px;border-radius:9px;display:grid;place-items:center;flex-shrink:0;}
    .gt-feed-ic i{width:16px;height:16px;}
    .gt-feed-body{flex:1;min-width:0;}
    .gt-feed-time{font-size:12px;color:var(--muted);white-space:nowrap;flex-shrink:0;margin-left:8px;}

    /* Projets 3-up */
    .gt-proj-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
    .gt-proj{border:1px solid var(--border);border-radius:12px;overflow:hidden;display:flex;flex-direction:column;}
    .gt-proj-thumb{height:60px;display:grid;place-items:center;}
    .gt-proj-thumb i{width:24px;height:24px;color:#fff;opacity:.92;}
    .gt-proj-bd{padding:10px;display:flex;flex-direction:column;gap:7px;flex:1;}
    .gt-proj-foot{border-top:1px solid var(--border);padding:8px 10px;}

    /* Membres */
    .gt-coord-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .gt-avatar-row{display:flex;flex-wrap:wrap;gap:6px;}
    .gt-avatar-more{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;background:var(--surface-sage);color:#2f694e;font-weight:800;font-size:13px;}

    /* carte reunion */
    .gt-next{display:flex;gap:16px;align-items:flex-start;}
    .gt-next-date{text-align:center;flex-shrink:0;min-width:46px;}
    .gt-next-date strong{display:block;font-size:30px;font-weight:800;line-height:1;color:#2f694e;}
    .gt-next-date small{display:block;font-size:12px;font-weight:700;text-transform:uppercase;color:#2f694e;margin-top:2px;}
    .gt-next-info{flex:1;min-width:0;}

    /* Listes (ressources, liens) avec carré coloré */
    .gt-listrow{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);font-size:14px;}
    .gt-listrow:last-child{border-bottom:none;}
    .gt-sq{width:30px;height:30px;border-radius:8px;display:grid;place-items:center;flex-shrink:0;}
    .gt-sq i{width:15px;height:15px;}
    .gt-quick-item{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:11px 0;border-bottom:1px solid var(--border);text-decoration:none;color:var(--text);font-weight:600;}
    .gt-quick-item:last-child{border-bottom:none;}

    .gt-fullbtn{display:block;width:100%;text-align:center;margin-top:14px;padding:9px;border-radius:10px;background:var(--surface-soft);border:1px solid var(--border);color:var(--text);font-weight:700;font-size:13px;cursor:pointer;text-decoration:none;}
    .gt-fullbtn:hover{background:#f0ece6;}

    /* Discussions pleine largeur */
    .gt-disc{margin-top:18px;}
    .gt-disc-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
    .gt-disc-item{display:flex;align-items:flex-start;gap:10px;}

    .gt-back{background:none;border:none;cursor:pointer;color:var(--blue);font-weight:700;display:inline-flex;align-items:center;gap:6px;margin-bottom:16px;font-size:14px;}
    .wg-modal-ov{position:fixed;inset:0;background:rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;z-index:60;padding:20px;}
    .wg-modal{background:#fff;border-radius:16px;max-width:480px;width:100%;max-height:80vh;overflow:auto;padding:22px;box-shadow:var(--shadow);}

    @media (max-width:1240px){
        .gt-body{grid-template-columns:1fr 1fr;}
        .gt-disc-grid{grid-template-columns:1fr 1fr;}
    }
    @media (max-width:960px){
        .gt-banner{flex-direction:column;}
        .gt-banner-about{width:100%;}
        .gt-body,.gt-disc-grid,.gt-proj-grid{grid-template-columns:1fr;}
    }
</style>

<div class="gt-page" x-data="{ tab: new URLSearchParams(location.search).get('tab') || 'accueil', membersOpen:false, newThread:false, planMeeting:false }">

    @if(session('success'))<div class="flash-success"><i data-lucide="check-circle"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error"><i data-lucide="alert-circle"></i>{{ session('error') }}</div>@endif

    <div class="gt-breadcrumb">
        <a href="{{ route('member.work-groups') }}">Groupes de travail</a>
        <i data-lucide="chevron-right" style="width:14px;height:14px;"></i>
        <span style="color:var(--text);font-weight:600;">{{ $workGroup->name }}</span>
    </div>

    {{-- ===================== TABLEAU DE BORD (accueil) ===================== --}}
    <div x-show="tab==='accueil'" x-cloak>

        {{-- Bandeau header : hero + carte À propos --}}
        <div class="gt-banner">
            @include('member.work-groups.partials.dashboard._hero')
            @include('member.work-groups.partials.dashboard._about_card')
        </div>

        {{-- Corps 3 colonnes --}}
        <div class="gt-body">
            <div class="gt-col">
                @include('member.work-groups.partials._activity')
                @include('member.work-groups.partials.dashboard._projects_card')
            </div>
            <div class="gt-col">
                @include('member.work-groups.partials.dashboard._members_card')
                @if($canViewResources)@include('member.work-groups.partials.dashboard._resources_card')@endif
            </div>
            <div class="gt-col">
                @include('member.work-groups.partials.dashboard._next_meeting_card')
                @include('member.work-groups.partials.dashboard._quick_links_card')
            </div>
        </div>

        {{-- Discussions récentes — pleine largeur --}}
        @if($workGroup->has_forum)
        <div class="gt-disc">
            @include('member.work-groups.partials.dashboard._discussions_card')
        </div>
        @endif
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
