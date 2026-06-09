<?php

namespace Tests\Unit\Services;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberUserLinkServiceTest extends TestCase
{
    use RefreshDatabase;

    private int $memberSeq = 0;

    private function makeMember(array $attrs = []): Member
    {
        $this->memberSeq++;

        return Member::create(array_merge([
            'contact_type'  => 'individuel',
            'member_number' => sprintf('OR-TEST-%04d', $this->memberSeq),
            'first_name'    => 'Jean',
            'last_name'     => 'Dupont',
            'email'         => 'jean.dupont@example.com',
        ], $attrs));
    }

    public function test_scopes_filter_linked_and_unlinked(): void
    {
        $user = User::factory()->create();
        $linked = $this->makeMember(['email' => 'a@example.com', 'user_id' => $user->id]);
        $orphan = $this->makeMember(['email' => 'b@example.com']);

        $this->assertEqualsCanonicalizing(
            [$orphan->id],
            Member::withoutAccount()->pluck('id')->all()
        );
        $this->assertEqualsCanonicalizing(
            [$user->id],
            User::query()->whereHas('member')->pluck('id')->all()
        );
        $this->assertTrue(User::withoutMember()->where('id', $user->id)->doesntExist());
    }

    public function test_suggestions_match_email_then_name_and_exclude_linked_and_anonymized(): void
    {
        $user = User::factory()->create([
            'name'  => 'Marie Martin',
            'email' => 'marie@perso.fr',
        ]);

        $byEmail = $this->makeMember(['first_name' => 'Autre', 'last_name' => 'Nom', 'email' => 'marie@perso.fr']);
        $byName = $this->makeMember(['first_name' => 'Marie', 'last_name' => 'Martin', 'email' => 'm.martin@asso.org']);
        $linked = $this->makeMember(['first_name' => 'Marie', 'last_name' => 'Martin', 'email' => 'x@example.com', 'user_id' => $user->id]);
        // members.email est unique en base : l'anonymise matche donc par nom (pas par email).
        $anon = $this->makeMember(['first_name' => 'Marie', 'last_name' => 'Martin', 'email' => 'anon@perso.fr', 'anonymise' => true]);
        $this->makeMember(['first_name' => 'Paul', 'last_name' => 'Durand', 'email' => 'paul@example.com']);

        $service = new \App\Services\MemberUserLinkService();
        $ids = $service->suggestionsFor($user)->pluck('id')->all();

        $this->assertSame([$byEmail->id, $byName->id], array_slice($ids, 0, 2));
        $this->assertNotContains($linked->id, $ids);
        $this->assertNotContains($anon->id, $ids);
    }

    public function test_link_sets_user_id_atomically_and_audits(): void
    {
        $user = User::factory()->create();
        $member = $this->makeMember();

        $service = new \App\Services\MemberUserLinkService();
        $ok = $service->link($user, $member);

        $this->assertTrue($ok);
        $this->assertSame($user->id, $member->fresh()->user_id);
        $this->assertDatabaseHas('audit_logs', [
            'table_name' => 'members',
            'record_id'  => $member->id,
            'action'     => 'UPDATE',
        ]);
    }

    public function test_link_refused_when_member_already_taken(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $member = $this->makeMember(['user_id' => $owner->id]);

        $service = new \App\Services\MemberUserLinkService();
        $ok = $service->link($other, $member);

        $this->assertFalse($ok);
        $this->assertSame($owner->id, $member->fresh()->user_id);
    }

    public function test_link_refused_when_user_already_has_member(): void
    {
        $user = User::factory()->create();
        $this->makeMember(['email' => 'first@example.com', 'user_id' => $user->id]);
        $candidate = $this->makeMember(['email' => 'second@example.com']);

        $service = new \App\Services\MemberUserLinkService();
        $ok = $service->link($user, $candidate);

        $this->assertFalse($ok);
        $this->assertNull($candidate->fresh()->user_id);
    }

    public function test_unlink_clears_user_id_and_audits(): void
    {
        $user = User::factory()->create();
        $member = $this->makeMember(['user_id' => $user->id]);

        $service = new \App\Services\MemberUserLinkService();
        $service->unlink($member);

        $this->assertNull($member->fresh()->user_id);
        $this->assertDatabaseHas('audit_logs', [
            'table_name' => 'members',
            'record_id'  => $member->id,
            'action'     => 'UPDATE',
        ]);
    }
}
