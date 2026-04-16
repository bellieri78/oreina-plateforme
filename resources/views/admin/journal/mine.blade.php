@extends('layouts.admin')

@section('title', 'Mes articles — Chersotis')

@section('breadcrumb')<span>Revue</span><span>/</span><span>Mes articles</span>@endsection

@section('content')
    <div class="page-header">
        <h1>Mes articles en charge</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom:1rem;">{{ session('error') }}</div>
    @endif

    @if($submissions->isEmpty())
        <div class="card">
            <div class="card-body" style="text-align:center;padding:2rem;color:#6b7280;">
                Aucun article pris en charge actuellement.
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Statut</th>
                            <th>Relectures</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($submissions as $s)
                        <tr>
                            <td>
                                <strong>{{ $s->title }}</strong>
                                <div class="text-muted" style="font-size:0.75rem;margin-top:0.25rem;">
                                    Soumis le {{ $s->submitted_at?->format('d/m/Y') ?? $s->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>{{ $s->author?->name ?? '—' }}</td>
                            <td>
                                @php $statusValue = $s->status?->value ?? 'draft'; @endphp
                                <span class="badge badge-{{ match($statusValue) {
                                    'submitted' => 'info',
                                    'under_initial_review' => 'warning',
                                    'revision_requested' => 'warning',
                                    'under_peer_review' => 'primary',
                                    'revision_after_review' => 'warning',
                                    'accepted' => 'success',
                                    'in_production' => 'info',
                                    'published' => 'success',
                                    'rejected' => 'danger',
                                    default => 'secondary',
                                } }}">
                                    {{ $s->status instanceof \App\Enums\SubmissionStatus ? $s->status->label() : $statusValue }}
                                </span>
                            </td>
                            <td>
                                {{ $s->reviews->where('status', \App\Models\Review::STATUS_COMPLETED)->count() }}
                                / {{ $s->reviews->count() }}
                            </td>
                            <td>
                                @include('admin.journal._transition_buttons', ['submission' => $s])
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
