@if($isAuthCurrentMember && $authMemberGroups->count() > 0)
<div class="nav-group">
    <div class="nav-title">Mes rôles</div>
    <div class="sidebar-roles">
        @foreach($authMemberGroups as $gt)
        @php
            $role = $gt->pivot->role ?? 'member';
            $roleLabel = in_array($role, ['leader', 'validator', 'admin'], true) ? 'Validateur' : 'Membre';
        @endphp
        <div class="role-chip">
            <div class="role-chip-avatar" style="background: {{ $gt->color ?? '#85B79D' }};">
                <i data-lucide="{{ $gt->icon ?? 'leaf' }}"></i>
            </div>
            <div class="role-chip-body">
                <strong>{{ $gt->name }}</strong>
                <span>{{ $roleLabel }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
