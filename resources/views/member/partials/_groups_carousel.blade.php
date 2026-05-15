@if($isCurrentMember)
<section>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <h2 style="margin:0;">Mes groupes & projets</h2>
        <a href="{{ route('member.work-groups') }}" class="text-link">
            <i data-lucide="arrow-right"></i>Voir tous mes groupes
        </a>
    </div>

    @if($myWorkGroups->count() === 0)
        <div class="card panel" style="text-align:center;padding:32px;">
            <i data-lucide="users" style="width:32px;height:32px;color:var(--muted);margin:0 auto 12px;display:block;"></i>
            <p style="margin:0 0 12px;color:var(--muted);">Vous n'êtes encore membre d'aucun groupe.</p>
            <a href="{{ route('member.work-groups') }}" class="btn btn-primary">
                <i data-lucide="users"></i>Rejoindre un groupe
            </a>
        </div>
    @else
        <div class="groups-carousel">
            @foreach($myWorkGroups as $gt)
            <a href="{{ route('member.work-groups.show', $gt) }}" class="group-card" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;">
                <div class="group-card-cover" style="background: {{ $gt->color ?? '#85B79D' }};{{ $gt->coverUrl() ? 'padding:0;' : '' }}">
                    @if($gt->coverUrl())
                        <img src="{{ $gt->coverUrl() }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <i data-lucide="{{ $gt->icon ?? 'leaf' }}"></i>
                    @endif
                    <div class="group-card-avatar" style="background: {{ $gt->color ?? '#85B79D' }};">
                        <i data-lucide="{{ $gt->icon ?? 'leaf' }}" style="color:white;"></i>
                    </div>
                </div>
                <div class="group-card-body">
                    <h3>{{ $gt->name }}</h3>
                    <span class="subtitle">{{ \Str::limit($gt->description ?? 'Groupe thématique', 80) }}</span>
                    <div class="group-card-chips">
                        <span><i data-lucide="message-circle"></i>{{ $gt->forum_threads_count }} discussion{{ $gt->forum_threads_count > 1 ? 's' : '' }}</span>
                        <span><i data-lucide="file-text"></i>{{ $gt->resources_count }} ressource{{ $gt->resources_count > 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</section>
@endif
