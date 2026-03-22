<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $submission->title }}</title>
    <style>
        @page {
            margin: 15mm 15mm 20mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5pt;
            line-height: 1.4;
            color: #333;
        }

        /* ============================================
           HEADER - TWO COLUMN (top of page 1 only)
           ============================================ */

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .sidebar-cell {
            width: 48mm;
            vertical-align: top;
            padding-right: 4mm;
        }

        .content-cell {
            vertical-align: top;
            padding-left: 4mm;
            border-left: 1px solid #e0e0e0;
        }

        /* ============================================
           SIDEBAR
           ============================================ */

        .logo-text {
            font-size: 20pt;
            font-weight: bold;
            color: #F97316;
            display: block;
        }

        .logo-subtitle {
            font-size: 7pt;
            color: #888;
            display: block;
            margin-bottom: 12px;
        }

        .meta-block {
            font-size: 7pt;
            line-height: 1.5;
            color: #555;
            margin-bottom: 6px;
        }

        .meta-label {
            font-weight: bold;
            color: #333;
        }

        .meta-section {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid #eee;
        }

        .sidebar-cell a {
            color: #0D9488;
            text-decoration: none;
            word-break: break-all;
            font-size: 6.5pt;
        }

        .license-box {
            margin-top: 8px;
            padding: 4px 6px;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            font-size: 6pt;
            color: #0369a1;
        }

        /* ============================================
           TITLE AREA
           ============================================ */

        .article-title {
            font-size: 14pt;
            font-weight: bold;
            font-style: italic;
            color: #EA580C;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .article-authors {
            font-size: 9pt;
            color: #1F2937;
            margin-bottom: 3px;
        }

        .author-sup {
            color: #0D9488;
            font-size: 6pt;
            vertical-align: super;
        }

        .article-affiliations {
            font-size: 7pt;
            color: #666;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .article-type {
            font-size: 9pt;
            font-weight: bold;
            color: #0D9488;
            margin-bottom: 6px;
        }

        /* Abstract */
        .abstract-box {
            background: #f7f7f7;
            padding: 8px 10px;
            margin-bottom: 6px;
        }

        .abstract-title {
            font-size: 8pt;
            font-weight: bold;
            color: #0D9488;
            margin-bottom: 4px;
        }

        .abstract-text {
            font-size: 8pt;
            text-align: justify;
            line-height: 1.35;
        }

        /* Keywords */
        .keywords {
            font-size: 7.5pt;
            margin-bottom: 10px;
        }

        .keywords-label {
            font-weight: bold;
            color: #0D9488;
        }

        /* ============================================
           MAIN CONTENT (full width after header)
           ============================================ */

        .main-content {
            margin-top: 5px;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0D9488;
            margin-top: 14px;
            margin-bottom: 6px;
        }

        .subsection-title {
            font-size: 9pt;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .main-content p {
            text-align: justify;
            margin-bottom: 6px;
            text-indent: 1.2em;
        }

        .section-title + p,
        .subsection-title + p {
            text-indent: 0;
        }

        /* Lists */
        .content-list {
            margin: 6px 0 6px 2em;
            padding: 0;
        }

        .content-list li {
            margin-bottom: 3px;
        }

        /* ============================================
           FIGURES & TABLES
           ============================================ */

        .figure {
            margin: 12px 0;
            text-align: center;
            page-break-inside: avoid;
        }

        .figure img {
            max-width: 100%;
            max-height: 200px;
        }

        .figure-caption {
            font-size: 7.5pt;
            color: #444;
            margin-top: 5px;
            text-align: left;
        }

        .figure-caption strong {
            color: #0D9488;
        }

        .table-container {
            margin: 12px 0;
            page-break-inside: avoid;
        }

        .table-caption {
            font-size: 7.5pt;
            margin-bottom: 5px;
        }

        .table-caption strong {
            color: #0D9488;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }

        table.data-table th {
            background: #f5f5f5;
            border-bottom: 1.5px solid #999;
            padding: 5px 6px;
            text-align: left;
            font-weight: bold;
        }

        table.data-table td {
            border-bottom: 0.5px solid #ddd;
            padding: 4px 6px;
        }

        /* ============================================
           ACKNOWLEDGEMENTS & REFERENCES
           ============================================ */

        .section-secondary {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .section-secondary-title {
            font-size: 10pt;
            font-weight: bold;
            color: #0D9488;
            margin-bottom: 6px;
        }

        .acknowledgements-text {
            font-size: 8pt;
            color: #555;
            text-align: justify;
        }

        .references {
            font-size: 7pt;
            line-height: 1.4;
        }

        .reference-item {
            margin-bottom: 4px;
            text-indent: -1.5em;
            margin-left: 1.5em;
            color: #444;
        }

        .reference-item a {
            color: #0D9488;
            text-decoration: none;
        }

        /* ============================================
           FOOTER (all pages)
           ============================================ */

        .page-footer {
            position: fixed;
            bottom: 0mm;
            left: 0;
            right: 0;
            height: 12mm;
            font-size: 6.5pt;
            color: #555;
            padding-top: 3mm;
            border-top: 0.5pt solid #ccc;
        }

        .page-footer a {
            color: #0D9488;
            text-decoration: none;
        }

        .page-footer .citation-journal {
            font-weight: bold;
            color: #0D9488;
        }

        /* ============================================
           UTILITIES
           ============================================ */

        .page-break {
            page-break-after: always;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        blockquote {
            margin: 10px 0 10px 15px;
            padding: 8px 12px;
            border-left: 2px solid #0D9488;
            background: #fafafa;
            font-style: italic;
            font-size: 8pt;
            color: #555;
        }

        blockquote p {
            margin: 0;
            text-indent: 0 !important;
        }
    </style>
</head>
<body>
    {{-- Fixed Footer --}}
    <div class="page-footer">
        <span style="color: #EA580C; font-weight: bold;">Citation</span>
        {{ $submission->author->name ?? 'Auteur' }} ({{ $submission->published_at?->format('Y') ?? now()->format('Y') }}),
        {{ Str::limit($submission->title, 50) }}.
        <span class="citation-journal">Chersotis</span>
        @if($submission->journalIssue)
            {{ $submission->journalIssue->volume_number }}({{ $submission->journalIssue->issue_number }})
            @if($submission->start_page && $submission->end_page)
                : {{ $submission->start_page }}-{{ $submission->end_page }}.
            @endif
        @endif
        @if($submission->doi)
            <a href="https://doi.org/{{ $submission->doi }}">https://doi.org/{{ $submission->doi }}</a>
        @endif
    </div>

    {{-- ==========================================
         HEADER SECTION - TWO COLUMNS
         ========================================== --}}

    <table class="header-table">
        <tr>
            {{-- LEFT SIDEBAR --}}
            <td class="sidebar-cell">
                <span class="logo-text">Chersotis</span>
                <span class="logo-subtitle">By oreina</span>

                <div class="meta-block">
                    <span class="meta-label">Reçu :</span> {{ ($submission->received_at ?? $submission->submitted_at)?->format('d/m/Y') ?? '—' }}
                </div>
                <div class="meta-block">
                    <span class="meta-label">Accepté :</span> {{ ($submission->accepted_at ?? $submission->decision_at)?->format('d/m/Y') ?? '—' }}
                </div>
                <div class="meta-block">
                    <span class="meta-label">Publié :</span> {{ $submission->published_at?->format('d/m/Y') ?? '—' }}
                </div>

                <div class="meta-section">
                    <span class="meta-label">Correspondance</span><br>
                    {{ $submission->author->name ?? '' }}<br>
                    <a href="mailto:{{ $submission->author->email ?? '' }}">{{ $submission->author->email ?? '' }}</a>
                </div>

                @if($submission->editor)
                <div class="meta-section">
                    <span class="meta-label">Editeur</span><br>
                    {{ $submission->editor->name }}
                </div>
                @endif

                @if($submission->doi)
                <div class="meta-section">
                    <a href="https://doi.org/{{ $submission->doi }}">https://doi.org/{{ $submission->doi }}</a>
                </div>
                @endif

                <div class="meta-section">
                    ISSN 0044-586X (print)<br>
                    ISSN 2107-7207 (electronic)
                </div>

                <div class="license-box">
                    Licensed under<br>
                    Creative Commons CC-BY 4.0
                </div>
            </td>

            {{-- RIGHT: TITLE + ABSTRACT --}}
            <td class="content-cell">
                <h1 class="article-title">{{ $submission->title }}</h1>

                <div class="article-authors">
                    {{ $submission->author->name ?? 'Auteur' }}<sup class="author-sup">a</sup>
                    @if($submission->co_authors && is_array($submission->co_authors))
                        @foreach($submission->co_authors as $index => $coAuthor)
                            , {{ $coAuthor['name'] ?? '' }}<sup class="author-sup">{{ chr(98 + $index) }}</sup>
                        @endforeach
                    @endif
                </div>

                <div class="article-affiliations">
                    @if($submission->author_affiliations && is_array($submission->author_affiliations) && count($submission->author_affiliations) > 0)
                        @foreach($submission->author_affiliations as $index => $affiliation)
                            <sup>{{ chr(97 + $index) }}</sup>{{ $affiliation }}<br>
                        @endforeach
                    @else
                        <sup>a</sup>{{ $submission->author->affiliation ?? 'Affiliation non spécifiée' }}
                    @endif
                </div>

                <div class="article-type">Original research</div>

                <div class="abstract-box">
                    <div class="abstract-title">Résumé</div>
                    <div class="abstract-text">{{ $submission->abstract }}</div>
                </div>

                @php
                    $keywordsArray = $submission->keywords;
                    if (is_string($keywordsArray)) {
                        $keywordsArray = array_map('trim', explode(',', $keywordsArray));
                    }
                @endphp
                @if((is_array($keywordsArray) && count($keywordsArray) > 0) || (is_string($submission->keywords) && !empty($submission->keywords)))
                <div class="keywords">
                    <span class="keywords-label">Mots-clés</span>
                    @if(is_array($keywordsArray))
                        {{ implode(' ; ', $keywordsArray) }}
                    @else
                        {{ str_replace(',', ' ; ', $submission->keywords) }}
                    @endif
                </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- ==========================================
         MAIN CONTENT - FULL WIDTH
         ========================================== --}}

    <div class="main-content">
        @if($submission->content_blocks && is_array($submission->content_blocks) && count($submission->content_blocks) > 0)
            @php $figureCount = 0; $tableCount = 0; @endphp

            @foreach($submission->content_blocks as $block)
                @php $blockType = $block['type'] ?? 'paragraph'; @endphp

                @if($blockType === 'heading')
                    <h2 class="section-title">{{ $block['content'] ?? '' }}</h2>

                @elseif($blockType === 'subheading')
                    <h3 class="subsection-title">{{ $block['content'] ?? '' }}</h3>

                @elseif($blockType === 'paragraph')
                    <p>{{ $block['content'] ?? '' }}</p>

                @elseif($blockType === 'image')
                    @php
                        $figureCount++;
                        $imgSrc = $block['url'] ?? $block['src'] ?? '';
                        $imgCaption = $block['caption'] ?? '';
                    @endphp
                    @if($imgSrc)
                        <div class="figure avoid-break">
                            <img src="{{ $imgSrc }}" alt="{{ $imgCaption }}">
                            @if($imgCaption)
                                <div class="figure-caption"><strong>Figure {{ $figureCount }}.</strong> {{ $imgCaption }}</div>
                            @endif
                        </div>
                    @endif

                @elseif($blockType === 'table')
                    @php
                        $tableCount++;
                        $tableData = $block['data'] ?? [];
                    @endphp
                    @if(count($tableData) > 0)
                        <div class="table-container avoid-break">
                            @if(!empty($block['caption']))
                                <div class="table-caption"><strong>Table {{ $tableCount }}.</strong> {{ $block['caption'] }}</div>
                            @endif
                            <table class="data-table">
                                @foreach($tableData as $rowIndex => $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            @if($rowIndex === 0)
                                                <th>{{ $cell }}</th>
                                            @else
                                                <td>{{ $cell }}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif

                @elseif($blockType === 'list')
                    @php
                        $listContent = $block['content'] ?? '';
                        $listItems = $block['items'] ?? [];
                        if (empty($listItems) && !empty($listContent)) {
                            $listItems = array_filter(array_map('trim', explode("\n", $listContent)));
                        }
                        $isOrdered = $block['ordered'] ?? false;
                    @endphp
                    @if(count($listItems) > 0)
                        @if($isOrdered)
                            <ol class="content-list">
                                @foreach($listItems as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ol>
                        @else
                            <ul class="content-list">
                                @foreach($listItems as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @endif
                    @endif

                @elseif($blockType === 'quote')
                    <blockquote>
                        <p>{{ $block['content'] ?? '' }}</p>
                    </blockquote>
                @endif
            @endforeach
        @elseif($submission->content_html)
            {!! $submission->content_html !!}
        @else
            <p style="text-align: center; color: #999; padding: 20px;">
                <em>Contenu non saisi.</em>
            </p>
        @endif
    </div>

    {{-- Acknowledgements --}}
    @if($submission->acknowledgements)
    <div class="section-secondary avoid-break">
        <div class="section-secondary-title">Remerciements</div>
        <div class="acknowledgements-text">{{ $submission->acknowledgements }}</div>
    </div>
    @endif

    {{-- References --}}
    @if($submission->references && is_array($submission->references) && count($submission->references) > 0)
    <div class="section-secondary">
        <div class="section-secondary-title">Références</div>
        <div class="references">
            @foreach($submission->references as $ref)
            <div class="reference-item">{{ $ref }}</div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Page numbering --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "{PAGE_NUM}";
            $size = 8;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $x = $pdf->get_width() - $width - 40;
            $y = $pdf->get_height() - 30;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.4, 0.4, 0.4));
        }
    </script>
</body>
</html>
