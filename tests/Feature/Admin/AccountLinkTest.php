<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountLinkTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    }

    private int $memberSeq = 0;

    private function makeMember(array $attrs = []): Member
    {
        $this->memberSeq++;
        return Member::create(array_merge([
            'contact_type'  => 'individuel',
            'first_name'    => 'Jean',
            'last_name'     => 'Dupont',
            'email'         => "member{$this->memberSeq}@example.com",
            'member_number' => sprintf('OR-TEST-%04d', $this->memberSeq),
        ], $attrs));
    }

    public function test_admin_can_link_member_to_user(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['email' => 'login@perso.fr']);
        $member = $this->makeMember(['email' => 'contact@asso.org']);

        $this->actingAs($admin)
            ->post(route('admin.users.link-member', $target), ['member_id' => $member->id])
            ->assertRedirect(route('admin.users.show', $target))
            ->assertSessionHas('success');

        $this->assertSame($target->id, $member->fresh()->user_id);
    }

    public function test_link_refused_when_member_already_taken(): void
    {
        $admin = $this->admin();
        $owner = User::factory()->create();
        $target = User::factory()->create();
        $member = $this->makeMember(['user_id' => $owner->id]);

        $this->actingAs($admin)
            ->post(route('admin.users.link-member', $target), ['member_id' => $member->id])
            ->assertSessionHas('error');

        $this->assertSame($owner->id, $member->fresh()->user_id);
    }

    public function test_admin_can_unlink_member(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $member = $this->makeMember(['user_id' => $target->id]);

        $this->actingAs($admin)
            ->post(route('admin.users.unlink-member', $target))
            ->assertRedirect(route('admin.users.show', $target))
            ->assertSessionHas('success');

        $this->assertNull($member->fresh()->user_id);
    }

    public function test_guest_cannot_link(): void
    {
        $target = User::factory()->create();
        $member = $this->makeMember();

        $this->post(route('admin.users.link-member', $target), ['member_id' => $member->id])
            ->assertRedirect();

        $this->assertNull($member->fresh()->user_id);
    }
}
