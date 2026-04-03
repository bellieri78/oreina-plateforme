@extends('layouts.member')

@section('title', 'Groupes de travail')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-xl font-bold text-oreina-dark">Groupes de travail</h1>
        <p class="text-sm text-gray-400 mt-0.5">Découvrez les groupes de travail actifs de l'association</p>
    </div>

    {{-- GT Grid --}}
    @if($workGroups->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($workGroups as $gt)
            <a href="{{ route('member.work-groups.show', $gt->slug) }}" class="member-card" style="border-left: 4px solid {{ $gt->color ?? '#2C5F2D' }}; padding-left: 1rem; display:block;">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-bold text-sm text-oreina-dark">{{ $gt->name }}</h3>
                    @if(in_array($gt->id, $myGroupIds))
                        <span class="inline-flex items-center text-[10px] font-semibold bg-green-500/10 text-green-600 px-2 py-0.5 rounded-full flex-shrink-0 ml-2">Membre</span>
                    @endif
                </div>

                @if($gt->description)
                    <p class="text-xs text-gray-500 mb-3 leading-relaxed">{{ Str::limit($gt->description, 120) }}</p>
                @endif

                <div class="flex items-center justify-between mt-auto">
                    <span class="text-[11px] text-gray-400">
                        <svg class="w-3.5 h-3.5 inline-block mr-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $gt->members_count }} membre{{ $gt->members_count > 1 ? 's' : '' }}
                    </span>

                    <span class="text-xs text-oreina-green font-medium">
                        Voir le groupe &rarr;
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    @else
        <div class="member-card text-center py-8">
            <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="text-gray-400 text-sm">Aucun groupe de travail pour le moment</p>
        </div>
    @endif
</div>
@endsection
