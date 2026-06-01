<?php

namespace Tests\Feature\Member;

use App\Models\Article;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function currentMember(array $attrs = []): Member
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create(array_merge([
            'user_id' => $u->id, 'member_number' => 'M'.uniqid(), 'email' => $u->email,
            'first_name' => 'A', 'last_name' => 'B', 'joined_at' => now(), 'is_active' => true,
        ], $attrs));
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return $m;
    }

    private function article(array $attrs = []): Article
    {
        $author = User::factory()->create();
        return Article::create(array_merge([
            'author_id' => $author->id,
            'title' => 'A'.uniqid(), 'slug' => 'a'.uniqid(), 'content' => 'x',
            'status' => 'published', 'published_at' => now()->subDay(), 'visibility' => Article::VIS_PUBLIC,
        ], $attrs));
    }

    public function test_simple_member_sees_public_and_members_only(): void
    {
        $m = $this->currentMember();
        $pub = $this->article(['visibility' => Article::VIS_PUBLIC]);
        $mem = $this->article(['visibility' => Article::VIS_MEMBERS]);
        $res = $this->article(['visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        $ids = Article::visibleToMember($m)->pluck('id');

        $this->assertTrue($ids->contains($pub->id));
        $this->assertTrue($ids->contains($mem->id));
        $this->assertFalse($ids->contains($res->id));
    }

    public function test_bureau_sees_ca_restricted_article(): void
    {
        $m = $this->currentMember(['adherent_roles' => ['bureau']]);
        $ca = $this->article(['visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['ca']]);
        $val = $this->article(['visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['validateur']]);

        $ids = Article::visibleToMember($m)->pluck('id');

        $this->assertTrue($ids->contains($ca->id));
        $this->assertFalse($ids->contains($val->id));
    }

    public function test_public_only_scope_excludes_non_public(): void
    {
        $this->article(['visibility' => Article::VIS_PUBLIC]);
        $this->article(['visibility' => Article::VIS_MEMBERS]);

        $this->assertSame(1, Article::publicOnly()->count());
    }

    public function test_is_visible_to_member_guards_null_and_non_public(): void
    {
        $pub = $this->article(['visibility' => Article::VIS_PUBLIC]);
        $mem = $this->article(['visibility' => Article::VIS_MEMBERS]);

        $this->assertTrue($pub->isVisibleToMember(null));
        $this->assertFalse($mem->isVisibleToMember(null));
        $this->assertTrue($mem->isVisibleToMember($this->currentMember()));
    }
}
