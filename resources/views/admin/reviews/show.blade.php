@extends('layouts.admin')
@section('title', 'Review')
@section('breadcrumb')
    <a href="{{ route('admin.reviews.index') }}">Reviews</a>
    <span>/</span>
    <span>Details</span>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div>
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Review de : {{ Str::limit($review->submission?->title, 50) }}</h3>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.reviews.edit', $review) }}" class="btn btn-secondary">Modifier</a>
                    </div>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Soumission</h4>
                        <a href="{{ route('admin.submissions.show', $review->submission) }}" style="color: #356B8A; font-weight: 500;">
                            {{ $review->submission?->title }}
                        </a>
                        <div style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">
                            par {{ $review->submission?->author?->name ?? 'Inconnu' }}
                        </div>
                    </div>

                    @if($review->comments_to_editor)
                        <div style="margin-bottom: 1.5rem;">
                            <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Commentaires a l'editeur</h4>
                            <div style="background-color: #fef3c7; padding: 1rem; border-radius: 0.375rem; color: #92400e;">
                                {{ $review->comments_to_editor }}
                            </div>
                        </div>
                    @endif

                    @if($review->comments_to_author)
                        <div style="margin-bottom: 1.5rem;">
                            <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Commentaires a l'auteur</h4>
                            <div style="background-color: #f3f4f6; padding: 1rem; border-radius: 0.375rem;">
                                {{ $review->comments_to_author }}
                            </div>
                        </div>
                    @endif

                    @if($review->review_file)
                        <div style="margin-bottom: 1.5rem;">
                            <h4 style="font-size: 0.875rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Document de review</h4>
                            <a href="{{ route('admin.reviews.download', $review) }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; background: #f3f4f6; border-radius: 0.375rem; color: #374151; text-decoration: none; font-size: 0.875rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <span>{{ basename($review->review_file) }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14" style="margin-left: auto;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @if($review->average_score)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Scores</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; text-align: center;">
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #356B8A;">{{ $review->score_originality ?? '-' }}</div>
                                <div style="font-size: 0.75rem; color: #6b7280;">Originalite</div>
                            </div>
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #356B8A;">{{ $review->score_methodology ?? '-' }}</div>
                                <div style="font-size: 0.75rem; color: #6b7280;">Methodologie</div>
                            </div>
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #356B8A;">{{ $review->score_clarity ?? '-' }}</div>
                                <div style="font-size: 0.75rem; color: #6b7280;">Clarte</div>
                            </div>
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #356B8A;">{{ $review->score_significance ?? '-' }}</div>
                                <div style="font-size: 0.75rem; color: #6b7280;">Significance</div>
                            </div>
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #356B8A;">{{ $review->score_references ?? '-' }}</div>
                                <div style="font-size: 0.75rem; color: #6b7280;">References</div>
                            </div>
                        </div>
                        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700; color: #2dce89;">{{ $review->average_score }}/5</div>
                            <div style="color: #6b7280;">Score moyen</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div>
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Statut</h3>
                </div>
                <div class="card-body">
                    @php
                        $statusColors = [
                            'invited' => 'warning',
                            'accepted' => 'info',
                            'declined' => 'danger',
                            'completed' => 'success',
                            'expired' => 'secondary',
                        ];
                    @endphp
                    <div style="margin-bottom: 1rem;">
                        <span class="badge badge-{{ $statusColors[$review->status] ?? 'secondary' }}" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            {{ \App\Models\Review::getStatuses()[$review->status] ?? $review->status }}
                        </span>
                    </div>

                    @if($review->recommendation)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Recommandation :</span>
                            <span style="font-weight: 500;">{{ \App\Models\Review::getRecommendations()[$review->recommendation] ?? $review->recommendation }}</span>
                        </div>
                    @endif

                    {{-- Quick Actions --}}
                    @if(!in_array($review->status, ['completed', 'expired']))
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.5rem;">Actions rapides :</div>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                @if($review->status === 'invited')
                                    <form action="{{ route('admin.reviews.update', $review) }}" method="POST" style="display: inline;">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="submission_id" value="{{ $review->submission_id }}">
                                        <input type="hidden" name="reviewer_id" value="{{ $review->reviewer_id }}">
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="btn btn-success" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">Accepter</button>
                                    </form>
                                    <form action="{{ route('admin.reviews.update', $review) }}" method="POST" style="display: inline;">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="submission_id" value="{{ $review->submission_id }}">
                                        <input type="hidden" name="reviewer_id" value="{{ $review->reviewer_id }}">
                                        <input type="hidden" name="status" value="declined">
                                        <button type="submit" class="btn btn-danger" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">Decliner</button>
                                    </form>
                                @endif

                                @if(in_array($review->status, ['invited', 'accepted']))
                                    <form action="{{ route('admin.reviews.send-reminder') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="ids" value="{{ $review->id }}">
                                        <button type="submit" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14" style="vertical-align: -2px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                            </svg>
                                            Rappel
                                        </button>
                                    </form>
                                @endif

                                @if($review->status === 'accepted')
                                    <form action="{{ route('admin.reviews.update', $review) }}" method="POST" style="display: inline;">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="submission_id" value="{{ $review->submission_id }}">
                                        <input type="hidden" name="reviewer_id" value="{{ $review->reviewer_id }}">
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;" onclick="return confirm('Marquer comme termine ?')">Terminer</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Overdue Warning --}}
                    @if($review->due_date && $review->due_date < now() && in_array($review->status, ['invited', 'accepted']))
                        <div style="margin-top: 1rem; padding: 0.75rem; background: #fef2f2; border-radius: 0.375rem; border: 1px solid #fecaca;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: #dc2626; font-weight: 500; font-size: 0.875rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                                Cette review est en retard !
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Reviewer</h3>
                </div>
                <div class="card-body">
                    <div style="font-weight: 600; font-size: 1.125rem;">{{ $review->reviewer?->name ?? 'Non assigne' }}</div>
                    @if($review->reviewer?->email)
                        <div style="color: #6b7280;">{{ $review->reviewer->email }}</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dates</h3>
                </div>
                <div class="card-body">
                    @if($review->invited_at)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Invite le :</span>
                            <span>{{ $review->invited_at->format('d/m/Y') }}</span>
                        </div>
                    @endif

                    @if($review->responded_at)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Repondu le :</span>
                            <span>{{ $review->responded_at->format('d/m/Y') }}</span>
                        </div>
                    @endif

                    @if($review->due_date)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Echeance :</span>
                            <span style="{{ $review->due_date < now() && $review->status !== 'completed' ? 'color: #dc2626; font-weight: 500;' : '' }}">
                                {{ $review->due_date->format('d/m/Y') }}
                            </span>
                        </div>
                    @endif

                    @if($review->completed_at)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Complete le :</span>
                            <span style="color: #2dce89; font-weight: 500;">{{ $review->completed_at->format('d/m/Y') }}</span>
                        </div>
                    @endif

                    @if($review->assignedBy)
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <span style="color: #6b7280;">Assigne par :</span>
                            <span>{{ $review->assignedBy->name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
