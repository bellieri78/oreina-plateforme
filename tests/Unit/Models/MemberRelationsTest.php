<?php

namespace Tests\Unit\Models;

use App\Models\LepisSuggestion;
use App\Models\Member;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_lepis_suggestions_relation_returns_member_suggestions(): void
    {
        $member = $this->makeMember();
        LepisSuggestion::create([
            'member_id' => $member->id,
            'title' => 'Idée 1',
            'content' => 'Contenu',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        LepisSuggestion::create([
            'member_id' => $member->id,
            'title' => 'Idée 2',
            'content' => 'Contenu',
            'status' => 'noted',
            'submitted_at' => now()->subDay(),
        ]);

        $this->assertCount(2, $member->fresh()->lepisSuggestions);
    }

    public function test_submissions_relation_returns_user_submissions(): void
    {
        $member = $this->makeMember();
        Submission::create([
            'author_id' => $member->user_id,
            'title' => 'Article 1',
            'abstract' => '',
            'manuscript_file' => '',
            'status' => 'submitted',
        ]);
        Submission::create([
            'author_id' => $member->user_id,
            'title' => 'Article 2',
            'abstract' => '',
            'manuscript_file' => '',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->assertCount(2, $member->fresh()->submissions);
        $this->assertEquals(['Article 1', 'Article 2'], $member->submissions->pluck('title')->sort()->values()->all());
    }

    public function test_submissions_relation_excludes_other_members_submissions(): void
    {
        $alice = $this->makeMember('alice@test.com');
        $bob = $this->makeMember('bob@test.com');
        Submission::create(['author_id' => $alice->user_id, 'title' => 'Alice', 'abstract' => '', 'manuscript_file' => '', 'status' => 'submitted']);
        Submission::create(['author_id' => $bob->user_id, 'title' => 'Bob', 'abstract' => '', 'manuscript_file' => '', 'status' => 'submitted']);

        $this->assertCount(1, $alice->fresh()->submissions);
        $this->assertSame('Alice', $alice->submissions->first()->title);
    }

    private function makeMember(string $email = null): Member
    {
        $email = $email ?: 'm' . random_int(1000, 9999) . '@test.com';
        $user = User::factory()->create(['email' => $email]);
        return Member::create([
            'user_id' => $user->id,
            'member_number' => 'M' . random_int(1000, 9999),
            'email' => $email,
            'first_name' => 'F',
            'last_name' => 'L',
            'joined_at' => now(),
        ]);
    }
}
