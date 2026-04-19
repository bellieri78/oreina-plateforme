{{-- Related Articles --}}
@if($relatedArticles->count() > 0)
<div class="related-section">
    <h2>Articles du même numéro</h2>
    <div class="related-grid">
        @foreach($relatedArticles as $related)
        <article class="related-card">
            <h3>
                <a href="{{ route('journal.articles.show', $related) }}">
                    {!! strip_tags($related->title, '<strong><em><sub><sup>') !!}
                </a>
            </h3>
            <p class="related-author">{{ $related->display_authors ?? $related->author?->name }}</p>
            @if($related->start_page && $related->end_page)
            <p class="related-pages">pp. {{ $related->start_page }}-{{ $related->end_page }}</p>
            @endif
        </article>
        @endforeach
    </div>
</div>
@endif
