@extends('layouts.member')
@section('title', $thread->title)
@section('page-title', $thread->title)
@section('page-subtitle', $workGroup->name . ' — Discussions')

@section('content')
<div>
    @if(session('success'))<div class="flash-success"><i data-lucide="check-circle"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash-error"><i data-lucide="alert-circle"></i>{{ session('error') }}</div>@endif

    <a href="{{ route('member.work-groups.show', [$workGroup, 'tab' => 'discussions']) }}" class="text-link" style="margin-bottom:14px;display:inline-flex;">
        <i data-lucide="arrow-left"></i>Forum du groupe
    </a>

    <div class="card panel">
        <div class="panel-head">
            <div>
                <h2 style="margin:0;">
                    @if($thread->is_pinned)<i data-lucide="pin" style="width:18px;height:18px;"></i>@endif
                    {{ $thread->title }}
                </h2>
                <small style="color:var(--muted);">
                    {{ $thread->category->name }} · par {{ $thread->author?->full_name ?? $thread->author?->first_name ?? 'Membre supprimé' }}
                    @if($thread->is_locked)· <span class="badge gold">Verrouillé</span>@endif
                </small>
            </div>
            @if($canManage)
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <form method="POST" action="{{ route('member.work-groups.forum.threads.pin', [$workGroup, $thread]) }}">@csrf
                    <button class="btn btn-secondary" style="height:32px;padding:0 10px;font-size:12px;"><i data-lucide="pin"></i>{{ $thread->is_pinned ? 'Désépingler' : 'Épingler' }}</button>
                </form>
                <form method="POST" action="{{ route('member.work-groups.forum.threads.lock', [$workGroup, $thread]) }}">@csrf
                    <button class="btn btn-secondary" style="height:32px;padding:0 10px;font-size:12px;"><i data-lucide="lock"></i>{{ $thread->is_locked ? 'Déverrouiller' : 'Verrouiller' }}</button>
                </form>
                <form method="POST" action="{{ route('member.work-groups.forum.threads.destroy', [$workGroup, $thread]) }}" onsubmit="return confirm('Supprimer ce fil et tous ses messages ?');">@csrf @method('DELETE')
                    <button class="btn btn-secondary" style="height:32px;padding:0 10px;font-size:12px;color:#dc2626;"><i data-lucide="trash-2"></i>Supprimer</button>
                </form>
            </div>
            @endif
        </div>

        <div style="display:grid;gap:14px;">
            @foreach($posts as $post)
            <div class="resource-item" style="grid-template-columns:1fr;" x-data="{ editing:false }">
                <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;">
                    <div style="display:flex;gap:10px;align-items:center;">
                        <div class="reseau-avatar" style="margin:0;">
                            @if($post->author?->photo_path)<img src="{{ \Storage::url($post->author->photo_path) }}" alt="">@else{{ strtoupper(substr($post->author?->first_name ?? '?',0,1)) }}@endif
                        </div>
                        <div>
                            <strong>{{ $post->author?->full_name ?? $post->author?->first_name ?? 'Membre supprimé' }}</strong>
                            <small style="display:block;color:var(--muted);">{{ $post->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @can('update', $post)
                    <div style="display:flex;gap:8px;">
                        <button type="button" @click="editing=!editing" class="text-link" style="background:none;border:none;cursor:pointer;font-size:12px;"><i data-lucide="pencil"></i></button>
                        <form method="POST" action="{{ route('member.work-groups.forum.posts.destroy', [$workGroup, $post]) }}" onsubmit="return confirm('Supprimer ce message ?');">@csrf @method('DELETE')
                            <button class="text-link" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:12px;"><i data-lucide="trash-2"></i></button>
                        </form>
                    </div>
                    @endcan
                </div>
                <div style="margin-top:10px;line-height:1.7;" x-show="!editing">{!! $post->renderedContent() !!}</div>
                @can('update', $post)
                <form method="POST" action="{{ route('member.work-groups.forum.posts.update', [$workGroup, $post]) }}" x-show="editing" x-cloak style="margin-top:10px;display:grid;gap:8px;">
                    @csrf @method('PUT')
                    <textarea name="content" required rows="4" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;">{{ $post->content }}</textarea>
                    <div style="display:flex;gap:8px;">
                        <button class="btn btn-primary" style="height:32px;padding:0 12px;font-size:12px;">Enregistrer</button>
                        <button type="button" @click="editing=false" class="btn btn-secondary" style="height:32px;padding:0 12px;font-size:12px;">Annuler</button>
                    </div>
                </form>
                @endcan
            </div>
            @endforeach
        </div>

        <div style="margin-top:16px;">{{ $posts->links() }}</div>

        @if($canParticipate && (! $thread->is_locked || $canManage))
        <form method="POST" action="{{ route('member.work-groups.forum.posts.store', [$workGroup, $thread]) }}" style="margin-top:18px;display:grid;gap:8px;">
            @csrf
            <textarea name="content" required rows="4" placeholder="Votre réponse…" class="form-input" style="padding:10px;border:1px solid var(--border);border-radius:10px;"></textarea>
            <button class="btn btn-primary" style="justify-self:start;"><i data-lucide="send"></i>Répondre</button>
        </form>
        @elseif($thread->is_locked)
        <p style="margin-top:16px;color:var(--muted);"><i data-lucide="lock" style="width:14px;height:14px;display:inline;"></i> Ce fil est verrouillé.</p>
        @elseif(! $canParticipate)
        <div style="margin-top:16px;background:var(--surface-soft,#f5f5f5);border-radius:12px;padding:14px;">
            Rejoignez ce groupe pour participer.
            <form method="POST" action="{{ route('member.work-groups.join', $workGroup) }}" style="display:inline;margin-left:8px;">@csrf
                <button class="btn btn-primary" style="height:32px;padding:0 12px;font-size:12px;">{{ $workGroup->join_policy === 'open' ? 'Rejoindre' : 'Demander à rejoindre' }}</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
