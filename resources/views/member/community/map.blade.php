@extends('layouts.member')

@section('title', 'Carte des membres')
@section('page-title', 'Carte des membres')
@section('page-subtitle', 'Répartition géographique des adhérents par département')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v10.5.0/ol.css">
<style>
    .map-container {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    #map {
        width: 100%;
        height: 600px;
    }
    .map-legend {
        padding: 18px 24px;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .legend-items {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--muted);
        font-weight: 600;
    }
    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    .map-info {
        font-size: 13px;
        color: var(--muted);
        font-weight: 800;
    }
    .ol-popup {
        position: absolute;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow);
        padding: 12px 16px;
        min-width: 160px;
        pointer-events: none;
        transform: translate(-50%, -110%);
    }
    .ol-popup strong {
        display: block;
        font-size: 14px;
        color: var(--text);
    }
    .ol-popup span {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        color: var(--muted);
        font-weight: 800;
    }
</style>
@endpush

@section('content')
<div class="map-container">
    <div id="map"></div>
    <div class="map-legend">
        <div class="legend-items">
            <div class="legend-item">
                <div class="legend-dot" style="background: hsl(150,40%,85%);"></div>
                Peu de membres
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: hsl(150,40%,60%);"></div>
                Moyen
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: hsl(150,40%,35%);"></div>
                Beaucoup
            </div>
        </div>
        <div class="map-info">
            @php
                $memberCounts = \Illuminate\Support\Facades\Cache::remember('member_map_counts', 86400, function () {
                    return \App\Models\Member::where('is_active', true)
                        ->whereNotNull('postal_code')
                        ->get()
                        ->groupBy(fn ($m) => substr($m->postal_code, 0, 2))
                        ->map->count()
                        ->toArray();
                });
                $totalMembers = array_sum($memberCounts);
            @endphp
            {{ $totalMembers }} membres actifs · {{ count($memberCounts) }} départements
        </div>
    </div>
</div>

<div id="popup" class="ol-popup" style="display:none;">
    <strong id="popup-title"></strong>
    <span id="popup-count"></span>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/ol@v10.5.0/dist/ol.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Member counts by department code
    const memberCounts = @json($memberCounts);
    const maxCount = Math.max(...Object.values(memberCounts), 1);

    // Color scale function
    function getDeptColor(count) {
        if (count === 0) return 'rgba(22,48,43,0.04)';
        const ratio = Math.min(count / maxCount, 1);
        const lightness = 85 - (50 * ratio);
        return `hsl(150, 40%, ${lightness}%)`;
    }

    // GeoJSON source for French departments
    const deptSource = new ol.source.Vector({
        url: 'https://raw.githubusercontent.com/gregoiredavid/france-geojson/master/departements-version-simplifiee.geojson',
        format: new ol.format.GeoJSON()
    });

    // Style function
    const styleFunction = function(feature) {
        const code = feature.get('code');
        const count = memberCounts[code] || 0;
        const color = getDeptColor(count);

        return new ol.style.Style({
            fill: new ol.style.Fill({ color: color }),
            stroke: new ol.style.Stroke({
                color: count > 0 ? '#2C5F2D' : 'rgba(22,48,43,0.15)',
                width: count > 0 ? 1.5 : 0.5
            })
        });
    };

    // Highlight style
    const highlightStyle = function(feature) {
        const code = feature.get('code');
        const count = memberCounts[code] || 0;
        return new ol.style.Style({
            fill: new ol.style.Fill({ color: count > 0 ? 'rgba(44,95,45,0.35)' : 'rgba(22,48,43,0.08)' }),
            stroke: new ol.style.Stroke({ color: '#16302B', width: 2 })
        });
    };

    const deptLayer = new ol.layer.Vector({
        source: deptSource,
        style: styleFunction
    });

    // Map
    const map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM({
                    url: 'https://{a-c}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}.png'
                })
            }),
            deptLayer
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([2.5, 46.5]),
            zoom: 6,
            minZoom: 5,
            maxZoom: 12
        }),
        controls: ol.control.defaults.defaults({ attribution: false }).extend([
            new ol.control.Zoom()
        ])
    });

    // Popup
    const popup = document.getElementById('popup');
    const popupTitle = document.getElementById('popup-title');
    const popupCount = document.getElementById('popup-count');

    const overlay = new ol.Overlay({
        element: popup,
        positioning: 'bottom-center',
        stopEvent: false
    });
    map.addOverlay(overlay);

    // Highlight on hover
    let highlighted = null;

    map.on('pointermove', function(evt) {
        if (highlighted) {
            highlighted.setStyle(undefined);
            highlighted = null;
        }

        const feature = map.forEachFeatureAtPixel(evt.pixel, function(f) { return f; });

        if (feature) {
            const code = feature.get('code');
            const name = feature.get('nom');
            const count = memberCounts[code] || 0;

            feature.setStyle(highlightStyle(feature));
            highlighted = feature;

            popupTitle.textContent = `${name} (${code})`;
            popupCount.textContent = `${count} membre${count > 1 ? 's' : ''} actif${count > 1 ? 's' : ''}`;
            popup.style.display = 'block';
            overlay.setPosition(evt.coordinate);

            map.getTargetElement().style.cursor = 'pointer';
        } else {
            popup.style.display = 'none';
            map.getTargetElement().style.cursor = '';
        }
    });
});
</script>
@endpush
