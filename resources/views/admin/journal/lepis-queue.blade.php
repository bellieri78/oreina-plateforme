@extends('layouts.admin')
@section('title', 'File Lepis')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span>/</span>
    <span>Revue</span>
    <span>/</span>
    <span>File Lepis</span>
@endsection

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #16302B; margin: 0 0 0.25rem 0;">File Lepis</h1>
    <p style="color: #6b7280; margin: 0; font-size: 0.9rem;">
        {{ $submissions->total() }} soumission(s) en attente de décision Lepis. L'auteur ne voit aucun changement de statut tant qu'une décision n'a pas été prise (transmission à Lepis ou rejet définitif).
    </p>
</div>

@if($submissions->isEmpty())
    <div class="card">
        <div class="card-body" style="text-align: center; padding: 3rem;">
            <p style="color: #6b7280; margin: 0;">Aucune soumission en attente de décision Lepis.</p>
        </div>
    </div>
@else
    <div class="card">
        <table class="table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Proposé par</th>
                    <th>Date</th>
                    <th>Motif</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $submission)
                    @php
                        $transition = $submission->transitions->first();
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('admin.submissions.show', $submission) }}" style="color: #356B8A; font-weight: 500;">
                                {{ \Illuminate\Support\Str::limit($submission->title, 60) }}
                            </a>
                        </td>
                        <td>
                            <div>{{ $submission->author?->name ?? '—' }}</div>
                            <div style="font-size: 0.75rem; color: #9ca3af;">{{ $submission->author?->email ?? '' }}</div>
                        </td>
                        <td>{{ $transition?->actor?->name ?? '—' }}</td>
                        <td>{{ $transition?->created_at?->format('d/m/Y') ?? '—' }}</td>
                        <td style="max-width: 20rem; color: #6b7280; font-size: 0.875rem;">
                            {{ \Illuminate\Support\Str::limit($transition?->notes ?? '—', 150) }}
                        </td>
                        <td style="text-align: right;">
                            @include('admin.journal._transition_buttons', ['submission' => $submission])
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1rem;">
        {{ $submissions->links() }}
    </div>
@endif
@endsection
