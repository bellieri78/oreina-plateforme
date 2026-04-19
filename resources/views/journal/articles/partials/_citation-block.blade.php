{{-- Citation Block --}}
<div id="citation-block" class="citation-block">
    <div class="citation-header">
        <div class="citation-icon">
            <i data-lucide="book-open" style="width:24px;height:24px;color:white"></i>
        </div>
        <div>
            <h3>Comment citer cet article</h3>
        </div>
    </div>
    @php
        $harvardCitation = app(\App\Services\CitationExportService::class)->toHarvard($submission);
    @endphp
    <div class="citation-text">
        {!! e($harvardCitation) !!}
    </div>
    <div class="citation-actions">
        <a href="{{ route('journal.articles.cite', [$submission, 'bibtex']) }}" class="btn-action primary" style="height:38px;font-size:13px" download>
            BibTeX
        </a>
        <a href="{{ route('journal.articles.cite', [$submission, 'ris']) }}" class="btn-action" style="height:38px;font-size:13px" download>
            RIS
        </a>
        <button onclick="copyCitation()" class="btn-action" style="height:38px;font-size:13px;color:var(--accent)">Exporter la citation</button>
    </div>
</div>
