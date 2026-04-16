@extends('layouts.admin')
@section('title', 'Vol. ' . $journalIssue->volume_number . ' N°' . $journalIssue->issue_number)
@section('breadcrumb')
    <a href="{{ route('admin.journal-issues.index') }}">Numeros</a>
    <span>/</span>
    <span>Vol. {{ $journalIssue->volume_number }} N°{{ $journalIssue->issue_number }}</span>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 1.5rem;">
        <div>
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">
                        Vol. {{ $journalIssue->volume_number }} N°{{ $journalIssue->issue_number }}
                        @if($journalIssue->title)
                            - {{ $journalIssue->title }}
                        @endif
                    </h3>
                    <div style="display: flex; gap: 0.5rem;">
                        @if($journalIssue->status === 'draft')
                            <form action="{{ route('admin.journal-issues.update', $journalIssue) }}" method="POST" style="display: inline;">
                                @csrf @method('PUT')
                                <input type="hidden" name="volume_number" value="{{ $journalIssue->volume_number }}">
                                <input type="hidden" name="issue_number" value="{{ $journalIssue->issue_number }}">
                                <input type="hidden" name="status" value="published">
                                <input type="hidden" name="publication_date" value="{{ now()->format('Y-m-d') }}">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Publier ce numero ?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    Publier
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.journal-issues.update', $journalIssue) }}" method="POST" style="display: inline;">
                                @csrf @method('PUT')
                                <input type="hidden" name="volume_number" value="{{ $journalIssue->volume_number }}">
                                <input type="hidden" name="issue_number" value="{{ $journalIssue->issue_number }}">
                                <input type="hidden" name="status" value="draft">
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Depublier ce numero ?')">Depublier</button>
                            </form>
                        @endif
                        <a href="{{ route('admin.journal-issues.edit', $journalIssue) }}" class="btn btn-secondary">Modifier</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($journalIssue->description)
                        <p style="color: #374151; line-height: 1.7;">{{ $journalIssue->description }}</p>
                    @else
                        <p style="color: #9ca3af;">Aucune description</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Articles ({{ $journalIssue->submissions->count() }})</h3>
                    <a href="{{ route('admin.submissions.create') }}?journal_issue_id={{ $journalIssue->id }}" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Ajouter
                    </a>
                </div>
                <div class="card-body" style="padding: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Statut</th>
                                <th style="width: 80px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($journalIssue->submissions as $sub)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.submissions.show', $sub) }}" style="color: #356B8A; text-decoration: none; font-weight: 500;">
                                            {{ Str::limit($sub->title, 50) }}
                                        </a>
                                    </td>
                                    <td>{{ $sub->author?->name ?? '-' }}</td>
                                    <td>
                                        @switch($sub->status instanceof \App\Enums\SubmissionStatus ? $sub->status->value : $sub->status)
                                            @case('published')
                                                <span class="badge badge-success">Publie</span>
                                                @break
                                            @case('accepted')
                                                <span class="badge badge-info">Accepte</span>
                                                @break
                                            @case('under_peer_review')
                                                <span class="badge badge-warning">En review</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $sub->status instanceof \App\Enums\SubmissionStatus ? $sub->status->label() : $sub->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.submissions.show', $sub) }}" class="btn btn-secondary" style="padding: 0.35rem 0.5rem;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align: center; color: #9ca3af; padding: 2rem;">Aucun article dans ce numero</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informations</h3>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 1rem;">
                        @if($journalIssue->status === 'published')
                            <span class="badge badge-success" style="font-size: 1rem; padding: 0.5rem 1rem;">Publie</span>
                        @else
                            <span class="badge badge-secondary" style="font-size: 1rem; padding: 0.5rem 1rem;">Brouillon</span>
                        @endif
                    </div>

                    @if($journalIssue->publication_date)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Publication :</span>
                            <span style="font-weight: 500;">{{ $journalIssue->publication_date->format('d/m/Y') }}</span>
                        </div>
                    @endif

                    @if($journalIssue->doi)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">DOI :</span>
                            <code style="background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.875rem;">{{ $journalIssue->doi }}</code>
                        </div>
                    @endif

                    @if($journalIssue->page_count)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Pages :</span>
                            <span>{{ $journalIssue->page_count }}</span>
                        </div>
                    @endif

                    <div style="margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">Articles :</span>
                        <span style="font-weight: 500;">{{ $journalIssue->submissions->count() }}</span>
                    </div>

                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; font-size: 0.875rem; color: #6b7280;">
                        <div>Citation :</div>
                        <code style="display: block; margin-top: 0.5rem; background: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem;">
                            {{ $journalIssue->citation }}
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
