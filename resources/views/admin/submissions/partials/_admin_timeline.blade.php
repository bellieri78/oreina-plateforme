@php
    $adminLabels = [
        'status_changed'          => 'Changement de statut',
        'editor_assigned'         => 'Éditeur assigné',
        'editor_taken'            => 'Article pris en charge',
        'editor_revoked'          => 'Éditeur retiré',
        'layout_editor_assigned'  => 'Maquettiste assigné',
        'layout_editor_revoked'   => 'Maquettiste retiré',
        'reviewer_invited'        => 'Relecteur invité',
    ];
@endphp

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">Journal des actions</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($submission->transitions->isEmpty())
            <div style="padding: 1.5rem; text-align: center; color: #9ca3af;">
                Aucune action enregistrée.
            </div>
        @else
            <div style="max-height: 400px; overflow-y: auto;">
                <table class="data-table" style="margin: 0;">
                    <thead>
                        <tr>
                            <th style="width: 130px;">Date</th>
                            <th>Action</th>
                            <th>Détail</th>
                            <th>Par</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submission->transitions as $t)
                            <tr>
                                <td style="white-space: nowrap; font-size: 0.8rem;">
                                    {{ $t->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    <span style="font-weight: 500;">
                                        {{ $adminLabels[$t->action] ?? $t->action }}
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem;">
                                    @if($t->from_status && $t->to_status)
                                        <code style="background:#f3f4f6;padding:0.125rem 0.375rem;border-radius:0.25rem;font-size:0.8rem;">
                                            {{ $t->from_status }} → {{ $t->to_status }}
                                        </code>
                                    @endif
                                    @if($t->target)
                                        <span style="color: #6b7280;">→ {{ $t->target->name }}</span>
                                    @endif
                                    @if($t->notes)
                                        <div style="margin-top: 0.25rem; font-size: 0.8rem; color: #6b7280; font-style: italic;">
                                            {{ Str::limit($t->notes, 100) }}
                                        </div>
                                    @endif
                                </td>
                                <td style="font-size: 0.85rem;">
                                    {{ $t->actor?->name ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
