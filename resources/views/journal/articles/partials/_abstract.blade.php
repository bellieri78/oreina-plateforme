{{-- Abstract / Summary --}}
@php
    $displayAbstract = $submission->display_abstract ?? $submission->abstract;
    $displaySummary = $submission->display_summary ?? null;
@endphp
@if($displayAbstract || $displaySummary)
<section>
    <div class="article-abstract">
        @if($displayAbstract)
        <h2>Résumé</h2>
        <p>{!! strip_tags($displayAbstract, '<strong><em><sub><sup>') !!}</p>
        @endif

        @if($displaySummary)
        <h2 style="margin-top:20px;">Summary</h2>
        <p style="font-style:italic;">{!! strip_tags($displaySummary, '<strong><em><sub><sup>') !!}</p>
        @endif

        @if($submission->keywords && is_array($submission->keywords) && count($submission->keywords) > 0)
        <div>
            <p class="kw-label">Mots-clés :</p>
            <div class="keywords">
                @foreach($submission->keywords as $keyword)
                <span class="kw">{{ $keyword }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endif
