<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Fichier PDF</h3>
    </div>
    <div class="card-body">
        @if ($bulletin->pdf_path)
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;padding:0.75rem 1rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;">
                <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <div>
                    <span style="color:#15803d;font-weight:500;">PDF déposé le {{ $bulletin->updated_at->format('d/m/Y à H:i') }}</span>
                    <br>
                    <a href="{{ Storage::url($bulletin->pdf_path) }}" target="_blank" style="color:#2C5F2D;font-size:0.875rem;">
                        Voir le PDF →
                    </a>
                </div>
            </div>
        @else
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;padding:0.75rem 1rem;background:#fffbeb;border:1px solid #fde68a;border-radius:6px;">
                <svg fill="none" stroke="#d97706" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span style="color:#92400e;">Aucun PDF déposé — requis avant publication.</span>
            </div>
        @endif

        <form action="{{ route('admin.lepis.update', $bulletin) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            {{-- Champs requis cachés pour satisfaire la validation --}}
            <input type="hidden" name="title" value="{{ $bulletin->title }}">
            <input type="hidden" name="issue_number" value="{{ $bulletin->issue_number }}">
            <input type="hidden" name="quarter" value="{{ $bulletin->quarter }}">
            <input type="hidden" name="year" value="{{ $bulletin->year }}">

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="pdf-upload">
                    {{ $bulletin->pdf_path ? 'Remplacer le PDF' : 'Téléverser un PDF' }}
                </label>
                <div style="display:flex;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
                    <div style="flex:1;min-width:200px;">
                        <input type="file" name="pdf" id="pdf-upload" class="form-input" accept=".pdf">
                        @error('pdf')<p style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary" style="white-space:nowrap;">Téléverser</button>
                </div>
            </div>
        </form>
    </div>
</div>
