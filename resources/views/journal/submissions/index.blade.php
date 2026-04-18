@extends('layouts.journal')

@section('title', 'Mes soumissions')
@section('meta_description', 'Suivez l\'état de vos soumissions à la revue OREINA.')

@section('content')
    <div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-oreina-dark">Mes soumissions</h1>
                    <p class="text-slate-600 mt-1">Suivez l'état de vos manuscrits soumis à la revue</p>
                </div>
                <a href="{{ route('journal.submissions.create') }}" class="btn-turquoise">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Nouvelle soumission
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($submissions->count() > 0)
                <div class="space-y-4">
                    @foreach($submissions as $submission)
                        <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6 hover:shadow-lg transition">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-3 mb-2">
                                        @php
                                            $statusColors = [
                                                'submitted' => 'bg-blue-100 text-blue-700',
                                                'under_initial_review' => 'bg-yellow-100 text-yellow-700',
                                                'under_peer_review' => 'bg-purple-100 text-purple-700',
                                                'revision_requested' => 'bg-orange-100 text-orange-700',
                                                'revision_after_review' => 'bg-orange-100 text-orange-700',
                                                'accepted' => 'bg-green-100 text-green-700',
                                                'rejected' => 'bg-red-100 text-red-700',
                                                'in_production' => 'bg-teal-100 text-teal-700',
                                                'awaiting_author_approval' => 'bg-violet-100 text-violet-700',
                                                'published' => 'bg-oreina-turquoise/20 text-oreina-teal',
                                            ];
                                            $submissionStatusValue = $submission->status instanceof \App\Enums\SubmissionStatus ? $submission->status->value : $submission->status;
                                        @endphp
                                        <span class="px-3 py-1 text-xs font-bold rounded-lg {{ $statusColors[$submissionStatusValue] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $submission->status instanceof \App\Enums\SubmissionStatus ? $submission->status->label() : (\App\Models\Submission::getStatuses()[$submission->status] ?? $submission->status) }}
                                        </span>
                                        @if(in_array($submissionStatusValue, ['revision_requested', 'revision_after_review']))
                                            <span class="px-2 py-0.5 text-xs font-bold rounded-lg bg-orange-100 text-orange-700 animate-pulse">
                                                Action requise
                                            </span>
                                        @endif
                                        <span class="text-sm text-slate-500">
                                            Soumis le {{ $submission->submitted_at?->format('d/m/Y') ?? $submission->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>

                                    <h3 class="text-lg font-bold text-oreina-dark mb-2">
                                        <a href="{{ route('journal.submissions.show', $submission) }}" class="hover:text-oreina-turquoise transition">
                                            {{ $submission->title }}
                                        </a>
                                    </h3>

                                    <p class="text-sm text-slate-600 line-clamp-2">{{ Str::limit($submission->abstract, 200) }}</p>

                                    @if($submission->decision)
                                        <div class="mt-3 flex items-center gap-2 text-sm">
                                            <span class="font-semibold text-slate-700">Décision :</span>
                                            @php
                                                $decisionColors = [
                                                    'accept' => 'text-green-600',
                                                    'minor_revision' => 'text-yellow-600',
                                                    'major_revision' => 'text-orange-600',
                                                    'reject' => 'text-red-600',
                                                ];
                                                $decisionLabels = \App\Models\Submission::getDecisions();
                                            @endphp
                                            <span class="font-medium {{ $decisionColors[$submission->decision] ?? 'text-slate-600' }}">
                                                {{ $decisionLabels[$submission->decision] ?? $submission->decision }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex sm:flex-col gap-2">
                                    <a href="{{ route('journal.submissions.show', $submission) }}" class="px-4 py-2 text-sm font-semibold text-oreina-turquoise hover:bg-oreina-turquoise/10 rounded-lg transition">
                                        Voir détails
                                    </a>
                                    @if($submission->status?->value === 'revision_after_review')
                                        <a href="{{ route('journal.submissions.edit', $submission) }}" class="px-4 py-2 text-sm font-semibold bg-orange-100 text-orange-700 hover:bg-orange-200 rounded-lg transition">
                                            Soumettre révision
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $submissions->links() }}
                </div>
            @else
                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-12 text-center">
                    <div class="w-16 h-16 bg-oreina-turquoise/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-oreina-turquoise" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-oreina-dark mb-2">Aucune soumission</h3>
                    <p class="text-slate-600 mb-6">Vous n'avez pas encore soumis de manuscrit à la revue.</p>
                    <a href="{{ route('journal.submissions.create') }}" class="btn-turquoise inline-flex">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Soumettre un article
                    </a>
                </div>
            @endif

            {{-- Help section --}}
            <div class="mt-8 bg-oreina-turquoise/5 rounded-2xl p-6">
                <h3 class="font-bold text-oreina-dark mb-3">Besoin d'aide ?</h3>
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <a href="{{ route('journal.authors') }}" class="flex items-center gap-2 text-oreina-turquoise hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Instructions aux auteurs
                    </a>
                    <a href="{{ route('journal.submit') }}" class="flex items-center gap-2 text-oreina-turquoise hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/>
                        </svg>
                        Processus de soumission
                    </a>
                    <a href="{{ route('hub.contact') }}" class="flex items-center gap-2 text-oreina-turquoise hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="20" height="16" x="2" y="4" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                        Contacter la rédaction
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
