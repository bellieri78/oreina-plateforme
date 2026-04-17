<?php

namespace App\Services;

use App\Models\Submission;

class CitationExportService
{
    public function toBibtex(Submission $submission): string
    {
        $submission->load(['author', 'journalIssue']);
        $year = $submission->published_at?->format('Y') ?? date('Y');
        $authorName = $submission->author?->name ?? 'Unknown';
        $key = strtolower(explode(' ', $authorName)[0]) . $year;
        $title = str_replace(['{', '}'], '', $submission->title);

        $lines = [
            "@article{{$key},",
            "  author = {{$authorName}},",
            "  title = {{$title}},",
            "  journal = {Chersotis},",
            "  year = {{$year}},",
        ];

        if ($co = $submission->co_authors) {
            if (is_array($co) && count($co) > 0) {
                $coNames = collect($co)->pluck('name')->filter()->join(' and ');
                if ($coNames) {
                    $lines[1] = "  author = {{$authorName} and {$coNames}},";
                }
            }
        }

        if ($submission->journalIssue) {
            $lines[] = "  volume = {{$submission->journalIssue->volume_number}},";
        }
        if ($submission->start_page && $submission->end_page) {
            $lines[] = "  pages = {{$submission->start_page}--{$submission->end_page}},";
        }
        if ($submission->doi) {
            $lines[] = "  doi = {{$submission->doi}}";
        }
        $lines[] = '}';

        return implode("\n", $lines) . "\n";
    }

    public function toRis(Submission $submission): string
    {
        $submission->load(['author', 'journalIssue']);
        $year = $submission->published_at?->format('Y') ?? date('Y');

        $lines = [
            'TY  - JOUR',
            'AU  - ' . ($submission->author?->name ?? 'Unknown'),
        ];

        if ($co = $submission->co_authors) {
            if (is_array($co)) {
                foreach ($co as $coAuthor) {
                    if (!empty($coAuthor['name'])) {
                        $lines[] = 'AU  - ' . $coAuthor['name'];
                    }
                }
            }
        }

        $lines[] = 'TI  - ' . $submission->title;
        $lines[] = 'JO  - Chersotis';

        if ($submission->journalIssue) {
            $lines[] = 'VL  - ' . $submission->journalIssue->volume_number;
        }
        if ($submission->start_page) {
            $lines[] = 'SP  - ' . $submission->start_page;
        }
        if ($submission->end_page) {
            $lines[] = 'EP  - ' . $submission->end_page;
        }
        $lines[] = 'PY  - ' . $year;
        if ($submission->doi) {
            $lines[] = 'DO  - ' . $submission->doi;
        }
        $lines[] = 'ER  -';

        return implode("\r\n", $lines) . "\r\n";
    }

    public function toHarvard(Submission $submission): string
    {
        $submission->load(['author', 'journalIssue']);
        $year = $submission->published_at?->format('Y') ?? date('Y');

        // Prefer display_authors (editor-curated, reflects actual article) over structured author + co_authors
        $displayAuthors = trim((string) ($submission->display_authors ?? ''));
        if ($displayAuthors !== '') {
            $names = array_values(array_filter(array_map('trim', explode(',', $displayAuthors))));
            $shortList = array_filter(array_map([$this, 'formatAuthorShort'], $names));
            $shortList = array_values($shortList);
            if (count($shortList) === 1) {
                $shortAuthor = $shortList[0];
            } elseif (count($shortList) === 2) {
                $shortAuthor = $shortList[0] . ' et ' . $shortList[1];
            } else {
                $last = array_pop($shortList);
                $shortAuthor = implode(', ', $shortList) . ' et ' . $last;
            }
        } else {
            // Fallback: legacy path using User model + co_authors JSON
            $authorName = $submission->author?->name ?? 'Unknown';
            $shortAuthor = $this->formatAuthorShort($authorName);

            if ($co = $submission->co_authors) {
                if (is_array($co) && count($co) > 0) {
                    $coShort = collect($co)
                        ->filter(fn($c) => !empty($c['name']))
                        ->map(fn($c) => $this->formatAuthorShort((string) $c['name']))
                        ->filter()
                        ->values()
                        ->all();
                    if (!empty($coShort)) {
                        if (count($coShort) === 1) {
                            $shortAuthor .= ' et ' . $coShort[0];
                        } else {
                            $last = array_pop($coShort);
                            $shortAuthor .= ', ' . implode(', ', $coShort) . ' et ' . $last;
                        }
                    }
                }
            }
        }

        $citation = "{$shortAuthor} ({$year}). {$submission->title}. Chersotis";

        if ($submission->journalIssue) {
            $citation .= ", Tome {$submission->journalIssue->volume_number}";
            if ($submission->journalIssue->title) {
                $citation .= " — {$submission->journalIssue->title}";
            }
        }

        // Pagination (pp. X–Y or p. X if single page)
        if ($submission->start_page) {
            if ($submission->end_page && $submission->end_page != $submission->start_page) {
                $citation .= ', pp. ' . $submission->start_page . "\u{2013}" . $submission->end_page;
            } else {
                $citation .= ', p. ' . $submission->start_page;
            }
        }

        $citation .= '.';
        if ($submission->doi) {
            $citation .= " doi: {$submission->doi}";
        }

        return $citation;
    }

    /**
     * Parse a display-author name like "Jérome ROBIN" into "ROBIN, J."
     * Takes the last whitespace-separated token as surname, the rest as given names whose first letters become initials.
     */
    private function formatAuthorShort(string $fullName): string
    {
        $fullName = trim($fullName);
        if ($fullName === '') {
            return '';
        }
        $parts = preg_split('/\s+/', $fullName);
        $surname = array_pop($parts);
        $initials = collect($parts)
            ->map(fn($p) => strtoupper(mb_substr($p, 0, 1)) . '.')
            ->join(' ');
        return trim("{$surname}, {$initials}");
    }
}
