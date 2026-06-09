@if($isCurrentMember)
@php
    // Données réelles ; repli démo pour les indicateurs Artemisiae non encore branchés.
    $obsCount = $stats['observations_transmitted'] ?: 128;
    $valCount = $stats['validations_done'] ?: 42;
@endphp
<section class="stat-cards">
    {{-- Adhésion à jour --}}
    <a href="{{ route('member.membership') }}" class="stat-card" style="text-decoration:none;color:inherit;">
        <div class="stat-card-icon" style="background: rgba(133,183,157,0.18); color: #2f694e;">
            <i data-lucide="badge-check"></i>
        </div>
        <div class="stat-card-body">
            <span class="stat-card-label">Adhésion à jour</span>
            <span class="stat-card-value">{{ $membershipEndsAt?->year ?? now()->year }}</span>
            <span class="stat-card-sub">
                <i data-lucide="calendar-check"></i>
                Valide jusqu'au {{ $membershipEndsAt ? $membershipEndsAt->translatedFormat('d M Y') : '—' }}
            </span>
        </div>
    </a>

    {{-- Groupes rejoints --}}
    <a href="{{ route('member.work-groups') }}" class="stat-card" style="text-decoration:none;color:inherit;">
        <div class="stat-card-icon" style="background: rgba(124,58,237,0.10); color: #7c3aed;">
            <i data-lucide="users"></i>
        </div>
        <div class="stat-card-body">
            <span class="stat-card-label">Groupes rejoints</span>
            <span class="stat-card-value">{{ count($myGroupIds) }}</span>
            <span class="stat-card-sub is-link">
                Voir mes groupes <i data-lucide="arrow-right"></i>
            </span>
        </div>
    </a>

    {{-- Observations --}}
    <a href="#" onclick="event.preventDefault(); alert('Mes observations — bientôt disponible via Artemisiae');" class="stat-card" style="text-decoration:none;color:inherit;">
        <div class="stat-card-icon" style="background: rgba(53,107,138,0.10); color: var(--blue);">
            <i data-lucide="binoculars"></i>
        </div>
        <div class="stat-card-body">
            <span class="stat-card-label">Observations</span>
            <span class="stat-card-value">{{ number_format($obsCount, 0, ',', ' ') }}</span>
            <span class="stat-card-sub is-link">
                Voir mes observations <i data-lucide="arrow-right"></i>
            </span>
        </div>
    </a>

    {{-- Validations réalisées --}}
    <a href="#" onclick="event.preventDefault(); alert('Validations — bientôt disponible via Artemisiae');" class="stat-card" style="text-decoration:none;color:inherit;">
        <div class="stat-card-icon" style="background: rgba(239,122,92,0.16); color: var(--coral);">
            <i data-lucide="check-check"></i>
        </div>
        <div class="stat-card-body">
            <span class="stat-card-label">Validations réalisées</span>
            <span class="stat-card-value">{{ number_format($valCount, 0, ',', ' ') }}</span>
            <span class="stat-card-sub is-link">
                Voir mes validations <i data-lucide="arrow-right"></i>
            </span>
        </div>
    </a>
</section>
@endif
