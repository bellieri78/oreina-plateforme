<?php

namespace Tests\Unit\Services;

use App\Models\JournalIssue;
use App\Models\Submission;
use App\Models\User;
use App\Services\CitationExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitationExportServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makePublishedSubmission(): Submission
    {
        $author = User::factory()->create(['name' => 'Jean Dupont', 'email_verified_at' => now()]);
        $issue = JournalIssue::create([
            'volume_number' => 1, 'issue_number' => 1, 'title' => 'Tome 1',
            'slug' => 'tome-1-test', 'year' => 2026, 'status' => 'published',
        ]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Distribution de Parnassius apollo dans les Pyrénées',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'published',
            'journal_issue_id' => $issue->id,
            'start_page' => 1, 'end_page' => 12,
            'doi' => '10.24349/chersotis.2026.0001',
            'published_at' => now(), 'submitted_at' => now(),
        ]);
    }

    public function test_bibtex_format(): void
    {
        $s = $this->makePublishedSubmission();
        $bib = (new CitationExportService())->toBibtex($s);

        $this->assertStringContainsString('@article{', $bib);
        $this->assertStringContainsString('author = {Jean Dupont}', $bib);
        $this->assertStringContainsString('journal = {Chersotis}', $bib);
        $this->assertStringContainsString('volume = {1}', $bib);
        $this->assertStringContainsString('pages = {1--12}', $bib);
        $this->assertStringContainsString('doi = {10.24349/chersotis.2026.0001}', $bib);
        $this->assertStringContainsString('year = {2026}', $bib);
    }

    public function test_ris_format(): void
    {
        $s = $this->makePublishedSubmission();
        $ris = (new CitationExportService())->toRis($s);

        $this->assertStringContainsString('TY  - JOUR', $ris);
        $this->assertStringContainsString('AU  - Jean Dupont', $ris);
        $this->assertStringContainsString('JO  - Chersotis', $ris);
        $this->assertStringContainsString('VL  - 1', $ris);
        $this->assertStringContainsString('SP  - 1', $ris);
        $this->assertStringContainsString('EP  - 12', $ris);
        $this->assertStringContainsString('DO  - 10.24349/chersotis.2026.0001', $ris);
        $this->assertStringContainsString('ER  -', $ris);
    }

    public function test_harvard_format(): void
    {
        $s = $this->makePublishedSubmission();
        $harvard = (new CitationExportService())->toHarvard($s);

        $this->assertStringContainsString('Dupont, J.', $harvard);
        $this->assertStringContainsString('(2026)', $harvard);
        $this->assertStringContainsString('Chersotis', $harvard);
        $this->assertStringContainsString('Tome 1', $harvard);
        $this->assertStringContainsString('doi:', $harvard);
    }

    public function test_uses_display_authors_when_set(): void
    {
        $s = $this->makePublishedSubmission();
        $s->display_authors = 'Jérome ROBIN, Alexis SANTALUCIA, Jérémy BUSCAIL, Baptiste CHARLOT';
        $s->save();

        $harvard = (new CitationExportService())->toHarvard($s->fresh());

        $this->assertStringContainsString('ROBIN, J.', $harvard);
        $this->assertStringContainsString('SANTALUCIA, A.', $harvard);
        $this->assertStringContainsString('BUSCAIL, J.', $harvard);
        $this->assertStringContainsString('CHARLOT, B.', $harvard);
        // Should NOT use the User account name (Jean Dupont)
        $this->assertStringNotContainsString('Dupont', $harvard);
    }

    public function test_falls_back_to_author_when_display_authors_empty(): void
    {
        $s = $this->makePublishedSubmission();
        $s->display_authors = null;
        $s->save();

        $harvard = (new CitationExportService())->toHarvard($s->fresh());

        $this->assertStringContainsString('Dupont, J.', $harvard);
    }

    public function test_includes_pagination_when_start_and_end_pages_set(): void
    {
        $s = $this->makePublishedSubmission();
        $s->start_page = 45;
        $s->end_page = 67;
        $s->save();

        $harvard = (new CitationExportService())->toHarvard($s->fresh());

        $this->assertStringContainsString('pp. 45', $harvard);
        $this->assertStringContainsString('67', $harvard);
    }

    public function test_includes_single_page_when_end_page_equals_start_page_or_null(): void
    {
        $s = $this->makePublishedSubmission();
        $s->start_page = 50;
        $s->end_page = null;
        $s->save();

        $harvard = (new CitationExportService())->toHarvard($s->fresh());

        $this->assertStringContainsString(', p. 50', $harvard);
        $this->assertStringNotContainsString('pp.', $harvard);
    }
}
