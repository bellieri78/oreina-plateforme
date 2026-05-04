@extends('layouts.member')

@section('title', 'Annuaire des adhérents')
@section('page-title', 'Annuaire des adhérents')
@section('page-subtitle', 'Adhérents à jour partageant leurs coordonnées')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v10.5.0/ol.css">
<style>
    .directory-toolbar { display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:16px; }
    .directory-toolbar input[type=search],
    .directory-toolbar select { padding:8px 12px; border:1px solid var(--border); border-radius:8px; }
    .directory-toggle { display:inline-flex; border:1px solid var(--border); border-radius:8px; overflow:hidden; }
    .directory-toggle button { padding:8px 16px; background:none; border:none; cursor:pointer; font-weight:600; }
    .directory-toggle button.active { background:var(--forest); color:white; }
    .directory-count { margin-left:auto; font-weight:600; color:var(--muted); }

    .directory-map-container { background:var(--surface); border:1px solid var(--border); border-radius:12px; overflow:hidden; }
    .directory-map-wrapper { position: relative; }
    #directory-map { width:100%; height:600px; }

    .directory-side-panel {
        position: absolute;
        top: 0;
        right: 0;
        width: 320px;
        max-width: 90%;
        height: 100%;
        background: var(--surface);
        border-left: 1px solid var(--border);
        padding: 20px 16px;
        overflow-y: auto;
        box-shadow: -4px 0 12px rgba(22,48,43,0.08);
        z-index: 10;
    }
    .directory-side-close {
        position: absolute;
        top: 8px;
        right: 8px;
        background: none;
        border: none;
        cursor: pointer;
        color: var(--muted);
    }
    .directory-side-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--forest);
        margin: 0 0 4px 0;
    }
    .directory-side-count {
        color: var(--muted);
        font-size: 14px;
        margin-bottom: 16px;
    }
    .directory-side-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .directory-side-list .directory-card { padding: 12px; }

    .directory-list { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:12px; }
    .directory-card { background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:16px; cursor:pointer; transition:transform .15s; }
    .directory-card:hover { transform:translateY(-2px); }
    .directory-card-name { font-weight:700; }
    .directory-card-meta { display:flex; gap:6px; flex-wrap:wrap; margin-top:8px; }

    /* Badges spécifiques à l'annuaire — surcharge volontaire du .badge du layout */
    .directory-card .badge { display:inline-block; padding:2px 8px; font-size:12px; border-radius:6px; background:var(--surface-soft); border:none; font-weight:600; color:var(--muted); white-space:nowrap; }
    .directory-card .badge-group-rhopalo { background:#fde68a; color:#92400e; }
    .directory-card .badge-group-micro   { background:#bfdbfe; color:#1e40af; }
    .directory-card .badge-group-macro   { background:#bbf7d0; color:#166534; }
    .directory-card .badge-group-zygenes { background:#fecaca; color:#991b1b; }

    /* Modal */
    .directory-modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.4); display:none; align-items:center; justify-content:center; z-index:1000; }
    .directory-modal-backdrop.open { display:flex; }
    .directory-modal-content { background:var(--surface); padding:24px; border-radius:12px; max-width:500px; width:90%; max-height:80vh; overflow-y:auto; position:relative; }
    .directory-modal-close { position:absolute; top:12px; right:12px; background:none; border:none; cursor:pointer; }
    .directory-modal-photo { width:96px; height:96px; border-radius:50%; object-fit:cover; }
    .directory-modal-photo-fallback { width:96px; height:96px; border-radius:50%; background:var(--forest); color:white; display:flex; align-items:center; justify-content:center; font-size:32px; font-weight:700; }
    .directory-modal-header { display:flex; gap:16px; align-items:center; margin-bottom:16px; }

    [x-cloak] { display:none !important; }
</style>
@endpush

@section('content')
<div x-data="directoryApp()" x-init="init()">

    {{-- Bandeau si pas opt-in --}}
    @if(!$member->directory_opt_in)
        <div class="card panel mb-6" style="background:var(--surface-blue)">
            <div class="flex gap-3 items-start">
                <i data-lucide="info" style="color:var(--forest)"></i>
                <div>
                    <p class="font-medium">Vous ne figurez pas dans l'annuaire</p>
                    <p class="text-sm mt-1">
                        Activez votre présence dans l'annuaire pour permettre aux autres adhérents de vous contacter.
                        <a href="{{ route('member.profile.preferences') }}" class="underline">Apparaître dans l'annuaire →</a>
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Filtres --}}
    <div class="directory-toolbar">
        <input type="search" placeholder="Rechercher un nom..." x-model.debounce.300ms="filters.q">

        <select x-ref="dept" multiple size="1" @change="filters.dept = Array.from($event.target.selectedOptions).map(o => o.value).filter(v => v)">
            <option value="">Tous les départements</option>
            <template x-for="d in availableDepartments" :key="d">
                <option :value="d" x-text="d"></option>
            </template>
        </select>

        <select x-ref="groups" multiple size="1" @change="filters.groups = Array.from($event.target.selectedOptions).map(o => o.value).filter(v => v)">
            <option value="">Tous les groupes</option>
            @foreach($groups as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>

        <div class="directory-toggle">
            <button type="button" :class="{ active: view === 'carte' }" @click="view = 'carte'">Carte</button>
            <button type="button" :class="{ active: view === 'liste' }" @click="view = 'liste'">Liste</button>
        </div>

        <span class="directory-count"><span x-text="count"></span> résultat<span x-show="count > 1">s</span></span>
    </div>

    {{-- Vue Carte --}}
    <div x-show="view === 'carte'" x-cloak class="directory-map-container">
        <div class="directory-map-wrapper">
            <div id="directory-map"></div>
            <div class="directory-side-panel" x-show="selectedDept" x-transition>
                <button type="button" class="directory-side-close" @click="selectedDept = null" aria-label="Fermer">
                    <i data-lucide="x" aria-hidden="true"></i>
                </button>
                <h3 class="directory-side-title" x-text="'Département ' + selectedDept"></h3>
                <p class="directory-side-count" x-text="membersInSelectedDept().length + ' membre' + (membersInSelectedDept().length > 1 ? 's' : '')"></p>
                <div class="directory-side-list">
                    <template x-for="m in membersInSelectedDept()" :key="m.id">
                        <div class="directory-card" @click="openModal(m.id)">
                            <div class="directory-card-name" x-text="m.first_name + ' ' + m.last_name"></div>
                            <div class="directory-card-meta">
                                <template x-for="g in m.groups">
                                    <span class="badge" :class="'badge-group-' + g" x-text="groupLabel(g)"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Vue Liste --}}
    <div x-show="view === 'liste'" x-cloak class="directory-list">
        <template x-for="m in members" :key="m.id">
            <div class="directory-card" @click="openModal(m.id)">
                <div class="directory-card-name" x-text="m.first_name + ' ' + m.last_name"></div>
                <div class="directory-card-meta">
                    <span class="badge" x-show="m.department" x-text="'Dépt ' + m.department"></span>
                    <template x-for="g in m.groups">
                        <span class="badge" :class="'badge-group-' + g" x-text="groupLabel(g)"></span>
                    </template>
                </div>
            </div>
        </template>
    </div>

    {{-- Modale --}}
    <div class="directory-modal-backdrop" :class="{ open: modalOpen }" @click.self="modalOpen = false">
        <div class="directory-modal-content">
            <button class="directory-modal-close" @click="modalOpen = false" aria-label="Fermer">
                <i data-lucide="x" aria-hidden="true"></i>
            </button>
            <div x-html="modalContent"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/ol@v10.5.0/dist/ol.js"></script>
<script>
const GROUP_LABELS = @json(\App\Models\Member::DIRECTORY_GROUPS);

function directoryApp() {
    return {
        view: new URLSearchParams(window.location.search).get('vue') || 'carte',
        filters: { q: '', dept: [], groups: [] },
        allMembers: [],
        members: [],
        count: 0,
        availableDepartments: [],
        selectedDept: null,
        modalOpen: false,
        modalContent: '',
        olMap: null,

        async init() {
            const res = await fetch('{{ route('member.directory.data') }}');
            const data = await res.json();
            this.allMembers = data.members;
            this.members = data.members;
            this.count = data.count;
            this.availableDepartments = Array.from(new Set(this.members.map(m => m.department).filter(Boolean))).sort();

            this.initMap();

            this.$watch('view', () => {
                if (this.view === 'carte' && this.olMap) {
                    setTimeout(() => this.olMap.updateSize(), 50);
                }
                this.syncUrl();
            });

            this.$watch('filters', () => {
                this.members = this.applyFilters();
                this.count = this.members.length;
                this.refreshMap();
            }, { deep: true });
        },

        applyFilters() {
            let result = this.allMembers;

            if (this.filters.q) {
                const q = this.filters.q.toLowerCase();
                result = result.filter(m =>
                    (m.first_name + ' ' + m.last_name).toLowerCase().includes(q)
                );
            }

            if (this.filters.dept.length > 0) {
                result = result.filter(m => this.filters.dept.includes(m.department));
            }

            if (this.filters.groups.length > 0) {
                result = result.filter(m =>
                    (m.groups || []).some(g => this.filters.groups.includes(g))
                );
            }

            return result;
        },

        membersInSelectedDept() {
            if (!this.selectedDept) return [];
            return this.allMembers.filter(m => m.department === this.selectedDept);
        },

        groupLabel(slug) {
            return GROUP_LABELS[slug] || slug;
        },

        syncUrl() {
            const params = new URLSearchParams(window.location.search);
            params.set('vue', this.view);
            window.history.replaceState({}, '', '?' + params.toString());
        },

        async openModal(memberId) {
            const res = await fetch('/espace-membre/annuaire/' + memberId);
            this.modalContent = await res.text();
            this.modalOpen = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        initMap() {
            this.olMap = new ol.Map({
                target: 'directory-map',
                layers: [
                    new ol.layer.Tile({
                        source: new ol.source.OSM({
                            url: 'https://{a-c}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}.png'
                        })
                    }),
                    new ol.layer.Vector({
                        source: new ol.source.Vector({
                            url: 'https://raw.githubusercontent.com/gregoiredavid/france-geojson/master/departements-version-simplifiee.geojson',
                            format: new ol.format.GeoJSON()
                        }),
                        style: (feature) => this.styleDept(feature)
                    })
                ],
                view: new ol.View({
                    center: ol.proj.fromLonLat([2.5, 46.5]),
                    zoom: 6,
                    minZoom: 5,
                    maxZoom: 12
                })
            });

            // Click sur dept → ouvre le panneau latéral
            this.olMap.on('click', (evt) => {
                this.olMap.forEachFeatureAtPixel(evt.pixel, (feature) => {
                    const code = feature.get('code');
                    const counts = this.deptCounts();
                    if (counts[code] > 0) {
                        this.selectedDept = code;
                        this.$nextTick(() => {
                            if (window.lucide) window.lucide.createIcons();
                        });
                    }
                });
            });
        },

        refreshMap() {
            if (!this.olMap) return;
            this.olMap.getLayers().forEach(layer => {
                if (layer instanceof ol.layer.Vector) {
                    layer.changed();
                }
            });
        },

        styleDept(feature) {
            const code = feature.get('code');
            const counts = this.deptCounts();
            const count = counts[code] || 0;
            const max = Math.max(1, ...Object.values(counts));
            const ratio = count / max;
            const lightness = 85 - (50 * ratio);
            return new ol.style.Style({
                fill: new ol.style.Fill({ color: count > 0 ? `hsl(150,40%,${lightness}%)` : 'rgba(22,48,43,0.04)' }),
                stroke: new ol.style.Stroke({ color: count > 0 ? '#2C5F2D' : 'rgba(22,48,43,0.15)', width: count > 0 ? 1.5 : 0.5 })
            });
        },

        deptCounts() {
            const counts = {};
            this.members.forEach(m => {
                if (m.department) counts[m.department] = (counts[m.department] || 0) + 1;
            });
            return counts;
        }
    }
}
</script>
@endpush
