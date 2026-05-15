<div class="user-card">
    <div class="avatar">
        @if($authMember?->photo_path)
            <img src="{{ \Storage::url($authMember->photo_path) }}" alt="">
        @else
            {{ $initials }}
        @endif
    </div>
    <div class="user-details">
        <strong>{{ $authMember?->full_name ?? $authUser->name }}</strong>
        <span>
            @if($department)Dept. {{ $department }}@endif
        </span>
        @if($isAuthCurrentMember)
            <div class="user-badge">Adhérent {{ now()->year }}</div>
            @if($authMemberSince)
                <span class="member-since">Membre depuis {{ $authMemberSince }}</span>
            @endif
        @endif
    </div>
</div>
