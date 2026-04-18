@extends('layouts.journal')

@section('title', $submission->title)
@section('meta_description', 'Détails de votre soumission à la revue OREINA.')

@section('content')
    <div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('journal.submissions.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-oreina-turquoise transition mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="m15 18-6-6 6-6"/>
                    </svg>
                    Mes soumissions
                </a>

                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="flex-1">
                        @php
                            $statusColors = [
                                'submitted' => 'bg-blue-100 text-blue-700',
                                'under_initial_review' => 'bg-yellow-100 text-yellow-700',
                                'under_peer_review' => 'bg-purple-100 text-purple-700',
                                'revision_after_review' => 'bg-orange-100 text-orange-700',
                                'accepted' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'published' => 'bg-oreina-turquoise/20 text-oreina-teal',
                            ];
                            $submissionStatusValue = $submission->status instanceof \App\Enums\SubmissionStatus ? $submission->status->value : $submission->status;
                        @endphp
                        <span class="inline-flex px-3 py-1 text-xs font-bold rounded-lg {{ $statusColors[$submissionStatusValue] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ $submission->status instanceof \App\Enums\SubmissionStatus ? $submission->status->label() : (\App\Models\Submission::getStatuses()[$submission->status] ?? $submission->status) }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-bold text-oreina-dark mt-3">{{ $submission->title }}</h1>
                    </div>

                    @if(in_array($submission->status?->value, ['revision_requested', 'revision_after_review']))
                        <a href="{{ route('journal.submissions.edit', $submission) }}" class="btn-turquoise">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 12V4m0 0L8 8m4-4 4 4"/>
                            </svg>
                            Soumettre révision
                        </a>
                    @endif
                </div>
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

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="space-y-6">
                {{-- Timeline 6 étapes --}}
                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6">
                    <h2 class="text-lg font-bold text-oreina-dark mb-6">Suivi de votre soumission</h2>

                    @php
                        $steps = [
                            ['label' => 'Soumis',        'icon' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8'],
                            ['label' => 'En évaluation', 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
                            ['label' => 'Relecture',     'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['label' => 'Décision',      'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['label' => 'Maquettage',    'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                            ['label' => 'Publié',        'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                        ];

                        $timelineIndex = match($submissionStatusValue) {
                            'submitted' => 0,
                            'under_initial_review', 'revision_requested' => 1,
                            'under_peer_review', 'revision_after_review' => 2,
                            'accepted', 'rejected' => 3,
                            'in_production' => 4,
                            'published' => 5,
                            default => 0,
                        };

                        $isRejected = $submissionStatusValue === 'rejected';
                        $needsAction = in_array($submissionStatusValue, ['revision_requested', 'revision_after_review']);
                    @endphp

                    <div class="flex items-start justify-between gap-0">
                        @foreach($steps as $index => $step)
                            @php
                                $isPast = $index < $timelineIndex;
                                $isCurrent = $index === $timelineIndex;
                                $isRejectedStep = $isRejected && $index === 3;
                            @endphp
                            <div class="flex flex-col items-center flex-1 relative">
                                <div class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 transition-all
                                    @if($isRejectedStep)
                                        bg-red-500 border-red-500 text-white
                                    @elseif($isPast)
                                        bg-oreina-turquoise border-oreina-turquoise text-white
                                    @elseif($isCurrent && $needsAction)
                                        bg-orange-500 border-orange-500 text-white animate-pulse
                                    @elseif($isCurrent)
                                        bg-oreina-turquoise border-oreina-turquoise text-white
                                    @else
                                        bg-white border-slate-300 text-slate-400
                                    @endif
                                ">
                                    @if($isPast)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                    @elseif($isRejectedStep)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="{{ $step['icon'] }}"/></svg>
                                    @endif
                                </div>
                                <span class="mt-2 text-xs font-semibold text-center leading-tight
                                    @if($isRejectedStep) text-red-600
                                    @elseif($isPast || $isCurrent) text-oreina-dark
                                    @else text-slate-400
                                    @endif
                                ">
                                    {{ $step['label'] }}
                                </span>
                            </div>

                            @if(!$loop->last)
                                <div class="flex-1 h-0.5 mt-5 sm:mt-6
                                    @if($index < $timelineIndex) bg-oreina-turquoise
                                    @else bg-slate-200
                                    @endif
                                "></div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Bandeau "Action requise" --}}
                @if($needsAction)
                    <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <p class="font-bold text-orange-800">Action requise</p>
                                <p class="text-sm text-orange-700 mt-1">
                                    @if($submissionStatusValue === 'revision_requested')
                                        Des compléments vous sont demandés avant que votre manuscrit parte en relecture.
                                    @else
                                        Une révision est demandée suite aux retours des relecteurs.
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('journal.submissions.edit', $submission) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 12V4m0 0L8 8m4-4 4 4"/>
                                </svg>
                                Soumettre ma révision
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Journal d'activité --}}
                @if($submission->transitions && $submission->transitions->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6">
                        <h2 class="text-lg font-bold text-oreina-dark mb-4">Historique</h2>

                        @php
                            $authorLabels = [
                                'submitted → under_initial_review'        => 'Votre manuscrit est en cours d\'évaluation',
                                'under_initial_review → under_peer_review' => 'Votre manuscrit a été envoyé en relecture',
                                'under_initial_review → revision_requested' => 'Des compléments vous sont demandés avant relecture',
                                'under_initial_review → rejected'          => 'Votre manuscrit n\'a pas été retenu',
                                'revision_requested → under_initial_review' => 'Votre manuscrit révisé a été reçu',
                                'under_peer_review → revision_after_review' => 'Une révision vous est demandée suite aux retours des relecteurs',
                                'under_peer_review → accepted'             => 'Votre manuscrit a été accepté pour publication',
                                'under_peer_review → rejected'             => 'Votre manuscrit n\'a pas été retenu',
                                'revision_after_review → under_peer_review' => 'Votre manuscrit révisé a été reçu et renvoyé en relecture',
                                'revision_after_review → accepted'         => 'Votre manuscrit a été accepté pour publication',
                                'revision_after_review → rejected'         => 'Votre manuscrit n\'a pas été retenu',
                                'accepted → in_production'                 => 'Votre article est en cours de maquettage',
                                'in_production → published'                => 'Votre article est publié !',
                            ];
                        @endphp

                        <div class="space-y-3">
                            @foreach($submission->transitions as $transition)
                                @php
                                    $key = ($transition->from_status ?? '') . ' → ' . ($transition->to_status ?? '');
                                    $label = $authorLabels[$key] ?? null;
                                @endphp
                                @if($label)
                                    <div class="flex items-start gap-3">
                                        <div class="shrink-0 w-2 h-2 mt-2 rounded-full
                                            @if(str_contains($transition->to_status ?? '', 'rejected')) bg-red-400
                                            @elseif(str_contains($transition->to_status ?? '', 'accepted') || str_contains($transition->to_status ?? '', 'published')) bg-green-400
                                            @elseif(str_contains($transition->to_status ?? '', 'revision')) bg-orange-400
                                            @else bg-oreina-turquoise
                                            @endif
                                        "></div>
                                        <div>
                                            <p class="text-sm text-oreina-dark">{{ $label }}</p>
                                            <p class="text-xs text-slate-500">{{ $transition->created_at->format('d/m/Y à H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Decision (if any) — only shown when status is at decision stage or later --}}
                @if($submission->decision && in_array($submissionStatusValue, ['accepted', 'rejected', 'revision_after_review', 'in_production', 'published']))
                    <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6">
                        <h2 class="text-lg font-bold text-oreina-dark mb-4">Décision éditoriale</h2>

                        @php
                            $decisionColors = [
                                'accept' => 'bg-green-100 text-green-700 border-green-200',
                                'minor_revision' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                'major_revision' => 'bg-orange-100 text-orange-700 border-orange-200',
                                'reject' => 'bg-red-100 text-red-700 border-red-200',
                            ];
                            $decisionLabels = \App\Models\Submission::getDecisions();
                        @endphp

                        <div class="p-4 rounded-xl border {{ $decisionColors[$submission->decision] ?? 'bg-slate-100 border-slate-200' }}">
                            <div class="flex items-center gap-3 mb-2">
                                @if($submission->decision === 'accept')
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($submission->decision === 'reject')
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                @endif
                                <span class="font-bold text-lg">{{ $decisionLabels[$submission->decision] ?? $submission->decision }}</span>
                            </div>

                            @if($submission->decision_at)
                                <p class="text-sm opacity-75">Décision rendue le {{ $submission->decision_at->format('d/m/Y à H:i') }}</p>
                            @endif
                        </div>

                        @if($submission->editor_notes)
                            <div class="mt-4">
                                <h3 class="font-semibold text-slate-700 mb-2">Commentaires de l'éditeur :</h3>
                                <div class="p-4 bg-slate-50 rounded-xl text-slate-700 text-sm leading-relaxed">
                                    {!! nl2br(e($submission->editor_notes)) !!}
                                </div>
                            </div>
                        @endif

                        @if(in_array($submission->decision, ['minor_revision', 'major_revision']) && $submissionStatusValue === 'revision_after_review')
                            <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                                <p class="text-orange-800 font-medium mb-2">Une révision est demandée</p>
                                <p class="text-sm text-orange-700">Veuillez prendre en compte les commentaires ci-dessus et soumettre une version révisée de votre manuscrit.</p>
                                <a href="{{ route('journal.submissions.edit', $submission) }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 12V4m0 0L8 8m4-4 4 4"/>
                                    </svg>
                                    Soumettre ma révision
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Article details --}}
                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6">
                    <h2 class="text-lg font-bold text-oreina-dark mb-4">Détails de l'article</h2>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-1">Résumé</h3>
                            <p class="text-slate-700 leading-relaxed">{{ $submission->abstract }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-1">Mots-clés</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $submission->keywords) as $keyword)
                                    <span class="px-3 py-1 bg-slate-100 text-slate-700 text-sm rounded-full">{{ trim($keyword) }}</span>
                                @endforeach
                            </div>
                        </div>

                        @if($submission->co_authors && count($submission->co_authors) > 0)
                            <div>
                                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2">Co-auteurs</h3>
                                <div class="grid sm:grid-cols-2 gap-3">
                                    @foreach($submission->co_authors as $coAuthor)
                                        <div class="p-3 bg-slate-50 rounded-lg">
                                            <p class="font-semibold text-slate-700">{{ $coAuthor['name'] }}</p>
                                            @if(!empty($coAuthor['affiliation']))
                                                <p class="text-sm text-slate-500">{{ $coAuthor['affiliation'] }}</p>
                                            @endif
                                            @if(!empty($coAuthor['email']))
                                                <p class="text-sm text-slate-500">{{ $coAuthor['email'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Manuscript file --}}
                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6">
                    <h2 class="text-lg font-bold text-oreina-dark mb-4">Manuscrit</h2>

                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-slate-700">Manuscrit PDF</p>
                            <p class="text-sm text-slate-500">Soumis le {{ $submission->submitted_at?->format('d/m/Y à H:i') ?? $submission->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <a href="{{ Storage::url($submission->manuscript_file) }}" target="_blank" class="px-4 py-2 bg-oreina-turquoise text-white font-semibold rounded-lg hover:bg-oreina-teal transition">
                            Télécharger
                        </a>
                    </div>
                </div>

                {{-- Publication info (if published) --}}
                @if($submissionStatusValue === 'published' && $submission->journalIssue)
                    <div class="bg-gradient-to-br from-oreina-turquoise/10 to-oreina-green/10 rounded-2xl border border-oreina-turquoise/30 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-oreina-turquoise rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-oreina-dark">Publié !</h2>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-slate-500">Numéro</p>
                                <p class="font-semibold text-slate-700">{{ $submission->journalIssue->title }}</p>
                            </div>
                            @if($submission->start_page && $submission->end_page)
                                <div>
                                    <p class="text-sm text-slate-500">Pages</p>
                                    <p class="font-semibold text-slate-700">{{ $submission->start_page }} - {{ $submission->end_page }}</p>
                                </div>
                            @endif
                            @if($submission->doi)
                                <div class="sm:col-span-2">
                                    <p class="text-sm text-slate-500">DOI</p>
                                    <a href="https://doi.org/{{ $submission->doi }}" target="_blank" class="font-semibold text-oreina-turquoise hover:underline">
                                        {{ $submission->doi }}
                                    </a>
                                </div>
                            @endif
                            @if($submission->published_at)
                                <div>
                                    <p class="text-sm text-slate-500">Date de publication</p>
                                    <p class="font-semibold text-slate-700">{{ $submission->published_at->format('d/m/Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Meta information --}}
                <div class="text-sm text-slate-500 flex flex-wrap gap-x-6 gap-y-2">
                    <span>Soumission #{{ $submission->id }}</span>
                    <span>Créée le {{ $submission->created_at->format('d/m/Y à H:i') }}</span>
                    @if($submission->updated_at != $submission->created_at)
                        <span>Mise à jour le {{ $submission->updated_at->format('d/m/Y à H:i') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
