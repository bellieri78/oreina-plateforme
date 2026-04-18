<?php

namespace Tests\Feature\Journal;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_form_page_loads_with_confirmation_modal_scaffolding(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->get(route('journal.submissions.create'))
            ->assertOk()
            ->assertSee('Confirmer la soumission')
            ->assertSee('Récapitulatif')
            ->assertSee('Checklist');
    }
}
