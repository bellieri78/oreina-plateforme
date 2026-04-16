@php
    use App\Enums\SubmissionStatus;
    use App\Policies\SubmissionPolicy;

    $policy = app(SubmissionPolicy::class);
    $user = auth()->user();
    $current = $submission->status;

    $candidates = [
        ['target' => SubmissionStatus::RevisionRequested,    'label' => 'Demander révision',          'color' => 'amber',  'needsNotes' => true,  'notesRequired' => false],
        ['target' => SubmissionStatus::UnderPeerReview,      'label' => 'Envoyer en relecture',       'color' => 'indigo', 'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::RevisionAfterReview,  'label' => 'Demander révision (relecture)', 'color' => 'orange', 'needsNotes' => true,  'notesRequired' => false],
        ['target' => SubmissionStatus::Accepted,             'label' => 'Accepter',                   'color' => 'green',  'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::Rejected,             'label' => 'Rejeter',                    'color' => 'red',    'needsNotes' => true,  'notesRequired' => true, 'showLepis' => true],
        ['target' => SubmissionStatus::InProduction,         'label' => 'Passer en maquettage',       'color' => 'teal',   'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::Published,            'label' => 'Publier',                    'color' => 'emerald','needsNotes' => false, 'notesRequired' => false],
    ];

    $actions = array_values(array_filter($candidates, fn($c) => $policy->transitionTo($user, $submission, $c['target'])));
@endphp

@if(count($actions) === 0)
    <span class="text-xs text-gray-400">—</span>
@else
    <div class="flex flex-wrap gap-1" x-data="{ modal: null }">
        @foreach($actions as $action)
            @if($action['needsNotes'] ?? false)
                <button type="button"
                        @click="modal = '{{ $action['target']->value }}'"
                        class="bg-{{ $action['color'] }}-600 hover:bg-{{ $action['color'] }}-700 text-white text-xs px-2 py-1 rounded">
                    {{ $action['label'] }}
                </button>
            @else
                <form method="POST" action="{{ route('admin.journal.submissions.transition', $submission) }}"
                      onsubmit="return confirm('Confirmer : {{ $action['label'] }} ?');">
                    @csrf
                    <input type="hidden" name="target_status" value="{{ $action['target']->value }}">
                    <button type="submit"
                            class="bg-{{ $action['color'] }}-600 hover:bg-{{ $action['color'] }}-700 text-white text-xs px-2 py-1 rounded">
                        {{ $action['label'] }}
                    </button>
                </form>
            @endif
        @endforeach

        @foreach($actions as $action)
            @if($action['needsNotes'] ?? false)
                <div x-show="modal === '{{ $action['target']->value }}'"
                     x-cloak
                     x-transition
                     @keydown.escape.window="modal = null"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.outside="modal = null">
                        <h3 class="text-lg font-bold mb-3">{{ $action['label'] }}</h3>
                        <form method="POST" action="{{ route('admin.journal.submissions.transition', $submission) }}">
                            @csrf
                            <input type="hidden" name="target_status" value="{{ $action['target']->value }}">

                            <label class="block text-sm font-medium mb-1">
                                Notes @if($action['notesRequired'] ?? false)<span class="text-red-600">*</span>@endif
                            </label>
                            <textarea name="notes" rows="4"
                                      class="w-full border-gray-300 rounded mb-3"
                                      @if($action['notesRequired'] ?? false) required @endif
                                      placeholder="Motif, retours, instructions..."></textarea>

                            @if($action['showLepis'] ?? false)
                                <label class="flex items-center gap-2 text-sm mb-3">
                                    <input type="checkbox" name="redirect_to_lepis" value="1">
                                    Recommander pour le bulletin <strong>Lepis</strong>
                                </label>
                            @endif

                            <div class="flex justify-end gap-2">
                                <button type="button" @click="modal = null"
                                        class="px-4 py-2 border border-gray-300 rounded text-sm">Annuler</button>
                                <button type="submit"
                                        class="bg-{{ $action['color'] }}-600 hover:bg-{{ $action['color'] }}-700 text-white px-4 py-2 rounded text-sm">
                                    Confirmer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif
