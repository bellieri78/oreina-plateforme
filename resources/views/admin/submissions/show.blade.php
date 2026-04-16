@extends('layouts.admin')
@section('title', $submission->title)
@section('breadcrumb')
    <a href="{{ route('admin.submissions.index') }}">Soumissions</a>
    <span>/</span>
    <span>{{ Str::limit($submission->title, 30) }}</span>
@endsection

@section('content')
    {{-- Workflow Timeline --}}
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-body" style="padding: 1rem 1.5rem;">
            <div class="workflow-timeline">
                @php
                    $stages = [
                        'draft' => ['label' => 'Brouillon', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
                        'submitted' => ['label' => 'Soumis', 'icon' => 'M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5'],
                        'under_initial_review' => ['label' => 'Eval. initiale', 'icon' => 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z'],
                        'under_peer_review' => ['label' => 'En review', 'icon' => 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z'],
                        'revision_after_review' => ['label' => 'Revision', 'icon' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99'],
                        'accepted' => ['label' => 'Accepte', 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                        'published' => ['label' => 'Publie', 'icon' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25'],
                    ];
                    $statusOrder = array_keys($stages);
                    $submissionStatusValue = $submission->status instanceof \App\Enums\SubmissionStatus ? $submission->status->value : $submission->status;
                    $currentIndex = array_search($submissionStatusValue, $statusOrder);
                    if ($submissionStatusValue === 'rejected') {
                        $currentIndex = array_search('under_peer_review', $statusOrder);
                    }
                @endphp

                @foreach($stages as $key => $stage)
                    @php
                        $index = array_search($key, $statusOrder);
                        $isActive = $submissionStatusValue === $key;
                        $isPast = $index < $currentIndex;
                        $isRejected = $submissionStatusValue === 'rejected' && $key === 'under_peer_review';
                    @endphp
                    <div class="workflow-step {{ $isActive ? 'active' : '' }} {{ $isPast ? 'completed' : '' }} {{ $isRejected ? 'rejected' : '' }}">
                        <div class="step-icon">
                            @if($isPast)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stage['icon'] }}" />
                                </svg>
                            @endif
                        </div>
                        <div class="step-label">{{ $stage['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div>
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">{{ $submission->title }}</h3>
                    <div style="display: flex; gap: 0.5rem;">
                        @if(in_array($submissionStatusValue, ['accepted', 'published']))
                            <a href="{{ route('admin.submissions.layout', $submission) }}" class="btn btn-turquoise">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                                Maquetter
                            </a>
                        @endif
                        <a href="{{ route('admin.submissions.edit', $submission) }}" class="btn btn-secondary">Modifier</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($submission->abstract)
                        <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Resume</h4>
                        <p style="color: #374151; line-height: 1.7; margin-bottom: 1.5rem;">{{ $submission->abstract }}</p>
                    @endif

                    @if($submission->keywords)
                        <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Mots-cles</h4>
                        <p style="color: #374151;">{{ $submission->keywords }}</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Reviews ({{ $submission->reviews->count() }})</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Reviewer</th>
                                <th>Statut</th>
                                <th>Recommandation</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submission->reviews as $review)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.reviews.show', $review) }}" style="color: #356B8A; font-weight: 500;">
                                            {{ $review->reviewer?->name ?? '-' }}
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $reviewStatusColors = [
                                                'invited' => 'warning',
                                                'accepted' => 'info',
                                                'declined' => 'danger',
                                                'completed' => 'success',
                                                'expired' => 'secondary',
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $reviewStatusColors[$review->status] ?? 'secondary' }}">
                                            {{ \App\Models\Review::getStatuses()[$review->status] ?? ucfirst($review->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($review->recommendation)
                                            {{ \App\Models\Review::getRecommendations()[$review->recommendation] ?? $review->recommendation }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $review->completed_at?->format('d/m/Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align: center; color: #9ca3af; padding: 2rem;">Aucune review</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @include('admin.submissions.partials._admin_timeline', ['submission' => $submission])
        </div>

        <div>
            {{-- Statut + Transition buttons --}}
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Statut</h3>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 1rem;">
                        <span class="badge badge-{{ match($submissionStatusValue) {
                            'draft' => 'secondary',
                            'submitted' => 'info',
                            'under_initial_review' => 'warning',
                            'revision_requested' => 'warning',
                            'under_peer_review' => 'primary',
                            'revision_after_review' => 'warning',
                            'accepted' => 'success',
                            'in_production' => 'info',
                            'rejected' => 'danger',
                            'published' => 'success',
                            default => 'secondary',
                        } }}" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            {{ $submission->status instanceof \App\Enums\SubmissionStatus ? $submission->status->label() : $submissionStatusValue }}
                        </span>
                    </div>

                    @if($submission->decision)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Décision :</span>
                            <span style="font-weight: 500;">{{ \App\Models\Submission::getDecisions()[$submission->decision] ?? $submission->decision }}</span>
                        </div>
                    @endif

                    @if($submission->decision_at)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Le :</span>
                            <span>{{ $submission->decision_at->format('d/m/Y') }}</span>
                        </div>
                    @endif

                    @if(!($submission->status instanceof \App\Enums\SubmissionStatus ? $submission->status->isTerminal() : false))
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.5rem;">Actions :</div>
                            @include('admin.journal._transition_buttons', ['submission' => $submission])
                        </div>
                    @endif
                </div>
            </div>

            @include('admin.submissions.partials._editorial_sidebar', [
                'submission' => $submission,
                'eligibleReviewers' => $eligibleReviewers ?? collect(),
                'eligibleEditors' => $eligibleEditors ?? collect(),
                'eligibleLayoutEditors' => $eligibleLayoutEditors ?? collect(),
            ])

            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Informations</h3>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">Auteur :</span>
                        <span style="font-weight: 500;">{{ $submission->author?->name ?? 'Non defini' }}</span>
                    </div>

                    @if($submission->editor)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Editeur :</span>
                            <span>{{ $submission->editor->name }}</span>
                        </div>
                    @endif

                    @if($submission->journalIssue)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Numero :</span>
                            <a href="{{ route('admin.journal-issues.show', $submission->journalIssue) }}" style="color: #356B8A;">
                                Vol. {{ $submission->journalIssue->volume_number }} N°{{ $submission->journalIssue->issue_number }}
                            </a>
                        </div>
                    @endif

                    @if($submission->submitted_at)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Soumis le :</span>
                            <span>{{ $submission->submitted_at->format('d/m/Y') }}</span>
                        </div>
                    @endif

                    @if($submission->published_at)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Publie le :</span>
                            <span>{{ $submission->published_at->format('d/m/Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Publication</h3>
                    </div>
                    <div class="card-body">
                        @if($submission->doi)
                            <div style="margin-bottom: 0.75rem;">
                                <span style="color: #6b7280;">DOI :</span>
                                <code style="background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.875rem;">{{ $submission->doi }}</code>
                            </div>
                        @endif

                        @if($submission->page_range)
                            <div style="margin-bottom: 0.75rem;">
                                <span style="color: #6b7280;">Pages :</span>
                                <span>{{ $submission->page_range }}</span>
                            </div>
                        @endif

                        {{-- Pagination continue --}}
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <h4 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Pagination continue</h4>

                            @if($submission->start_page && $submission->end_page)
                                <div style="margin-bottom: 0.75rem;">
                                    <span class="badge badge-success" style="font-size: 0.9rem;">
                                        pp. {{ $submission->start_page }}–{{ $submission->end_page }}
                                    </span>
                                    <span style="color: #6b7280; font-size: 0.8rem; margin-left: 0.5rem;">
                                        ({{ $submission->end_page - $submission->start_page + 1 }} pages)
                                    </span>
                                </div>
                            @else
                                <p style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Pages non assignées.</p>
                            @endif

                            @if($submission->journal_issue_id)
                                <form method="POST" action="{{ route('admin.submissions.assign-pages', $submission) }}"
                                      style="display: flex; gap: 0.5rem; align-items: center;">
                                    @csrf
                                    <input type="number" name="page_count" min="1" max="500" required
                                           placeholder="Nb pages"
                                           value="{{ $submission->start_page && $submission->end_page ? $submission->end_page - $submission->start_page + 1 : '' }}"
                                           class="form-input" style="width: 100px; font-size: 0.85rem; padding: 4px 8px;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        {{ $submission->start_page ? 'Recalculer' : 'Calculer' }}
                                    </button>
                                </form>
                                @if($submission->journalIssue)
                                    <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">
                                        Prochaine page dans {{ $submission->journalIssue->title }} :
                                        p. {{ app(\App\Services\PaginationService::class)->getNextStartPage($submission->journalIssue) }}
                                    </p>
                                @endif
                            @else
                                <p style="color: #dc2626; font-size: 0.8rem;">Rattacher d'abord à un numéro.</p>
                            @endif
                        </div>
                    </div>
                </div>

            @if($submission->editor_notes)
                <div class="card" style="margin-top: 1.5rem;">
                    <div class="card-header">
                        <h3 class="card-title">Notes editeur</h3>
                    </div>
                    <div class="card-body">
                        <p style="color: #374151; font-size: 0.875rem;">{{ $submission->editor_notes }}</p>
                    </div>
                </div>
            @endif

            {{-- Fichiers --}}
            @if($submission->manuscript_file || $submission->pdf_file)
                <div class="card" style="margin-top: 1.5rem;">
                    <div class="card-header">
                        <h3 class="card-title">Fichiers</h3>
                    </div>
                    <div class="card-body">
                        @if($submission->manuscript_file)
                            <a href="{{ route('admin.submissions.download', ['submission' => $submission, 'type' => 'manuscript']) }}" class="file-link">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <span>Manuscrit</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14" style="margin-left: auto;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </a>
                        @endif
                        @if($submission->pdf_file)
                            <a href="{{ route('admin.submissions.download', ['submission' => $submission, 'type' => 'pdf']) }}" class="file-link" style="margin-top: 0.5rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <span>PDF final</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14" style="margin-left: auto;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Publication & PDF (only for accepted or published) --}}
            @if(in_array($submissionStatusValue, ['accepted', 'published']))
                {{-- Layout Status Card --}}
                <div class="card" style="margin-top: 1.5rem;">
                    <div class="card-header">
                        <h3 class="card-title">Maquette de l'article</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $hasContent = $submission->content_blocks && is_array($submission->content_blocks) && count($submission->content_blocks) > 0;
                            $blockCount = $hasContent ? count($submission->content_blocks) : 0;
                            $refCount = is_array($submission->references) ? count($submission->references) : 0;
                        @endphp
                        @if($hasContent)
                            <div class="layout-status has-content">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span><strong>{{ $blockCount }}</strong> bloc(s) de contenu, <strong>{{ $refCount }}</strong> reference(s)</span>
                            </div>
                        @else
                            <div class="layout-status empty">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                                <span>Aucun contenu. La maquette n'a pas ete saisie.</span>
                            </div>
                        @endif
                        <a href="{{ route('admin.submissions.layout', $submission) }}" class="btn btn-turquoise" style="margin-top: 0.75rem; width: 100%; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            {{ $hasContent ? 'Modifier la maquette' : 'Creer la maquette' }}
                        </a>
                    </div>
                </div>

                <div class="card publication-card" style="margin-top: 1.5rem;">
                    <div class="card-header" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                        <h3 class="card-title" style="color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18" style="margin-right: 0.5rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                            Publication
                        </h3>
                    </div>
                    <div class="card-body">
                        {{-- PDF Section --}}
                        <div class="publication-section">
                            <h4>PDF de l'article</h4>
                            <div class="publication-actions">
                                @if($submission->pdf_file)
                                    <span class="status-badge success">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        PDF genere
                                    </span>
                                    <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                        <a href="{{ route('admin.submissions.preview-pdf', $submission) }}" class="btn btn-sm btn-secondary" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Voir
                                        </a>
                                        <a href="{{ route('admin.submissions.download-pdf', $submission) }}" class="btn btn-sm btn-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                            Telecharger
                                        </a>
                                        <form action="{{ route('admin.submissions.generate-pdf', $submission) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Regenerer le PDF ?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                </svg>
                                                Regenerer
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <form action="{{ route('admin.submissions.generate-pdf', $submission) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            Generer le PDF
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- DOI Section --}}
                        <div class="publication-section" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <h4>DOI (Digital Object Identifier)</h4>
                            <div class="publication-actions">
                                @if($submission->doi)
                                    <span class="status-badge success">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        DOI assigne
                                    </span>
                                    <div style="margin-top: 0.5rem;">
                                        <code style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; display: block; margin-bottom: 0.5rem;">
                                            {{ $submission->doi }}
                                        </code>
                                        <a href="https://doi.org/{{ $submission->doi }}" target="_blank" class="btn btn-sm btn-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                            </svg>
                                            Voir sur doi.org
                                        </a>
                                    </div>
                                @else
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <form action="{{ route('admin.submissions.assign-doi', $submission) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary" onclick="return confirm('Assigner un DOI local (sans enregistrement Crossref) ?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                                </svg>
                                                Assigner DOI local
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.submissions.register-doi', $submission) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary" onclick="return confirm('Enregistrer le DOI sur Crossref ?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                                </svg>
                                                Enregistrer sur Crossref
                                            </button>
                                        </form>
                                    </div>
                                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem;">
                                        "Local" assigne un DOI sans l'enregistrer. "Crossref" l'enregistre officiellement.
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Publish Action (only for accepted) --}}
                        @if($submissionStatusValue === 'accepted')
                            <div class="publication-section" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                <h4>Publication finale</h4>
                                <form action="{{ route('admin.submissions.publish', $submission) }}" method="POST">
                                    @csrf
                                    {{-- Issue selection --}}
                                    @php
                                        $issues = \App\Models\JournalIssue::orderBy('volume_number', 'desc')
                                            ->orderBy('issue_number', 'desc')
                                            ->get();
                                    @endphp
                                    <div style="margin-bottom: 0.75rem;">
                                        <label style="font-size: 0.75rem; color: #6b7280; display: block; margin-bottom: 0.25rem;">Numero *</label>
                                        <select name="journal_issue_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                            <option value="">Choisir un numero...</option>
                                            @foreach($issues as $issue)
                                                <option value="{{ $issue->id }}" {{ $submission->journal_issue_id == $issue->id ? 'selected' : '' }}>
                                                    Vol. {{ $issue->volume_number }} N°{{ $issue->issue_number }}
                                                    @if($issue->title) - {{ Str::limit($issue->title, 30) }}@endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Page range --}}
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.75rem;">
                                        <div>
                                            <label style="font-size: 0.75rem; color: #6b7280; display: block; margin-bottom: 0.25rem;">Page debut *</label>
                                            <input type="number" name="start_page" min="1" value="{{ $submission->start_page }}" required
                                                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                        </div>
                                        <div>
                                            <label style="font-size: 0.75rem; color: #6b7280; display: block; margin-bottom: 0.25rem;">Page fin *</label>
                                            <input type="number" name="end_page" min="1" value="{{ $submission->end_page }}" required
                                                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                        </div>
                                    </div>

                                    <div style="margin-bottom: 1rem;">
                                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                            <input type="checkbox" name="generate_pdf" value="1" {{ !$submission->pdf_file ? 'checked' : '' }}>
                                            <span style="font-size: 0.875rem;">Generer/Regenerer le PDF</span>
                                        </label>
                                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-top: 0.5rem;">
                                            <input type="checkbox" name="register_doi" value="1">
                                            <span style="font-size: 0.875rem;">Enregistrer DOI sur Crossref</span>
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-success" style="width: 100%;" onclick="return confirm('Publier cet article ?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                        Publier l'article
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
    .workflow-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
    }
    .workflow-timeline::before {
        content: '';
        position: absolute;
        top: 18px;
        left: 30px;
        right: 30px;
        height: 2px;
        background: #e5e7eb;
        z-index: 0;
    }
    .workflow-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 1;
        flex: 1;
    }
    .step-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f3f4f6;
        border: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        margin-bottom: 0.5rem;
    }
    .step-label {
        font-size: 0.75rem;
        color: #9ca3af;
        text-align: center;
    }
    .workflow-step.completed .step-icon {
        background: #2dce89;
        border-color: #2dce89;
        color: white;
    }
    .workflow-step.completed .step-label {
        color: #2dce89;
    }
    .workflow-step.active .step-icon {
        background: #5e72e4;
        border-color: #5e72e4;
        color: white;
        box-shadow: 0 0 0 4px rgba(94, 114, 228, 0.2);
    }
    .workflow-step.active .step-label {
        color: #5e72e4;
        font-weight: 600;
    }
    .workflow-step.rejected .step-icon {
        background: #dc2626;
        border-color: #dc2626;
        color: white;
    }
    .workflow-step.rejected .step-label {
        color: #dc2626;
    }
    .quick-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .quick-actions .btn {
        font-size: 0.75rem;
        padding: 0.35rem 0.75rem;
    }
    .file-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: #f3f4f6;
        border-radius: 0.375rem;
        color: #374151;
        text-decoration: none;
        font-size: 0.875rem;
        transition: background 0.15s;
    }
    .file-link:hover {
        background: #e5e7eb;
    }
    .publication-card .card-header {
        border-bottom: none;
    }
    .publication-section h4 {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
    }
    .publication-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-badge.success {
        background: #d1fae5;
        color: #065f46;
    }
    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.35rem 0.75rem;
    }
    .publication-section .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    .btn-turquoise {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        color: white;
        border: none;
    }
    .btn-turquoise:hover {
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        color: white;
    }
    .layout-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        margin-top: 0.75rem;
    }
    .layout-status.empty {
        background: #fef3c7;
        color: #92400e;
    }
    .layout-status.has-content {
        background: #d1fae5;
        color: #065f46;
    }
    </style>
@endsection
