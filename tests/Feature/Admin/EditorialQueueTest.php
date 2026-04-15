<?php

namespace Tests\Feature\Admin;

use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialQueueTest extends TestCase
{
    use RefreshDatabase;

    private function makeEditor(): User
    {
        // role=editor (for admin middleware : isEditor() returns true via capability)
        $u = User::factory()->create(['email_verified_at' => now(), 'role' => 'editor']);
        $u->grantCapability(EditorialCapability::EDITOR);
        return $u;
    }

    private function makeChief(): User
    {
        $u = User::factory()->create(['email_verified_at' => now(), 'role' => 'admin']);
        $u->grantCapability(EditorialCapability::CHIEF_EDITOR);
        return $u;
    }

    private function makeRandom(): User
    {
        // role=user, no capabilities : isEditor() + isAdmin() both false → admin middleware rejects
        return User::factory()->create(['email_verified_at' => now(), 'role' => 'user']);
    }

    private function makeUnassignedSubmission(?string $title = null): Submission
    {
        static $counter = 0;
        $counter++;
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => $title ?? ('Article Test ' . $counter),
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => Submission::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function test_editor_sees_queue_and_take_button(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeUnassignedSubmission();

        $this->actingAs($editor)
            ->get(route('admin.journal.queue.index'))
            ->assertOk()
            ->assertSee('Prendre en charge')
            ->assertSee($sub->title);
    }

    public function test_random_user_gets_403_or_redirect_on_queue(): void
    {
        $random = $this->makeRandom();

        $response = $this->actingAs($random)->get(route('admin.journal.queue.index'));
        // Admin middleware may reject with 403 or redirect to admin login.
        $this->assertContains($response->status(), [302, 403]);
    }

    public function test_editor_can_take_an_unassigned_submission(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeUnassignedSubmission();

        $this->actingAs($editor)
            ->post(route('admin.journal.queue.take', $sub))
            ->assertRedirect();

        $this->assertSame($editor->id, $sub->fresh()->editor_id);
    }

    public function test_cannot_take_already_assigned(): void
    {
        $first = $this->makeEditor();
        $second = $this->makeEditor();
        $sub = $this->makeUnassignedSubmission();
        $sub->update(['editor_id' => $first->id]);

        $this->actingAs($second)
            ->post(route('admin.journal.queue.take', $sub))
            ->assertForbidden();
    }

    public function test_chief_can_assign_an_editor(): void
    {
        $chief = $this->makeChief();
        $editor = $this->makeEditor();
        $sub = $this->makeUnassignedSubmission();

        $this->actingAs($chief)
            ->post(route('admin.journal.queue.assign', $sub), [
                'user_id' => $editor->id,
            ])
            ->assertRedirect();

        $this->assertSame($editor->id, $sub->fresh()->editor_id);
    }

    public function test_editor_dashboard_shows_own_articles(): void
    {
        $editor = $this->makeEditor();
        $mine = $this->makeUnassignedSubmission('Article Mine Unique Title');
        $other = $this->makeUnassignedSubmission('Article Other Distinct Title');
        $mine->update(['editor_id' => $editor->id]);

        $this->actingAs($editor)
            ->get(route('admin.journal.mine'))
            ->assertOk()
            ->assertSee($mine->title)
            ->assertDontSee($other->title);
    }
}
