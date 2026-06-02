<div class="card panel">
    <div class="panel-head">
        <div><h2>Discussions récentes</h2></div>
        <button type="button" @click="tab='discussions'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;"><i data-lucide="arrow-right"></i>Voir toutes les discussions</button>
    </div>

    @if($recentThreads->count())
    <div class="gt-disc-grid">
        @foreach($recentThreads->take(3) as $thread)
        <a href="{{ route('member.work-groups.forum.threads.show', [$workGroup, $thread]) }}" class="gt-disc-item" style="text-decoration:none;color:inherit;">
            <span class="gt-sq" style="background:#e7f3ec;color:#2f694e;"><i data-lucide="{{ $thread->is_pinned ? 'pin' : 'message-square' }}"></i></span>
            <span style="flex:1;min-width:0;">
                <strong style="display:block;font-size:13.5px;line-height:1.35;">{{ $thread->title }}</strong>
                <small style="color:var(--muted);">{{ $thread->author?->first_name ?? 'Anonyme' }} · {{ $thread->posts_count }} réponse{{ $thread->posts_count > 1 ? 's' : '' }}</small>
            </span>
            <small style="color:var(--muted);white-space:nowrap;">{{ optional($thread->last_posted_at ?? $thread->created_at)->diffForHumans() }}</small>
        </a>
        @endforeach
    </div>
    @else
    <p style="color:var(--muted);padding:8px 0;">Aucune discussion pour le moment.</p>
    @endif
</div>
