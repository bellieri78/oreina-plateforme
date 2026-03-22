@php
    $typeColors = [
        'national' => '#dc2626',
        'regional' => '#d97706',
        'departemental' => '#0891b2',
        'local' => '#7c3aed',
    ];
    $color = $typeColors[$structure->type] ?? '#6b7280';
@endphp

<div class="tree-item" style="margin-left: {{ $depth * 1.5 }}rem;">
    @if($depth > 0)
    <div class="tree-line"></div>
    @endif
    <div class="tree-node">
        <div class="tree-node-content">
            <div class="structure-icon-sm" style="background: {{ $color }};">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    @switch($structure->type)
                        @case('national')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @break
                        @case('regional')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            @break
                        @case('departemental')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            @break
                        @default
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    @endswitch
                </svg>
            </div>

            <div class="tree-node-info">
                <a href="{{ route('admin.structures.show', $structure) }}" class="tree-node-name {{ $structure->children->isNotEmpty() ? 'has-children' : '' }}">
                    {{ $structure->name }}
                </a>
                <div class="tree-node-meta">
                    <span class="badge" style="background: {{ $color }}15; color: {{ $color }};">{{ $structure->type_label }}</span>
                    <span class="tree-node-code">{{ $structure->code }}</span>
                    @if($structure->active_members_count > 0)
                    <span class="tree-node-count">{{ $structure->active_members_count }} membre(s)</span>
                    @endif
                    @if($structure->responsable)
                    <span class="tree-node-responsable">{{ $structure->responsable->full_name }}</span>
                    @endif
                    @if(!$structure->is_active)
                    <span class="badge badge-default">Inactif</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="tree-node-actions">
            <a href="{{ route('admin.structures.members', $structure) }}" class="btn btn-ghost btn-sm" title="Membres">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </a>
            <a href="{{ route('admin.structures.edit', $structure) }}" class="btn btn-ghost btn-sm" title="Modifier">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
        </div>
    </div>

    @if($structure->children->isNotEmpty())
    <div class="tree-children">
        @foreach($structure->children as $child)
            @include('admin.structures._tree-item', ['structure' => $child, 'depth' => $depth + 1])
        @endforeach
    </div>
    @endif
</div>
