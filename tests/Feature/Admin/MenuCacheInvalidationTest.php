<?php

namespace Tests\Feature\Admin;

use App\Models\MenuItem;
use App\Services\MenuRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuCacheInvalidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_item_invalidates_the_cache(): void
    {
        $renderer = new MenuRenderer();
        $first = $renderer->forLocation('header');
        $this->assertCount(0, $first);

        MenuItem::create(['location' => 'header', 'label' => 'New', 'url' => '/n', 'is_active' => true]);

        $second = $renderer->forLocation('header');
        $this->assertCount(1, $second);
        $this->assertSame('New', $second[0]->label);
    }

    public function test_deleting_an_item_invalidates_the_cache(): void
    {
        $item = MenuItem::create(['location' => 'header', 'label' => 'X', 'url' => '/x', 'is_active' => true]);

        $renderer = new MenuRenderer();
        $first = $renderer->forLocation('header');
        $this->assertCount(1, $first);

        $item->delete();

        $second = $renderer->forLocation('header');
        $this->assertCount(0, $second);
    }
}
