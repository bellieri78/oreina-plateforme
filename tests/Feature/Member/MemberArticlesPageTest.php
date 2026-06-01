<?php

namespace Tests\Feature\Member;

use App\Models\Article;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberArticlesPageTest extends TestCase
{
    use RefreshDatabase;

    private function currentUser(): User
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create([
            'user_id' => $u->id, 'member_number' => 'M'.uniqid(), 'email' => $u->email,
            'first_name' => 'A', 'last_name' => 'B', 'joined_at' => now(), 'is_active' => true,
        ]);
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return $u;
    }

    public function test_member_articles_page_lists_visible_excludes_restricted(): void
    {
        $u = $this->currentUser();
        $author = User::factory()->create()->id;
        Article::create(['title' => 'Visible adherents', 'slug' => 'vis-adh', 'content' => 'x', 'author_id' => $author,
            'status' => 'published', 'published_at' => now()->subDay(), 'visibility' => Article::VIS_MEMBERS]);
        Article::create(['title' => 'Reserve CA', 'slug' => 'res-ca', 'content' => 'x', 'author_id' => $author,
            'status' => 'published', 'published_at' => now()->subDay(),
            'visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        $this->actingAs($u)->get(route('member.articles.index'))
            ->assertOk()
            ->assertSee('Visible adherents')
            ->assertDontSee('Reserve CA');
    }

    public function test_guest_redirected(): void
    {
        $this->get(route('member.articles.index'))->assertRedirect();
    }
}
