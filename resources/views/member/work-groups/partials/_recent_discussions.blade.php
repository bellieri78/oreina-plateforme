<div class="card panel">
    <div class="panel-head">
        <div><h2>Discussions récentes</h2></div>
        <button type="button" @click="tab='discussions'" class="text-link" style="background:none;border:none;cursor:pointer;font-size:13px;"><i data-lucide="arrow-right"></i>Tout voir</button>
    </div>

    @forelse($recentThreads as $thread)
    <div style="display:flex;align-items:flex-start;gap:10px;padding:9px 0;border-bottom:1px solid var(--border);">
        <i data-lucide="{{ $thread->is_pinned ? 'pin' : 'message-square' }}" style="width:16px;height:16px;flex-shrink:0;margin-top:3px;color:var(--blue);"></i>
        <div style="flex:1;min-width:0;">
            <a href="{{ route('member.work-groups.forum.threads.show', [$workGroup, $thread]) }}" class="text-link" style="display:block;font-weight:700;">{{ $thread->title }}</a>
            <small style="color:var(--muted);">{{ $thread->author?->first_name ?? 'Anonyme' }} · {{ $thread->posts_count }} message{{ $thread->posts_count > 1 ? 's' : '' }} · {{ optional($thread->last_posted_at ?? $thread->created_at)->diffForHumans() }}</small>
        </div>
    </div>
    @empty
    <p style="color:var(--muted);padding:8px 0;">Aucune discussion pour le moment.</p>
    @endforelse
</div>
