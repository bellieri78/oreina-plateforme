@extends('layouts.journal')

@section('title', strip_tags($submission->title))
@section('meta_description', Str::limit(strip_tags($submission->display_abstract ?? $submission->abstract), 160))

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
        color: var(--accent);
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

    /* Meta logos (Open Access + CC) */
    .meta-logos {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 2px;
    }
    .meta-logo {
        height: 24px;
        width: auto;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    .meta-logo:hover {
        opacity: 1;
    }
    .cc-logo {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 600;
        color: var(--accent);
        padding: 3px 8px;
        border: 1px solid var(--accent);
        border-radius: 4px;
        transition: all 0.2s;
        text-decoration: none;
    }
    .cc-logo:hover {
        background: var(--accent);
        color: white;
    }
    .cc-logo svg {
        flex-shrink: 0;
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
        color: var(--accent);
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

    /* Taxon links (Artemisiae) */
    a[href*="artemisiae"] {
        color: #0d9488;
        text-decoration: none;
        border-bottom: 1.5px dotted #0d9488;
        padding-bottom: 1px;
        transition: all 0.2s ease;
    }
    a[href*="artemisiae"]:hover {
        color: #0f766e;
        border-bottom-style: solid;
        background: rgba(13, 148, 136, 0.08);
        border-radius: 2px;
        padding: 0 2px;
        margin: 0 -2px;
    }

    /* Inline citation tooltips */
    .cite {
        cursor: help;
        position: relative;
        display: inline;
        border-bottom: 1px dashed #9ca3af;
        padding-bottom: 1px;
        transition: all 0.2s ease;
    }
    .cite:hover {
        border-bottom-color: #0d9488;
        color: #0d9488;
    }
    .cite[data-ref]:hover::after {
        content: attr(data-ref);
        position: absolute;
        bottom: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%);
        background: #1e293b;
        color: #f1f5f9;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-style: normal;
        font-weight: 400;
        line-height: 1.6;
        white-space: normal;
        width: max-content;
        max-width: 420px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        pointer-events: none;
        z-index: 100;
    }
    .cite[data-ref]:hover::before {
        content: '';
        position: absolute;
        bottom: calc(100% + 2px);
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #1e293b;
        pointer-events: none;
        z-index: 100;
    }
    @media (max-width: 640px) {
        .cite[data-ref]:hover::after {
            left: 0;
            transform: none;
            max-width: 280px;
        }
        .cite[data-ref]:hover::before {
            left: 20px;
            transform: none;
        }
    }

    /* === Layout 2 colonnes (≥1024px) === */
    .article-layout {
        display: block;
    }
    @media (min-width: 1024px) {
        .article-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 48px;
            align-items: start;
        }
        .article-sidebar {
            position: sticky;
            top: 24px;
            max-height: calc(100vh - 48px);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
            font-size: 14px;
        }
    }
    .article-sidebar { display: none; }
    @media (min-width: 1024px) { .article-sidebar { display: flex; } }

    .sidebar-section {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 20px;
        box-shadow: var(--shadow);
    }
    .sidebar-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
        margin: 0 0 12px;
    }
    .sidebar-actions { display: flex; flex-direction: column; gap: 8px; }
    .sidebar-actions .btn-action { justify-content: center; width: 100%; }

    .sidebar-toc ol {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .sidebar-toc a {
        display: block;
        padding: 6px 10px;
        border-left: 2px solid transparent;
        color: var(--text);
        text-decoration: none;
        font-size: 13px;
        line-height: 1.4;
        transition: all .2s;
    }
    .sidebar-toc a:hover { color: var(--accent); border-left-color: var(--accent); }
    .sidebar-toc a.active {
        color: var(--accent);
        border-left-color: var(--accent);
        background: var(--accent-surface);
        font-weight: 600;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .metric-tile {
        background: var(--accent-surface);
        border-radius: var(--radius-md);
        padding: 12px;
        text-align: center;
    }
    .metric-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--forest);
        line-height: 1;
    }
    .metric-label {
        margin-top: 4px;
        font-size: 11px;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    /* === Mobile FAB + drawer === */
    .mobile-fab-wrapper { display: contents; }
    .mobile-fab {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 60;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: var(--accent);
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 10px 24px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    @media (min-width: 1024px) { .mobile-fab { display: none; } }
    [x-cloak] { display: none !important; }
    .mobile-drawer-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 60;
    }
    .mobile-drawer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 61;
        background: var(--surface);
        border-top-left-radius: 18px;
        border-top-right-radius: 18px;
        max-height: 80vh;
        overflow-y: auto;
        padding: 16px 20px 32px;
    }
    .mobile-drawer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 12px;
        font-weight: 700;
        color: var(--forest);
    }
    .mobile-drawer-header button {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--muted);
    }
    .mobile-drawer-body { display: flex; flex-direction: column; gap: 16px; }
</style>
@endpush

@section('content')
    <div style="padding: 40px 0;">
        <div class="container">
            <div class="article-layout">
                <article>
                    @include('journal.articles.partials._header')
                    @include('journal.articles.partials._metadata')

                    <div class="article-content">
                        @include('journal.articles.partials._abstract')
                        @include('journal.articles.partials._content-blocks')
                        @include('journal.articles.partials._acknowledgements')
                        @include('journal.articles.partials._references')
                    </div>

                    @include('journal.articles.partials._citation-block')
                    @include('journal.articles.partials._related')
                </article>

                @include('journal.articles.partials._sidebar')
            </div>
        </div>
    </div>

    @include('journal.articles.partials._mobile-fab')
    @include('journal.articles.partials._bottom-nav')
    @include('journal.articles.partials._lightbox')

    @php
        $bibtexYear = $submission->published_at?->year ?? date('Y');
        $bibtexAuthor = $submission->display_authors ?? $submission->author?->name ?? 'Auteur';
        $bibtexTitle = str_replace(['"', '\\'], ['\"', '\\\\'], strip_tags($submission->title));
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

        // === TOC scrollspy ===
        (function () {
            const links = document.querySelectorAll('.sidebar-toc a[data-toc-target]');
            if (!links.length) return;
            const targets = Array.from(links).map(a => document.getElementById(a.dataset.tocTarget)).filter(Boolean);
            if (!targets.length) return;

            const setActive = (id) => {
                links.forEach(a => a.classList.toggle('active', a.dataset.tocTarget === id));
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) setActive(entry.target.id);
                });
            }, { rootMargin: '-40% 0px -55% 0px', threshold: 0 });

            targets.forEach(t => observer.observe(t));
        })();

        // === Share tracking ===
        async function shareArticle(network) {
            const url = window.location.href;
            const title = {!! json_encode(strip_tags($submission->title)) !!};

            const endpoints = {
                twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`,
                linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`,
                mail: `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(url)}`,
            };

            if (network === 'copy') {
                await navigator.clipboard.writeText(url);
                alert('Lien copié !');
            } else if (network === 'native' && navigator.share) {
                try { await navigator.share({ title, url }); }
                catch (e) { return; }
            } else if (endpoints[network]) {
                window.open(endpoints[network], '_blank', 'noopener');
            } else {
                return;
            }

            try {
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                await fetch({!! json_encode(route('journal.articles.share', $submission)) !!}, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfMeta ? csrfMeta.content : '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ network })
                });
            } catch (e) { /* silencieux */ }
        }

        // Share picker — v1: minimal prompt. The sidebar "Partager" button
        // dispatches `open-share` which the Alpine mobile drawer listens for;
        // on desktop we also show a picker.
        window.addEventListener('open-share', () => {
            // Only pop the picker on desktop. Mobile: Alpine drawer already opened.
            if (window.innerWidth < 1024) return;
            const choice = prompt('Partager via : twitter, linkedin, mail, copy, native', 'copy');
            if (choice && ['twitter','linkedin','mail','copy','native'].includes(choice)) {
                shareArticle(choice);
            }
        });
    </script>
    @endpush
@endsection
