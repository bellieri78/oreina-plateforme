<?php

namespace Tests\Feature\Admin;

use App\Mail\AccountInvitation;
use App\Models\EditorialCapability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackofficeSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private function makeEditor(string $capability = EditorialCapability::EDITOR): User
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user->grantCapability($capability);
        return $user;
    }

    private function basePayload(): array
    {
        return [
            'title' => 'Un article sur les Chersotis',
            'abstract' => str_repeat('x', 150),
            'keywords' => 'chersotis, noctuidae',
            'manuscript_file' => UploadedFile::fake()->create('manuscrit.docx', 500),
        ];
    }

    public function test_chief_editor_can_create_submission_for_existing_author(): void
    {
        Storage::fake('public');
        Mail::fake();

        $chief = $this->makeEditor(EditorialCapability::CHIEF_EDITOR);
        $author = User::factory()->create();

        $response = $this->actingAs($chief)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            ['author_mode' => 'existing', 'author_id' => $author->id],
        ));

        $response->assertRedirect();
        $this->assertDatabaseHas('submissions', [
            'author_id' => $author->id,
            'submitted_by_user_id' => $chief->id,
            'title' => 'Un article sur les Chersotis',
        ]);
        Mail::assertNothingQueued();
    }

    public function test_editor_can_create_submission_for_new_author(): void
    {
        Storage::fake('public');
        Mail::fake();

        $editor = $this->makeEditor(EditorialCapability::EDITOR);

        $response = $this->actingAs($editor)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            [
                'author_mode' => 'new',
                'author_name' => 'Jean Nouveau',
                'author_email' => 'jean@example.com',
            ],
        ));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'jean@example.com',
            'name' => 'Jean Nouveau',
            'invited_by_user_id' => $editor->id,
        ]);

        $author = User::where('email', 'jean@example.com')->first();
        $this->assertTrue($author->isGhost());

        $this->assertDatabaseHas('submissions', [
            'author_id' => $author->id,
            'submitted_by_user_id' => $editor->id,
            'status' => 'submitted',
        ]);

        Mail::assertQueued(AccountInvitation::class, fn ($m) => $m->hasTo('jean@example.com'));
    }

    public function test_user_without_editorial_capability_gets_403(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $author = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            ['author_mode' => 'existing', 'author_id' => $author->id],
        ));

        $response->assertForbidden();
    }

    public function test_get_create_without_capability_gets_403(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($user)->get(route('admin.submissions.create'));

        $response->assertForbidden();
    }

    public function test_duplicate_email_in_new_author_mode_fails_validation(): void
    {
        Storage::fake('public');
        $editor = $this->makeEditor();
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($editor)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            [
                'author_mode' => 'new',
                'author_name' => 'Existing Dude',
                'author_email' => 'existing@example.com',
            ],
        ));

        $response->assertSessionHasErrors('author_email');
    }

    public function test_missing_manuscript_fails_validation(): void
    {
        Storage::fake('public');
        $editor = $this->makeEditor();
        $author = User::factory()->create();

        $payload = $this->basePayload();
        unset($payload['manuscript_file']);

        $response = $this->actingAs($editor)->post(
            route('admin.submissions.store'),
            array_merge($payload, ['author_mode' => 'existing', 'author_id' => $author->id])
        );

        $response->assertSessionHasErrors('manuscript_file');
    }

    public function test_new_author_mode_without_name_fails_validation(): void
    {
        Storage::fake('public');
        $editor = $this->makeEditor();

        $response = $this->actingAs($editor)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            ['author_mode' => 'new', 'author_email' => 'x@example.com'],
        ));

        $response->assertSessionHasErrors('author_name');
    }

    public function test_existing_mode_without_author_id_fails_validation(): void
    {
        Storage::fake('public');
        $editor = $this->makeEditor();

        $response = $this->actingAs($editor)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            ['author_mode' => 'existing'],
        ));

        $response->assertSessionHasErrors('author_id');
    }

    public function test_author_creating_own_submission_via_admin_gets_null_submitted_by(): void
    {
        Storage::fake('public');
        Mail::fake();

        $chief = $this->makeEditor(EditorialCapability::CHIEF_EDITOR);

        $this->actingAs($chief)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            ['author_mode' => 'existing', 'author_id' => $chief->id],
        ));

        $this->assertDatabaseHas('submissions', [
            'author_id' => $chief->id,
            'submitted_by_user_id' => null,
        ]);
    }
}
