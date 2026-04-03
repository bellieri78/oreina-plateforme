@extends('layouts.member')

@section('title', 'Mes contributions')
@section('page-title', 'Mes contributions')
@section('page-subtitle', 'Vos participations aux groupes et projets')

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="stat">
            <strong>{{ $stats['groups_count'] }}</strong>
            <span>Groupe{{ $stats['groups_count'] > 1 ? 's' : '' }} rejoint{{ $stats['groups_count'] > 1 ? 's' : '' }}</span>
        </div>
        <div class="stat">
            <strong>{{ $stats['total_members'] }}</strong>
            <span>Membres dans vos GT</span>
        </div>
    </div>

    {{-- My Groups --}}
    @if($myGroups->count() > 0)
        <div class="card panel">
            <div class="panel-head">
                <span class="dot" style="background: var(--sage);"></span>
                <div>
                    <h2>Mes groupes de travail</h2>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($myGroups as $gt)
                <div class="flex items-start gap-3 p-3 rounded-xl" style="background:var(--surface-sage); border:1px solid var(--border)">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: {{ $gt->color ?? 'var(--sage)' }}; flex-shrink: 0; margin-top: 4px;"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <h3 class="font-semibold text-sm" style="color:var(--forest)">{{ $gt->name }}</h3>
                            @if($gt->pivot?->role === 'leader')
                                <span class="badge" style="background:var(--surface-amber);color:var(--amber);font-size:10px">Responsable</span>
                            @endif
                        </div>
                        @if($gt->description)
                            <p class="text-xs leading-relaxed" style="color:var(--muted)">{{ Str::limit($gt->description, 150) }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-1.5">
                            <span class="text-xs" style="color:var(--muted)">{{ $gt->members_count }} membre{{ $gt->members_count > 1 ? 's' : '' }}</span>
                            @if($gt->website_url)
                                <a href="{{ $gt->website_url }}" target="_blank" rel="noopener" class="text-xs font-medium text-link hover:underline">Voir le site &rarr;</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="card panel text-center py-8">
            <i data-lucide="box" class="mx-auto mb-3" style="width:40px;height:40px;display:block;color:var(--border)"></i>
            <p class="text-sm mb-1" style="color:var(--muted)">Vous ne participez encore a aucun groupe de travail</p>
            <p class="text-xs mb-4" style="color:var(--muted)">Explorez les groupes actifs pour rejoindre ceux qui vous interessent</p>
            <a href="{{ route('member.work-groups') }}" class="btn btn-primary text-xs">
                Voir les groupes de travail
            </a>
        </div>
    @endif
</div>
@endsection
