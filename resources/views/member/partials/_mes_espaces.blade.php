<article class="card panel">
    <div class="panel-head">
        <div>
            <h2>Mes Espaces participatifs</h2>
        </div>
        <a href="{{ route('member.work-groups') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir tous mes espaces</a>
    </div>

    <div class="spaces-list">
        @forelse($myWorkGroups->take(3) as $gt)
            @php
                if ($gt->forum_threads_count > 0) {
                    $chip = ['sage', $gt->forum_threads_count . ' discussion' . ($gt->forum_threads_count > 1 ? 's' : '')];
                } elseif ($gt->resources_count > 0) {
                    $chip = ['gold', $gt->resources_count . ' ressource' . ($gt->resources_count > 1 ? 's' : '')];
                } else {
                    $chip = ['muted', 'Nouveau'];
                }
            @endphp
            <a href="{{ route('member.work-groups.show', $gt) }}" class="space-row">
                <span class="space-row-icon" style="background: {{ $gt->color ?? '#85B79D' }};">
                    <i data-lucide="{{ $gt->icon ?? 'leaf' }}"></i>
                </span>
                <span class="space-row-body">
                    <strong>{{ $gt->name }}</strong>
                    <span>{{ \Str::limit($gt->description ?? 'Groupe thématique', 42) }} · {{ $gt->members_count }} membre{{ $gt->members_count > 1 ? 's' : '' }}</span>
                </span>
                <span class="space-row-chip {{ $chip[0] }}">{{ $chip[1] }}</span>
                <i data-lucide="chevron-right" class="chev"></i>
            </a>
        @empty
            <div style="text-align:center;padding:20px;color:var(--muted);">
                <i data-lucide="users" style="width:28px;height:28px;margin:0 auto 10px;display:block;opacity:0.4;"></i>
                <p style="margin:0 0 12px;font-size:14px;">Vous n'êtes encore membre d'aucun groupe.</p>
                <a href="{{ route('member.work-groups') }}" class="btn btn-primary" style="height:36px;padding:0 14px;font-size:13px;">
                    <i data-lucide="users" style="width:14px;height:14px;"></i>Rejoindre un groupe
                </a>
            </div>
        @endforelse

        {{-- Messagerie adhérents --}}
        <a href="{{ route('member.chat') }}" class="space-row">
            <span class="space-row-icon" style="background: #7c3aed;">
                <i data-lucide="message-circle"></i>
            </span>
            <span class="space-row-body">
                <strong>Messagerie adhérents</strong>
                <span>Échanges entre tous les membres</span>
            </span>
            @if($chatUnreadCount > 0)
                <span class="space-row-chip sage">{{ $chatUnreadCount }} nouveau{{ $chatUnreadCount > 1 ? 'x' : '' }} message{{ $chatUnreadCount > 1 ? 's' : '' }}</span>
            @else
                <span class="space-row-chip muted">Accéder</span>
            @endif
            <i data-lucide="chevron-right" class="chev"></i>
        </a>
    </div>
</article>
