<div class="directory-modal" data-member-id="{{ $member->id }}">
    <div class="directory-modal-header">
        @if($member->photo_path)
            <img src="{{ \Storage::disk('public')->url($member->photo_path) }}" alt="Photo de {{ $member->first_name }} {{ $member->last_name }}" class="directory-modal-photo">
        @else
            <div class="directory-modal-photo-fallback" aria-hidden="true">
                {{ strtoupper(mb_substr($member->first_name, 0, 1)) }}{{ strtoupper(mb_substr($member->last_name, 0, 1)) }}
            </div>
        @endif
        <div>
            <h2>{{ $member->first_name }} {{ $member->last_name }}</h2>
            @php($dept = $member->directoryDepartment())
            @if($dept)
                <span class="badge">Département {{ $dept }}</span>
            @endif
        </div>
    </div>

    @if(!empty($groups))
        <div class="directory-modal-groups">
            <h3>Groupes de prédilection</h3>
            <div class="group-badges">
                @foreach($groups as $g)
                    <span class="badge badge-group badge-group-{{ $g }}">
                        {{ \App\Models\Member::DIRECTORY_GROUPS[$g] ?? $g }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="directory-modal-contact">
        <h3>Coordonnées</h3>
        <p>
            <i data-lucide="mail" aria-hidden="true"></i>
            <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
        </p>
        @if($phone)
            <p>
                <i data-lucide="phone" aria-hidden="true"></i>
                <a href="tel:{{ $phone }}">{{ $phone }}</a>
            </p>
        @endif
    </div>

    <div class="directory-modal-actions" style="margin-top:18px;">
        <a href="{{ route('member.chat', ['with' => $member->id]) }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:8px;">
            <i data-lucide="message-circle" aria-hidden="true"></i> Envoyer un message
        </a>
    </div>
</div>
