{{-- Carte Fiche contact (rattachement User <-> Member) --}}
<div class="card" style="margin-bottom: 1.5rem;" x-data="memberLink({{ $user->id }})">
    <div class="card-header">
        <h3 class="card-title">Fiche contact</h3>
    </div>
    <div class="card-body">
        @if($user->member)
            <div style="margin-bottom: 0.75rem;">
                <a href="{{ route('admin.members.show', $user->member) }}" style="color: var(--color-primary); font-weight: 600;">
                    {{ $user->member->full_name }}
                </a>
                @if($user->member->member_number)
                    <span class="badge badge-secondary" style="margin-left: 0.5rem;">{{ $user->member->member_number }}</span>
                @endif
            </div>
            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
                {{ $user->member->email }}
            </div>
            <form action="{{ route('admin.users.unlink-member', $user) }}" method="POST"
                  onsubmit="return confirm('Détacher cette fiche du compte ?');">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">Détacher</button>
            </form>
        @else
            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
                Ce compte n'est rattaché à aucune fiche contact.
            </p>

            @if($memberSuggestions->isNotEmpty())
                <h4 style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Suggestions</h4>
                @foreach($memberSuggestions as $candidate)
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="min-width: 0;">
                            <div style="font-weight: 600; font-size: 0.875rem;">{{ $candidate->full_name }}</div>
                            <div style="font-size: 0.75rem; color: #6b7280; overflow: hidden; text-overflow: ellipsis;">{{ $candidate->email }}</div>
                        </div>
                        <form action="{{ route('admin.users.link-member', $user) }}" method="POST">
                            @csrf
                            <input type="hidden" name="member_id" value="{{ $candidate->id }}">
                            <button type="submit" class="btn btn-primary btn-sm">Rattacher</button>
                        </form>
                    </div>
                @endforeach
            @endif

            <div style="margin-top: 1rem;">
                <input type="text" x-model="query" @input.debounce.300ms="search()"
                       placeholder="Rechercher une fiche (nom, email, n°)..."
                       class="form-input" style="width: 100%; margin-bottom: 0.5rem;">
                <template x-if="loading">
                    <p style="font-size: 0.75rem; color: #6b7280;">Recherche...</p>
                </template>
                <template x-for="result in results" :key="result.id">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="min-width: 0;">
                            <div style="font-weight: 600; font-size: 0.875rem;" x-text="result.name"></div>
                            <div style="font-size: 0.75rem; color: #6b7280;" x-text="result.email"></div>
                        </div>
                        <form :action="linkUrl" method="POST">
                            @csrf
                            <input type="hidden" name="member_id" :value="result.id">
                            <button type="submit" class="btn btn-primary btn-sm">Rattacher</button>
                        </form>
                    </div>
                </template>
                <template x-if="!loading && query.length >= 2 && results.length === 0">
                    <p style="font-size: 0.75rem; color: #6b7280;">Aucune fiche sans compte ne correspond.</p>
                </template>
            </div>
        @endif
    </div>
</div>

@once
@push('scripts')
<script>
function memberLink(userId) {
    return {
        query: '',
        results: [],
        loading: false,
        searchUrl: '{{ url('extranet/users') }}/' + userId + '/member-search',
        linkUrl: '{{ url('extranet/users') }}/' + userId + '/link-member',
        async search() {
            if (this.query.length < 2) { this.results = []; return; }
            this.loading = true;
            try {
                const res = await fetch(this.searchUrl + '?q=' + encodeURIComponent(this.query), {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.results = data.results || [];
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endpush
@endonce
