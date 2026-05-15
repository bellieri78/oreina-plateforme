@if($isCurrentMember)
<section class="kpi-bar">
    <div class="kpi-bar-stats">
        <div class="kpi-item">
            <div class="kpi-item-icon" style="background: rgba(133,183,157,0.16); color: #2f694e;">
                <i data-lucide="calendar-days"></i>
            </div>
            <div>
                <strong>{{ $stats['membership_years'] }}</strong>
                <span>années d'adhésion</span>
            </div>
        </div>

        <div class="kpi-item">
            <div class="kpi-item-icon" style="background: rgba(239,122,92,0.16); color: var(--coral);">
                <i data-lucide="heart"></i>
            </div>
            <div>
                <strong>{{ number_format($stats['total_donations'], 0, ',', ' ') }}&nbsp;€</strong>
                <span>{{ $stats['total_donations'] > 0 ? 'de dons · Merci !' : 'de dons' }}</span>
            </div>
        </div>

        <div class="kpi-item">
            <div class="kpi-item-icon" style="background: rgba(53,107,138,0.10); color: var(--blue);">
                <i data-lucide="trending-up"></i>
            </div>
            <div>
                <strong>{{ $stats['observations_transmitted'] }}</strong>
                <span>observations transmises</span>
            </div>
        </div>

        <div class="kpi-item">
            <div class="kpi-item-icon" style="background: rgba(133,183,157,0.16); color: #2f694e;">
                <i data-lucide="check-circle"></i>
            </div>
            <div>
                <strong>{{ $stats['validations_done'] }}</strong>
                <span>validations effectuées</span>
            </div>
        </div>

        <div class="kpi-item">
            <div class="kpi-item-icon" style="background: rgba(124,58,237,0.10); color: #7c3aed;">
                <i data-lucide="file-text"></i>
            </div>
            <div>
                <strong>{{ $stats['articles_published'] }}</strong>
                <span>articles publiés</span>
            </div>
        </div>
    </div>

    <a href="{{ route('member.contributions') }}" class="text-link">
        <i data-lucide="arrow-right"></i>Voir toutes mes statistiques
    </a>
</section>
@endif
