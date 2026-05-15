@if($isAuthCurrentMember)
<div class="sidebar-progress">
    <div class="sidebar-progress-label">
        <span>Profil complété</span>
        <strong style="color:var(--gold);">{{ $authProfileCompletion }}%</strong>
    </div>
    <div class="sidebar-progress-bar">
        <div class="sidebar-progress-fill" style="width: {{ $authProfileCompletion }}%;"></div>
    </div>
</div>
@endif
