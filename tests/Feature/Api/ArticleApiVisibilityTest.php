<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleApiVisibilityTest extends TestCase
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

    public function test_api_index_lists_only_public_articles(): void
    {
        $this->article(['title' => 'API public', 'visibility' => Article::VIS_PUBLIC]);
        $this->article(['title' => 'API reserve', 'visibility' => Article::VIS_MEMBERS]);

        $res = $this->getJson('/api/v1/articles')->assertOk();
        $res->assertSee('API public');
        $res->assertDontSee('API reserve');
    }

    public function test_api_show_404_for_non_public_article(): void
    {
        $mem = $this->article(['visibility' => Article::VIS_MEMBERS]);

        $this->getJson('/api/v1/articles/'.$mem->slug)->assertNotFound();
    }

    public function test_api_category_excludes_non_public(): void
    {
        $this->article(['title' => 'Cat public', 'category' => 'actualites', 'visibility' => Article::VIS_PUBLIC]);
        $this->article(['title' => 'Cat reserve', 'category' => 'actualites', 'visibility' => Article::VIS_MEMBERS]);

        $this->getJson('/api/v1/articles/category/actualites')->assertOk()
            ->assertSee('Cat public')->assertDontSee('Cat reserve');
    }
}
