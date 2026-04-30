@php
    $paperCount = $bulletin->paperRecipientsCount();
    $digitalCount = $bulletin->digitalRecipientsCount();
    $total = $paperCount + $digitalCount;
    $lastSnapshotAt = $bulletin->recipients()->max('included_at');
@endphp

<div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1.25rem; margin-bottom: 1rem;">
    <h3 style="margin-top: 0; margin-bottom: 1rem; font-weight: 600;">Diffusion</h3>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 1rem;">
        <tbody>
            <tr>
                <td style="padding: 0.5rem 0;">Papier</td>
                <td style="padding: 0.5rem 0; text-align: right; font-weight: 600;">{{ $paperCount }}</td>
                <td style="padding: 0.5rem 0; text-align: right;">
                    @if($paperCount > 0)
                        <a href="{{ route('admin.lepis.recipients.export', ['bulletin' => $bulletin->id, 'format' => 'paper']) }}"
                           style="background: #2C5F2D; color: white; padding: 0.25rem 0.75rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.875rem;">
                            Exporter CSV
                        </a>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 0;">Numerique</td>
                <td style="padding: 0.5rem 0; text-align: right; font-weight: 600;">{{ $digitalCount }}</td>
                <td style="padding: 0.5rem 0; text-align: right; color: #6b7280; font-size: 0.875rem;">
                    @if($bulletin->brevo_list_id)
                        Liste Brevo #{{ $bulletin->brevo_list_id }}
                    @endif
                </td>
            </tr>
            <tr style="border-top: 1px solid #e5e7eb;">
                <td style="padding: 0.5rem 0; font-weight: 600;">Total</td>
                <td style="padding: 0.5rem 0; text-align: right; font-weight: 600;">{{ $total }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    @if($lastSnapshotAt)
        <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.75rem 0;">
            Dernier snapshot : {{ \Carbon\Carbon::parse($lastSnapshotAt)->locale('fr')->isoFormat('LLL') }}
        </p>
    @endif

    <form method="POST" action="{{ route('admin.lepis.recipients.snapshot', ['bulletin' => $bulletin->id]) }}"
          onsubmit="return confirm('Recalculer le snapshot des destinataires ? Met a jour la liste papier et numerique selon les adhesions actuelles.');"
          style="display: inline;">
        @csrf
        <button type="submit" style="background: #f3f4f6; color: #374151; padding: 0.375rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.25rem; cursor: pointer;">
            Recalculer le snapshot
        </button>
    </form>
</div>
