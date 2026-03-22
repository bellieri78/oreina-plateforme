@extends('layouts.admin')

@section('title', $structure->name)

@section('breadcrumb')
    <a href="{{ route('admin.structures.index') }}">Structures</a>
    <span>/</span>
    <span>{{ $structure->name }}</span>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div>
            {{-- Main info --}}
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">{{ $structure->name }}</h3>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.structures.members', $structure) }}" class="btn btn-secondary">Gerer les membres</a>
                        <a href="{{ route('admin.structures.edit', $structure) }}" class="btn btn-secondary">Modifier</a>
                        <form action="{{ route('admin.structures.destroy', $structure) }}" method="POST" style="display: inline;"
                              onsubmit="return confirm('Supprimer cette structure ?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Informations</h4>
                            <div style="margin-bottom: 0.75rem;">
                                <span style="color: #6b7280;">Code :</span>
                                <code>{{ $structure->code }}</code>
                            </div>
                            <div style="margin-bottom: 0.75rem;">
                                <span style="color: #6b7280;">Type :</span>
                                @php
                                    $typeColors = [
                                        'national' => 'danger',
                                        'regional' => 'warning',
                                        'departemental' => 'info',
                                        'local' => 'secondary',
                                    ];
                                @endphp
                                <span class="badge badge-{{ $typeColors[$structure->type] ?? 'secondary' }}">
                                    {{ $structure->type_label }}
                                </span>
                            </div>
                            @if($structure->parent)
                                <div style="margin-bottom: 0.75rem;">
                                    <span style="color: #6b7280;">Parent :</span>
                                    <a href="{{ route('admin.structures.show', $structure->parent) }}">{{ $structure->parent->name }}</a>
                                </div>
                            @endif
                            @if($structure->description)
                                <div style="margin-bottom: 0.75rem;">
                                    <span style="color: #6b7280;">Description :</span>
                                    <p>{{ $structure->description }}</p>
                                </div>
                            @endif
                            <div style="margin-bottom: 0.75rem;">
                                <span style="color: #6b7280;">Statut :</span>
                                @if($structure->is_active)
                                    <span class="badge badge-success">Actif</span>
                                @else
                                    <span class="badge badge-secondary">Inactif</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Contact</h4>
                            @if($structure->responsable)
                                <div style="margin-bottom: 0.75rem;">
                                    <span style="color: #6b7280;">Responsable :</span>
                                    <a href="{{ route('admin.members.show', $structure->responsable) }}">{{ $structure->responsable->full_name }}</a>
                                </div>
                            @endif
                            @if($structure->email)
                                <div style="margin-bottom: 0.75rem;">
                                    <span style="color: #6b7280;">Email :</span>
                                    <a href="mailto:{{ $structure->email }}">{{ $structure->email }}</a>
                                </div>
                            @endif
                            @if($structure->phone)
                                <div style="margin-bottom: 0.75rem;">
                                    <span style="color: #6b7280;">Telephone :</span>
                                    {{ $structure->phone }}
                                </div>
                            @endif
                            @if($structure->address || $structure->city)
                                <div style="margin-bottom: 0.75rem;">
                                    <span style="color: #6b7280;">Adresse :</span>
                                    {{ $structure->address }}<br>
                                    {{ $structure->postal_code }} {{ $structure->city }}
                                </div>
                            @endif
                            @if($structure->departement_code)
                                <div style="margin-bottom: 0.75rem;">
                                    <span style="color: #6b7280;">Departement :</span>
                                    {{ $structure->departement_code }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sous-structures --}}
            @if($structure->children->isNotEmpty())
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-header">
                        <h3 class="card-title">Sous-structures ({{ $structure->children->count() }})</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Responsable</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($structure->children as $child)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.structures.show', $child) }}">{{ $child->name }}</a>
                                            <code style="font-size: 0.75rem; margin-left: 0.5rem;">{{ $child->code }}</code>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $typeColors[$child->type] ?? 'secondary' }}">
                                                {{ $child->type_label }}
                                            </span>
                                        </td>
                                        <td>{{ $child->responsable?->full_name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.structures.show', $child) }}" class="btn btn-sm btn-secondary">Voir</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Membres --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Membres ({{ $structure->activeMembers->count() }})</h3>
                    <a href="{{ route('admin.structures.members', $structure) }}" class="btn btn-secondary">Gerer</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Role</th>
                                <th>Depuis</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($structure->activeMembers as $member)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.members.show', $member) }}">{{ $member->full_name }}</a>
                                    </td>
                                    <td>
                                        @php
                                            $roles = \App\Models\Structure::getRoles();
                                        @endphp
                                        <span class="badge badge-{{ $member->pivot->role === 'responsable' ? 'primary' : 'secondary' }}">
                                            {{ $roles[$member->pivot->role] ?? $member->pivot->role }}
                                        </span>
                                    </td>
                                    <td>{{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.members.show', $member) }}" class="btn btn-sm btn-secondary">Voir</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #6b7280;">Aucun membre dans cette structure.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            {{-- Hierarchy --}}
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Hierarchie</h3>
                </div>
                <div class="card-body">
                    @php
                        $ancestors = $structure->getAncestors();
                    @endphp
                    <div style="font-size: 0.875rem;">
                        @foreach($ancestors as $ancestor)
                            <div style="padding-left: {{ $loop->index * 1 }}rem; margin-bottom: 0.5rem;">
                                <span style="color: #9ca3af;">└</span>
                                <a href="{{ route('admin.structures.show', $ancestor) }}">{{ $ancestor->name }}</a>
                            </div>
                        @endforeach
                        <div style="padding-left: {{ $ancestors->count() * 1 }}rem; margin-bottom: 0.5rem;">
                            @if($ancestors->isNotEmpty())<span style="color: #9ca3af;">└</span>@endif
                            <strong style="color: var(--color-primary);">{{ $structure->name }}</strong>
                        </div>
                        @foreach($structure->children as $child)
                            <div style="padding-left: {{ ($ancestors->count() + 1) * 1 }}rem; margin-bottom: 0.5rem;">
                                <span style="color: #9ca3af;">└</span>
                                <a href="{{ route('admin.structures.show', $child) }}">{{ $child->name }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistiques</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: center;">
                        <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-primary);">{{ $structure->activeMembers->count() }}</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Membres directs</div>
                        </div>
                        <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-primary);">{{ $structure->countAllMembers() }}</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Membres total</div>
                        </div>
                        <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-primary);">{{ $structure->children->count() }}</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Sous-structures</div>
                        </div>
                        <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-primary);">{{ $structure->getDescendants()->count() }}</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Total descendants</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
