@extends('layouts.member')
@section('title', 'Groupes de travail')
@section('page-title', 'Groupes de travail')
@section('page-subtitle', 'Espaces d\'échange et de création collaborative')

@section('content')
<section>
    @if(session('success'))<div class="flash-success"><i data-lucide="check-circle"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error"><i data-lucide="alert-circle"></i>{{ session('error') }}</div>@endif

    <div class="groups-carousel" style="grid-auto-flow: row; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); display:grid;">
        @forelse($workGroups as $g)
        @php($status = $myStatuses[$g->id] ?? null)
        <article class="group-card">
            <div class="group-card-cover" style="background: {{ $g->color ?? '#85B79D' }};">
                <i data-lucide="{{ $g->icon ?? 'users' }}"></i>
            </div>
            <div class="group-card-body">
                <h3>{{ $g->name }}</h3>
                <span class="subtitle">{{ \Str::limit($g->description ?? 'Groupe thématique', 90) }}</span>
                <div class="group-card-chips">
                    <span><i data-lucide="users"></i>{{ $g->active_members_count }} membres</span>
                    <span><i data-lucide="{{ $g->join_policy === 'open' ? 'unlock' : 'lock' }}"></i>{{ $g->join_policy === 'open' ? 'Ouvert' : 'Sur demande' }}</span>
                </div>
                <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
                    @if($status === 'active')
                        <a href="{{ route('member.work-groups.show', $g) }}" class="btn btn-primary" style="height:36px;padding:0 14px;font-size:13px;"><i data-lucide="arrow-right"></i>Accéder</a>
                        <form method="POST" action="{{ route('member.work-groups.leave', $g) }}" onsubmit="return confirm('Quitter ce groupe ?');">@csrf @method('DELETE')
                            <button class="btn btn-secondary" style="height:36px;padding:0 14px;font-size:13px;">Quitter</button>
                        </form>
                    @elseif($status === 'pending')
                        <span class="badge gold">Demande en attente</span>
                        <a href="{{ route('member.work-groups.show', $g) }}" class="text-link"><i data-lucide="eye"></i>Aperçu</a>
                    @elseif($g->join_policy === 'open')
                        <form method="POST" action="{{ route('member.work-groups.join', $g) }}">@csrf
                            <button class="btn btn-primary" style="height:36px;padding:0 14px;font-size:13px;"><i data-lucide="plus"></i>Rejoindre</button>
                        </form>
                        <a href="{{ route('member.work-groups.show', $g) }}" class="text-link"><i data-lucide="eye"></i>Aperçu</a>
                    @else
                        <form method="POST" action="{{ route('member.work-groups.join', $g) }}">@csrf
                            <button class="btn btn-primary" style="height:36px;padding:0 14px;font-size:13px;"><i data-lucide="send"></i>Demander à rejoindre</button>
                        </form>
                        <a href="{{ route('member.work-groups.show', $g) }}" class="text-link"><i data-lucide="eye"></i>Aperçu</a>
                    @endif
                </div>
            </div>
        </article>
        @empty
        <p style="color:var(--muted);">Aucun groupe de travail pour le moment.</p>
        @endforelse
    </div>
</section>
@endsection
