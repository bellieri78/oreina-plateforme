@extends('layouts.admin')
@section('title', 'Modifier groupe de travail')
@section('breadcrumb')
    <a href="{{ route('admin.work-groups.index') }}">Groupes de travail</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Modifier le groupe de travail</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.work-groups.update', $workGroup) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.work-groups._form', ['workGroup' => $workGroup])
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.work-groups.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Pending Join Requests -->
    @php($pending = $workGroup->pendingRequests()->get())
    @if($pending->count() > 0)
        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title">Demandes en attente ({{ $pending->count() }})</h3>
            </div>
            <div class="card-body">
                @foreach($pending as $p)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">
                        <span>
                            <strong>{{ $p->last_name }} {{ $p->first_name }}</strong>
                            <small class="text-muted">&mdash; demandé le {{ optional($p->pivot->requested_at)->format('d/m/Y') }}</small>
                        </span>
                        <span style="display: flex; gap: 0.5rem;">
                            <form action="{{ route('admin.work-groups.requests.approve', [$workGroup, $p]) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Accepter</button>
                            </form>
                            <form action="{{ route('admin.work-groups.requests.reject', [$workGroup, $p]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Refuser cette demande ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-secondary btn-sm text-danger">Refuser</button>
                            </form>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Member Management Section -->
    <div class="card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h3 class="card-title">Membres du groupe ({{ $workGroup->members->count() }})</h3>
        </div>
        <div class="card-body">
            <!-- Add Member Form -->
            <form action="{{ route('admin.work-groups.add-member', $workGroup) }}" method="POST" style="display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                @csrf
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <label class="form-label" for="member_id">Ajouter un membre</label>
                    <select name="member_id" id="member_id" class="form-input" required>
                        <option value="">-- Selectionner un contact --</option>
                        @foreach($availableMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->last_name }} {{ $member->first_name }} {{ $member->email ? '(' . $member->email . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="width: 200px; margin-bottom: 0;">
                    <label class="form-label" for="role">Role</label>
                    <select name="role" id="role" class="form-input">
                        <option value="member">Membre</option>
                        <option value="coordinator">Coordinateur</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>

            <!-- Current Members List -->
            @if($workGroup->members->count() > 0)
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Depuis</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workGroup->members as $member)
                                <tr>
                                    <td><strong>{{ $member->last_name }} {{ $member->first_name }}</strong></td>
                                    <td><span class="text-muted">{{ $member->email ?: '-' }}</span></td>
                                    <td>
                                        @if($member->pivot->role === 'coordinator')
                                            <span class="badge badge-warning">Coordinateur</span>
                                        @elseif($member->pivot->role === 'leader')
                                            <span class="badge badge-warning">Responsable</span>
                                        @else
                                            <span class="badge badge-outline">Membre</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d/m/Y') : '-' }}</span>
                                    </td>
                                    <td class="text-right">
                                        <form action="{{ route('admin.work-groups.remove-member', [$workGroup, $member]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Retirer ce membre du groupe ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-sm text-danger" title="Retirer">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted" style="text-align: center; padding: 2rem;">Aucun membre dans ce groupe.</p>
            @endif
        </div>
    </div>
@endsection
