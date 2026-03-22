@extends('layouts.journal')

@section('title', $submission->title)
@section('meta_description', Str::limit($submission->abstract, 160))

@section('content')
    <article class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        {{-- Header --}}
        <div class="mb-8">
            {{-- Category & Date --}}
            <div class="flex flex-wrap items-center gap-3 mb-6">
                <span class="px-3 py-1.5 bg-teal-100 text-teal-700 text-sm font-bold rounded-lg">
                    Article scientifique
                </span>
                @if($submission->journalIssue)
                <a href="{{ route('journal.issues.show', $submission->journalIssue) }}" class="px-3 py-1.5 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-200 transition">
                    Vol. {{ $submission->journalIssue->volume_number }} N°{{ $submission->journalIssue->issue_number }}
                </a>
                @endif
                <div class="flex items-center gap-2 text-slate-500 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span>{{ $submission->published_at?->translatedFormat('d F Y') ?? 'Non publié' }}</span>
                </div>
            </div>

            {{-- Title --}}
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-slate-900 mb-8 leading-tight">
                {{ $submission->title }}
            </h1>

            {{-- Authors & Affiliations --}}
            <div class="space-y-4 mb-8">
                <div>
                    <p class="text-lg font-semibold text-slate-900">
                        {{ $submission->author?->name ?? 'Auteur inconnu' }}@if($submission->co_authors && is_array($submission->co_authors))@foreach($submission->co_authors as $coAuthor)@if(!empty($coAuthor['name'])), {{ $coAuthor['name'] }}@endif @endforeach @endif
                    </p>
                </div>
                @if($submission->author_affiliations && is_array($submission->author_affiliations) && count($submission->author_affiliations) > 0)
                <div class="text-sm text-slate-600 space-y-1 pl-4 border-l-2 border-slate-200">
                    @foreach($submission->author_affiliations as $index => $affiliation)
                    <p><sup class="text-teal-600 font-semibold">{{ $index + 1 }}</sup> {{ $affiliation }}</p>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-3 mb-8">
                @if($submission->pdf_file)
                <a href="{{ Storage::url($submission->pdf_file) }}" target="_blank" class="flex items-center gap-2 px-5 py-2.5 bg-teal-700 text-white rounded-lg hover:bg-teal-800 transition font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
                    </svg>
                    Télécharger PDF
                </a>
                @endif
                <button onclick="document.getElementById('citation-block').scrollIntoView({ behavior: 'smooth' })" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/>
                    </svg>
                    Citer l'article
                </button>
                <button onclick="navigator.share ? navigator.share({title: '{{ $submission->title }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('Lien copié !'))" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/>
                    </svg>
                    Partager
                </button>
            </div>

            {{-- Metadata Box --}}
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 mb-12">
                <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6 text-sm">
                    @if($submission->doi)
                    <div>
                        <p class="text-slate-500 font-semibold mb-1">DOI</p>
                        <p class="font-mono text-slate-900 break-all">{{ $submission->doi }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-slate-500 font-semibold mb-1">Date de publication</p>
                        <p class="text-slate-900">{{ $submission->published_at?->translatedFormat('d F Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 font-semibold mb-1">Type d'article</p>
                        <p class="text-slate-900">Article de recherche</p>
                    </div>
                    <div>
                        <p class="text-slate-500 font-semibold mb-1">Licence</p>
                        <p class="text-slate-900">CC BY 4.0</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="prose prose-slate prose-lg max-w-none">
            {{-- Abstract --}}
            @if($submission->abstract)
            <section class="mb-12">
                <div class="bg-amber-50 border-l-4 border-amber-500 rounded-r-lg p-6 mb-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-4 mt-0">Résumé</h2>
                    <p class="text-slate-700 leading-relaxed mb-4">
                        {{ $submission->abstract }}
                    </p>
                    @if($submission->keywords && is_array($submission->keywords) && count($submission->keywords) > 0)
                    <div>
                        <p class="text-sm font-semibold text-slate-700 mb-2">Mots-clés :</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($submission->keywords as $keyword)
                            <span class="px-3 py-1 bg-white border border-amber-200 text-slate-700 text-sm font-medium rounded-lg">{{ $keyword }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </section>
            @endif

            {{-- Main Content --}}
            @if($submission->content_blocks && is_array($submission->content_blocks) && count($submission->content_blocks) > 0)
            <section class="mb-12">
                @php $sectionNumber = 0; @endphp
                @foreach($submission->content_blocks as $blockIndex => $block)
                    @php $blockType = $block['type'] ?? 'paragraph'; @endphp

                    @if($blockType === 'heading')
                        @php $sectionNumber++; @endphp
                        @if(($block['level'] ?? 'h2') === 'h2')
                            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-6 pb-3 border-b-2 border-teal-600 mt-12 first:mt-0">{{ $sectionNumber }}. {{ $block['content'] ?? '' }}</h2>
                        @else
                            <h3 class="text-xl sm:text-2xl font-semibold text-slate-900 mb-4 mt-8">{{ $sectionNumber }}.{{ $loop->iteration }}. {{ $block['content'] ?? '' }}</h3>
                        @endif

                    @elseif($blockType === 'paragraph')
                        <p class="text-slate-700 leading-relaxed mb-4">{!! $block['content'] ?? '' !!}</p>

                    @elseif($blockType === 'image')
                        @php
                            $imgSrc = $block['url'] ?? $block['src'] ?? '';
                            $imgCaption = $block['caption'] ?? '';
                        @endphp
                        @if($imgSrc)
                        <figure class="my-8">
                            <img src="{{ $imgSrc }}" alt="{{ $imgCaption }}" class="rounded-lg w-full shadow-md">
                            @if($imgCaption)
                            <figcaption class="mt-3 text-sm text-slate-600 text-center italic">
                                <strong>Figure {{ $blockIndex + 1 }}.</strong> {{ $imgCaption }}
                            </figcaption>
                            @endif
                        </figure>
                        @endif

                    @elseif($blockType === 'table')
                        @php $tableData = $block['data'] ?? []; @endphp
                        @if(count($tableData) > 0)
                        <div class="my-8 bg-slate-50 rounded-lg p-6 border border-slate-200">
                            @if(!empty($block['caption']))
                            <p class="text-sm font-semibold text-slate-600 mb-4">
                                <strong>Tableau {{ $blockIndex + 1 }}.</strong> {{ $block['caption'] }}
                            </p>
                            @endif
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="border-b-2 border-slate-300">
                                        @if(isset($tableData[0]))
                                        <tr class="text-left">
                                            @foreach($tableData[0] as $cell)
                                            <th class="py-2 pr-4 font-semibold text-slate-900">{{ $cell }}</th>
                                            @endforeach
                                        </tr>
                                        @endif
                                    </thead>
                                    <tbody class="text-slate-700">
                                        @foreach(array_slice($tableData, 1) as $row)
                                        <tr class="border-b border-slate-200">
                                            @foreach($row as $cell)
                                            <td class="py-2 pr-4">{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                    @elseif($blockType === 'list')
                        @php
                            $listItems = $block['items'] ?? [];
                            $isOrdered = $block['ordered'] ?? false;
                        @endphp
                        @if(count($listItems) > 0)
                        <ul class="list-none space-y-2 mb-6 pl-4">
                            @foreach($listItems as $item)
                            <li class="text-slate-700 leading-relaxed flex gap-3">
                                <span class="text-teal-600 font-bold">•</span>
                                <span>{{ $item }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @endif

                    @elseif($blockType === 'quote')
                        <blockquote class="my-6 pl-6 border-l-4 border-teal-500 bg-teal-50 py-4 pr-6 rounded-r-lg">
                            <p class="text-slate-700 italic">{{ $block['content'] ?? '' }}</p>
                            @if(!empty($block['source']))
                            <cite class="block mt-2 text-sm text-slate-500 not-italic">— {{ $block['source'] }}</cite>
                            @endif
                        </blockquote>
                    @endif
                @endforeach
            </section>
            @elseif($submission->content_html)
            <section class="mb-12">
                {!! $submission->content_html !!}
            </section>
            @endif

            {{-- Acknowledgements --}}
            @if($submission->acknowledgements)
            <section class="mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-6 pb-3 border-b-2 border-slate-300">Remerciements</h2>
                <p class="text-slate-700 leading-relaxed">
                    {{ $submission->acknowledgements }}
                </p>
            </section>
            @endif

            {{-- References --}}
            @if($submission->references && is_array($submission->references) && count($submission->references) > 0)
            <section class="mb-12 bg-slate-50 rounded-xl p-8 border border-slate-200">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-6 pb-3 border-b-2 border-slate-300 mt-0">Références bibliographiques</h2>
                <div class="space-y-4 text-sm text-slate-700">
                    @foreach($submission->references as $reference)
                    <p class="leading-relaxed">{{ $reference }}</p>
                    @endforeach
                </div>
            </section>
            @endif
        </div>

        {{-- Citation Block --}}
        <div id="citation-block" class="border-t-2 border-slate-200 pt-8 mt-12">
            <div class="bg-gradient-to-br from-teal-50 to-emerald-50 rounded-xl p-6 sm:p-8 border border-teal-200">
                <div class="flex flex-col sm:flex-row items-start gap-4">
                    <div class="p-3 bg-teal-600 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Comment citer cet article</h3>
                        <div class="bg-white rounded-lg p-4 border border-teal-200 mb-4">
                            <p class="text-sm text-slate-700 font-mono leading-relaxed">
                                {{ $submission->author?->name ?? 'Auteur' }}@if($submission->co_authors && is_array($submission->co_authors) && count($submission->co_authors) > 0)@foreach($submission->co_authors as $index => $coAuthor)@if(!empty($coAuthor['name'])), {{ $coAuthor['name'] }}@endif @endforeach @endif ({{ $submission->published_at?->year ?? date('Y') }}). {{ $submission->title }}. <em>Revue scientifique OREINA</em>@if($submission->journalIssue), <strong>{{ $submission->journalIssue->volume_number }}</strong>({{ $submission->journalIssue->issue_number }})@endif @if($submission->start_page && $submission->end_page), {{ $submission->start_page }}-{{ $submission->end_page }}@endif. @if($submission->doi)https://doi.org/{{ $submission->doi }}@endif
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button onclick="copyBibtex()" class="px-4 py-2 bg-teal-700 text-white rounded-lg hover:bg-teal-800 transition font-semibold text-sm">Format BibTeX</button>
                            <button onclick="copyCitation()" class="px-4 py-2 bg-white border border-teal-300 text-teal-700 rounded-lg hover:bg-teal-50 transition font-semibold text-sm">Copier la citation</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Articles --}}
        @if($relatedArticles->count() > 0)
        <div class="mt-12 pt-8 border-t border-slate-200">
            <h2 class="text-xl font-bold text-oreina-dark mb-6">Articles du même numéro</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($relatedArticles as $related)
                <article class="bg-white rounded-2xl p-6 border border-oreina-beige/50 hover:shadow-lg transition group">
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-turquoise transition line-clamp-2">
                        <a href="{{ route('journal.articles.show', $related) }}">
                            {{ $related->title }}
                        </a>
                    </h3>
                    <p class="text-sm text-slate-500 mb-3">{{ $related->author?->name }}</p>
                    @if($related->start_page && $related->end_page)
                    <p class="text-xs text-slate-400">pp. {{ $related->start_page }}-{{ $related->end_page }}</p>
                    @endif
                </article>
                @endforeach
            </div>
        </div>
        @endif
    </article>

    {{-- Bottom Navigation --}}
    <div class="border-t border-slate-200 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('journal.articles.index') }}" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 transition font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                    Retour aux articles
                </a>
                <div class="flex gap-3">
                    @if($submission->pdf_file)
                    <a href="{{ Storage::url($submission->pdf_file) }}" target="_blank" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 transition font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
                        </svg>
                        Télécharger PDF
                    </a>
                    @endif
                    <button onclick="window.print()" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 transition font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/>
                        </svg>
                        Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    @php
        $bibtexYear = $submission->published_at?->year ?? date('Y');
        $bibtexAuthor = $submission->author?->name ?? 'Auteur';
        $bibtexTitle = str_replace(['"', '\\'], ['\"', '\\\\'], $submission->title);
        $bibtexLines = [
            "@article{oreina{$bibtexYear},",
            "  author = {{$bibtexAuthor}},",
            "  title = {{$bibtexTitle}},",
            "  journal = {Revue scientifique OREINA},",
            "  year = {{$bibtexYear}},",
        ];
        if ($submission->journalIssue) {
            $bibtexLines[] = "  volume = {{$submission->journalIssue->volume_number}},";
            $bibtexLines[] = "  number = {{$submission->journalIssue->issue_number}},";
        }
        if ($submission->start_page && $submission->end_page) {
            $bibtexLines[] = "  pages = {{$submission->start_page}--{$submission->end_page}},";
        }
        if ($submission->doi) {
            $bibtexLines[] = "  doi = {{$submission->doi}}";
        }
        $bibtexLines[] = "}";
        $bibtexString = implode("\n", $bibtexLines);
    @endphp

    @push('scripts')
    <script>
        function copyCitation() {
            const citation = document.querySelector('#citation-block .font-mono').innerText;
            navigator.clipboard.writeText(citation).then(() => {
                alert('Citation copiée !');
            });
        }

        function copyBibtex() {
            const bibtex = @json($bibtexString);
            navigator.clipboard.writeText(bibtex).then(() => {
                alert('BibTeX copié !');
            });
        }
    </script>
    @endpush
@endsection
