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
        'purple'  => 'background:#7c3aed;color:#fff;',
    ];

    $candidates = [
        ['target' => SubmissionStatus::UnderInitialReview,   'label' => 'Commencer l\'évaluation',        'color' => 'indigo', 'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::RevisionRequested,    'label' => 'Demander révision',              'color' => 'amber',  'needsNotes' => true,  'notesRequired' => false],
        ['target' => SubmissionStatus::UnderPeerReview,      'label' => 'Envoyer en relecture',           'color' => 'indigo', 'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::RevisionAfterReview,  'label' => 'Demander révision (relecture)',  'color' => 'orange', 'needsNotes' => true,  'notesRequired' => false],
        ['target' => SubmissionStatus::Accepted,             'label' => 'Accepter',                       'color' => 'green',  'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::Rejected,             'label' => 'Rejeter',                        'color' => 'red',    'needsNotes' => true,  'notesRequired' => true, 'showLepis' => true],
        ['target' => SubmissionStatus::InProduction,            'label' => 'Passer en maquettage',                      'color' => 'teal',   'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::AwaitingAuthorApproval, 'label' => 'Envoyer à l\'auteur pour approbation',       'color' => 'purple', 'needsNotes' => false, 'notesRequired' => false],
        ['target' => SubmissionStatus::Published,              'label' => 'Publier',                                   'color' => 'emerald','needsNotes' => false, 'notesRequired' => false],
    ];

    $actions = array_values(array_filter($candidates, fn($c) => $policy->transitionTo($user, $submission, $c['target'])));
    $modalId = 'transition_modal_' . $submission->id;
@endphp

@if(count($actions) === 0)
    <span style="font-size:0.75rem;color:#9ca3af;">—</span>
@else
    <div x-data="{ modal: null }" style="display:flex;flex-wrap:wrap;gap:4px;">
        @foreach($actions as $action)
            <button type="button"
                    @click="modal = '{{ $action['target']->value }}'"
                    style="{{ $btnStyles[$action['color']] }}font-size:0.75rem;padding:2px 8px;border-radius:4px;border:none;cursor:pointer;">
                {{ $action['label'] }}
            </button>
        @endforeach

        {{-- Modales rendues en position fixe, hors du flux du tableau --}}
        @foreach($actions as $action)
            <template x-teleport="body">
                <div x-show="modal === '{{ $action['target']->value }}'"
                     x-cloak
                     x-transition
                     @keydown.escape.window="modal = null"
                     style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;background:rgba(0,0,0,0.5);">
                    <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;padding:1rem;">
                    <div @click.outside="modal = null"
                         style="background:#fff;border-radius:8px;box-shadow:0 25px 50px rgba(0,0,0,0.25);max-width:32rem;width:100%;padding:1.75rem;">
                        <h3 style="font-size:1.15rem;font-weight:700;margin:0 0 0.5rem 0;color:#16302B;">{{ $action['label'] }}</h3>

                        @if($action['needsNotes'] ?? false)
                            <p style="font-size:0.875rem;color:#6b7280;margin:0 0 1rem 0;">
                                Vous êtes sur le point de faire passer la soumission au statut
                                <strong>{{ $action['target']->label() }}</strong>.
                                Renseignez {{ ($action['notesRequired'] ?? false) ? 'les motifs' : 'le détail de votre retour' }} ci-dessous.
                            </p>
                        @else
                            <p style="font-size:0.875rem;color:#6b7280;margin:0 0 1rem 0;">
                                Confirmez le passage de la soumission au statut
                                <strong>{{ $action['target']->label() }}</strong>. Cette action est journalisée.
                            </p>
                        @endif

                        <form method="POST" action="{{ route('admin.journal.submissions.transition', $submission) }}">
                            @csrf
                            <input type="hidden" name="target_status" value="{{ $action['target']->value }}">

                            @if($action['needsNotes'] ?? false)
                                <label style="display:block;font-size:0.875rem;font-weight:500;margin-bottom:0.375rem;color:#374151;">
                                    Notes @if($action['notesRequired'] ?? false)<span style="color:#dc2626;">*</span>@endif
                                </label>
                                <textarea name="notes" rows="6"
                                          style="width:100%;min-height:140px;box-sizing:border-box;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;margin-bottom:1rem;font-size:0.875rem;font-family:inherit;resize:vertical;line-height:1.5;"
                                          @if($action['notesRequired'] ?? false) required @endif
                                          placeholder="Motif, retours aux auteurs, instructions..."></textarea>
                            @endif

                            @if($action['showLepis'] ?? false)
                                <label style="display:flex;align-items:flex-start;gap:0.5rem;font-size:0.875rem;margin-bottom:1rem;padding:0.75rem;background:#fef3c7;border-radius:6px;border:1px solid #fde68a;">
                                    <input type="checkbox" name="redirect_to_lepis" value="1" style="margin-top:2px;">
                                    <span>Recommander pour le bulletin <strong>Lepis</strong> (l'auteur ne sera pas notifié du rejet immédiatement, François évaluera la pertinence pour Lepis).</span>
                                </label>
                            @endif

                            <div style="display:flex;justify-content:flex-end;gap:0.5rem;">
                                <button type="button" @click="modal = null"
                                        style="padding:0.5rem 1rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;background:#fff;cursor:pointer;color:#374151;">
                                    Annuler
                                </button>
                                <button type="submit"
                                        style="{{ $btnStyles[$action['color']] }}padding:0.5rem 1.25rem;border-radius:6px;font-size:0.875rem;font-weight:500;border:none;cursor:pointer;">
                                    Confirmer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </template>
        @endforeach
    </div>
@endif
