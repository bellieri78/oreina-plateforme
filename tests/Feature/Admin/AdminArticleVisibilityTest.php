<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminArticleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_restricted_article_with_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.articles.store'), [
                'title' => 'Note interne CA',
                'content' => 'corps',
                'status' => 'draft',
                'visibility' => 'restricted',
                'audience_roles' => ['ca', 'bureau'],
            ])->assertRedirect();

        $article = Article::where('title', 'Note interne CA')->firstOrFail();
        $this->assertSame('restricted', $article->visibility);
        $this->assertEqualsCanonicalizing(['ca', 'bureau'], $article->audience_roles);
    }

    public function test_restricted_requires_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.articles.store'), [
                'title' => 'Sans cible', 'content' => 'corps', 'status' => 'draft',
                'visibility' => 'restricted',
            ])->assertSessionHasErrors('audience_roles');
    }

    public function test_members_visibility_clears_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.articles.store'), [
                'title' => 'Pour adherents', 'content' => 'corps', 'status' => 'draft',
                'visibility' => 'members', 'audience_roles' => ['ca'],
            ])->assertRedirect();

        $article = Article::where('title', 'Pour adherents')->firstOrFail();
        $this->assertNull($article->audience_roles);
    }
}
