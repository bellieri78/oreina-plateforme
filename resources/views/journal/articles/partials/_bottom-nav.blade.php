{{-- Bottom Navigation --}}
<div class="bottom-nav">
    <div class="container">
        <div class="nav-inner">
            <a href="{{ route('journal.articles.index') }}" class="btn-action">
                <i data-lucide="chevron-left" style="width:16px;height:16px"></i>
                Retour aux articles
            </a>
            <div class="nav-actions">
                @if($submission->pdf_file)
                <a href="{{ route('journal.articles.pdf', $submission) }}" target="_blank" class="btn-action">
                    <i data-lucide="download" style="width:16px;height:16px"></i>
                    Télécharger PDF
                </a>
                @endif
                <button onclick="window.print()" class="btn-action">
                    <i data-lucide="printer" style="width:16px;height:16px"></i>
                    Imprimer
                </button>
            </div>
        </div>
    </div>
</div>
