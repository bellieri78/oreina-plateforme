@extends('layouts.admin')

@section('title', 'Membres - ' . $structure->name)

@section('breadcrumb')
    <a href="{{ route('admin.structures.index') }}">Structures</a>
    <span>/</span>
    <a href="{{ route('admin.structures.show', $structure) }}">{{ $structure->name }}</a>
    <span>/</span>
    <span>Membres</span>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div>
            {{-- Current members --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Membres actuels ({{ $structure->activeMembers->count() }})</h3>
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
                                        @if($member->email)
                                            <div style="font-size: 0.75rem; color: #6b7280;">{{ $member->email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.structures.members.update', [$structure, $member]) }}" method="POST" style="display: flex; gap: 0.5rem;">
                                            @csrf @method('PUT')
                                            <select name="role" class="form-input" style="width: auto;" onchange="this.form.submit()">
                                                @foreach(\App\Models\Structure::getRoles() as $key => $label)
                                                    <option value="{{ $key }}" {{ $member->pivot->role == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        <form action="{{ route('admin.structures.members.remove', [$structure, $member]) }}" method="POST"
                                              onsubmit="return confirm('Retirer ce membre de la structure ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Retirer</button>
                                        </form>
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
            {{-- Add member --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ajouter un membre</h3>
                </div>
                <form action="{{ route('admin.structures.members.add', $structure) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="member_id" class="form-label">Contact *</label>
                            <select name="member_id" id="member_id" class="form-input @error('member_id') is-invalid @enderror" required>
                                <option value="">-- Selectionner --</option>
                                @foreach($availableMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-input">
                                @foreach(\App\Models\Structure::getRoles() as $key => $label)
                                    <option value="{{ $key }}" {{ $key == 'membre' ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="joined_at" class="form-label">Date d'adhesion</label>
                            <input type="date" name="joined_at" id="joined_at" value="{{ date('Y-m-d') }}" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="2" class="form-input"></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Ajouter</button>
                    </div>
                </form>
            </div>

            {{-- Back link --}}
            <div style="margin-top: 1rem;">
                <a href="{{ route('admin.structures.show', $structure) }}" class="btn btn-secondary" style="width: 100%;">
                    Retour a la structure
                </a>
            </div>
        </div>
    </div>
@endsection
