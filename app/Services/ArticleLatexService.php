<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ArticleLatexService
{
    protected string $tempDir;
    protected string $pdflatexPath;
    protected string $driver;
    protected string $apiUrl;
    protected int $apiTimeout;
    protected bool $debugMode = false;
    protected bool $compilationSucceeded = false;

    public function __construct()
    {
        // Read configuration
        $this->driver = config('services.latex.driver', 'local');
        $this->apiUrl = config('services.latex.api_url', 'https://latexonline.cc/compile');
        $this->apiTimeout = config('services.latex.api_timeout', 60);

        // Default to MiKTeX installation path on Windows
        $defaultPath = PHP_OS_FAMILY === 'Windows'
            ? 'C:\\Program Files\\MiKTeX\\miktex\\bin\\x64\\pdflatex.exe'
            : '/usr/bin/pdflatex';

        $this->pdflatexPath = config('services.latex.pdflatex_path', $defaultPath);
        $this->debugMode = config('app.debug', false);
    }

    /**
     * Generate PDF from LaTeX
     */
    public function generatePdf(Submission $submission): string
    {
        $submission->load(['author', 'editor', 'journalIssue']);

        // Create temp directory with debug prefix if enabled
        $prefix = $this->debugMode ? 'debug_' : '';
        $this->tempDir = storage_path('app/temp/latex/' . $prefix . $submission->id . '_' . time());
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        $this->compilationSucceeded = false;

        try {
            // Generate LaTeX content
            $texContent = $this->generateLatexContent($submission);

            // Write .tex file
            $texFile = $this->tempDir . '/article.tex';
            file_put_contents($texFile, $texContent);

            Log::info('LaTeX file generated', ['path' => $texFile, 'driver' => $this->driver]);

            // Copy images (only needed for local compilation)
            if ($this->driver === 'local') {
                $this->copyImages($submission);
            }

            // Compile LaTeX based on driver
            if ($this->driver === 'api') {
                $this->compileViaApi($texContent, $submission);
            } else {
                // Local compilation (run twice for references)
                $this->compileLaTeX($texFile);
                $this->compileLaTeX($texFile);
            }

            $this->compilationSucceeded = true;

            // Move PDF to storage
            $pdfPath = $this->movePdfToStorage($submission);

            // Update submission
            $submission->update(['pdf_file' => $pdfPath]);

            return $pdfPath;

        } finally {
            // Only cleanup if compilation succeeded or not in debug mode
            if ($this->compilationSucceeded || !$this->debugMode) {
                $this->cleanup();
            } else {
                Log::warning('Debug mode: keeping temp files for inspection', [
                    'temp_dir' => $this->tempDir
                ]);
            }
        }
    }

    /**
     * Get LaTeX configuration
     */
    protected function getConfig(string $key, $default = null)
    {
        return config("latex.{$key}", $default);
    }

    /**
     * Generate LaTeX preamble (packages, geometry, colors, etc.)
     */
    protected function generatePreamble(string $title): string
    {
        // Get config values
        $fontSize = $this->getConfig('fonts.base_size', '11pt');
        $fontPackage = $this->getConfig('fonts.main', 'lmodern');

        $margins = $this->getConfig('margins', []);
        $top = $margins['top'] ?? 22;
        $bottom = $margins['bottom'] ?? 28;
        $left = $margins['left'] ?? 18;
        $right = $margins['right'] ?? 18;

        $colors = $this->getConfig('colors', []);
        $colorPrimary = $colors['primary'] ?? 'EA580C';
        $colorSecondary = $colors['secondary'] ?? '0D9488';
        $colorText = $colors['text'] ?? '333333';
        $colorGray = $colors['gray'] ?? '555555';
        $colorLight = $colors['light_gray'] ?? 'F7F7F7';
        $colorDarkTeal = $this->getConfig('colors_extra.dark_teal', '0F766E');
        $colorTitleGreen = $this->getConfig('colors_extra.title_green', '2C5F2D');

        $spacing = $this->getConfig('spacing', []);
        $sectionBefore = $spacing['section_before'] ?? 18;
        $sectionAfter = $spacing['section_after'] ?? 8;
        $subsectionBefore = $spacing['subsection_before'] ?? 12;
        $subsectionAfter = $spacing['subsection_after'] ?? 6;

        $header = $this->getConfig('header', []);
        $headerLeft = $header['left'] ?? 'Chersotis';
        $headerRight = $header['right'] ?? config('journal.tagline', config('journal.name'));

        $journal = $this->getConfig('journal', []);
        $journalName = $journal['name'] ?? 'Chersotis';

        // Font package selection
        $fontPackageLatex = match ($fontPackage) {
            'times' => "\\usepackage{mathptmx}",
            'palatino' => "\\usepackage{palatino}",
            'helvetica' => "\\usepackage{helvet}\n\\renewcommand{\\familydefault}{\\sfdefault}",
            default => "\\usepackage{lmodern}",
        };

        return <<<PREAMBLE
\\documentclass[{$fontSize},a4paper]{article}

% PACKAGES
\\usepackage[utf8]{inputenc}
\\usepackage[T1]{fontenc}
{$fontPackageLatex}
\\usepackage[french]{babel}
\\usepackage{geometry}
\\usepackage{xcolor}
\\usepackage{graphicx}
\\usepackage{fancyhdr}
\\usepackage{titlesec}
\\usepackage{enumitem}
\\usepackage{hyperref}
\\usepackage{parskip}
\\usepackage{ragged2e}
\\usepackage[babel=true]{microtype}
\\usepackage{lastpage}

% GEOMETRY
\\geometry{
    a4paper,
    top={$top}mm,
    bottom={$bottom}mm,
    left={$left}mm,
    right={$right}mm,
    headheight=15pt,
    footskip=18mm
}

% COLORS
\\definecolor{chersotisOrange}{HTML}{{$colorPrimary}}
\\definecolor{chersotisTeal}{HTML}{{$colorSecondary}}
\\definecolor{chersotisDarkTeal}{HTML}{{$colorDarkTeal}}
\\definecolor{chersotisTitleGreen}{HTML}{{$colorTitleGreen}}
\\definecolor{chersotisGray}{HTML}{{$colorGray}}
\\definecolor{chersotisText}{HTML}{{$colorText}}
\\definecolor{chersotisLightGray}{HTML}{{$colorLight}}

% HYPERREF
\\hypersetup{
    colorlinks=true,
    linkcolor=chersotisTeal,
    urlcolor=chersotisTeal,
    citecolor=chersotisTeal,
    pdftitle={{$title}}
}

% SECTION STYLING
% Décision réunion Chersotis 2026-04-16 §10 :
% - H1 (\section) en vert Chersotis (chartier OREINA, préféré au bleu)
% - H2 (\subsection) en noir (pas de couleur)
% - Non-italique (caractères droits) pour que les noms d'espèces en italique
%   restent distincts
\\titleformat{\\section}
    {\\normalfont\\large\\bfseries\\color{chersotisTitleGreen}}
    {\\thesection.}{0.5em}{}

\\titleformat{\\subsection}
    {\\normalfont\\normalsize\\bfseries\\color{black}}
    {\\thesubsection.}{0.5em}{}

\\titlespacing*{\\section}{0pt}{{$sectionBefore}pt}{{$sectionAfter}pt}
\\titlespacing*{\\subsection}{0pt}{{$subsectionBefore}pt}{{$subsectionAfter}pt}

% HEADER & FOOTER
\\pagestyle{fancy}
\\fancyhf{}

% Header for pages 2+
\\fancyhead[L]{\\small\\textbf{\\textcolor{chersotisOrange}{{$headerLeft}}}}
\\fancyhead[R]{\\small\\textcolor{chersotisGray}{{$headerRight}}}
\\renewcommand{\\headrulewidth}{0.5pt}
\\renewcommand{\\headrule}{\\hbox to\\headwidth{\\color{chersotisTeal}\\leaders\\hrule height \\headrulewidth\\hfill}}
PREAMBLE;
    }

    /**
     * Generate LaTeX content
     */
    protected function generateLatexContent(Submission $submission): string
    {
        $author = $submission->author;
        $issue = $submission->journalIssue;
        $editor = $submission->editor;

        // Prepare data
        $title = $this->escapeLatex($submission->title);
        // Prefer edited/final version (display_abstract) over original submission (abstract); convert HTML tags to LaTeX
        $abstractSource = $submission->display_abstract ?? $submission->abstract ?? '';
        $abstract = $this->convertHtmlToLatex($abstractSource);
        $authorName = $this->escapeLatex($author?->name ?? 'Auteur');
        $authorEmail = $author?->email ?? '';
        $editorName = $editor ? $this->escapeLatex($editor->name) : '';

        // Correspondance auteur: prefer first entry from author_affiliations (reflects actual first author of the article)
        // Expected format: "Prénom NOM : affiliation, email@example.org"
        $correspondenceName = $authorName;  // escaped fallback
        $correspondenceEmail = $authorEmail;
        if (is_array($submission->author_affiliations) && count($submission->author_affiliations) > 0) {
            $firstAffil = (string) $submission->author_affiliations[0];
            if (preg_match('/^(.+?)\s*:\s*(.*)$/', $firstAffil, $parts)) {
                $parsedName = trim($parts[1]);
                $parsedRest = $parts[2];
                if ($parsedName !== '') {
                    $correspondenceName = $this->escapeLatex($parsedName);
                }
                // Email — first @-containing token
                if (preg_match('/([^\s,;]+@[^\s,;]+\.[^\s,;]+)/', $parsedRest, $emailMatch)) {
                    $correspondenceEmail = trim($emailMatch[1]);
                }
            }
        }

        // Affiliations
        $affiliations = $submission->author_affiliations ?? [];
        if (empty($affiliations) && $author?->affiliation) {
            $affiliations = [$author->affiliation];
        }

        // Keywords
        $keywords = $this->formatKeywords($submission->keywords);

        // Dates
        $receivedDate = $this->formatDate($submission->received_at ?? $submission->submitted_at);
        $acceptedDate = $this->formatDate($submission->accepted_at ?? $submission->decision_at);
        $publishedDate = $this->formatDate($submission->published_at);
        $year = $submission->published_at?->format('Y') ?? now()->format('Y');

        // Volume info
        $volumeInfo = '';
        if ($issue) {
            $volumeInfo = 'Tome ' . $issue->volume_number;
            if ($submission->start_page && $submission->end_page) {
                $volumeInfo .= ', pp. ' . $submission->start_page . "\u{2013}" . $submission->end_page;
            }
        }

        // DOI
        $doi = $submission->doi ?? '';
        $doiUrl = $doi ? 'https://doi.org/' . $doi : '';

        // Short title
        $shortTitle = $this->escapeLatex(Str::limit($submission->title, 50));

        // Harvard citation for sidebar
        $harvardCitation = app(\App\Services\CitationExportService::class)->toHarvard($submission);
        $harvardCitationLatex = $this->escapeLatex($harvardCitation);

        // title_en (optional)
        $titleEn = $this->escapeLatex($submission->title_en ?? '');

        // Summary (optional)
        $displaySummarySource = $submission->display_summary ?? '';
        $displaySummary = $this->convertHtmlToLatex($displaySummarySource);

        // Supplementary files
        $supplementaryFiles = [];
        if (is_array($submission->supplementary_files)) {
            foreach ($submission->supplementary_files as $suppFile) {
                $name = $suppFile['name'] ?? ($suppFile['filename'] ?? basename($suppFile['path'] ?? 'fichier'));
                $url = $suppFile['url'] ?? (isset($suppFile['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($suppFile['path']) : '');
                if ($url) {
                    $supplementaryFiles[] = [
                        'name' => $this->escapeLatex($name),
                        'url' => $url,
                    ];
                }
            }
        }

        // ORCID list
        $showOrcid = config('journal.show_orcid', false);
        $authorOrcids = [];
        if ($showOrcid) {
            if ($author && !empty($author->orcid)) {
                $authorOrcids[] = ['name' => $this->escapeLatex($author->name), 'orcid' => $author->orcid];
            }
            if (is_array($submission->co_authors)) {
                foreach ($submission->co_authors as $co) {
                    if (!empty($co['orcid']) && !empty($co['name'])) {
                        $authorOrcids[] = [
                            'name' => $this->escapeLatex($co['name']),
                            'orcid' => $co['orcid'],
                        ];
                    }
                }
            }
        }

        // Asset filenames — these files are copied to the temp compilation dir
        // by copyImages() (local) or collectImagesForApi() (remote) — see Task 10
        $papillonLogoPath = 'oreina-papillon.png';
        $oreinaLogoPath = 'oreina-noir-ligne.png';
        $openAccessLogoPath = 'open-access.png';
        $ccbyLogoPath = 'cc-by-4.0.png';

        $copyrightYear = $submission->published_at?->format('Y') ?? now()->format('Y');

        // Build ESM LaTeX (conditional)
        $esmLatex = '';
        if (!empty($supplementaryFiles)) {
            $esmLatex = "\n    \\vspace{6pt}\\color{gray!30}\\hrule\\vspace{6pt}\\color{black}\n" .
                        "    \\textbf{\\textcolor{chersotisTeal}{Matériel supplémentaire}}\\\\\n" .
                        "    Disponible en ligne :\\\\\n";
            foreach ($supplementaryFiles as $sf) {
                $esmLatex .= "    \\href{{$sf['url']}}{\\textcolor{chersotisTeal}{{$sf['name']}}}\\\\\n";
            }
        }

        // Build ORCID LaTeX (conditional)
        $orcidLatex = '';
        if (!empty($authorOrcids)) {
            $orcidLatex = "\n    \\vspace{6pt}\\color{gray!30}\\hrule\\vspace{6pt}\\color{black}\n" .
                          "    \\textbf{\\textcolor{chersotisTeal}{ORCID}}\\\\\n";
            foreach ($authorOrcids as $ao) {
                $orcidUrl = 'https://orcid.org/' . $ao['orcid'];
                $orcidLatex .= "    {$ao['name']} : \\href{{$orcidUrl}}{\\textcolor{chersotisTeal}{\\small {$ao['orcid']}}}\\\\\n";
            }
        }

        // Wrap ESM + ORCID in a single conditional outer separator block.
        // When both are empty the outer \vspace+\hrule+\vspace block must NOT be emitted,
        // otherwise it produces ~57 pt of blank space that pushes the sidebar past \textheight.
        $esmOrcidBlock = '';
        if (!empty($esmLatex) || !empty($orcidLatex)) {
            $esmOrcidBlock = "\n    \\vspace{14pt}\\color{gray!30}\\hrule\\vspace{14pt}\\color{black}\n" .
                             $esmLatex .
                             $orcidLatex .
                             "\n";
        }

        // Build affiliations LaTeX — numeric superscripts (1, 2, 3)
        $affiliationsLatex = '';
        foreach ($affiliations as $index => $aff) {
            $affiliationsLatex .= "\\textsuperscript{" . ($index + 1) . "}" . $this->escapeLatex($aff) . "\\\\\n";
        }

        // Build authors line for right column display
        $authorsDisplay = $submission->display_authors ?? $authorName;
        $authorsDisplayEscaped = $this->escapeLatex($authorsDisplay);

        // Build Summary box LaTeX (conditional)
        $summaryBoxLatex = '';
        if (!empty($displaySummary)) {
            $titleEnLine = '';
            if (!empty($titleEn)) {
                $titleEnLine = "{\\normalsize\\textbf{{$titleEn}}}\\\\[4pt]\n";
            }

            $summaryBoxLatex = "\n    \\vspace{8pt}\n" .
                "    \\colorbox{chersotisLightGray}{%\n" .
                "        \\parbox{0.95\\linewidth}{%\n" .
                "            \\vspace{6pt}\n" .
                "            {\\small\\textbf{\\textcolor{chersotisTeal}{Summary}}}\\\\[4pt]\n" .
                "            {$titleEnLine}" .
                "            {\\small\\justifying {$displaySummary}}\n" .
                "            \\vspace{6pt}\n" .
                "        }%\n" .
                "    }\n";
        }

        // Build content blocks
        $contentLatex = $this->buildContentBlocks($submission->content_blocks ?? []);

        // Build acknowledgements
        $acknowledgementsLatex = '';
        if ($submission->acknowledgements) {
            $acknowledgementsLatex = "
\\vspace{12pt}
\\hrule
\\vspace{8pt}

{\\normalsize\\textbf{\\textcolor{chersotisTeal}{Remerciements}}}

\\vspace{4pt}

{\\small " . $this->escapeLatex($submission->acknowledgements) . "}
";
        }

        // Supplementary material section in body (before References)
        $supplementaryBodyLatex = '';
        if (!empty($supplementaryFiles)) {
            $supplementaryBodyLatex = "\n\\vspace{12pt}\\hrule\\vspace{8pt}\n" .
                "{\\normalsize\\textbf{\\textcolor{chersotisDarkTeal}{Matériel supplémentaire}}}\n\n" .
                "\\vspace{4pt}\n\n{\\small\n" .
                "\\begin{description}[style=unboxed,leftmargin=1.5em,labelsep=0pt,font=\\normalfont]\n";
            foreach ($supplementaryFiles as $sf) {
                $supplementaryBodyLatex .= "\\item \\href{{$sf['url']}}{\\textcolor{chersotisTeal}{{$sf['name']}}}\n";
            }
            $supplementaryBodyLatex .= "\\end{description}\n}\n";
        }

        // Build references
        $referencesLatex = '';
        if (!empty($submission->references)) {
            $referencesLatex = "
\\vspace{12pt}
\\hrule
\\vspace{8pt}

{\\normalsize\\textbf{\\textcolor{chersotisTeal}{Références}}}

\\vspace{6pt}

{\\small
\\begin{description}[style=unboxed,leftmargin=1.5em,labelsep=0pt,font=\\normalfont]
";
            foreach ($submission->references as $ref) {
                $referencesLatex .= "\\item " . $this->escapeLatex($ref) . "\n";
            }
            $referencesLatex .= "\\end{description}\n}\n";
        }

        // Get config values for document body
        $journal = $this->getConfig('journal', []);
        $journalName = $journal['name'] ?? 'Chersotis';
        $journalSubtitle = $journal['subtitle'] ?? 'By oreina';
        $issnPrint = $journal['issn_print'] ?? '0044-586X';
        $issnElectronic = $journal['issn_electronic'] ?? '2107-7207';
        $license = $journal['license'] ?? 'Creative Commons CC-BY 4.0';

        $firstPage = $this->getConfig('first_page', []);
        $leftColWidth = $firstPage['left_column_width'] ?? 0.28;
        $rightColWidth = $firstPage['right_column_width'] ?? 0.68;

        $sizes = $this->getConfig('sizes', []);
        $journalTitleSize = $sizes['journal_title'] ?? 22;
        $articleTitleSize = $sizes['article_title'] ?? 15;

        // Main content geometry (décision réunion 2026-04-16 §10 : marge gauche
        // élargie pour ~130 mm de largeur utile au lieu de 160 mm).
        $bodyMargins = $this->getConfig('main_content_margins', []);
        $bodyTop = $bodyMargins['top'] ?? 22;
        $bodyBottom = $bodyMargins['bottom'] ?? 28;
        $bodyLeft = $bodyMargins['left'] ?? 60;
        $bodyRight = $bodyMargins['right'] ?? 20;

        // Body alignment (décision réunion 2026-04-16 §10 : aligné à gauche,
        // non justifié — meilleure lisibilité, accessibilité dyslexie).
        $bodyAlignmentCmd = $this->getConfig('body_alignment', 'ragged') === 'ragged'
            ? '\\RaggedRight'
            : '\\justifying';

        // Build preamble
        $preamble = $this->generatePreamble($title);

        // Build the complete LaTeX document
        return <<<LATEX
{$preamble}

% Footer
\\fancyfoot[L]{\\footnotesize\\textcolor{chersotisGray}{{$authorName} ({$year}), \\textbf{\\textcolor{chersotisTeal}{Chersotis}} {$volumeInfo}. \\url{{$doiUrl}}}}
\\fancyfoot[R]{\\footnotesize\\textcolor{chersotisGray}{\\thepage}}
\\renewcommand{\\footrulewidth}{0.3pt}

% First page style — license footer
\\fancypagestyle{firstpage}{
    \\fancyhf{}
    \\renewcommand{\\headrulewidth}{0pt}
    \\fancyfoot[C]{%
        \\parbox{0.95\\textwidth}{%
            \\color{gray!40}\\hrule\\vspace{4pt}\\color{chersotisGray}
            \\centering\\footnotesize
            \\textcopyright\\ {$copyrightYear} OREINA. Publié par l'association OREINA sous licence
            Creative Commons Attribution CC BY 4.0, qui autorise toute utilisation,
            distribution et reproduction, à condition que l'auteur et la source originale soient cités.\\\\
            \\url{https://creativecommons.org/licenses/by/4.0/}
        }%
    }
    \\renewcommand{\\footrulewidth}{0pt}
}

\\begin{document}
\\thispagestyle{firstpage}

% HEADER - TWO COLUMNS
\\noindent
\\begin{minipage}[t][\\textheight][t]{{$leftColWidth}\\textwidth}
    \\raggedright

    % Papillon logo
    \\includegraphics[width=0.7\\linewidth]{{$papillonLogoPath}}\\\\[6pt]

    % Chersotis wordmark
    {\\fontsize{{$journalTitleSize}}{32}\\selectfont\\textbf{\\textcolor{chersotisOrange}{Chersotis}}}\\\\[2pt]

    % Revue URL
    {\\footnotesize\\textcolor{chersotisGray}{chersotis.oreina.org}}

    \\vspace{12pt}

    % Badges Open Access + CC BY
    \\includegraphics[height=24pt]{{$openAccessLogoPath}}\\hspace{4pt}%
    \\includegraphics[height=24pt]{{$ccbyLogoPath}}

    \\vspace{14pt}\\color{gray!30}\\hrule\\vspace{14pt}\\color{black}

    % Citation
    \\textbf{\\textcolor{chersotisTeal}{Citer cet article :}}\\\\[3pt]
    {\\footnotesize\\justifying {$harvardCitationLatex}}

    \\vspace{14pt}\\color{gray!30}\\hrule\\vspace{14pt}\\color{black}

    % Dates
    {\\small
    \\textbf{Reçu :} {$receivedDate}\\\\[2pt]
    \\textbf{Accepté :} {$acceptedDate}\\\\[2pt]
    \\textbf{Publié :} {$publishedDate}
    }

    \\vspace{14pt}\\color{gray!30}\\hrule\\vspace{14pt}\\color{black}

    % Keywords
    \\textbf{\\textcolor{chersotisTeal}{Mots-clés}}\\\\[2pt]
    {\\footnotesize {$keywords}}

{$esmOrcidBlock}
    \\vspace{14pt}\\color{gray!30}\\hrule\\vspace{14pt}\\color{black}

    % Correspondance
    \\textbf{\\textcolor{chersotisTeal}{Correspondance auteur}}\\\\
    {\\small {$correspondenceName}\\\\
    \\href{mailto:{$correspondenceEmail}}{\\textcolor{chersotisTeal}{{$correspondenceEmail}}}}

    \\vfill

    % Logo Oreina en bas (aligned left, sidebar already uses \\raggedright)
    \\includegraphics[width=0.6\\linewidth]{{$oreinaLogoPath}}

\\end{minipage}%
\\hfill
\\vrule width 0.5pt
\\hfill
\\begin{minipage}[t]{{$rightColWidth}\\textwidth}
    \\vspace*{-84pt}
    \\raggedright

    % Title — vert Chersotis, non-italique (réunion 2026-04-16 §10)
    {\\fontsize{18}{24}\\selectfont\\textbf{\\textcolor{chersotisTitleGreen}{{$title}}}\\par}

    \\vspace{18pt}

    % Authors with numeric superscripts
    {\\normalsize {$authorsDisplayEscaped}}

    \\vspace{6pt}

    % Affiliations
    {\\footnotesize\\textcolor{chersotisGray}{
{$affiliationsLatex}    }}

    \\vspace{22pt}

    % Abstract (French)
    \\colorbox{chersotisLightGray}{%
        \\parbox{0.95\\linewidth}{%
            \\vspace{6pt}
            {\\small\\textbf{\\textcolor{chersotisTeal}{Résumé}}}\\\\[4pt]
            {\\small\\justifying {$abstract}}
            \\vspace{6pt}
        }%
    }
{$summaryBoxLatex}
\\end{minipage}

\\newpage
\\newgeometry{top={$bodyTop}mm,bottom={$bodyBottom}mm,left={$bodyLeft}mm,right={$bodyRight}mm,headheight=15pt,footskip=18mm}

% MAIN CONTENT (body alignment scopé au contenu, restaure auto après)
\\begingroup
{$bodyAlignmentCmd}
{$contentLatex}
\\endgroup

{$acknowledgementsLatex}

{$supplementaryBodyLatex}

{$referencesLatex}

\\end{document}
LATEX;
    }

    /**
     * Build content blocks LaTeX
     */
    protected function buildContentBlocks(array $blocks): string
    {
        $latex = '';
        $imageIndex = 0;

        foreach ($blocks as $block) {
            $type = $block['type'] ?? 'paragraph';
            $content = $block['content'] ?? '';

            switch ($type) {
                case 'heading':
                    $latex .= "\n\\section*{" . $this->escapeLatex($content) . "}\n\n";
                    break;

                case 'subheading':
                    $latex .= "\n\\subsection*{" . $this->escapeLatex($content) . "}\n\n";
                    break;

                case 'paragraph':
                    $latex .= $this->convertHtmlToLatex($content) . "\n\n";
                    break;

                case 'list':
                    $items = $block['items'] ?? [];
                    if (empty($items) && !empty($content)) {
                        $items = array_filter(array_map('trim', explode("\n", $content)));
                    }
                    if (!empty($items)) {
                        $latex .= "\\begin{itemize}[nosep,leftmargin=1.5em]\n";
                        foreach ($items as $item) {
                            $latex .= "    \\item " . $this->escapeLatex($item) . "\n";
                        }
                        $latex .= "\\end{itemize}\n\n";
                    }
                    break;

                case 'image':
                    $imagePath = $block['url'] ?? $block['src'] ?? '';
                    $caption = $block['caption'] ?? '';
                    $align = $block['align'] ?? 'center';
                    $width = $block['width'] ?? 'auto';

                    if (!empty($imagePath)) {
                        $filename = $this->getImageFilename($imagePath, $imageIndex);
                        if ($filename) {
                            $imageIndex++;

                            // Calculate width
                            $widthParam = '0.9\\textwidth';
                            if ($width !== 'auto' && is_numeric($width)) {
                                $widthParam = ($width / 100) . '\\textwidth';
                            } elseif ($align === 'full') {
                                $widthParam = '\\textwidth';
                            }

                            $latex .= "\\begin{figure}[htbp]\n";

                            // Alignment
                            switch ($align) {
                                case 'left':
                                    $latex .= "    \\raggedright\n";
                                    break;
                                case 'right':
                                    $latex .= "    \\raggedleft\n";
                                    break;
                                default:
                                    $latex .= "    \\centering\n";
                            }

                            $latex .= "    \\includegraphics[width={$widthParam},keepaspectratio]{{$filename}}\n";
                            if (!empty($caption)) {
                                $latex .= "    \\caption{" . $this->escapeLatex($caption) . "}\n";
                            }
                            $latex .= "\\end{figure}\n\n";
                        }
                    }
                    break;

                case 'table':
                    $headers = $block['headers'] ?? [];
                    $rows = $block['rows'] ?? [];
                    $caption = $block['caption'] ?? '';

                    if (!empty($headers)) {
                        $numCols = count($headers);
                        $colSpec = str_repeat('l', $numCols);

                        $latex .= "\\begin{table}[htbp]\n";
                        $latex .= "    \\centering\n";
                        $latex .= "    \\begin{tabular}{|" . implode('|', array_fill(0, $numCols, 'l')) . "|}\n";
                        $latex .= "    \\hline\n";

                        // Headers
                        $headerCells = array_map(fn($h) => "\\textbf{" . $this->escapeLatex($h) . "}", $headers);
                        $latex .= "    " . implode(' & ', $headerCells) . " \\\\\n";
                        $latex .= "    \\hline\n";

                        // Rows
                        foreach ($rows as $row) {
                            $rowCells = array_map(fn($c) => $this->escapeLatex($c), $row);
                            $latex .= "    " . implode(' & ', $rowCells) . " \\\\\n";
                        }

                        $latex .= "    \\hline\n";
                        $latex .= "    \\end{tabular}\n";

                        if (!empty($caption)) {
                            $latex .= "    \\caption{" . $this->escapeLatex($caption) . "}\n";
                        }

                        $latex .= "\\end{table}\n\n";
                    }
                    break;

                case 'quote':
                    $latex .= "\\begin{quote}\n";
                    $latex .= "\\textit{" . $this->escapeLatex($content) . "}\n";
                    $latex .= "\\end{quote}\n\n";
                    break;
            }
        }

        return $latex;
    }

    /**
     * Get image filename based on source type
     */
    protected function getImageFilename(string $imagePath, int $index): ?string
    {
        // Handle data URLs (base64 embedded images)
        if (Str::startsWith($imagePath, 'data:image/')) {
            if (preg_match('/^data:image\/(\w+);base64,/', $imagePath, $matches)) {
                $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
                return "image_" . ($index + 1) . ".{$extension}";
            }
            return null;
        }

        // Handle storage paths
        if (Str::startsWith($imagePath, '/storage/')) {
            return basename($imagePath);
        }

        // Handle regular paths
        if (!Str::startsWith($imagePath, 'http')) {
            return basename($imagePath);
        }

        // External URLs not supported
        return null;
    }

    /**
     * Escape LaTeX special characters
     */
    protected function escapeLatex(string $text): string
    {
        $replacements = [
            '\\' => '\\textbackslash{}',
            '{' => '\\{',
            '}' => '\\}',
            '$' => '\\$',
            '&' => '\\&',
            '%' => '\\%',
            '#' => '\\#',
            '_' => '\\_',
            '^' => '\\textasciicircum{}',
            '~' => '\\textasciitilde{}',
        ];

        $text = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $text
        );

        // Greek letters and special characters
        $text = str_replace(
            ['α', 'β', 'γ', 'δ', 'µ', '±', '°', '×', '÷', '≤', '≥', '≠', '∞'],
            ['$\\alpha$', '$\\beta$', '$\\gamma$', '$\\delta$', '$\\mu$', '$\\pm$', '$^\\circ$', '$\\times$', '$\\div$', '$\\leq$', '$\\geq$', '$\\neq$', '$\\infty$'],
            $text
        );

        return $text;
    }

    /**
     * Convert HTML formatting to LaTeX
     * Handles: <strong>, <em>, <u>, <sub>, <sup>
     */
    protected function convertHtmlToLatex(string $text): string
    {
        // Pre-process: convert <a> links to just their inner content (LaTeX doesn't render HTML links)
        $text = preg_replace('/<a\s[^>]*>(.*?)<\/a>/is', '$1', $text);

        // Pre-process: convert <span class="cite" ...> to just their inner content
        $text = preg_replace('/<span\s[^>]*>(.*?)<\/span>/is', '$1', $text);

        // Decode HTML entities before LaTeX processing
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Split text by HTML tags, process each part separately
        // This regex captures both the tags and the text between them
        $pattern = '/(<\/?(?:strong|em|b|i|u|sub|sup)>)/i';
        $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        $result = '';
        foreach ($parts as $part) {
            // Check if this part is an HTML tag
            if (preg_match('/^<\/?(?:strong|em|b|i|u|sub|sup)>$/i', $part)) {
                // Convert HTML tag to LaTeX
                $tag = strtolower($part);
                switch ($tag) {
                    case '<strong>':
                    case '<b>':
                        $result .= '\\textbf{';
                        break;
                    case '</strong>':
                    case '</b>':
                        $result .= '}';
                        break;
                    case '<em>':
                    case '<i>':
                        $result .= '\\textit{';
                        break;
                    case '</em>':
                    case '</i>':
                        $result .= '}';
                        break;
                    case '<u>':
                        $result .= '\\underline{';
                        break;
                    case '</u>':
                        $result .= '}';
                        break;
                    case '<sub>':
                        $result .= '\\textsubscript{';
                        break;
                    case '</sub>':
                        $result .= '}';
                        break;
                    case '<sup>':
                        $result .= '\\textsuperscript{';
                        break;
                    case '</sup>':
                        $result .= '}';
                        break;
                }
            } else {
                // Escape LaTeX special characters in text content
                $result .= $this->escapeLatex($part);
            }
        }

        return $result;
    }

    /**
     * Format keywords
     */
    protected function formatKeywords($keywords): string
    {
        if (is_array($keywords)) {
            return $this->escapeLatex(implode(' ; ', $keywords));
        }
        if (is_string($keywords)) {
            return $this->escapeLatex(str_replace(',', ' ;', $keywords));
        }
        return '';
    }

    /**
     * Format date
     */
    protected function formatDate($date): string
    {
        if (!$date) {
            return '—';
        }
        return $date->format('d/m/Y');
    }

    /**
     * Copy LaTeX logo assets (papillon, oreina, open-access, cc-by) to temp dir
     * for local compilation.
     */
    protected function copyLogoAssets(): void
    {
        $assets = [
            'oreina-papillon.png' => $this->getConfig('assets.papillon_logo'),
            'oreina-noir-ligne.png' => $this->getConfig('assets.oreina_logo'),
            'open-access.png' => $this->getConfig('assets.open_access'),
            'cc-by-4.0.png' => $this->getConfig('assets.cc_by'),
        ];

        foreach ($assets as $targetName => $sourcePath) {
            if ($sourcePath && file_exists($sourcePath)) {
                copy($sourcePath, $this->tempDir . '/' . $targetName);
            } else {
                Log::warning('LaTeX asset missing', ['target' => $targetName, 'path' => $sourcePath]);
            }
        }
    }

    /**
     * Copy images to temp directory (for local compilation)
     */
    protected function copyImages(Submission $submission): void
    {
        // Copy logo assets first
        $this->copyLogoAssets();

        $blocks = $submission->content_blocks ?? [];
        $imageIndex = 0;

        foreach ($blocks as $block) {
            if (($block['type'] ?? '') !== 'image') {
                continue;
            }

            $imagePath = $block['url'] ?? $block['src'] ?? '';
            if (empty($imagePath)) {
                continue;
            }

            $imageIndex++;

            // Handle data URLs (base64 embedded images)
            if (Str::startsWith($imagePath, 'data:image/')) {
                if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $imagePath, $matches)) {
                    $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
                    $filename = "image_{$imageIndex}.{$extension}";
                    $content = base64_decode($matches[2]);
                    file_put_contents($this->tempDir . '/' . $filename, $content);

                    Log::info('Data URL image saved for local compilation', ['filename' => $filename]);
                }
            }
            // Handle storage paths
            elseif (Str::startsWith($imagePath, '/storage/')) {
                $storagePath = str_replace('/storage/', '', $imagePath);
                $fullPath = storage_path('app/public/' . $storagePath);

                if (file_exists($fullPath)) {
                    $filename = basename($fullPath);
                    copy($fullPath, $this->tempDir . '/' . $filename);
                }
            }
        }
    }

    /**
     * Collect images as base64 resources for API compilation
     */
    protected function collectImagesForApi(Submission $submission): array
    {
        $images = [];

        // Include logo assets
        $assets = [
            'oreina-papillon.png' => $this->getConfig('assets.papillon_logo'),
            'oreina-noir-ligne.png' => $this->getConfig('assets.oreina_logo'),
            'open-access.png' => $this->getConfig('assets.open_access'),
            'cc-by-4.0.png' => $this->getConfig('assets.cc_by'),
        ];
        foreach ($assets as $targetName => $sourcePath) {
            if ($sourcePath && file_exists($sourcePath)) {
                $images[] = [
                    'path' => $targetName,
                    'file' => base64_encode(file_get_contents($sourcePath)),
                ];
            }
        }

        $blocks = $submission->content_blocks ?? [];
        $imageIndex = 0;

        foreach ($blocks as $block) {
            if (($block['type'] ?? '') !== 'image') {
                continue;
            }

            $imagePath = $block['url'] ?? $block['src'] ?? '';
            if (empty($imagePath)) {
                continue;
            }

            $imageIndex++;
            $filename = null;
            $base64Content = null;

            // Handle data URLs (base64 embedded images)
            if (Str::startsWith($imagePath, 'data:image/')) {
                // Extract mime type and base64 data
                if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $imagePath, $matches)) {
                    $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
                    $filename = "image_{$imageIndex}.{$extension}";
                    $base64Content = $matches[2];

                    Log::info('Data URL image found', ['filename' => $filename]);
                }
            }
            // Handle storage paths
            elseif (Str::startsWith($imagePath, '/storage/')) {
                $storagePath = str_replace('/storage/', '', $imagePath);
                $fullPath = storage_path('app/public/' . $storagePath);

                if (file_exists($fullPath)) {
                    $filename = basename($fullPath);
                    $base64Content = base64_encode(file_get_contents($fullPath));
                }
            }
            // Handle external URLs
            elseif (Str::startsWith($imagePath, 'http')) {
                Log::warning('External image URL not supported in API mode', ['url' => $imagePath]);
                continue;
            }

            if ($filename && $base64Content) {
                $images[] = [
                    'path' => $filename,
                    'file' => $base64Content,
                ];

                Log::info('Image added to API request', [
                    'filename' => $filename,
                    'size' => strlen($base64Content),
                ]);
            }
        }

        return $images;
    }

    /**
     * Compile LaTeX
     */
    protected function compileLaTeX(string $texFile): void
    {
        // Change to temp directory for compilation
        $currentDir = getcwd();
        chdir($this->tempDir);

        // Build command with proper escaping for Windows
        $cmd = sprintf(
            '"%s" -interaction=nonstopmode "%s" 2>&1',
            $this->pdflatexPath,
            basename($texFile)
        );

        // Execute
        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        // Restore directory
        chdir($currentDir);

        $outputStr = implode("\n", $output);

        if ($returnCode !== 0) {
            Log::warning('LaTeX compilation warnings', [
                'return_code' => $returnCode,
                'output' => $outputStr,
            ]);
        }

        // Check PDF exists
        $pdfFile = $this->tempDir . '/article.pdf';
        if (!file_exists($pdfFile)) {
            // Read log file for detailed error
            $logFile = $this->tempDir . '/article.log';
            $logContent = file_exists($logFile) ? file_get_contents($logFile) : 'No log file';

            // Extract error lines from log
            $errorLines = [];
            if (preg_match_all('/^!.*$/m', $logContent, $matches)) {
                $errorLines = array_slice($matches[0], 0, 5);
            }

            Log::error('LaTeX compilation failed', [
                'return_code' => $returnCode,
                'output' => $outputStr,
                'tex_file' => $texFile,
                'temp_dir' => $this->tempDir,
                'error_lines' => $errorLines,
            ]);

            $errorMsg = !empty($errorLines)
                ? implode(' | ', $errorLines)
                : substr($outputStr, 0, 500);

            throw new \RuntimeException('LaTeX compilation failed: ' . $errorMsg);
        }
    }

    /**
     * Compile LaTeX via external API (for shared hosting)
     * Uses YtoTech LaTeX API: https://github.com/YtoTech/latex-on-http
     */
    protected function compileViaApi(string $texContent, Submission $submission): void
    {
        $apiUrl = 'https://latex.ytotech.com/builds/sync';
        Log::info('Compiling LaTeX via YtoTech API');

        try {
            // Build HTTP client
            $http = Http::timeout($this->apiTimeout);

            // Disable SSL verification in development (Windows cert issues)
            if (config('app.debug') && PHP_OS_FAMILY === 'Windows') {
                $http = $http->withoutVerifying();
            }

            // Build resources array with main .tex file
            $resources = [
                [
                    'main' => true,
                    'content' => $texContent,
                ]
            ];

            // Add images as base64-encoded resources
            $images = $this->collectImagesForApi($submission);
            foreach ($images as $image) {
                $resources[] = $image;
            }

            Log::info('LaTeX API request', [
                'images_count' => count($images),
                'image_names' => array_column($images, 'path'),
            ]);

            // YtoTech API expects JSON with compiler and resources
            $response = $http->post($apiUrl, [
                'compiler' => 'pdflatex',
                'resources' => $resources,
            ]);

            if (!$response->successful()) {
                $errorBody = $response->json() ?? $response->body();
                Log::error('LaTeX API error', [
                    'status' => $response->status(),
                    'body' => is_array($errorBody) ? json_encode($errorBody) : substr($errorBody, 0, 500),
                ]);

                $errorMsg = is_array($errorBody) && isset($errorBody['error'])
                    ? $errorBody['error']
                    : 'Status ' . $response->status();

                throw new \RuntimeException('LaTeX API error: ' . $errorMsg);
            }

            // Check if response is PDF (starts with %PDF)
            $body = $response->body();
            if (!str_starts_with($body, '%PDF')) {
                // Might be JSON error response
                $json = $response->json();
                if ($json && isset($json['error'])) {
                    throw new \RuntimeException('LaTeX compilation error: ' . $json['error']);
                }
                if ($json && isset($json['logs'])) {
                    Log::error('LaTeX API compilation logs', ['logs' => $json['logs']]);
                    throw new \RuntimeException('LaTeX compilation failed. Check logs.');
                }

                Log::error('LaTeX API did not return PDF', [
                    'response_start' => substr($body, 0, 200),
                ]);
                throw new \RuntimeException('LaTeX API did not return a valid PDF');
            }

            // Save PDF to temp directory
            $pdfFile = $this->tempDir . '/article.pdf';
            file_put_contents($pdfFile, $body);

            Log::info('LaTeX API compilation successful', ['size' => strlen($body)]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('LaTeX API connection error', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Cannot connect to LaTeX API: ' . $e->getMessage());
        }
    }

    /**
     * Move PDF to storage
     */
    protected function movePdfToStorage(Submission $submission): string
    {
        $pdfFile = $this->tempDir . '/article.pdf';
        $filename = $this->generateFilename($submission);
        $storagePath = 'submissions/pdfs/' . $filename;

        if ($submission->pdf_file && Storage::disk('public')->exists($submission->pdf_file)) {
            Storage::disk('public')->delete($submission->pdf_file);
        }

        Storage::disk('public')->put($storagePath, file_get_contents($pdfFile));

        return $storagePath;
    }

    /**
     * Generate filename
     */
    protected function generateFilename(Submission $submission): string
    {
        $slug = Str::slug($submission->title);
        $slug = substr($slug, 0, 50);

        $issue = $submission->journalIssue;
        if ($issue) {
            $prefix = "chersotis-{$issue->volume_number}-{$issue->issue_number}";
        } else {
            $prefix = "article";
        }

        return "{$prefix}-{$submission->id}-{$slug}.pdf";
    }

    /**
     * Cleanup temp files
     */
    protected function cleanup(): void
    {
        if (empty($this->tempDir) || !is_dir($this->tempDir)) {
            return;
        }

        $files = glob($this->tempDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        @rmdir($this->tempDir);
    }

    /**
     * Stream PDF
     */
    public function stream(Submission $submission): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $pdfPath = $this->generatePdf($submission);
        $fullPath = storage_path('app/public/' . $pdfPath);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($pdfPath) . '"',
        ]);
    }

    /**
     * Download PDF
     */
    public function download(Submission $submission): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $pdfPath = $this->generatePdf($submission);
        $fullPath = storage_path('app/public/' . $pdfPath);

        return response()->download($fullPath, basename($pdfPath));
    }

    /**
     * Check if can generate PDF
     */
    public function canGeneratePdf(Submission $submission): bool
    {
        return in_array($submission->status, [
            SubmissionStatus::Accepted,
            SubmissionStatus::InProduction,
            SubmissionStatus::Published,
        ]);
    }

    /**
     * Get PDF URL
     */
    public function getPdfUrl(Submission $submission): ?string
    {
        if ($submission->pdf_file && Storage::disk('public')->exists($submission->pdf_file)) {
            return Storage::url($submission->pdf_file);
        }
        return null;
    }
}
