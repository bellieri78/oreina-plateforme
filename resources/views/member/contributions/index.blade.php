@extends('layouts.member')

@section('title', 'Mes contributions')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-xl font-bold text-oreina-dark">Mes contributions</h1>
        <p class="text-sm text-gray-400 mt-0.5">Vos groupes de travail et participations</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="member-stat">
            <div class="member-stat-value">{{ $stats['groups_count'] }}</div>
            <div class="member-stat-label">Groupe{{ $stats['groups_count'] > 1 ? 's' : '' }} rejoint{{ $stats['groups_count'] > 1 ? 's' : '' }}</div>
        </div>
        <div class="member-stat">
            <div class="member-stat-value">{{ $stats['total_members'] }}</div>
            <div class="member-stat-label">Membres dans vos GT</div>
        </div>
    </div>

    {{-- My Groups --}}
    @if($myGroups->count() > 0)
        <div class="member-card">
            <div class="member-card-header">
                <span class="dot" style="background: #2C5F2D;"></span>
                Mes groupes de travail
            </div>
            <div class="space-y-3">
                @foreach($myGroups as $gt)
                <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50/50 border border-gray-100">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: {{ $gt->color ?? '#2C5F2D' }}; flex-shrink: 0; margin-top: 4px;"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <h3 class="font-semibold text-sm text-oreina-dark">{{ $gt->name }}</h3>
                            @if($gt->pivot?->role === 'leader')
                                <span class="text-[10px] font-semibold bg-amber-500/10 text-amber-600 px-2 py-0.5 rounded-full">Responsable</span>
                            @endif
                        </div>
                        @if($gt->description)
                            <p class="text-xs text-gray-500 leading-relaxed">{{ Str::limit($gt->description, 150) }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-1.5">
                            <span class="text-[11px] text-gray-400">{{ $gt->members_count }} membre{{ $gt->members_count > 1 ? 's' : '' }}</span>
                            @if($gt->website_url)
                                <a href="{{ $gt->website_url }}" target="_blank" rel="noopener" class="text-[11px] text-oreina-green font-medium hover:underline">Voir le site &rarr;</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="member-card text-center py-8">
            <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <p class="text-gray-500 text-sm mb-1">Vous ne participez encore a aucun groupe de travail</p>
            <p class="text-gray-400 text-xs mb-4">Explorez les groupes actifs pour rejoindre ceux qui vous interessent</p>
            <a href="{{ route('member.work-groups') }}" class="btn-member text-xs">
                Voir les groupes de travail
            </a>
        </div>
    @endif
</div>
@endsection
