{{-- resources/views/journal/articles/partials/_sidebar-actions.blade.php --}}
<div class="sidebar-section">
    <h3 class="sidebar-title">Actions</h3>
    <div class="sidebar-actions">
        @if($submission->pdf_file)
        <a href="{{ route('journal.articles.pdf', $submission) }}" target="_blank" class="btn-action primary">
            <i data-lucide="download" style="width:16px;height:16px"></i>
            Télécharger PDF
        </a>
        @endif
        <button type="button" onclick="document.getElementById('citation-block').scrollIntoView({behavior:'smooth'})" class="btn-action">
            <i data-lucide="quote" style="width:16px;height:16px"></i>
            Citer
        </button>
        <button type="button" x-data x-on:click="$dispatch('open-share')" class="btn-action">
            <i data-lucide="share-2" style="width:16px;height:16px"></i>
            Partager
        </button>
    </div>
</div>
