{{-- resources/views/journal/articles/partials/_sidebar-metrics.blade.php --}}
<div class="sidebar-section sidebar-metrics">
    <h3 class="sidebar-title">Métriques</h3>
    <div class="metrics-grid">
        <div class="metric-tile" data-metric="views">
            <div class="metric-value">{{ number_format($articleMetrics['views'], 0, ',', ' ') }}</div>
            <div class="metric-label">Vues</div>
        </div>
        <div class="metric-tile" data-metric="pdf_downloads">
            <div class="metric-value">{{ number_format($articleMetrics['pdf_downloads'], 0, ',', ' ') }}</div>
            <div class="metric-label">Téléch. PDF</div>
        </div>
        <div class="metric-tile" data-metric="shares">
            <div class="metric-value">{{ number_format($articleMetrics['shares'], 0, ',', ' ') }}</div>
            <div class="metric-label">Partages</div>
        </div>
        @if($submission->doi)
        <div class="metric-tile" data-metric="citations">
            <div class="metric-value">{{ number_format($articleMetrics['citations'], 0, ',', ' ') }}</div>
            <div class="metric-label">Citations</div>
        </div>
        @endif
    </div>
</div>
