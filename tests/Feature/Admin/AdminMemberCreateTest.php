<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMemberCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_store_generates_member_number(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.members.store'), [
                'civilite' => 'M.',
                'first_name' => 'Quentin',
                'last_name' => 'Marquet',
                'email' => 'quentin.marquet@oreina.org',
                'contact_type' => 'individuel',
                'country' => 'France',
            ])
            ->assertRedirect();

        $member = Member::where('email', 'quentin.marquet@oreina.org')->first();

        $this->assertNotNull($member);
        $this->assertMatchesRegularExpression('/^OR\d{8}$/', $member->member_number);
    }

    public function test_generated_number_ignores_non_conforming_numbers(): void
    {
        $year = date('Y');

        // Numero legacy/import qui ne suit pas le format OR{annee}{sequence}
        Member::create([
            'member_number' => "OR{$year}0042",
            'first_name' => 'Ada', 'last_name' => 'L',
            'email' => 'ada@example.test', 'contact_type' => 'individuel',
        ]);
        Member::create([
            'member_number' => 'GT213dbd9',
            'first_name' => 'Bob', 'last_name' => 'M',
            'email' => 'bob@example.test', 'contact_type' => 'individuel',
        ]);

        $this->assertSame("OR{$year}0043", Member::generateMemberNumber());
    }

    public function test_member_number_is_assigned_on_create_when_missing(): void
    {
        $member = Member::create([
            'first_name' => 'Cleo', 'last_name' => 'N',
            'email' => 'cleo@example.test', 'contact_type' => 'individuel',
        ]);

        $this->assertNotEmpty($member->member_number);
        $this->assertMatchesRegularExpression('/^OR\d{8}$/', $member->member_number);
    }

    public function test_explicit_member_number_is_preserved(): void
    {
        $member = Member::create([
            'member_number' => 'LEGACY-001',
            'first_name' => 'Dan', 'last_name' => 'O',
            'email' => 'dan@example.test', 'contact_type' => 'individuel',
        ]);

        $this->assertSame('LEGACY-001', $member->fresh()->member_number);
    }
}
