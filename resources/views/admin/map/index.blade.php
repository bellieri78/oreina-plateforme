@extends('layouts.admin')

@section('title', 'Carte des adherents')

@section('breadcrumb')
    <span>Carte interactive</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">
            <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            Carte des adherents
        </h1>
        <p class="page-subtitle" id="mapSubtitle">
            <span id="totalCount">{{ number_format($stats['geolocated']) }}</span> adherent(s) geolocalise(s) sur {{ number_format($stats['total']) }}
        </p>
    </div>
    <div class="page-header-actions">
        @if($stats['not_geolocated'] > 0)
        <button type="button" class="btn btn-outline" onclick="startBulkGeocode()">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            Geocoder ({{ $stats['not_geolocated'] }})
        </button>
        @endif
        <button type="button" class="btn btn-outline" id="exportMapBtn" onclick="exportMapAsPNG()">
            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Exporter en image
        </button>
    </div>
</div>

{{-- Filtres --}}
<div class="card mb-4">
    <div class="filters-bar">
        <div class="filters-row">
            <div class="filter-group">
                <select id="filterType" class="form-select">
                    <option value="">Tous les types</option>
                    @foreach($contactTypes as $type)
                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <select id="filterDepartment" class="form-select">
                    <option value="">Tous les departements</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                    <option value="other">Autres</option>
                </select>
            </div>

            <div class="filter-group">
                <select id="filterStatus" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actifs</option>
                    <option value="inactive">Inactifs</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrer
                </button>
                <button type="button" class="btn btn-ghost" onclick="resetFilters()">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Effacer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Outil selection par rayon --}}
<div class="card mb-4" id="radiusToolCard">
    <div class="radius-tool-header">
        <div class="radius-tool-left">
            <button type="button" class="btn btn-sm btn-primary" id="btnDrawRadius" onclick="startDrawingRadius()">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Dessiner un cercle
            </button>
            <h3 class="radius-tool-title">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                </svg>
                Selection par rayon
            </h3>
        </div>
        <div class="radius-tool-actions">
            <button type="button" class="btn btn-sm btn-ghost" id="btnClearRadius" onclick="clearRadiusSelection()" style="display: none;">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Effacer
            </button>
        </div>
    </div>
</div>

{{-- Layout carte + sidebar --}}
<div class="map-layout">
    {{-- Carte --}}
    <div class="map-container card">
        <div id="map"></div>

        {{-- Loader --}}
        <div class="map-loader" id="mapLoader">
            <div class="loader-spinner"></div>
            <span>Chargement des adherents...</span>
        </div>

        {{-- Panneau flottant des resultats du rayon --}}
        <div class="radius-panel" id="radiusPanel" style="display: none;">
            <div class="radius-panel-header">
                <h4>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                    </svg>
                    <span id="radiusCount">0</span> adherent(s)
                </h4>
                <button type="button" class="radius-panel-close" onclick="clearRadiusSelection()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="radius-panel-body">
                {{-- Slider de rayon --}}
                <div class="radius-slider-group">
                    <label>Rayon :</label>
                    <input type="range" id="radiusSlider" min="1" max="100" value="10" class="radius-slider">
                    <span id="radiusSliderValue">10 km</span>
                </div>

                {{-- Legende des types --}}
                <div class="radius-source-legend">
                    <span class="source-legend-item">
                        <span class="legend-dot" style="background: var(--color-oreina-green);"></span>
                        Adherent
                    </span>
                    <span class="source-legend-item">
                        <span class="legend-dot" style="background: #d97706;"></span>
                        Donateur
                    </span>
                    <span class="source-legend-item">
                        <span class="legend-dot" style="background: #6366f1;"></span>
                        Prospect
                    </span>
                </div>

                {{-- Liste des contacts dans le rayon --}}
                <div class="radius-contacts-list" id="radiusContactsList">
                    {{-- Rempli dynamiquement --}}
                </div>

                {{-- Actions d'export --}}
                <div class="radius-export-actions">
                    <a href="javascript:void(0)" id="exportRadiusCSV" class="btn btn-sm btn-outline" onclick="exportRadiusResults('csv')">
                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        CSV
                    </a>
                    <a href="javascript:void(0)" id="exportRadiusExcel" class="btn btn-sm btn-outline" onclick="exportRadiusResults('xlsx')">
                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="map-sidebar">
        {{-- Stats par departement --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="card-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Par departement
                </h3>
            </div>
            <div class="card-body p-0">
                <div id="statsContainer" class="stats-list">
                    <div class="loading-state">
                        <div class="loader-spinner-sm"></div>
                        Chargement...
                    </div>
                </div>
            </div>
        </div>

        {{-- Legende --}}
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="card-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                    Legende
                </h3>
            </div>
            <div class="card-body">
                <div class="legend">
                    <div class="legend-item">
                        <span class="legend-marker" style="background: var(--color-oreina-green);"></span>
                        <span>Adherent</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-marker" style="background: #d97706;"></span>
                        <span>Donateur</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-marker" style="background: #6366f1;"></span>
                        <span>Prospect</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-marker" style="background: #ec4899;"></span>
                        <span>Partenaire</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-marker" style="background: #8b5cf6;"></span>
                        <span>Institution</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques rapides --}}
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="card-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Statistiques
                </h3>
            </div>
            <div class="card-body">
                <div class="quick-stats">
                    <div class="quick-stat">
                        <span class="quick-stat-value text-success">{{ number_format($stats['geolocated']) }}</span>
                        <span class="quick-stat-label">Geolocalises</span>
                    </div>
                    <div class="quick-stat">
                        <span class="quick-stat-value text-warning">{{ number_format($stats['not_geolocated']) }}</span>
                        <span class="quick-stat-label">Non geolocalises</span>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: {{ $stats['total'] > 0 ? ($stats['geolocated'] / $stats['total']) * 100 : 0 }}%;"></div>
                </div>
                <p class="stat-progress-text">{{ $stats['total'] > 0 ? round(($stats['geolocated'] / $stats['total']) * 100) : 0 }}% geolocalises</p>
            </div>
        </div>
    </div>
</div>

{{-- Modal geocodage --}}
<div class="modal-backdrop" id="geocodeModal" style="display: none;">
    <div class="modal modal-sm">
        <div class="modal-header">
            <h3 class="modal-title">Geocodage en cours...</h3>
        </div>
        <div class="modal-body">
            <div class="geocode-progress">
                <div class="geocode-progress-bar" id="geocodeProgressBar"></div>
            </div>
            <div class="geocode-stats">
                <div class="geocode-stat">
                    <span class="geocode-stat-value text-success" id="geocodedCount">0</span>
                    <span class="geocode-stat-label">Geolocalises</span>
                </div>
                <div class="geocode-stat">
                    <span class="geocode-stat-value text-danger" id="geocodeFailedCount">0</span>
                    <span class="geocode-stat-label">Echoues</span>
                </div>
                <div class="geocode-stat">
                    <span class="geocode-stat-value" id="geocodeRemainingCount">0</span>
                    <span class="geocode-stat-label">Restants</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="stopBulkGeocode()">Arreter</button>
        </div>
    </div>
</div>

{{-- Contact detail panel --}}
<div class="contact-panel" id="contactPanel" style="display: none;">
    <button class="contact-panel-close" onclick="closeContactPanel()">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <div class="contact-panel-content" id="contactPanelContent"></div>
</div>

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<style>
/* Layout carte */
.map-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 1.5rem;
    min-height: 600px;
}

.map-container {
    position: relative;
    overflow: hidden;
}

#map {
    width: 100%;
    height: 100%;
    min-height: 600px;
    border-radius: var(--radius-lg);
    background: var(--grey-100);
}

/* Sidebar */
.map-sidebar {
    display: flex;
    flex-direction: column;
}

/* Stats list */
.stats-list {
    max-height: 280px;
    overflow-y: auto;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--grey-100);
    cursor: pointer;
    transition: background 0.15s ease;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-item:hover {
    background: var(--grey-50);
}

.stat-item-info {
    display: flex;
    flex-direction: column;
}

.stat-item-name {
    font-weight: 500;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.stat-item-dept {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.stat-item-value {
    font-weight: 600;
    color: var(--color-oreina-green);
    font-size: 0.875rem;
}

/* Legend */
.legend {
    display: flex;
    flex-direction: column;
    gap: 0.625rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-size: 0.8125rem;
}

.legend-marker {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* Quick stats */
.quick-stats {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 0.75rem;
}

.quick-stat {
    display: flex;
    flex-direction: column;
}

.quick-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
}

.quick-stat-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.stat-progress {
    height: 6px;
    background: var(--grey-200);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.stat-progress-bar {
    height: 100%;
    background: var(--color-oreina-green);
    border-radius: 3px;
    transition: width 0.3s ease;
}

.stat-progress-text {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin: 0;
}

/* Radius tool header */
.radius-tool-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
}

.radius-tool-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.radius-tool-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0;
    color: var(--text-primary);
}

.radius-tool-actions {
    display: flex;
    gap: 0.5rem;
}

/* Radius panel (floating on map) */
.radius-panel {
    position: absolute;
    top: 1rem;
    left: 1rem;
    z-index: 1000;
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    width: 320px;
    max-height: calc(100% - 2rem);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.radius-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: var(--color-oreina-green);
    color: white;
}

.radius-panel-header h4 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    font-size: 0.875rem;
    font-weight: 600;
}

.radius-panel-close {
    background: rgba(255,255,255,0.2);
    border: none;
    border-radius: 0.375rem;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: white;
    transition: background 0.2s;
}

.radius-panel-close:hover {
    background: rgba(255,255,255,0.3);
}

.radius-panel-body {
    padding: 0.75rem;
    overflow-y: auto;
    flex: 1;
}

/* Radius slider */
.radius-slider-group {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--grey-100);
}

.radius-slider-group label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    white-space: nowrap;
}

.radius-slider {
    flex: 1;
    height: 6px;
    -webkit-appearance: none;
    appearance: none;
    background: var(--grey-200);
    border-radius: 3px;
    cursor: pointer;
}

.radius-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 16px;
    height: 16px;
    background: var(--color-oreina-green);
    border-radius: 50%;
    cursor: pointer;
}

.radius-slider::-moz-range-thumb {
    width: 16px;
    height: 16px;
    background: var(--color-oreina-green);
    border-radius: 50%;
    cursor: pointer;
    border: none;
}

#radiusSliderValue {
    font-weight: 500;
    font-size: 0.8125rem;
    min-width: 45px;
}

/* Radius source legend */
.radius-source-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    background: var(--grey-50);
    border-radius: var(--radius-sm);
    font-size: 0.6875rem;
}

.source-legend-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: var(--text-secondary);
}

/* Radius contacts list */
.radius-contacts-list {
    max-height: 220px;
    overflow-y: auto;
    margin-bottom: 0.75rem;
    border: 1px solid var(--grey-200);
    border-radius: var(--radius-md);
}

.radius-contact-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.625rem 0.75rem;
    border-bottom: 1px solid var(--grey-100);
    font-size: 0.8125rem;
}

.radius-contact-item:last-child {
    border-bottom: none;
}

.radius-contact-item:hover {
    background: var(--grey-50);
}

.radius-contact-name {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-weight: 500;
    color: var(--text-primary);
}

.radius-contact-name a {
    color: inherit;
    text-decoration: none;
}

.radius-contact-name a:hover {
    color: var(--color-oreina-green);
}

.radius-contact-info {
    display: flex;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.75rem;
}

.radius-contact-distance {
    font-weight: 500;
    color: var(--color-oreina-green);
}

.radius-empty {
    padding: 1.5rem;
    text-align: center;
    color: var(--text-secondary);
    font-size: 0.8125rem;
}

/* Radius export actions */
.radius-export-actions {
    display: flex;
    gap: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid var(--grey-100);
}

.radius-export-actions .btn {
    flex: 1;
    justify-content: center;
}

/* Map loader */
.map-loader {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 1.5rem 2rem;
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    z-index: 1000;
}

.loader-spinner {
    width: 32px;
    height: 32px;
    border: 3px solid var(--grey-200);
    border-top-color: var(--color-oreina-green);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loader-spinner-sm {
    width: 20px;
    height: 20px;
    border: 2px solid var(--grey-200);
    border-top-color: var(--color-oreina-green);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-state {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1.5rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

/* Contact panel */
.contact-panel {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 300px;
    max-height: calc(100% - 2rem);
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    z-index: 1001;
    overflow: hidden;
}

.contact-panel-close {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 28px;
    height: 28px;
    background: var(--grey-100);
    border: none;
    border-radius: 50%;
    color: var(--text-secondary);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.contact-panel-close:hover {
    background: var(--grey-200);
    color: var(--text-primary);
}

.contact-panel-content {
    padding: 1.25rem;
}

.contact-panel-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    padding-right: 2rem;
}

.contact-panel-type {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: rgba(44, 95, 45, 0.1);
    color: var(--color-oreina-green);
    border-radius: var(--radius-sm);
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 1rem;
}

.contact-panel-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.contact-panel-row {
    display: flex;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.contact-panel-row svg {
    width: 16px;
    height: 16px;
    color: var(--text-secondary);
    flex-shrink: 0;
    margin-top: 2px;
}

.contact-panel-row span {
    color: var(--text-secondary);
}

.contact-panel-actions {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--grey-100);
}

.contact-panel-actions .btn {
    width: 100%;
    justify-content: center;
}

/* Geocode modal */
.geocode-progress {
    height: 8px;
    background: var(--grey-200);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 1.25rem;
}

.geocode-progress-bar {
    height: 100%;
    background: var(--color-oreina-green);
    width: 0%;
    transition: width 0.3s ease;
}

.geocode-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
}

.geocode-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.geocode-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
}

.geocode-stat-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

/* Filters */
.filters-bar {
    padding: 0;
}

.filters-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
    padding: 1rem;
}

.filter-group {
    flex: 1;
    min-width: 150px;
    max-width: 200px;
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
}

/* Leaflet popup custom */
.leaflet-popup-content-wrapper {
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
}

.leaflet-popup-content {
    margin: 0.75rem 1rem;
    font-family: inherit;
}

.popup-title {
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.popup-info {
    font-size: 0.8125rem;
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
}

.popup-link {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    margin-top: 0.5rem;
    font-size: 0.8125rem;
    color: var(--color-oreina-green);
    text-decoration: none;
    font-weight: 500;
}

.popup-link:hover {
    text-decoration: underline;
}

/* Drawing mode */
.drawing-mode #map {
    cursor: crosshair !important;
}

.btn-drawing {
    background: var(--color-oreina-yellow) !important;
    border-color: var(--color-oreina-yellow) !important;
    color: var(--color-oreina-dark) !important;
}

/* Responsive */
@media (max-width: 1024px) {
    .map-layout {
        grid-template-columns: 1fr;
    }

    .map-sidebar {
        order: -1;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .map-sidebar .card {
        margin-top: 0 !important;
    }

    .stats-list {
        max-height: 200px;
    }

    #map {
        min-height: 450px;
    }
}

@media (max-width: 768px) {
    .map-sidebar {
        grid-template-columns: 1fr;
    }

    .radius-panel {
        width: calc(100% - 2rem);
        max-height: 50%;
    }

    .contact-panel {
        width: calc(100% - 2rem);
        left: 1rem;
        right: 1rem;
    }

    .filters-row {
        flex-direction: column;
    }

    .filter-group {
        max-width: none;
        width: 100%;
    }

    .filter-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>

{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
// Configuration
const defaultCenter = [46.603354, 1.888334]; // France center
const defaultZoom = 6;

// Contact type colors
const typeColors = {
    'adherent': 'var(--color-oreina-green, #2C5F2D)',
    'donateur': '#d97706',
    'prospect': '#6366f1',
    'partenaire': '#ec4899',
    'institution': '#8b5cf6',
    'default': '#2C5F2D'
};

// Global variables
let map;
let markersCluster;
let markers = [];
let currentCircle = null;
let radiusCenter = null;
let isDrawing = false;
let geocodingInProgress = false;
let radiusContacts = [];

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    initRadiusSlider();
    loadMembers();
    loadStats();
});

// Initialize map
function initMap() {
    map = L.map('map').setView(defaultCenter, defaultZoom);

    // OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);

    // Marker cluster group
    markersCluster = L.markerClusterGroup({
        chunkedLoading: true,
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true,
        iconCreateFunction: function(cluster) {
            const count = cluster.getChildCount();
            let size = 'small';
            if (count > 50) size = 'large';
            else if (count > 10) size = 'medium';

            return L.divIcon({
                html: `<div><span>${count}</span></div>`,
                className: `marker-cluster marker-cluster-${size}`,
                iconSize: L.point(40, 40)
            });
        }
    });

    map.addLayer(markersCluster);

    // Click handler for radius drawing
    map.on('click', function(e) {
        if (isDrawing) {
            placeCircle(e.latlng.lat, e.latlng.lng);
        }
    });
}

// Initialize radius slider
function initRadiusSlider() {
    const slider = document.getElementById('radiusSlider');
    const valueDisplay = document.getElementById('radiusSliderValue');

    slider.addEventListener('input', function() {
        valueDisplay.textContent = this.value + ' km';

        if (currentCircle && radiusCenter) {
            currentCircle.setRadius(parseInt(this.value) * 1000);
            debouncedSearch();
        }
    });
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

const debouncedSearch = debounce(searchContactsInRadius, 300);

// Start drawing radius
function startDrawingRadius() {
    isDrawing = true;
    document.body.classList.add('drawing-mode');

    const btn = document.getElementById('btnDrawRadius');
    btn.innerHTML = `
        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
        </svg>
        Cliquez sur la carte
    `;
    btn.classList.add('btn-drawing');
}

// Place circle on map
function placeCircle(lat, lng) {
    isDrawing = false;
    document.body.classList.remove('drawing-mode');

    radiusCenter = { lat: lat, lng: lng };

    // Reset button
    const btn = document.getElementById('btnDrawRadius');
    btn.innerHTML = `
        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
        </svg>
        Dessiner un cercle
    `;
    btn.classList.remove('btn-drawing');

    // Remove existing circle
    if (currentCircle) {
        map.removeLayer(currentCircle);
    }

    // Create new circle
    const radiusKm = parseInt(document.getElementById('radiusSlider').value);
    currentCircle = L.circle([lat, lng], {
        radius: radiusKm * 1000,
        color: '#17322D',
        fillColor: '#17322D',
        fillOpacity: 0.12,
        weight: 2
    }).addTo(map);

    // Show panels
    document.getElementById('radiusPanel').style.display = 'flex';
    document.getElementById('btnClearRadius').style.display = 'inline-flex';

    // Search contacts
    searchContactsInRadius();
}

// Search contacts in radius
function searchContactsInRadius() {
    if (!radiusCenter) return;

    const radiusKm = parseInt(document.getElementById('radiusSlider').value);

    // Get current filters
    const filters = {
        lat: radiusCenter.lat,
        lng: radiusCenter.lng,
        radius: radiusKm,
        contact_type: document.getElementById('filterType').value,
        department: document.getElementById('filterDepartment').value,
        status: document.getElementById('filterStatus').value
    };

    // Show loading
    document.getElementById('radiusCount').textContent = '...';
    document.getElementById('radiusContactsList').innerHTML = `
        <div class="radius-empty">
            <div class="loader-spinner-sm"></div>
            Recherche en cours...
        </div>
    `;

    fetch(`{{ route('admin.map.members') }}?${new URLSearchParams(filters)}`)
        .then(response => response.json())
        .then(data => {
            radiusContacts = data.members || [];
            displayRadiusResults(radiusContacts);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('radiusCount').textContent = '0';
            document.getElementById('radiusContactsList').innerHTML = `
                <div class="radius-empty">Erreur lors de la recherche</div>
            `;
        });
}

// Display radius results
function displayRadiusResults(contacts) {
    document.getElementById('radiusCount').textContent = contacts.length;

    const container = document.getElementById('radiusContactsList');

    if (contacts.length === 0) {
        container.innerHTML = '<div class="radius-empty">Aucun adherent dans ce rayon</div>';
        return;
    }

    // Sort by distance and take first 50
    const sortedContacts = contacts.slice(0, 50);

    let html = '';
    sortedContacts.forEach(contact => {
        const distance = contact.distance ? contact.distance.toFixed(1) : '?';
        html += `
            <div class="radius-contact-item">
                <div class="radius-contact-name">
                    <span class="legend-dot" style="background: ${getTypeColor(contact.contact_type)};"></span>
                    <a href="{{ url('extranet/members') }}/${contact.id}">${escapeHtml(contact.name)}</a>
                </div>
                <div class="radius-contact-info">
                    <span>${escapeHtml(contact.city || '-')}</span>
                    <span class="radius-contact-distance">${distance} km</span>
                </div>
            </div>
        `;
    });

    if (contacts.length > 50) {
        html += `<div class="radius-empty">Et ${contacts.length - 50} autres...</div>`;
    }

    container.innerHTML = html;
}

// Clear radius selection
function clearRadiusSelection() {
    if (currentCircle) {
        map.removeLayer(currentCircle);
        currentCircle = null;
    }

    radiusCenter = null;
    radiusContacts = [];
    isDrawing = false;

    document.body.classList.remove('drawing-mode');
    document.getElementById('radiusPanel').style.display = 'none';
    document.getElementById('btnClearRadius').style.display = 'none';

    // Reset button
    const btn = document.getElementById('btnDrawRadius');
    btn.innerHTML = `
        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
        </svg>
        Dessiner un cercle
    `;
    btn.classList.remove('btn-drawing');
}

// Load members
function loadMembers(filters = {}) {
    document.getElementById('mapLoader').style.display = 'flex';

    const params = new URLSearchParams(filters);

    fetch(`{{ route('admin.map.members') }}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            displayMarkers(data.members || []);
            document.getElementById('mapLoader').style.display = 'none';
            document.getElementById('totalCount').textContent = (data.members || []).length;
        })
        .catch(error => {
            console.error('Error loading members:', error);
            document.getElementById('mapLoader').style.display = 'none';
        });
}

// Display markers
function displayMarkers(members) {
    markersCluster.clearLayers();
    markers = [];

    if (members.length === 0) return;

    members.forEach(member => {
        const color = getTypeColor(member.contact_type);

        const icon = L.divIcon({
            className: 'custom-marker-wrapper',
            html: `<div style="background: ${color}; width: 14px; height: 14px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.25);"></div>`,
            iconSize: [18, 18],
            iconAnchor: [9, 9]
        });

        const marker = L.marker([member.lat, member.lng], { icon: icon });

        // Popup content
        const popupContent = `
            <div class="popup-title">${escapeHtml(member.name)}</div>
            ${member.contact_type ? `<div class="popup-info"><strong>Type:</strong> ${escapeHtml(member.contact_type)}</div>` : ''}
            ${member.city ? `<div class="popup-info"><strong>Ville:</strong> ${escapeHtml(member.city)}</div>` : ''}
            <a href="{{ url('extranet/members') }}/${member.id}" class="popup-link">Voir la fiche &rarr;</a>
        `;

        marker.bindPopup(popupContent);

        marker.on('click', function() {
            showContactPanel(member);
        });

        markers.push(marker);
        markersCluster.addLayer(marker);
    });
}

// Get color for contact type
function getTypeColor(type) {
    if (!type) return typeColors.default;
    const key = type.toLowerCase();
    return typeColors[key] || typeColors.default;
}

// Load department statistics
function loadStats() {
    fetch(`{{ route('admin.map.stats') }}`)
        .then(response => response.json())
        .then(data => {
            displayStats(data.stats || []);
        })
        .catch(error => {
            console.error('Error loading stats:', error);
            document.getElementById('statsContainer').innerHTML = '<div class="loading-state">Erreur de chargement</div>';
        });
}

// Display department statistics
function displayStats(stats) {
    const container = document.getElementById('statsContainer');

    if (stats.length === 0) {
        container.innerHTML = '<div class="loading-state">Aucune donnee</div>';
        return;
    }

    let html = '';
    stats.forEach(stat => {
        html += `
            <div class="stat-item" onclick="zoomToDepartment('${stat.department}')">
                <div class="stat-item-info">
                    <div class="stat-item-name">${escapeHtml(stat.name || stat.department)}</div>
                    <div class="stat-item-dept">${stat.department}</div>
                </div>
                <div class="stat-item-value">${stat.count}</div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Zoom to department
function zoomToDepartment(dept) {
    // Center coordinates for some French departments (could be expanded)
    const centers = {
        '75': [48.8566, 2.3522],   // Paris
        '13': [43.2965, 5.3698],   // Bouches-du-Rhone
        '69': [45.7640, 4.8357],   // Rhone
        '33': [44.8378, -0.5792],  // Gironde
        '31': [43.6047, 1.4442],   // Haute-Garonne
        '59': [50.6292, 3.0573],   // Nord
        '67': [48.5734, 7.7521],   // Bas-Rhin
        '44': [47.2184, -1.5536],  // Loire-Atlantique
    };

    if (centers[dept]) {
        map.setView(centers[dept], 10);
    }
}

// Show contact panel
function showContactPanel(member) {
    const panel = document.getElementById('contactPanel');
    const content = document.getElementById('contactPanelContent');

    content.innerHTML = `
        <div class="contact-panel-name">${escapeHtml(member.name)}</div>
        ${member.contact_type ? `<div class="contact-panel-type">${escapeHtml(member.contact_type)}</div>` : ''}
        <div class="contact-panel-info">
            ${member.email ? `
            <div class="contact-panel-row">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>${escapeHtml(member.email)}</span>
            </div>` : ''}
            ${member.phone ? `
            <div class="contact-panel-row">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span>${escapeHtml(member.phone)}</span>
            </div>` : ''}
            ${member.address || member.city ? `
            <div class="contact-panel-row">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>
                <span>${[member.address, member.postal_code, member.city].filter(Boolean).join(', ')}</span>
            </div>` : ''}
        </div>
        <div class="contact-panel-actions">
            <a href="{{ url('extranet/members') }}/${member.id}" class="btn btn-primary btn-sm">
                Voir la fiche complete
            </a>
        </div>
    `;

    panel.style.display = 'block';
    map.setView([member.lat, member.lng], 12);
}

// Close contact panel
function closeContactPanel() {
    document.getElementById('contactPanel').style.display = 'none';
}

// Apply filters
function applyFilters() {
    const filters = {};

    const type = document.getElementById('filterType').value;
    if (type) filters.contact_type = type;

    const dept = document.getElementById('filterDepartment').value;
    if (dept) filters.department = dept;

    const status = document.getElementById('filterStatus').value;
    if (status) filters.status = status;

    // Add radius if set
    if (radiusCenter) {
        filters.lat = radiusCenter.lat;
        filters.lng = radiusCenter.lng;
        filters.radius = document.getElementById('radiusSlider').value;
    }

    loadMembers(filters);

    // Also update radius results if circle exists
    if (radiusCenter) {
        searchContactsInRadius();
    }
}

// Reset filters
function resetFilters() {
    document.getElementById('filterType').value = '';
    document.getElementById('filterDepartment').value = '';
    document.getElementById('filterStatus').value = '';
    loadMembers();
}

// Export radius results
function exportRadiusResults(format) {
    if (!radiusCenter || radiusContacts.length === 0) {
        alert('Aucun contact a exporter');
        return;
    }

    const params = new URLSearchParams({
        lat: radiusCenter.lat,
        lng: radiusCenter.lng,
        radius: document.getElementById('radiusSlider').value,
        format: format
    });

    window.location.href = `{{ route('admin.map.export-radius') }}?${params}`;
}

// Export map as PNG
function exportMapAsPNG() {
    const btn = document.getElementById('exportMapBtn');
    const originalContent = btn.innerHTML;

    btn.innerHTML = `
        <div class="loader-spinner-sm"></div>
        Export en cours...
    `;
    btn.disabled = true;

    html2canvas(document.getElementById('map'), {
        useCORS: true,
        allowTaint: true,
        scale: 2,
        backgroundColor: '#ffffff'
    }).then(canvas => {
        const link = document.createElement('a');
        const date = new Date().toISOString().slice(0, 10);
        link.download = `carte-adherents-${date}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();

        btn.innerHTML = originalContent;
        btn.disabled = false;
    }).catch(error => {
        console.error('Export error:', error);
        alert('Erreur lors de l\'export');
        btn.innerHTML = originalContent;
        btn.disabled = false;
    });
}

// Bulk geocoding
function startBulkGeocode() {
    if (geocodingInProgress) return;

    geocodingInProgress = true;
    document.getElementById('geocodeModal').style.display = 'flex';
    document.getElementById('geocodedCount').textContent = '0';
    document.getElementById('geocodeFailedCount').textContent = '0';
    document.getElementById('geocodeRemainingCount').textContent = '{{ $stats["not_geolocated"] }}';
    document.getElementById('geocodeProgressBar').style.width = '0%';

    runBulkGeocode();
}

function runBulkGeocode() {
    if (!geocodingInProgress) return;

    fetch(`{{ route('admin.map.bulk-geocode') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ limit: 10 })
    })
    .then(response => response.json())
    .then(data => {
        const currentGeocoded = parseInt(document.getElementById('geocodedCount').textContent) + (data.geocoded || 0);
        const currentFailed = parseInt(document.getElementById('geocodeFailedCount').textContent) + (data.failed || 0);

        document.getElementById('geocodedCount').textContent = currentGeocoded;
        document.getElementById('geocodeFailedCount').textContent = currentFailed;
        document.getElementById('geocodeRemainingCount').textContent = data.remaining || 0;

        const total = {{ $stats['not_geolocated'] }};
        const progress = total > 0 ? ((total - (data.remaining || 0)) / total) * 100 : 100;
        document.getElementById('geocodeProgressBar').style.width = progress + '%';

        if ((data.remaining || 0) > 0 && geocodingInProgress) {
            setTimeout(runBulkGeocode, 500);
        } else {
            stopBulkGeocode();
        }
    })
    .catch(error => {
        console.error('Geocode error:', error);
        stopBulkGeocode();
    });
}

function stopBulkGeocode() {
    geocodingInProgress = false;
    document.getElementById('geocodeModal').style.display = 'none';
    loadMembers();
    setTimeout(() => location.reload(), 1000);
}

// Helper: escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endsection
