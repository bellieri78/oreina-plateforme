<?php

namespace Tests\Unit\Services;

use App\Models\MenuItem;
use App\Services\MenuRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuRendererTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_only_active_root_items(): void
    {
        MenuItem::create(['location' => 'header', 'label' => 'Visible', 'url' => '/v', 'is_active' => true, 'sort_order' => 1]);
        MenuItem::create(['location' => 'header', 'label' => 'Hidden', 'url' => '/h', 'is_active' => false, 'sort_order' => 2]);

        $items = (new MenuRenderer())->forLocation('header');

        $this->assertCount(1, $items);
        $this->assertSame('Visible', $items[0]->label);
    }

    public function test_returns_items_for_correct_location(): void
    {
        MenuItem::create(['location' => 'header', 'label' => 'In header', 'url' => '/h', 'sort_order' => 1]);
        MenuItem::create(['location' => 'footer', 'label' => 'In footer', 'url' => '/f', 'sort_order' => 1]);

        $headerItems = (new MenuRenderer())->forLocation('header');
        $footerItems = (new MenuRenderer())->forLocation('footer');

        $this->assertCount(1, $headerItems);
        $this->assertSame('In header', $headerItems[0]->label);
        $this->assertCount(1, $footerItems);
        $this->assertSame('In footer', $footerItems[0]->label);
    }

    public function test_returns_items_sorted_by_sort_order_then_id(): void
    {
        $second = MenuItem::create(['location' => 'header', 'label' => 'Second', 'url' => '/2', 'sort_order' => 20]);
        $first = MenuItem::create(['location' => 'header', 'label' => 'First', 'url' => '/1', 'sort_order' => 10]);

        $items = (new MenuRenderer())->forLocation('header');

        $this->assertSame('First', $items[0]->label);
        $this->assertSame('Second', $items[1]->label);
    }

    public function test_eager_loads_children_filtered_active(): void
    {
        $parent = MenuItem::create(['location' => 'header', 'label' => 'Parent', 'url' => '/p', 'sort_order' => 1]);
        MenuItem::create(['location' => 'header', 'parent_id' => $parent->id, 'label' => 'Child active', 'url' => '/ca', 'is_active' => true, 'sort_order' => 1]);
        MenuItem::create(['location' => 'header', 'parent_id' => $parent->id, 'label' => 'Child inactive', 'url' => '/ci', 'is_active' => false, 'sort_order' => 2]);

        $items = (new MenuRenderer())->forLocation('header');

        $this->assertCount(1, $items);
        $this->assertCount(1, $items[0]->children);
        $this->assertSame('Child active', $items[0]->children[0]->label);
    }
}
