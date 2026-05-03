<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleShowAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_renders_featured_image_when_present(): void
    {
        $admin = $this->makeAdmin();
        $article = Article::create([
            'author_id' => $admin->id,
            'title' => 'Test article',
            'slug' => 'test-article',
            'content' => '<p>Contenu</p>',
            'featured_image' => 'articles/images/example.jpg',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/articles/{$article->id}");

        $response->assertOk()
            ->assertSee('articles/images/example.jpg', escape: false);
    }

    public function test_show_does_not_render_image_block_when_absent(): void
    {
        $admin = $this->makeAdmin();
        $article = Article::create([
            'author_id' => $admin->id,
            'title' => 'Sans image',
            'slug' => 'sans-image',
            'content' => '<p>Pas d\'image</p>',
            'featured_image' => null,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/articles/{$article->id}");

        $response->assertOk()
            ->assertDontSee('articles/images/', escape: false);
    }

    public function test_show_renders_content_html_correctly(): void
    {
        $admin = $this->makeAdmin();
        $article = Article::create([
            'author_id' => $admin->id,
            'title' => 'HTML rendu',
            'slug' => 'html-rendu',
            'content' => '<p>Bonjour <strong>monde</strong></p>',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/articles/{$article->id}");

        $response->assertOk()
            ->assertSee('<strong>monde</strong>', escape: false)
            ->assertDontSee('&lt;strong&gt;', escape: false);
    }

    public function test_show_displays_validation_block_when_validated(): void
    {
        $admin = $this->makeAdmin();
        $validator = User::factory()->create(['name' => 'Jean Dupont']);
        $article = Article::create([
            'author_id' => $admin->id,
            'title' => 'Validé',
            'slug' => 'valide',
            'content' => '<p>Validé</p>',
            'status' => 'validated',
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'validation_notes' => 'OK pour publication',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/articles/{$article->id}");

        $response->assertOk()
            ->assertSee('Jean Dupont')
            ->assertSee('OK pour publication');
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }
}
