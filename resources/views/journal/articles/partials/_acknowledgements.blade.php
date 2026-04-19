{{-- Acknowledgements --}}
@if($submission->acknowledgements)
<section class="article-section">
    <h2>Remerciements</h2>
    <p>{{ $submission->acknowledgements }}</p>
</section>
@endif
