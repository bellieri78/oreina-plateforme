@extends('layouts.journal')

@section('title', $submission->title)
@section('meta_description', Str::limit($submission->abstract, 160))

@push('styles')
<style>
    /* === Article Page — DS V4 === */
    .article-header {
        padding-bottom: 32px;
        margin-bottom: 32px;
    }

    .article-header .tag-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-bottom: 28px;
    }

    .article-header .tag {
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 700;
        border-radius: var(--radius-md);
        background: var(--accent-surface);
        color: var(--accent);
    }

    .article-header .tag-issue {
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        border-radius: var(--radius-md);
        background: var(--surface-sage);
        color: var(--forest);
        text-decoration: none;
        transition: background .2s;
    }
    .article-header .tag-issue:hover {
        background: var(--sage);
        color: white;
    }

    .article-header .date-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--muted);
    }

    .article-header h1 {
        font-size: clamp(1.75rem, 4vw, 2.75rem);
        font-weight: 800;
        color: var(--forest);
        line-height: 1.2;
        margin: 0 0 28px;
    }

    .article-header .authors {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text);
    }

    .article-header .affiliations {
        font-size: 13px;
        color: var(--muted);
        padding-left: 16px;
        border-left: 2px solid var(--accent);
        margin-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .article-header .affiliations sup {
        font-weight: 700;
        color: var(--accent);
    }

    .article-header .action-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 28px;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text);
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
    }
    .btn-action:hover {
        background: var(--surface-sage);
        border-color: var(--accent);
    }
    .btn-action.primary {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }
    .btn-action.primary:hover {
        background: var(--accent-light);
        border-color: var(--accent-light);
    }

    /* Metadata card */
    .article-meta {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow);
        padding: 28px 32px;
        margin-bottom: 32px;
    }
    .article-meta .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 24px;
        font-size: 14px;
    }
    .article-meta .meta-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
        margin-bottom: 4px;
    }
    .article-meta .meta-value {
        color: var(--text);
        font-weight: 500;
    }
    .article-meta .meta-value.mono {
        font-family: 'SF Mono', 'Fira Code', monospace;
        word-break: break-all;
    }

    /* License block */
    .license-block {
        background: var(--accent-surface);
        border: 1px solid rgba(15,118,110,0.18);
        border-left: 4px solid var(--accent);
        border-radius: 0 var(--radius-lg) var(--radius-lg) 0;
        padding: 24px 28px;
        margin-bottom: 40px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .license-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }
    .license-badge {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 12px 18px;
        flex: 0 1 auto;
    }
    .license-badge .badge-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .license-badge.oa .badge-icon {
        background: #f59e0b;
    }
    .license-badge.cc .badge-icon {
        background: var(--accent);
    }
    .license-badge strong {
        display: block;
        font-size: 14px;
        color: var(--text);
    }
    .license-badge span {
        font-size: 12px;
        color: var(--muted);
    }
    .license-text {
        font-size: 13px;
        line-height: 1.6;
        color: var(--muted);
        margin: 0;
    }
    .license-text a {
        color: var(--accent);
        font-weight: 600;
        text-decoration: underline;
    }
    .license-text a:hover {
        color: var(--accent-light);
    }

    /* Abstract */
    .article-abstract {
        background: var(--accent-surface);
        border-left: 4px solid var(--accent);
        border-radius: 0 var(--radius-lg) var(--radius-lg) 0;
        padding: 28px 32px;
        margin-bottom: 40px;
    }
    .article-abstract h2 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--forest);
        margin: 0 0 14px;
    }
    .article-abstract p {
        color: var(--text);
        line-height: 1.7;
        margin: 0 0 16px;
    }
    .article-abstract .keywords {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }
    .article-abstract .kw-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--muted);
        margin-bottom: 6px;
    }
    .article-abstract .kw {
        padding: 4px 12px;
        font-size: 13px;
        font-weight: 500;
        background: var(--surface);
        border: 1px solid rgba(15,118,110,0.18);
        border-radius: 8px;
        color: var(--text);
    }

    /* Article content */
    .article-content h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--forest);
        margin: 48px 0 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--accent);
    }
    .article-content h2:first-child {
        margin-top: 0;
    }
    .article-content h3 {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--forest);
        margin: 32px 0 14px;
    }
    .article-content p {
        color: var(--text);
        line-height: 1.75;
        margin-bottom: 16px;
    }
    /* Figures — reduced with zoom */
    .article-content figure {
        margin: 32px 0;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }
    .figure-preview {
        position: relative;
        cursor: zoom-in;
        max-height: 400px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f8f6;
    }
    .figure-preview img {
        max-height: 400px;
        width: auto;
        max-width: 100%;
        object-fit: contain;
        transition: 0.2s ease;
    }
    .figure-preview:hover img {
        opacity: 0.92;
    }
    .figure-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 6px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .figure-preview:hover .figure-actions {
        opacity: 1;
    }
    .figure-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: rgba(0,0,0,0.55);
        backdrop-filter: blur(8px);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.15s ease;
    }
    .figure-action-btn:hover {
        background: rgba(0,0,0,0.75);
    }
    .article-content figcaption {
        padding: 14px 18px;
        font-size: 13px;
        color: var(--muted);
        line-height: 1.55;
        border-top: 1px solid var(--border);
        background: var(--surface);
    }
    .article-content figcaption strong {
        color: var(--forest);
        font-size: 13px;
    }

    /* Lightbox */
    .lightbox {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 100;
        background: rgba(0,0,0,0.88);
        backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        cursor: zoom-out;
        padding: 40px;
    }
    .lightbox.open {
        display: flex;
    }
    .lightbox img {
        max-width: 95vw;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }
    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s ease;
    }
    .lightbox-close:hover {
        background: rgba(255,255,255,0.25);
    }
    .lightbox-caption {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        color: rgba(255,255,255,0.8);
        font-size: 13px;
        text-align: center;
        max-width: 600px;
        line-height: 1.5;
    }
    .lightbox-download {
        position: absolute;
        top: 20px;
        left: 20px;
        height: 44px;
        padding: 0 18px;
        border-radius: 12px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        font-family: inherit;
        transition: 0.2s ease;
        text-decoration: none;
    }
    .lightbox-download:hover {
        background: rgba(255,255,255,0.25);
    }

    /* Tables — reduced with zoom */
    .article-content .content-table {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        margin: 32px 0;
        overflow: hidden;
    }
    .article-content .content-table .table-caption {
        font-size: 13px;
        font-weight: 600;
        color: var(--muted);
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
    }
    .table-scroll {
        padding: 18px;
        overflow-x: auto;
        max-height: 350px;
        overflow-y: auto;
        position: relative;
    }
    .table-expand-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px;
        border-top: 1px solid var(--border);
        background: var(--surface);
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        color: var(--accent);
        transition: 0.15s ease;
    }
    .table-expand-btn:hover {
        background: var(--accent-surface);
    }
    .article-content table {
        width: 100%;
        font-size: 14px;
        border-collapse: collapse;
    }
    .article-content thead {
        border-bottom: 2px solid var(--border);
    }
    .article-content th {
        text-align: left;
        padding: 8px 12px;
        font-weight: 600;
        color: var(--forest);
    }
    .article-content td {
        padding: 8px 12px;
        color: var(--text);
        border-bottom: 1px solid var(--border);
    }
    .article-content .content-list {
        list-style: none;
        padding-left: 16px;
        margin-bottom: 24px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .article-content .content-list li {
        display: flex;
        gap: 12px;
        color: var(--text);
        line-height: 1.6;
    }
    .article-content .content-list .bullet {
        font-weight: 700;
        color: var(--accent);
    }
    .article-content blockquote {
        margin: 24px 0;
        padding: 16px 24px;
        background: var(--accent-surface);
        border-left: 4px solid var(--accent);
        border-radius: 0 var(--radius-md) var(--radius-md) 0;
    }
    .article-content blockquote p {
        font-style: italic;
        margin-bottom: 0;
    }
    .article-content blockquote cite {
        display: block;
        margin-top: 8px;
        font-size: 13px;
        color: var(--muted);
        font-style: normal;
    }

    /* Sections: Acknowledgements, References */
    .article-section {
        margin-bottom: 40px;
    }
    .article-section h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--forest);
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--border);
    }
    .article-section p {
        color: var(--text);
        line-height: 1.7;
    }

    .references-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow);
        padding: 32px;
        margin-bottom: 40px;
    }
    .references-card h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--forest);
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--border);
    }
    .references-card .ref-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        font-size: 14px;
        color: var(--text);
        line-height: 1.6;
    }

    /* Citation block */
    .citation-block {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow);
        padding: 32px;
        margin-top: 48px;
        border-top: 3px solid var(--accent);
    }
    .citation-block .citation-header {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 20px;
    }
    .citation-block .citation-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .citation-block h3 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--forest);
        margin: 4px 0 0;
    }
    .citation-block .citation-text {
        background: var(--surface-soft);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px 20px;
        font-family: 'SF Mono', 'Fira Code', monospace;
        font-size: 13px;
        color: var(--text);
        line-height: 1.7;
        margin-bottom: 16px;
    }
    .citation-block .citation-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    /* Related articles */
    .related-section {
        margin-top: 48px;
        padding-top: 32px;
        border-top: 1px solid var(--border);
    }
    .related-section h2 {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--forest);
        margin-bottom: 24px;
    }
    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
    .related-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        padding: 24px;
        transition: all .2s;
    }
    .related-card:hover {
        box-shadow: var(--shadow);
        border-color: var(--accent);
    }
    .related-card h3 {
        font-size: 15px;
        font-weight: 700;
        margin: 0 0 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .related-card h3 a {
        color: var(--forest);
        text-decoration: none;
    }
    .related-card h3 a:hover {
        color: var(--accent);
    }
    .related-card .related-author {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 6px;
    }
    .related-card .related-pages {
        font-size: 12px;
        color: var(--sage);
    }

    /* Bottom navigation */
    .bottom-nav {
        border-top: 1px solid var(--border);
        background: var(--surface);
        padding: 24px 0;
        margin-top: 48px;
    }
    .bottom-nav .nav-inner {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .bottom-nav .nav-actions {
        display: flex;
        gap: 12px;
    }

    @media (max-width: 640px) {
        .article-meta .meta-grid {
            grid-template-columns: 1fr 1fr;
        }
        .license-badges {
            flex-direction: column;
        }
        .citation-block .citation-header {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
    <div style="padding: 40px 0;">
        <div class="container">
            <article>
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
                            {{ $submission->author?->name ?? 'Auteur inconnu' }}@if($submission->co_authors && is_array($submission->co_authors))@foreach($submission->co_authors as $coAuthor)@if(!empty($coAuthor['name'])), {{ $coAuthor['name'] }}@endif @endforeach @endif
                        </p>
                        @if($submission->author_affiliations && is_array($submission->author_affiliations) && count($submission->author_affiliations) > 0)
                        <div class="affiliations">
                            @foreach($submission->author_affiliations as $index => $affiliation)
                            <p><sup>{{ $index + 1 }}</sup> {{ $affiliation }}</p>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="action-row">
                        @if($submission->pdf_file)
                        <a href="{{ Storage::url($submission->pdf_file) }}" target="_blank" class="btn-action primary">
                            <i data-lucide="download" style="width:16px;height:16px"></i>
                            Télécharger PDF
                        </a>
                        @endif
                        <button onclick="document.getElementById('citation-block').scrollIntoView({ behavior: 'smooth' })" class="btn-action">
                            <i data-lucide="quote" style="width:16px;height:16px"></i>
                            Citer l'article
                        </button>
                        <button onclick="navigator.share ? navigator.share({title: '{{ $submission->title }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('Lien copié !'))" class="btn-action">
                            <i data-lucide="share-2" style="width:16px;height:16px"></i>
                            Partager
                        </button>
                    </div>
                </div>

                {{-- Metadata Box --}}
                <div class="article-meta">
                    <div class="meta-grid">
                        @if($submission->doi)
                        <div>
                            <p class="meta-label">DOI</p>
                            <p class="meta-value mono">{{ $submission->doi }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="meta-label">Date de publication</p>
                            <p class="meta-value">{{ $submission->published_at?->translatedFormat('d F Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="meta-label">Type d'article</p>
                            <p class="meta-value">Article de recherche</p>
                        </div>
                        <div>
                            <p class="meta-label">Licence</p>
                            <p class="meta-value">CC BY 4.0</p>
                        </div>
                    </div>
                </div>

                {{-- Open Access + CC BY 4.0 License Block --}}
                <div class="license-block">
                    <div class="license-badges">
                        <div class="license-badge oa">
                            <img src="/images/open-access.png" alt="Open Access" style="height:36px;width:auto;">
                            <div>
                                <strong>Open Access</strong>
                                <span>Cet article est en accès libre</span>
                            </div>
                        </div>
                        <div class="license-badge cc">
                            <div class="badge-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M15 9.354a4 4 0 1 0 0 5.292"/>
                                </svg>
                            </div>
                            <div>
                                <strong>CC BY 4.0</strong>
                                <span>Creative Commons Attribution License</span>
                            </div>
                        </div>
                    </div>
                    <p class="license-text">
                        Cet article est distribué sous les termes de la licence
                        <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank" rel="noopener">
                            Creative Commons Attribution 4.0 International (CC BY 4.0)</a>,
                        qui autorise l'utilisation, la distribution et la reproduction sur tout support,
                        à condition que l'œuvre originale soit correctement citée.
                    </p>
                </div>

                {{-- Content --}}
                <div class="article-content">
                    {{-- Abstract --}}
                    @if($submission->abstract)
                    <section>
                        <div class="article-abstract">
                            <h2>Résumé</h2>
                            <p>{{ $submission->abstract }}</p>
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

                    {{-- Main Content --}}
                    @if($submission->content_blocks && is_array($submission->content_blocks) && count($submission->content_blocks) > 0)
                    <section style="margin-bottom:40px">
                        @php $sectionNumber = 0; @endphp
                        @foreach($submission->content_blocks as $blockIndex => $block)
                            @php $blockType = $block['type'] ?? 'paragraph'; @endphp

                            @if($blockType === 'heading')
                                @php $sectionNumber++; @endphp
                                @if(($block['level'] ?? 'h2') === 'h2')
                                    <h2>{{ $sectionNumber }}. {{ $block['content'] ?? '' }}</h2>
                                @else
                                    <h3>{{ $sectionNumber }}.{{ $loop->iteration }}. {{ $block['content'] ?? '' }}</h3>
                                @endif

                            @elseif($blockType === 'paragraph')
                                <p>{!! $block['content'] ?? '' !!}</p>

                            @elseif($blockType === 'image')
                                @php
                                    $imgSrc = $block['url'] ?? $block['src'] ?? '';
                                    $imgCaption = $block['caption'] ?? '';
                                @endphp
                                @if($imgSrc)
                                <figure>
                                    <div class="figure-preview" onclick="openLightbox('{{ $imgSrc }}', 'Figure {{ $blockIndex + 1 }}. {{ addslashes($imgCaption) }}')">
                                        <img src="{{ $imgSrc }}" alt="{{ $imgCaption }}">
                                        <div class="figure-actions">
                                            <button class="figure-action-btn" title="Agrandir" onclick="event.stopPropagation(); openLightbox('{{ $imgSrc }}', 'Figure {{ $blockIndex + 1 }}. {{ addslashes($imgCaption) }}')">
                                                <i data-lucide="maximize-2" style="width:16px;height:16px;"></i>
                                            </button>
                                            <a class="figure-action-btn" href="{{ $imgSrc }}" download title="Télécharger" onclick="event.stopPropagation();">
                                                <i data-lucide="download" style="width:16px;height:16px;"></i>
                                            </a>
                                        </div>
                                    </div>
                                    @if($imgCaption)
                                    <figcaption>
                                        <strong>Figure {{ $blockIndex + 1 }}.</strong> {{ $imgCaption }}
                                    </figcaption>
                                    @endif
                                </figure>
                                @endif

                            @elseif($blockType === 'table')
                                @php $tableData = $block['data'] ?? []; @endphp
                                @if(count($tableData) > 0)
                                <div class="content-table">
                                    @if(!empty($block['caption']))
                                    <p class="table-caption">
                                        <strong>Tableau {{ $blockIndex + 1 }}.</strong> {{ $block['caption'] }}
                                    </p>
                                    @endif
                                    <div class="table-scroll">
                                        <table>
                                            <thead>
                                                @if(isset($tableData[0]))
                                                <tr>
                                                    @foreach($tableData[0] as $cell)
                                                    <th>{{ $cell }}</th>
                                                    @endforeach
                                                </tr>
                                                @endif
                                            </thead>
                                            <tbody>
                                                @foreach(array_slice($tableData, 1) as $row)
                                                <tr>
                                                    @foreach($row as $cell)
                                                    <td>{{ $cell }}</td>
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
                                <ul class="content-list">
                                    @foreach($listItems as $item)
                                    <li>
                                        <span class="bullet">•</span>
                                        <span>{{ $item }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif

                            @elseif($blockType === 'quote')
                                <blockquote>
                                    <p>{{ $block['content'] ?? '' }}</p>
                                    @if(!empty($block['source']))
                                    <cite>— {{ $block['source'] }}</cite>
                                    @endif
                                </blockquote>
                            @endif
                        @endforeach
                    </section>
                    @elseif($submission->content_html)
                    <section style="margin-bottom:40px">
                        {!! $submission->content_html !!}
                    </section>
                    @endif

                    {{-- Acknowledgements --}}
                    @if($submission->acknowledgements)
                    <section class="article-section">
                        <h2>Remerciements</h2>
                        <p>{{ $submission->acknowledgements }}</p>
                    </section>
                    @endif

                    {{-- References --}}
                    @if($submission->references && is_array($submission->references) && count($submission->references) > 0)
                    <div class="references-card">
                        <h2>Références bibliographiques</h2>
                        <div class="ref-list">
                            @foreach($submission->references as $reference)
                            <p>{{ $reference }}</p>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

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
                    <div class="citation-text">
                        {{ $submission->author?->name ?? 'Auteur' }}@if($submission->co_authors && is_array($submission->co_authors) && count($submission->co_authors) > 0)@foreach($submission->co_authors as $index => $coAuthor)@if(!empty($coAuthor['name'])), {{ $coAuthor['name'] }}@endif @endforeach @endif ({{ $submission->published_at?->year ?? date('Y') }}). {{ $submission->title }}. <em>Chersotis</em>@if($submission->journalIssue), <strong>{{ $submission->journalIssue->volume_number }}</strong>({{ $submission->journalIssue->issue_number }})@endif @if($submission->start_page && $submission->end_page), {{ $submission->start_page }}-{{ $submission->end_page }}@endif. @if($submission->doi)https://doi.org/{{ $submission->doi }}@endif
                    </div>
                    <div class="citation-actions">
                        <button onclick="copyBibtex()" class="btn-action primary" style="height:38px;font-size:13px">Format BibTeX</button>
                        <button onclick="copyCitation()" class="btn-action" style="height:38px;font-size:13px;color:var(--accent)">Copier la citation</button>
                    </div>
                </div>

                {{-- Related Articles --}}
                @if($relatedArticles->count() > 0)
                <div class="related-section">
                    <h2>Articles du même numéro</h2>
                    <div class="related-grid">
                        @foreach($relatedArticles as $related)
                        <article class="related-card">
                            <h3>
                                <a href="{{ route('journal.articles.show', $related) }}">
                                    {{ $related->title }}
                                </a>
                            </h3>
                            <p class="related-author">{{ $related->author?->name }}</p>
                            @if($related->start_page && $related->end_page)
                            <p class="related-pages">pp. {{ $related->start_page }}-{{ $related->end_page }}</p>
                            @endif
                        </article>
                        @endforeach
                    </div>
                </div>
                @endif
            </article>
        </div>
    </div>

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
                    <a href="{{ Storage::url($submission->pdf_file) }}" target="_blank" class="btn-action">
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

    @php
        $bibtexYear = $submission->published_at?->year ?? date('Y');
        $bibtexAuthor = $submission->author?->name ?? 'Auteur';
        $bibtexTitle = str_replace(['"', '\\'], ['\"', '\\\\'], $submission->title);
        $lb = '{'; $rb = '}';
        $bibtexLines = [
            "@article{$lb}chersotis{$bibtexYear},",
            "  author = {$lb}{$bibtexAuthor}{$rb},",
            "  title = {$lb}{$bibtexTitle}{$rb},",
            "  journal = {$lb}Chersotis{$rb},",
            "  year = {$lb}{$bibtexYear}{$rb},",
        ];
        if ($submission->journalIssue) {
            $bibtexLines[] = "  volume = {$lb}{$submission->journalIssue->volume_number}{$rb},";
            $bibtexLines[] = "  number = {$lb}{$submission->journalIssue->issue_number}{$rb},";
        }
        if ($submission->start_page && $submission->end_page) {
            $bibtexLines[] = "  pages = {$lb}{$submission->start_page}--{$submission->end_page}{$rb},";
        }
        if ($submission->doi) {
            $bibtexLines[] = "  doi = {$lb}{$submission->doi}{$rb}";
        }
        $bibtexLines[] = "{$rb}";
        $bibtexString = implode("\n", $bibtexLines);
    @endphp

    {{-- Lightbox --}}
    <div class="lightbox" id="lightbox" onclick="closeLightbox()">
        <button class="lightbox-close" onclick="closeLightbox()">
            <i data-lucide="x" style="width:20px;height:20px;"></i>
        </button>
        <a class="lightbox-download" id="lightbox-download" href="#" download onclick="event.stopPropagation();">
            <i data-lucide="download" style="width:16px;height:16px;"></i>
            Télécharger
        </a>
        <img id="lightbox-img" src="" alt="" onclick="event.stopPropagation();">
        <div class="lightbox-caption" id="lightbox-caption"></div>
    </div>

    @push('scripts')
    <script>
        function openLightbox(src, caption) {
            const lb = document.getElementById('lightbox');
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox-caption').textContent = caption || '';
            document.getElementById('lightbox-download').href = src;
            lb.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('open');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });

        function copyCitation() {
            const citation = document.querySelector('#citation-block .citation-text').innerText;
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
