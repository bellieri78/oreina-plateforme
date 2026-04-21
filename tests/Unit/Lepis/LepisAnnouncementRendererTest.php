<?php

namespace Tests\Unit\Lepis;

use App\Models\LepisBulletin;
use App\Services\LepisAnnouncementRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LepisAnnouncementRendererTest extends TestCase
{
    use RefreshDatabase;

    public function test_interpolates_bulletin_link_token(): void
    {
        $bulletin = LepisBulletin::create([
            'title' => 'Dossier hibernation',
            'issue_number' => 42,
            'quarter' => 'Q2',
            'year' => 2026,
            'pdf_path' => 'lepis/test.pdf',
            'status' => 'members',
            'announcement_subject' => 'Lepis n°42 vient de paraître',
            'announcement_body' => "Bonjour,\n\nLe nouveau numéro est en ligne : {{lien_bulletin}}\n\nBonne lecture.",
        ]);

        $rendered = (new LepisAnnouncementRenderer())->render($bulletin);

        $this->assertSame('Lepis n°42 vient de paraître', $rendered['subject']);
        $expectedUrl = route('hub.lepis.bulletins.show', $bulletin);
        $this->assertStringContainsString($expectedUrl, $rendered['body_html']);
        $this->assertStringNotContainsString('{{lien_bulletin}}', $rendered['body_html']);
    }

    public function test_renders_markdown_to_html(): void
    {
        $bulletin = LepisBulletin::create([
            'title' => 'T',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'lepis/test.pdf',
            'status' => 'members',
            'announcement_subject' => 'Hello',
            'announcement_body' => "# Titre\n\nTexte avec **gras** et *italique*.",
        ]);

        $rendered = (new LepisAnnouncementRenderer())->render($bulletin);

        $this->assertStringContainsString('<h1>Titre</h1>', $rendered['body_html']);
        $this->assertStringContainsString('<strong>gras</strong>', $rendered['body_html']);
        $this->assertStringContainsString('<em>italique</em>', $rendered['body_html']);
    }

    public function test_returns_empty_strings_when_template_not_filled(): void
    {
        $bulletin = LepisBulletin::create([
            'title' => 'T',
            'issue_number' => 1,
            'quarter' => 'Q1',
            'year' => 2026,
            'pdf_path' => 'lepis/test.pdf',
            'status' => 'members',
        ]);

        $rendered = (new LepisAnnouncementRenderer())->render($bulletin);

        $this->assertSame('', $rendered['subject']);
        $this->assertSame('', $rendered['body_html']);
    }
}
