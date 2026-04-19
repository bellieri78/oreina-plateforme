<?php

namespace Tests\Unit\Services;

use App\Services\ArticleLatexService;
use Tests\TestCase;

class ArticleLatexServicePreambleTest extends TestCase
{
    public function test_preamble_defines_dark_teal_color(): void
    {
        $service = app(ArticleLatexService::class);

        $reflection = new \ReflectionMethod($service, 'generatePreamble');
        $reflection->setAccessible(true);
        $output = $reflection->invoke($service, 'Test title');

        $this->assertStringContainsString(
            '\\definecolor{chersotisDarkTeal}{HTML}{0F766E}',
            $output
        );
    }

    public function test_section_uses_chersotis_title_green(): void
    {
        // Décision réunion 2026-04-16 §10 : sections H1 en vert Chersotis
        // (charte OREINA, préféré au bleu/teal)
        $service = app(ArticleLatexService::class);

        $reflection = new \ReflectionMethod($service, 'generatePreamble');
        $reflection->setAccessible(true);
        $output = $reflection->invoke($service, 'T');

        $this->assertStringContainsString(
            '\\color{chersotisTitleGreen}',
            $output
        );
    }

    public function test_preamble_defines_chersotis_title_green_color(): void
    {
        $service = app(ArticleLatexService::class);

        $reflection = new \ReflectionMethod($service, 'generatePreamble');
        $reflection->setAccessible(true);
        $output = $reflection->invoke($service, 'Test title');

        $this->assertStringContainsString(
            '\\definecolor{chersotisTitleGreen}{HTML}{2C5F2D}',
            $output
        );
    }

    public function test_header_left_uses_orange_wordmark(): void
    {
        $service = app(ArticleLatexService::class);

        $reflection = new \ReflectionMethod($service, 'generatePreamble');
        $reflection->setAccessible(true);
        $output = $reflection->invoke($service, 'T');

        $this->assertStringContainsString('\\textcolor{chersotisOrange}', $output);
    }
}
