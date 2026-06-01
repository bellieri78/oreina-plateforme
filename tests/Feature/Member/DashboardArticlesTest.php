<?php

namespace Tests\Feature\Member;

use App\Models\Article;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_members_article_not_restricted(): void
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create([
            'user_id' => $u->id, 'member_number' => 'MA', 'email' => $u->email,
            'first_name' => 'Ada', 'last_name' => 'L', 'joined_at' => now(), 'is_active' => true,
        ]);
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);

        $author = User::factory()->create()->id;
        Article::create(['title' => 'Actu adherents', 'slug' => 'actu-adh', 'content' => 'x', 'author_id' => $author,
            'status' => 'published', 'published_at' => now()->subDay(), 'visibility' => Article::VIS_MEMBERS]);
        Article::create(['title' => 'Note CA confidentielle', 'slug' => 'note-ca', 'content' => 'x', 'author_id' => $author,
            'status' => 'published', 'published_at' => now()->subDay(),
            'visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        $this->actingAs($u)->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Actu adherents')
            ->assertDontSee('Note CA confidentielle');
    }
}
