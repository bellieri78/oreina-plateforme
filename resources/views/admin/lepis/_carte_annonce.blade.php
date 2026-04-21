@if ($bulletin->isInMembersPhase() || $bulletin->isPublic())
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Annonce adhérents</h3>
    </div>
    <div class="card-body">

        {{-- Bloc 1 : statut Brevo --}}
        <div style="margin-bottom:1.5rem;">
            @if ($bulletin->brevo_sync_failed)
                <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;margin-bottom:0.75rem;">
                    <svg fill="none" stroke="#dc2626" viewBox="0 0 24 24" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span style="color:#b91c1c;flex:1;">La synchronisation Brevo a échoué.</span>
                    <form method="POST" action="{{ route('admin.lepis.resync-brevo', $bulletin) }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline" style="border-color:#dc2626;color:#dc2626;">
                            Relancer la synchro
                        </button>
                    </form>
                </div>
            @elseif ($bulletin->brevo_synced_at)
                <div style="padding:0.75rem 1rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;">
                    <span style="color:#15803d;">
                        Liste Brevo : <strong>{{ $bulletin->brevo_list_name }}</strong>
                        — synchronisée le {{ $bulletin->brevo_synced_at->format('d/m/Y à H:i') }}
                        @if ($bulletin->brevo_list_url)
                            &nbsp;·&nbsp;
                            <a href="{{ $bulletin->brevo_list_url }}" target="_blank" style="color:#2C5F2D;">
                                Voir dans Brevo →
                            </a>
                        @endif
                    </span>
                </div>
            @else
                <div style="padding:0.75rem 1rem;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;">
                    <span style="color:#1e40af;">
                        Liste Brevo : synchronisation en cours…
                    </span>
                </div>
            @endif
        </div>

        {{-- Bloc 2 : formulaire template --}}
        <form method="POST" action="{{ route('admin.lepis.announcement', $bulletin) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="announcement_subject">Objet de l'email</label>
                <input type="text" name="announcement_subject" id="announcement_subject" class="form-input"
                    value="{{ old('announcement_subject', $bulletin->announcement_subject) }}">
                @error('announcement_subject')<p style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="announcement_body">Corps de l'annonce</label>
                <textarea name="announcement_body" id="announcement_body" class="form-input" rows="8"
                    style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:0.875rem;">{{ old('announcement_body', $bulletin->announcement_body) }}</textarea>
                <p style="font-size:0.8rem;color:#6b7280;margin-top:0.25rem;">
                    Markdown supporté. Le lien vers le bulletin sera inséré via le token <code>{{'{{'}}lien_bulletin{{'}}'}}</code>.
                </p>
                @error('announcement_body')<p style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:1rem;">
                <button type="submit" class="btn btn-primary">Enregistrer le template</button>
            </div>
        </form>

        {{-- Bloc 3 : aperçu rendu --}}
        @if ($bulletin->announcement_body)
            @php
                $rendered = app(\App\Services\LepisAnnouncementRenderer::class)->render($bulletin);
            @endphp
            @if ($rendered['body_html'])
                <hr style="margin:1.5rem 0;border:none;border-top:1px solid #e5e7eb;">
                <div style="margin-bottom:0.75rem;">
                    <p style="margin-bottom:0.5rem;font-size:0.875rem;">
                        <strong>Objet :</strong> {{ $rendered['subject'] }}
                    </p>
                </div>
                <div style="padding:1rem;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;font-size:0.9rem;line-height:1.6;">
                    {!! $rendered['body_html'] !!}
                </div>
                <textarea id="lepis-announcement-html" style="display:none;">{{ $rendered['body_html'] }}</textarea>
                <div style="display:flex;justify-content:flex-end;margin-top:0.75rem;">
                    <button type="button" class="btn btn-outline btn-sm"
                        onclick="navigator.clipboard.writeText(document.getElementById('lepis-announcement-html').value); this.textContent='Copié ✓';">
                        Copier le HTML (pour Brevo)
                    </button>
                </div>
            @endif
        @endif

    </div>
</div>
@endif
