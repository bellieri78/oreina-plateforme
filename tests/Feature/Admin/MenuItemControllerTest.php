<?php

namespace Tests\Feature\Admin;

use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuItemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_both_header_and_footer_sections(): void
    {
        $admin = $this->makeAdmin();
        MenuItem::create(['location' => 'header', 'label' => 'Header item', 'url' => '/h']);
        MenuItem::create(['location' => 'footer', 'label' => 'Footer item', 'url' => '/f']);

        $response = $this->actingAs($admin)->get('/extranet/menus');

        $response->assertOk()
            ->assertSee('Menu Header', escape: false)
            ->assertSee('Menu Footer', escape: false)
            ->assertSee('Header item')
            ->assertSee('Footer item');
    }

    public function test_create_form_renders(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/extranet/menus/create');

        $response->assertOk()
            ->assertSee('Libellé', escape: false)
            ->assertSee('Localisation');
    }

    public function test_store_validates_required_fields(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post('/extranet/menus', []);

        $response->assertSessionHasErrors(['label', 'location', 'url']);
    }

    public function test_store_persists_a_new_header_item(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post('/extranet/menus', [
            'label'      => 'Nouveau',
            'location'   => 'header',
            'url'        => '/nouveau',
            'sort_order' => 5,
            'is_active'  => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('menu_items', ['label' => 'Nouveau', 'location' => 'header', 'url' => '/nouveau']);
    }

    public function test_store_forces_parent_id_null_when_location_is_footer(): void
    {
        $admin = $this->makeAdmin();
        $headerParent = MenuItem::create(['location' => 'header', 'label' => 'P', 'url' => '/p']);

        $this->actingAs($admin)->post('/extranet/menus', [
            'label'     => 'Footer item',
            'location'  => 'footer',
            'parent_id' => $headerParent->id, // ignoré car footer est plat
            'url'       => '/f',
        ])->assertRedirect();

        $created = MenuItem::where('label', 'Footer item')->first();
        $this->assertNull($created->parent_id);
    }

    public function test_store_rejects_parent_assignment_to_already_child_item(): void
    {
        $admin = $this->makeAdmin();
        $parent = MenuItem::create(['location' => 'header', 'label' => 'P', 'url' => '/p']);
        $child  = MenuItem::create(['location' => 'header', 'parent_id' => $parent->id, 'label' => 'C', 'url' => '/c']);

        $response = $this->actingAs($admin)->post('/extranet/menus', [
            'label'     => 'Sub-sub',
            'location'  => 'header',
            'parent_id' => $child->id, // child est déjà un sous-item → ne peut pas être parent
            'url'       => '/ss',
        ]);

        $response->assertSessionHasErrors('parent_id');
    }

    public function test_destroy_cascades_children(): void
    {
        $admin  = $this->makeAdmin();
        $parent = MenuItem::create(['location' => 'header', 'label' => 'P', 'url' => '/p']);
        $child  = MenuItem::create(['location' => 'header', 'parent_id' => $parent->id, 'label' => 'C', 'url' => '/c']);

        $this->actingAs($admin)->delete("/extranet/menus/{$parent->id}")->assertRedirect();

        $this->assertDatabaseMissing('menu_items', ['id' => $parent->id]);
        $this->assertDatabaseMissing('menu_items', ['id' => $child->id]);
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }
}
