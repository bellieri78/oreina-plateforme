<?php

namespace Tests\Feature\Hub;

use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_renders_active_items_with_correct_urls(): void
    {
        MenuItem::create(['location' => 'header', 'label' => 'Visible Item', 'url' => '/visible', 'is_active' => true, 'sort_order' => 1]);
        MenuItem::create(['location' => 'header', 'label' => 'Hidden Item', 'url' => '/hidden', 'is_active' => false, 'sort_order' => 2]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Visible Item')
            ->assertSee('/visible', escape: false)
            ->assertDontSee('Hidden Item');
    }

    public function test_dropdown_appears_for_parent_with_children(): void
    {
        $parent = MenuItem::create(['location' => 'header', 'label' => 'Parent menu', 'url' => '/parent', 'is_active' => true, 'sort_order' => 1]);
        MenuItem::create(['location' => 'header', 'parent_id' => $parent->id, 'label' => 'Child A', 'url' => '/child-a', 'is_active' => true, 'sort_order' => 1]);
        MenuItem::create(['location' => 'header', 'parent_id' => $parent->id, 'label' => 'Child B', 'url' => '/child-b', 'is_active' => true, 'sort_order' => 2]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Parent menu')
            ->assertSee('Child A')
            ->assertSee('Child B')
            ->assertSee('hub-nav-dropdown', escape: false);
    }

    public function test_open_in_new_tab_renders_target_blank_attribute(): void
    {
        MenuItem::create(['location' => 'header', 'label' => 'External', 'url' => 'https://example.com', 'is_active' => true, 'open_in_new_tab' => true, 'sort_order' => 1]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('target="_blank"', escape: false)
            ->assertSee('rel="noopener"', escape: false);
    }
}
