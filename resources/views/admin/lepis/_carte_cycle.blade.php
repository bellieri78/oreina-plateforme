<div class="card mb-4">
    <div class="card-header" style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
        <h3 class="card-title" style="margin:0;">Cycle de publication</h3>
        <div style="display:flex;align-items:center;gap:0.5rem;">
            @if ($bulletin->isDraft())
                <span class="badge badge-default">Brouillon</span>
            @elseif ($bulletin->isInMembersPhase())
                <span class="badge badge-info">Phase adhérents</span>
                @if ($bulletin->published_to_members_at)
                    <span class="text-muted" style="font-size:0.85rem;">depuis le {{ $bulletin->published_to_members_at->format('d/m/Y') }}</span>
                @endif
            @elseif ($bulletin->isPublic())
                <span class="badge badge-success">Public</span>
                @if ($bulletin->published_public_at)
                    <span class="text-muted" style="font-size:0.85rem;">depuis le {{ $bulletin->published_public_at->format('d/m/Y') }}</span>
                @endif
            @endif
        </div>
    </div>
    <div class="card-body">

        @if ($bulletin->isPublic())
            <div style="margin-bottom:1.25rem;padding:0.75rem 1rem;background:#f8fafc;border:1px solid #e5e7eb;border-radius:6px;font-size:0.875rem;color:#6b7280;">
                <strong>Historique</strong><br>
                @if ($bulletin->published_to_members_at)
                    Phase adhérents : du {{ $bulletin->published_to_members_at->format('d/m/Y') }}
                    @if ($bulletin->published_public_at)
                        au {{ $bulletin->published_public_at->format('d/m/Y') }}
                    @endif
                    <br>
                @endif
                @if ($bulletin->published_public_at)
                    Public depuis le : {{ $bulletin->published_public_at->format('d/m/Y') }}
                @endif
            </div>
        @endif

        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
            @if ($bulletin->isDraft())
                <form method="POST" action="{{ route('admin.lepis.publish-to-members', $bulletin) }}"
                    onsubmit="return confirm('Publier aux adhérents ? Une liste Brevo sera créée.');">
                    @csrf
                    <button type="submit" class="btn btn-primary" {{ !$bulletin->pdf_path ? 'disabled' : '' }}>
                        Publier aux adhérents
                    </button>
                </form>
                @if (!$bulletin->pdf_path)
                    <span class="text-muted" style="font-size:0.875rem;">(PDF requis)</span>
                @endif

            @elseif ($bulletin->isInMembersPhase())
                <form method="POST" action="{{ route('admin.lepis.make-public', $bulletin) }}"
                    onsubmit="return confirm('Rendre ce bulletin public ?');">
                    @csrf
                    <button type="submit" class="btn btn-primary">Rendre public</button>
                </form>

                @if (!$bulletin->brevo_synced_at)
                    <form method="POST" action="{{ route('admin.lepis.revert-to-draft', $bulletin) }}"
                        onsubmit="return confirm('Remettre ce bulletin en brouillon ? La liste Brevo ne sera pas supprimée.');">
                        @csrf
                        <button type="submit" class="btn btn-ghost text-danger" style="font-size:0.875rem;">
                            Retour en brouillon
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>
