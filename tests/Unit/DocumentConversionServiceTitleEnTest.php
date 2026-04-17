<?php

namespace Tests\Unit;

use App\Services\DocumentConversionService;
use Tests\TestCase;

class DocumentConversionServiceTitleEnTest extends TestCase
{
    private DocumentConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DocumentConversionService::class);
    }

    public function test_extracts_title_en_from_english_title_label(): void
    {
        $md = <<<MD
# Titre français

**English title:** Phylogenetic study of Lycaenidae

## Résumé

Lorem ipsum.
MD;

        $result = $this->service->enrichMarkdown($md);

        $this->assertEquals(
            'Phylogenetic study of Lycaenidae',
            $result['title_en'] ?? null
        );
    }

    public function test_extracts_title_en_from_title_en_label(): void
    {
        $md = <<<MD
# Titre français

**Title (EN):** Short English title

## Introduction
MD;

        $result = $this->service->enrichMarkdown($md);

        $this->assertEquals('Short English title', $result['title_en'] ?? null);
    }

    public function test_extracts_title_en_from_title_english_label(): void
    {
        $md = <<<MD
# Titre français

**Title (English):** A third variant
MD;

        $result = $this->service->enrichMarkdown($md);

        $this->assertEquals('A third variant', $result['title_en'] ?? null);
    }

    public function test_returns_empty_title_en_when_absent(): void
    {
        $md = <<<MD
# Titre français seul

## Résumé

Lorem ipsum.
MD;

        $result = $this->service->enrichMarkdown($md);

        $this->assertSame('', $result['title_en'] ?? 'missing');
    }
}
