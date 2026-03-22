@extends('layouts.journal')

@section('title', $submission->title)
@section('meta_description', Str::limit($submission->abstract, 160))

@section('content')
    <div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto">
            {{-- Breadcrumb --}}
            <nav class="flex items-center text-sm text-slate-500 mb-8">
                <a href="{{ route('journal.articles.index') }}" class="hover:text-oreina-turquoise transition">Articles</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
                <span class="text-slate-900 truncate max-w-xs">{{ $submission->title }}</span>
            </nav>

            {{-- Article Header --}}
            <article class="bg-white rounded-3xl border border-oreina-beige/50 overflow-hidden">
                <div class="p-6 sm:p-8 lg:p-12">
                    {{-- Tags & Date --}}
                    <div class="flex flex-wrap items-center gap-3 mb-6">
                        <span class="px-3 py-1.5 text-xs font-bold rounded-lg bg-oreina-turquoise/10 text-oreina-teal">
                            Article scientifique
                        </span>
                        @if($submission->journalIssue)
                        <a href="{{ route('journal.issues.show', $submission->journalIssue) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                            {{ $submission->journalIssue->full_reference }}
                        </a>
                        @endif
                        <div class="flex items-center gap-2 text-slate-500 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <span>{{ $submission->published_at?->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-oreina-dark mb-6 leading-tight">
                        {{ $submission->title }}
                    </h1>

                    {{-- Authors --}}
                    <div class="mb-8 pb-8 border-b border-oreina-beige/50">
                        <div class="flex flex-wrap items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-oreina-teal/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-oreina-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-slate-900">
                                    {{ $submission->author?->name ?? 'Auteur inconnu' }}
                                    @if($submission->co_authors && is_array($submission->co_authors))
                                        @foreach($submission->co_authors as $coAuthor)
                                            @if(!empty($coAuthor['name']))
                                                , {{ $coAuthor['name'] }}
                                            @endif
                                        @endforeach
                                    @endif
                                </p>
                                @if($submission->author_affiliations && is_array($submission->author_affiliations) && count($submission->author_affiliations) > 0)
                                    <div class="text-sm text-slate-500 mt-1">
                                        @foreach($submission->author_affiliations as $affiliation)
                                            <p>{{ $affiliation }}</p>
                                        @endforeach
                                    </div>
                                @elseif($submission->author?->affiliation)
                                    <p class="text-sm text-slate-500">{{ $submission->author->affiliation }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Abstract --}}
                    @if($submission->abstract)
                    <div class="mb-8">
                        <h2 class="text-lg font-bold text-oreina-dark mb-3">Résumé</h2>
                        <div class="bg-slate-50 rounded-2xl p-6 text-slate-700 leading-relaxed">
                            {{ $submission->abstract }}
                        </div>
                    </div>
                    @endif

                    {{-- Keywords --}}
                    @if($submission->keywords && is_array($submission->keywords) && count($submission->keywords) > 0)
                    <div class="mb-8">
                        <h2 class="text-lg font-bold text-oreina-dark mb-3">Mots-clés</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($submission->keywords as $keyword)
                            <span class="px-3 py-1.5 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg">{{ $keyword }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Full Content --}}
                    @if($submission->content_blocks && is_array($submission->content_blocks) && count($submission->content_blocks) > 0)
                    <div class="prose prose-slate max-w-none mb-8">
                        @foreach($submission->content_blocks as $blockIndex => $block)
                            @php $blockType = $block['type'] ?? 'paragraph'; @endphp

                            @if($blockType === 'heading')
                                @if(($block['level'] ?? 'h2') === 'h2')
                                    <h2>{{ $block['content'] ?? '' }}</h2>
                                @else
                                    <h3>{{ $block['content'] ?? '' }}</h3>
                                @endif

                            @elseif($blockType === 'paragraph')
                                <p>{!! $block['content'] ?? '' !!}</p>

                            @elseif($blockType === 'image')
                                @php
                                    $imgSrc = $block['url'] ?? $block['src'] ?? '';
                                    $imgCaption = $block['caption'] ?? '';
                                @endphp
                                @if($imgSrc)
                                    <figure class="my-6">
                                        <img src="{{ $imgSrc }}" alt="{{ $imgCaption }}" class="rounded-lg w-full">
                                        @if($imgCaption)
                                            <figcaption class="mt-2 text-sm text-slate-600 text-center">
                                                <strong>Figure {{ $blockIndex + 1 }}.</strong> {{ $imgCaption }}
                                            </figcaption>
                                        @endif
                                    </figure>
                                @endif

                            @elseif($blockType === 'table')
                                @php $tableData = $block['data'] ?? []; @endphp
                                @if(count($tableData) > 0)
                                    <div class="my-6 overflow-x-auto">
                                        @if(!empty($block['caption']))
                                            <p class="text-sm font-semibold text-slate-700 mb-2">
                                                <strong>Tableau {{ $blockIndex + 1 }}.</strong> {{ $block['caption'] }}
                                            </p>
                                        @endif
                                        <table class="min-w-full divide-y divide-slate-200">
                                            <tbody class="divide-y divide-slate-200">
                                                @foreach($tableData as $rowIndex => $row)
                                                    <tr class="{{ $rowIndex === 0 ? 'bg-slate-50' : '' }}">
                                                        @foreach($row as $cell)
                                                            @if($rowIndex === 0)
                                                                <th class="px-4 py-2 text-left text-sm font-semibold text-slate-900">{{ $cell }}</th>
                                                            @else
                                                                <td class="px-4 py-2 text-sm text-slate-700">{{ $cell }}</td>
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                            @elseif($blockType === 'list')
                                @php
                                    $listItems = $block['items'] ?? [];
                                    $isOrdered = $block['ordered'] ?? false;
                                @endphp
                                @if(count($listItems) > 0)
                                    @if($isOrdered)
                                        <ol class="list-decimal pl-6 my-4 space-y-1">
                                            @foreach($listItems as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ol>
                                    @else
                                        <ul class="list-disc pl-6 my-4 space-y-1">
                                            @foreach($listItems as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                @endif

                            @elseif($blockType === 'quote')
                                <blockquote class="my-6 pl-4 border-l-4 border-oreina-turquoise bg-slate-50 py-4 pr-4 italic">
                                    <p class="text-slate-700">{{ $block['content'] ?? '' }}</p>
                                    @if(!empty($block['source']))
                                        <cite class="block mt-2 text-sm text-slate-500 not-italic">— {{ $block['source'] }}</cite>
                                    @endif
                                </blockquote>
                            @endif
                        @endforeach
                    </div>
                    @elseif($submission->content_html)
                    <div class="prose prose-slate max-w-none mb-8">
                        {!! $submission->content_html !!}
                    </div>
                    @endif

                    {{-- Acknowledgements --}}
                    @if($submission->acknowledgements)
                    <div class="mb-8">
                        <h2 class="text-lg font-bold text-oreina-dark mb-3">Remerciements</h2>
                        <div class="bg-slate-50 rounded-2xl p-6 text-slate-700 leading-relaxed italic">
                            {{ $submission->acknowledgements }}
                        </div>
                    </div>
                    @endif

                    {{-- References --}}
                    @if($submission->references && is_array($submission->references) && count($submission->references) > 0)
                    <div class="mb-8">
                        <h2 class="text-lg font-bold text-oreina-dark mb-3">Références</h2>
                        <div class="bg-slate-50 rounded-2xl p-6">
                            <ul class="space-y-2 text-sm text-slate-700">
                                @foreach($submission->references as $reference)
                                <li class="pl-4 -indent-4">{{ $reference }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    {{-- Metadata & Download --}}
                    <div class="bg-slate-50 rounded-2xl p-6 mt-8">
                        <h2 class="text-lg font-bold text-oreina-dark mb-4">Informations</h2>
                        <div class="grid sm:grid-cols-2 gap-4 text-sm">
                            @if($submission->doi)
                            <div>
                                <span class="text-slate-500">DOI</span>
                                <p class="font-mono text-slate-900 break-all">{{ $submission->doi }}</p>
                            </div>
                            @endif
                            @if($submission->journalIssue)
                            <div>
                                <span class="text-slate-500">Numéro</span>
                                <p class="text-slate-900">{{ $submission->journalIssue->full_reference }}</p>
                            </div>
                            @endif
                            @if($submission->start_page && $submission->end_page)
                            <div>
                                <span class="text-slate-500">Pages</span>
                                <p class="text-slate-900">{{ $submission->start_page }} - {{ $submission->end_page }}</p>
                            </div>
                            @endif
                            @if($submission->received_at ?? $submission->submitted_at)
                            <div>
                                <span class="text-slate-500">Reçu le</span>
                                <p class="text-slate-900">{{ ($submission->received_at ?? $submission->submitted_at)->translatedFormat('d F Y') }}</p>
                            </div>
                            @endif
                            @if($submission->accepted_at ?? $submission->decision_at)
                            <div>
                                <span class="text-slate-500">Accepté le</span>
                                <p class="text-slate-900">{{ ($submission->accepted_at ?? $submission->decision_at)->translatedFormat('d F Y') }}</p>
                            </div>
                            @endif
                            @if($submission->published_at)
                            <div>
                                <span class="text-slate-500">Publié le</span>
                                <p class="text-slate-900">{{ $submission->published_at->translatedFormat('d F Y') }}</p>
                            </div>
                            @endif
                        </div>

                        @if($submission->pdf_file)
                        <div class="mt-6 pt-6 border-t border-slate-200">
                            <a href="{{ Storage::url($submission->pdf_file) }}" class="btn-turquoise inline-flex items-center gap-2" target="_blank">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Télécharger le PDF
                            </a>
                        </div>
                        @endif
                    </div>

                    {{-- Citation --}}
                    <div class="mt-8 p-6 bg-oreina-teal/5 rounded-2xl border border-oreina-teal/20">
                        <h2 class="text-lg font-bold text-oreina-dark mb-3">Comment citer cet article</h2>
                        <p class="text-sm text-slate-700 font-mono leading-relaxed">
                            {{ $submission->author?->name ?? 'Auteur' }} ({{ $submission->published_at?->year ?? date('Y') }}).
                            {{ $submission->title }}.
                            <em>OREINA</em>@if($submission->journalIssue), {{ $submission->journalIssue->volume_number }}({{ $submission->journalIssue->issue_number }})@endif@if($submission->start_page && $submission->end_page), {{ $submission->start_page }}-{{ $submission->end_page }}@endif.
                            @if($submission->doi) https://doi.org/{{ $submission->doi }}@endif
                        </p>
                    </div>
                </div>
            </article>

            {{-- Related Articles --}}
            @if($relatedArticles->count() > 0)
            <div class="mt-12">
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

            {{-- Back link --}}
            <div class="mt-8">
                <a href="{{ route('journal.articles.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-oreina-turquoise transition font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                    Retour aux articles
                </a>
            </div>
        </div>
    </div>
@endsection
