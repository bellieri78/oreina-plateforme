<?php

namespace Tests\Feature\Member;

use App\Models\Article;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestrictedArticleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $num, array $attrs = []): User
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create(array_merge([
            'user_id' => $u->id, 'member_number' => $num, 'email' => $u->email,
            'first_name' => 'A', 'last_name' => 'B', 'joined_at' => now(), 'is_active' => true,
        ], $attrs));
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return $u;
    }

    public function test_ca_article_detail_visible_to_bureau_not_to_simple_member(): void
    {
        $bureau = $this->makeUser('BUR', ['adherent_roles' => ['bureau']]);
        $simple = $this->makeUser('SIM');

        $article = Article::create(['title' => 'Compte rendu CA', 'slug' => 'cr-ca', 'content' => 'x',
            'author_id' => User::factory()->create()->id,
            'status' => 'published', 'published_at' => now()->subDay(),
            'visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        // Bureau (cascade ⊇ ca) : 200 sur le détail
        $this->actingAs($bureau)->get(route('hub.articles.show', $article))->assertOk()->assertSee('Compte rendu CA');
        // Adhérent simple : 404
        $this->actingAs($simple)->get(route('hub.articles.show', $article))->assertNotFound();
    }
}
