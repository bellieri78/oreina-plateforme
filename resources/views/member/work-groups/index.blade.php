@extends('layouts.member')

@section('title', 'Groupes de travail')
@section('page-title', 'Groupes de travail')
@section('page-subtitle', 'Les espaces collaboratifs du réseau OREINA')

@section('content')
<div class="space-y-6">
    {{-- GT Grid --}}
    @if($workGroups->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($workGroups as $gt)
            <a href="{{ route('member.work-groups.show', $gt->slug) }}" class="card panel" style="border-left: 4px solid {{ $gt->color ?? 'var(--sage)' }}; padding-left: 1rem; display:block;">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-bold text-sm" style="color:var(--forest)">{{ $gt->name }}</h3>
                    @if(in_array($gt->id, $myGroupIds))
                        <span class="badge sage flex-shrink-0 ml-2" style="font-size:10px">Membre</span>
                    @endif
                </div>

                @if($gt->description)
                    <p class="text-xs mb-3 leading-relaxed" style="color:var(--muted)">{{ Str::limit($gt->description, 120) }}</p>
                @endif

                <div class="flex items-center justify-between mt-auto">
                    <span class="text-xs" style="color:var(--muted)">
                        <i data-lucide="users" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:2px"></i>
                        {{ $gt->members_count }} membre{{ $gt->members_count > 1 ? 's' : '' }}
                    </span>

                    <span class="text-xs font-medium text-link">
                        Voir le groupe &rarr;
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    @else
        <div class="card panel text-center py-8">
            <i data-lucide="users" class="mx-auto mb-3" style="width:40px;height:40px;display:block;color:var(--border)"></i>
            <p class="text-sm" style="color:var(--muted)">Aucun groupe de travail pour le moment</p>
        </div>
    @endif
</div>
@endsection
