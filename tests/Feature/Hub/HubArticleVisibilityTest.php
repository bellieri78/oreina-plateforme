<?php

namespace Tests\Feature\Hub;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubArticleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function article(array $attrs = []): Article
    {
        return Article::create(array_merge([
            'title' => 'A'.uniqid(), 'slug' => 'a'.uniqid(), 'content' => 'x',
            'author_id' => User::factory()->create()->id,
            'status' => 'published', 'published_at' => now()->subDay(), 'visibility' => Article::VIS_PUBLIC,
        ], $attrs));
    }

    public function test_hub_index_lists_only_public_articles(): void
    {
        $this->article(['title' => 'Article public', 'visibility' => Article::VIS_PUBLIC]);
        $this->article(['title' => 'Reserve adherents', 'visibility' => Article::VIS_MEMBERS]);

        $this->get(route('hub.articles.index'))
            ->assertOk()
            ->assertSee('Article public')
            ->assertDontSee('Reserve adherents');
    }

    public function test_hub_show_404_for_members_article_as_guest(): void
    {
        $mem = $this->article(['visibility' => Article::VIS_MEMBERS]);

        $this->get(route('hub.articles.show', $mem))->assertNotFound();
    }

    public function test_hub_show_ok_for_public_article(): void
    {
        $pub = $this->article(['visibility' => Article::VIS_PUBLIC]);

        $this->get(route('hub.articles.show', $pub))->assertOk();
    }

    public function test_related_articles_exclude_non_public(): void
    {
        $pub = $this->article(['title' => 'Public principal', 'category' => 'actualites', 'visibility' => Article::VIS_PUBLIC]);
        $this->article(['title' => 'Reserve meme categorie', 'category' => 'actualites', 'visibility' => Article::VIS_MEMBERS]);

        $this->get(route('hub.articles.show', $pub))
            ->assertOk()
            ->assertDontSee('Reserve meme categorie');
    }
}
