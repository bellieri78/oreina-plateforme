{{-- Header --}}
<div class="article-header">
    {{-- Category & Date --}}
    <div class="tag-row">
        <span class="tag">Article scientifique</span>
        @if($submission->journalIssue)
        <a href="{{ route('journal.issues.show', $submission->journalIssue) }}" class="tag-issue">
            Vol. {{ $submission->journalIssue->volume_number }} N°{{ $submission->journalIssue->issue_number }}
        </a>
        @endif
        <div class="date-info">
            <i data-lucide="calendar" style="width:16px;height:16px"></i>
            <span>{{ $submission->published_at?->translatedFormat('d F Y') ?? 'Non publié' }}</span>
        </div>
    </div>

    {{-- Title --}}
    <h1>{{ $submission->title }}</h1>

    {{-- Authors & Affiliations --}}
    <div style="margin-bottom:28px">
        <p class="authors">
            @if($submission->display_authors)
                {{ $submission->display_authors }}
            @else
                {{ $submission->author?->name ?? 'Auteur inconnu' }}@if($submission->co_authors && is_array($submission->co_authors))@foreach($submission->co_authors as $coAuthor)@if(!empty($coAuthor['name'])), {{ $coAuthor['name'] }}@endif @endforeach @endif
            @endif
        </p>
        @if($submission->author_affiliations && is_array($submission->author_affiliations) && count($submission->author_affiliations) > 0)
        <div class="affiliations">
            @foreach($submission->author_affiliations as $index => $affiliation)
            <p><sup>{{ $index + 1 }}</sup> {{ $affiliation }}</p>
            @endforeach
        </div>
        @endif
    </div>
</div>
