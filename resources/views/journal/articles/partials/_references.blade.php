{{-- References --}}
@if($submission->references && is_array($submission->references) && count($submission->references) > 0)
<div class="references-card">
    <h2>Références bibliographiques</h2>
    <div class="ref-list">
        @foreach($submission->references as $reference)
        <p>{!! preg_replace('/\*(.+?)\*/', '<em>$1</em>', e($reference)) !!}</p>
        @endforeach
    </div>
</div>
@endif
