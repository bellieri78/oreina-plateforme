<?php

namespace Tests\Feature\Admin;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateLayoutTitleEnTest extends TestCase
{
    use RefreshDatabase;

    public function test_updateLayout_stores_title_en(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Titre français',
            'abstract' => '',
            'manuscript_file' => 'placeholder.pdf',
            'status' => SubmissionStatus::Accepted,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.submissions.layout.update', $submission), [
                'title_en' => 'English title',
                'display_authors' => 'Dupont J., Martin P.',
            ])
            ->assertRedirect();

        $this->assertEquals('English title', $submission->fresh()->title_en);
    }

    public function test_updateLayout_rejects_title_en_over_500_chars(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Titre',
            'abstract' => '',
            'manuscript_file' => 'placeholder.pdf',
            'status' => SubmissionStatus::Accepted,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.submissions.layout.update', $submission), [
                'title_en' => str_repeat('a', 501),
            ])
            ->assertSessionHasErrors('title_en');
    }
}
