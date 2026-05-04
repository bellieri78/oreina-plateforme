<div class="directory-modal" data-member-id="{{ $member->id }}">
    <div class="directory-modal-header">
        @if($member->photo_path)
            <img src="{{ \Storage::disk('public')->url($member->photo_path) }}" alt="" class="directory-modal-photo">
        @else
            <div class="directory-modal-photo-fallback">
                {{ strtoupper(mb_substr($member->first_name, 0, 1)) }}{{ strtoupper(mb_substr($member->last_name, 0, 1)) }}
            </div>
        @endif
        <div>
            <h2>{{ $member->first_name }} {{ $member->last_name }}</h2>
            @if($member->directoryDepartment())
                <span class="badge">Département {{ $member->directoryDepartment() }}</span>
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
            <i data-lucide="mail"></i>
            <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
        </p>
        @if($phone)
            <p>
                <i data-lucide="phone"></i>
                <a href="tel:{{ $phone }}">{{ $phone }}</a>
            </p>
        @endif
    </div>
</div>
