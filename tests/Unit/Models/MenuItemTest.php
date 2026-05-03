<?php

namespace Tests\Unit\Models;

use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MenuItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_constants_are_defined(): void
    {
        $this->assertSame('header', MenuItem::LOCATION_HEADER);
        $this->assertSame('footer', MenuItem::LOCATION_FOOTER);
    }

    public function test_children_relation_returns_descendants_ordered(): void
    {
        $parent = MenuItem::create(['location' => 'header', 'label' => 'P', 'url' => '/p', 'sort_order' => 1]);
        MenuItem::create(['location' => 'header', 'parent_id' => $parent->id, 'label' => 'C2', 'url' => '/c2', 'sort_order' => 2]);
        MenuItem::create(['location' => 'header', 'parent_id' => $parent->id, 'label' => 'C1', 'url' => '/c1', 'sort_order' => 1]);

        $children = $parent->fresh()->children;
        $this->assertCount(2, $children);
        $this->assertSame('C1', $children[0]->label);
        $this->assertSame('C2', $children[1]->label);
    }

    public function test_can_have_children_returns_true_for_root_item(): void
    {
        $root = new MenuItem(['parent_id' => null]);
        $this->assertTrue($root->canHaveChildren());

        $child = new MenuItem(['parent_id' => 1]);
        $this->assertFalse($child->canHaveChildren());
    }

    public function test_saving_invalidates_menu_cache(): void
    {
        Cache::put('menu.header', 'sentinel-header', 60);
        Cache::put('menu.footer', 'sentinel-footer', 60);

        MenuItem::create(['location' => 'header', 'label' => 'X', 'url' => '/x']);

        $this->assertNull(Cache::get('menu.header'));
        $this->assertNull(Cache::get('menu.footer'));
    }

    public function test_deleting_invalidates_menu_cache(): void
    {
        $item = MenuItem::create(['location' => 'header', 'label' => 'X', 'url' => '/x']);
        Cache::put('menu.header', 'sentinel', 60);

        $item->delete();

        $this->assertNull(Cache::get('menu.header'));
    }
}
