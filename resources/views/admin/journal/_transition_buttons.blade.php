@php
    use App\Enums\SubmissionStatus;
    use App\Policies\SubmissionPolicy;

    $policy = app(SubmissionPolicy::class);
    $user = auth()->user();

    $btnStyles = [
        'amber'   => 'background:#d97706;color:#fff;',
        'indigo'  => 'background:#4f46e5;color:#fff;',
        'orange'  => 'background:#ea580c;color:#fff;',
        'green'   => 'background:#16a34a;color:#fff;',
        'red'     => 'background:#dc2626;color:#fff;',
        'teal'    => 'background:#0d9488;color:#fff;',
        'emerald' => 'background:#059669;color:#fff;',
    ];

    $candidates = [
        ['target' => SubmissionStatus::UnderInitialReview,   'label' => 'Commencer l\'évaluation',        'color' => 'indigo', 'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::RevisionRequested,    'label' => 'Demander révision',              'color' => 'amber',  'needsNotes' => true,  'notesRequired' => false],
        ['target' => SubmissionStatus::UnderPeerReview,      'label' => 'Envoyer en relecture',           'color' => 'indigo', 'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::RevisionAfterReview,  'label' => 'Demander révision (relecture)',  'color' => 'orange', 'needsNotes' => true,  'notesRequired' => false],
        ['target' => SubmissionStatus::Accepted,             'label' => 'Accepter',                       'color' => 'green',  'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::Rejected,             'label' => 'Rejeter',                        'color' => 'red',    'needsNotes' => true,  'notesRequired' => true, 'showLepis' => true],
        ['target' => SubmissionStatus::InProduction,         'label' => 'Passer en maquettage',           'color' => 'teal',   'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::Published,            'label' => 'Publier',                        'color' => 'emerald','needsNotes' => false, 'notesRequired' => false],
    ];

    $actions = array_values(array_filter($candidates, fn($c) => $policy->transitionTo($user, $submission, $c['target'])));
    $modalId = 'transition_modal_' . $submission->id;
@endphp

@if(count($actions) === 0)
    <span style="font-size:0.75rem;color:#9ca3af;">—</span>
@else
    <div x-data="{ modal: null }" style="display:flex;flex-wrap:wrap;gap:4px;">
        @foreach($actions as $action)
            @if($action['needsNotes'] ?? false)
                <button type="button"
                        @click="modal = '{{ $action['target']->value }}'"
                        style="{{ $btnStyles[$action['color']] }}font-size:0.75rem;padding:2px 8px;border-radius:4px;border:none;cursor:pointer;">
                    {{ $action['label'] }}
                </button>
            @else
                <form method="POST" action="{{ route('admin.journal.submissions.transition', $submission) }}"
                      onsubmit="return confirm('Confirmer : {{ $action['label'] }} ?');"
                      style="margin:0;">
                    @csrf
                    <input type="hidden" name="target_status" value="{{ $action['target']->value }}">
                    <button type="submit"
                            style="{{ $btnStyles[$action['color']] }}font-size:0.75rem;padding:2px 8px;border-radius:4px;border:none;cursor:pointer;">
                        {{ $action['label'] }}
                    </button>
                </form>
            @endif
        @endforeach

        {{-- Modales rendues en position fixe, hors du flux du tableau --}}
        @foreach($actions as $action)
            @if($action['needsNotes'] ?? false)
                <template x-teleport="body">
                    <div x-show="modal === '{{ $action['target']->value }}'"
                         x-cloak
                         x-transition
                         @keydown.escape.window="modal = null"
                         style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;background:rgba(0,0,0,0.5);">
                        <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
                        <div @click.outside="modal = null"
                             style="background:#fff;border-radius:8px;box-shadow:0 25px 50px rgba(0,0,0,0.25);max-width:28rem;width:90%;padding:1.5rem;">
                            <h3 style="font-size:1.1rem;font-weight:700;margin:0 0 12px 0;">{{ $action['label'] }}</h3>
                            <form method="POST" action="{{ route('admin.journal.submissions.transition', $submission) }}">
                                @csrf
                                <input type="hidden" name="target_status" value="{{ $action['target']->value }}">

                                <label style="display:block;font-size:0.875rem;font-weight:500;margin-bottom:4px;">
                                    Notes @if($action['notesRequired'] ?? false)<span style="color:#dc2626;">*</span>@endif
                                </label>
                                <textarea name="notes" rows="4"
                                          style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:8px;margin-bottom:12px;font-size:0.875rem;"
                                          @if($action['notesRequired'] ?? false) required @endif
                                          placeholder="Motif, retours, instructions..."></textarea>

                                @if($action['showLepis'] ?? false)
                                    <label style="display:flex;align-items:center;gap:8px;font-size:0.875rem;margin-bottom:12px;">
                                        <input type="checkbox" name="redirect_to_lepis" value="1">
                                        Recommander pour le bulletin <strong>Lepis</strong>
                                    </label>
                                @endif

                                <div style="display:flex;justify-content:flex-end;gap:8px;">
                                    <button type="button" @click="modal = null"
                                            style="padding:6px 16px;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;cursor:pointer;">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                            style="{{ $btnStyles[$action['color']] }}padding:6px 16px;border-radius:6px;font-size:0.875rem;border:none;cursor:pointer;">
                                        Confirmer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    </div>
                </template>
            @endif
        @endforeach
    </div>
@endif
