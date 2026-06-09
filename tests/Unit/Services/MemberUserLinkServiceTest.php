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
}
