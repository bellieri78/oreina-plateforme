<?php

namespace Tests\Feature\Admin;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportMarkdownReturnsTitleEnTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_markdown_returns_title_en_when_present(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $submission = Submission::create([
            'author_id' => $admin->id,
            'title' => 'tmp',
            'abstract' => '',
            'manuscript_file' => 'placeholder.pdf',
            'status' => SubmissionStatus::Accepted,
        ]);

        $markdown = <<<MD
# Titre français

**English title:** My English title

## Résumé

Lorem.
MD;

        $response = $this->actingAs($admin)
            ->postJson(route('admin.submissions.import-markdown', $submission), [
                'markdown_content' => $markdown,
            ]);

        $response->assertOk()
                 ->assertJson(['title_en' => 'My English title']);
    }

    public function test_import_markdown_returns_empty_title_en_when_absent(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $submission = Submission::create([
            'author_id' => $admin->id,
            'title' => 'tmp',
            'abstract' => '',
            'manuscript_file' => 'placeholder.pdf',
            'status' => SubmissionStatus::Accepted,
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.submissions.import-markdown', $submission), [
                'markdown_content' => "# Seul le titre FR\n\nLorem ipsum.",
            ]);

        $response->assertOk()
                 ->assertJson(['title_en' => '']);
    }
}
