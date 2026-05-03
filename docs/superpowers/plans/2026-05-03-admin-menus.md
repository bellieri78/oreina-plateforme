# Gestion admin des menus du hub — Plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Permettre à l'admin de gérer les menus du hub public (header + footer) depuis `/extranet/menus`, avec hiérarchie 2 niveaux pour le header, footer plat, label/url/ordre/actif/nouvel onglet par item, et invalidation automatique du cache au save.

**Architecture:** Une table `menu_items` avec `parent_id` self-reference + colonne `location`. Un service `MenuRenderer` mis en cache 1h, invalidé par observer Eloquent. View Composer injecte `$headerMenu` et `$footerMenu` dans les partials hub. CRUD admin Blade custom dans `/extranet/menus`.

**Tech Stack:** Laravel 12, PostgreSQL (compat 9.6 prod), Blade + Alpine.js (déjà utilisé pour le menu mobile et les dropdowns ailleurs), Lucide icons, classes admin existantes (`.card`, `.form-input`, `.badge`).

**Spec source:** `docs/superpowers/specs/2026-05-03-admin-menus-design.md`

---

## File map

**Created:**
- `database/migrations/2026_05_03_140000_create_menu_items_table.php`
- `database/seeders/MenuItemsSeeder.php`
- `app/Models/MenuItem.php`
- `app/Services/MenuRenderer.php`
- `app/Http/Controllers/Admin/MenuItemController.php`
- `resources/views/admin/menus/index.blade.php`
- `resources/views/admin/menus/create.blade.php`
- `resources/views/admin/menus/edit.blade.php`
- `resources/views/admin/menus/_form.blade.php`
- `tests/Unit/Services/MenuRendererTest.php`
- `tests/Feature/Admin/MenuItemControllerTest.php`
- `tests/Feature/Hub/MenuRenderingTest.php`
- `tests/Feature/Admin/MenuCacheInvalidationTest.php`

**Modified:**
- `app/Providers/AppServiceProvider.php` — ajout du View Composer
- `resources/views/partials/hub/header.blade.php` — boucles Blade au lieu de liens hardcodés (desktop + mobile)
- `resources/views/partials/hub/footer.blade.php` — boucle Blade au lieu de liens hardcodés
- `resources/views/layouts/admin.blade.php` — ajout du lien "Menus" dans la sidebar nav (après Événements, ligne ~165)
- `routes/admin.php` — `Route::resource('menus', ...)` + route `reorder` (après le bloc Events ligne ~107)

---

## Task 1 — Migration + Modèle MenuItem (avec observer cache)

**Files:**
- Create: `database/migrations/2026_05_03_140000_create_menu_items_table.php`
- Create: `app/Models/MenuItem.php`
- Create: `tests/Unit/Models/MenuItemTest.php`

- [ ] **Step 1: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->string('location', 20);
            $table->string('label', 255);
            $table->string('url', 500);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('open_in_new_tab')->default(false);
            $table->timestamps();

            $table->index(['location', 'is_active', 'sort_order'], 'menu_items_location_active_order_idx');
            $table->index('parent_id', 'menu_items_parent_idx');
        });

        // CHECK constraint via raw SQL (PG 9.6 compat)
        DB::statement("ALTER TABLE menu_items ADD CONSTRAINT menu_items_location_check CHECK (location IN ('header', 'footer'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
```

- [ ] **Step 2: Run migration**

`php artisan migrate`
Expected: `2026_05_03_140000_create_menu_items_table ... DONE`.

- [ ] **Step 3: Write failing unit test for the model**

Create `tests/Unit/Models/MenuItemTest.php`:

```php
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
```

- [ ] **Step 4: Run test, expect failure**

`php artisan test --filter=MenuItemTest`
Expected: FAIL — class not found.

- [ ] **Step 5: Create the model**

Create `app/Models/MenuItem.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class MenuItem extends Model
{
    public const LOCATION_HEADER = 'header';
    public const LOCATION_FOOTER = 'footer';

    protected $fillable = [
        'parent_id',
        'location',
        'label',
        'url',
        'sort_order',
        'is_active',
        'open_in_new_tab',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeForLocation($q, string $location)
    {
        return $q->where('location', $location);
    }

    /** Un item dont parent_id != null ne peut pas avoir d'enfants (limite 2 niveaux). */
    public function canHaveChildren(): bool
    {
        return $this->parent_id === null;
    }

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('menu.header');
            Cache::forget('menu.footer');
        });
        static::deleted(function () {
            Cache::forget('menu.header');
            Cache::forget('menu.footer');
        });
    }
}
```

- [ ] **Step 6: Run tests, expect pass**

`php artisan test --filter=MenuItemTest`
Expected: 5 passing.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_05_03_140000_create_menu_items_table.php app/Models/MenuItem.php tests/Unit/Models/MenuItemTest.php
git commit -m "feat(menus): table menu_items + modèle MenuItem avec observer cache"
```

---

## Task 2 — Service MenuRenderer (avec tests)

**Files:**
- Create: `app/Services/MenuRenderer.php`
- Create: `tests/Unit/Services/MenuRendererTest.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Unit/Services/MenuRendererTest.php`:

```php
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
```

- [ ] **Step 2: Run tests, expect failure**

`php artisan test --filter=MenuRendererTest`
Expected: FAIL — service class missing.

- [ ] **Step 3: Create the service**

Create `app/Services/MenuRenderer.php`:

```php
<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MenuRenderer
{
    public function forLocation(string $location): Collection
    {
        return Cache::remember(
            "menu.{$location}",
            3600,
            fn () => MenuItem::query()
                ->forLocation($location)
                ->active()
                ->whereNull('parent_id')
                ->with(['children' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
        );
    }
}
```

- [ ] **Step 4: Run tests, expect pass**

`php artisan test --filter=MenuRendererTest`
Expected: 4 passing.

- [ ] **Step 5: Commit**

```bash
git add app/Services/MenuRenderer.php tests/Unit/Services/MenuRendererTest.php
git commit -m "feat(menus): service MenuRenderer avec cache 1h par location"
```

---

## Task 3 — Seeder + appel via DatabaseSeeder

**Files:**
- Create: `database/seeders/MenuItemsSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php` — appel idempotent du seeder

- [ ] **Step 1: Create the seeder**

Create `database/seeders/MenuItemsSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemsSeeder extends Seeder
{
    public function run(): void
    {
        if (MenuItem::count() > 0) {
            $this->command?->info('MenuItems déjà présents, seeder ignoré (idempotent).');
            return;
        }

        // Header (5 items)
        MenuItem::create(['location' => 'header', 'label' => 'Association', 'url' => '/a-propos', 'sort_order' => 10, 'is_active' => true]);
        MenuItem::create(['location' => 'header', 'label' => 'Projets', 'url' => '#projets', 'sort_order' => 20, 'is_active' => false]);
        MenuItem::create(['location' => 'header', 'label' => 'Actualités', 'url' => '/actualites', 'sort_order' => 30, 'is_active' => true]);
        MenuItem::create(['location' => 'header', 'label' => 'Réseau', 'url' => '#reseau', 'sort_order' => 40, 'is_active' => false]);
        MenuItem::create(['location' => 'header', 'label' => 'Chersotis', 'url' => '/revue', 'sort_order' => 50, 'is_active' => true]);

        // Footer (7 items)
        MenuItem::create(['location' => 'footer', 'label' => 'Association', 'url' => '/a-propos', 'sort_order' => 10, 'is_active' => true]);
        MenuItem::create(['location' => 'footer', 'label' => 'Portail', 'url' => '/', 'sort_order' => 20, 'is_active' => true]);
        MenuItem::create(['location' => 'footer', 'label' => 'Projets', 'url' => '#', 'sort_order' => 30, 'is_active' => false]);
        MenuItem::create(['location' => 'footer', 'label' => 'Actualités', 'url' => '/actualites', 'sort_order' => 40, 'is_active' => true]);
        MenuItem::create(['location' => 'footer', 'label' => 'Réseau', 'url' => '/contact', 'sort_order' => 50, 'is_active' => true]);
        MenuItem::create(['location' => 'footer', 'label' => 'Mentions légales', 'url' => '#', 'sort_order' => 60, 'is_active' => false]);
        MenuItem::create(['location' => 'footer', 'label' => 'Politique de données', 'url' => '#', 'sort_order' => 70, 'is_active' => false]);
    }
}
```

- [ ] **Step 2: Update DatabaseSeeder to call MenuItemsSeeder**

Read `database/seeders/DatabaseSeeder.php` first to find where other seeders are called. Add `$this->call(MenuItemsSeeder::class);` in the appropriate place (typically inside `run()` after the existing seeder calls). If the file uses a list pattern like `$this->call([SeederA::class, SeederB::class])`, add `MenuItemsSeeder::class` to that array.

If `DatabaseSeeder` is empty or only has minimal setup, just add:

```php
$this->call(\Database\Seeders\MenuItemsSeeder::class);
```

- [ ] **Step 3: Run the seeder**

`php artisan db:seed --class=MenuItemsSeeder`
Expected: `Database seeding completed successfully.` (or similar). 12 rows in `menu_items`.

- [ ] **Step 4: Verify**

```bash
php artisan tinker --execute="echo \App\Models\MenuItem::where('location','header')->count() . ' / ' . \App\Models\MenuItem::where('location','footer')->count();"
```
Expected: `5 / 7`.

```bash
php artisan tinker --execute="echo \App\Models\MenuItem::where('is_active', false)->count();"
```
Expected: `4` (les 4 placeholders cassés).

- [ ] **Step 5: Verify idempotence (re-run)**

`php artisan db:seed --class=MenuItemsSeeder`
Expected: message "MenuItems déjà présents, seeder ignoré". Pas d'erreur.

`php artisan tinker --execute="echo \App\Models\MenuItem::count();"`
Expected: `12` (toujours, pas dupliqué).

- [ ] **Step 6: Commit**

```bash
git add database/seeders/MenuItemsSeeder.php database/seeders/DatabaseSeeder.php
git commit -m "feat(menus): seeder idempotent migrant les 12 items hardcodés (4 placeholders désactivés)"
```

---

## Task 4 — MenuItemController CRUD + routes admin

**Files:**
- Create: `app/Http/Controllers/Admin/MenuItemController.php`
- Modify: `routes/admin.php` — ajouter `Route::resource('menus', ...)` après le bloc Events (ligne 107)
- Create: `tests/Feature/Admin/MenuItemControllerTest.php`

- [ ] **Step 1: Add routes**

In `routes/admin.php`, find the block ending with `Route::resource('events', EventController::class);` (around line 107). Just below it, add:

```php

    // Menus (header + footer)
    Route::post('menus/{menu}/reorder/{direction}', [\App\Http\Controllers\Admin\MenuItemController::class, 'reorder'])
        ->whereIn('direction', ['up', 'down'])
        ->name('menus.reorder');
    Route::resource('menus', \App\Http\Controllers\Admin\MenuItemController::class);
```

NB: déclarer la route `menus.reorder` AVANT `Route::resource` pour éviter que Laravel ne l'interprète comme un `show` du resource. La route param `{menu}` est lié au modèle MenuItem via implicit binding.

- [ ] **Step 2: Write failing tests**

Create `tests/Feature/Admin/MenuItemControllerTest.php`:

```php
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
            'label' => 'Nouveau',
            'location' => 'header',
            'url' => '/nouveau',
            'sort_order' => 5,
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('menu_items', ['label' => 'Nouveau', 'location' => 'header', 'url' => '/nouveau']);
    }

    public function test_store_forces_parent_id_null_when_location_is_footer(): void
    {
        $admin = $this->makeAdmin();
        $headerParent = MenuItem::create(['location' => 'header', 'label' => 'P', 'url' => '/p']);

        $this->actingAs($admin)->post('/extranet/menus', [
            'label' => 'Footer item',
            'location' => 'footer',
            'parent_id' => $headerParent->id,  // ignored because footer is flat
            'url' => '/f',
        ])->assertRedirect();

        $created = MenuItem::where('label', 'Footer item')->first();
        $this->assertNull($created->parent_id);
    }

    public function test_store_rejects_parent_assignment_to_already_child_item(): void
    {
        $admin = $this->makeAdmin();
        $parent = MenuItem::create(['location' => 'header', 'label' => 'P', 'url' => '/p']);
        $child = MenuItem::create(['location' => 'header', 'parent_id' => $parent->id, 'label' => 'C', 'url' => '/c']);

        $response = $this->actingAs($admin)->post('/extranet/menus', [
            'label' => 'Sub-sub',
            'location' => 'header',
            'parent_id' => $child->id,  // child is already a child → cannot be parent
            'url' => '/ss',
        ]);

        $response->assertSessionHasErrors('parent_id');
    }

    public function test_destroy_cascades_children(): void
    {
        $admin = $this->makeAdmin();
        $parent = MenuItem::create(['location' => 'header', 'label' => 'P', 'url' => '/p']);
        $child = MenuItem::create(['location' => 'header', 'parent_id' => $parent->id, 'label' => 'C', 'url' => '/c']);

        $this->actingAs($admin)->delete("/extranet/menus/{$parent->id}")->assertRedirect();

        $this->assertDatabaseMissing('menu_items', ['id' => $parent->id]);
        $this->assertDatabaseMissing('menu_items', ['id' => $child->id]);
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }
}
```

- [ ] **Step 3: Run tests, expect failure**

`php artisan test --filter=MenuItemControllerTest`
Expected: FAIL — controller missing, no routes.

- [ ] **Step 4: Create the controller**

Create `app/Http/Controllers/Admin/MenuItemController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MenuItemController extends Controller
{
    public function index()
    {
        $headerItems = MenuItem::query()
            ->where('location', 'header')
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->orderBy('sort_order')->orderBy('id')
            ->get();

        $footerItems = MenuItem::query()
            ->where('location', 'footer')
            ->whereNull('parent_id')
            ->orderBy('sort_order')->orderBy('id')
            ->get();

        return view('admin.menus.index', compact('headerItems', 'footerItems'));
    }

    public function create(Request $request)
    {
        $defaultLocation = $request->query('location', 'header');
        $availableParents = MenuItem::query()
            ->where('location', $defaultLocation)
            ->whereNull('parent_id')
            ->orderBy('label')
            ->get();

        return view('admin.menus.create', [
            'menuItem' => null,
            'defaultLocation' => $defaultLocation,
            'availableParents' => $availableParents,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $this->applyParentRules($validated, null);

        MenuItem::create($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Item de menu créé.');
    }

    public function edit(MenuItem $menu)
    {
        $availableParents = MenuItem::query()
            ->where('location', $menu->location)
            ->whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('label')
            ->get();

        return view('admin.menus.edit', [
            'menuItem' => $menu,
            'availableParents' => $availableParents,
        ]);
    }

    public function update(Request $request, MenuItem $menu)
    {
        $validated = $this->validateRequest($request);
        $this->applyParentRules($validated, $menu);

        $menu->update($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Item de menu mis à jour.');
    }

    public function destroy(MenuItem $menu)
    {
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Item de menu supprimé (et ses sous-items en cascade).');
    }

    public function reorder(MenuItem $menu, string $direction)
    {
        $sibling = MenuItem::query()
            ->where('location', $menu->location)
            ->where('parent_id', $menu->parent_id)
            ->when($direction === 'up', fn ($q) => $q->where('sort_order', '<', $menu->sort_order)->orderByDesc('sort_order'))
            ->when($direction === 'down', fn ($q) => $q->where('sort_order', '>', $menu->sort_order)->orderBy('sort_order'))
            ->first();

        if ($sibling) {
            $tmp = $menu->sort_order;
            $menu->update(['sort_order' => $sibling->sort_order]);
            $sibling->update(['sort_order' => $tmp]);
        }

        return redirect()->route('admin.menus.index');
    }

    private function validateRequest(Request $request): array
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'location' => ['required', 'in:header,footer'],
            'parent_id' => 'nullable|exists:menu_items,id',
            'url' => 'required|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'open_in_new_tab' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['open_in_new_tab'] = $request->has('open_in_new_tab');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }

    private function applyParentRules(array &$validated, ?MenuItem $current): void
    {
        // Footer = plat
        if ($validated['location'] === 'footer') {
            $validated['parent_id'] = null;
            return;
        }

        if (empty($validated['parent_id'])) {
            $validated['parent_id'] = null;
            return;
        }

        $parent = MenuItem::find($validated['parent_id']);
        if (! $parent) {
            $validated['parent_id'] = null;
            return;
        }

        if ($parent->parent_id !== null) {
            throw ValidationException::withMessages([
                'parent_id' => 'Le parent choisi est déjà un sous-item (limite à 2 niveaux).',
            ]);
        }

        if ($parent->location !== $validated['location']) {
            throw ValidationException::withMessages([
                'parent_id' => 'Le parent doit être dans la même localisation.',
            ]);
        }

        // Si on édite un item qui a déjà des enfants, il ne peut pas devenir lui-même enfant
        if ($current && $current->exists && $current->children()->exists()) {
            throw ValidationException::withMessages([
                'parent_id' => 'Cet item a déjà des sous-items, il ne peut pas devenir lui-même un sous-item.',
            ]);
        }
    }
}
```

- [ ] **Step 5: Run tests, expect partial pass (views still missing)**

`php artisan test --filter=MenuItemControllerTest`
Expected: tests `index`, `create_form_renders` fail because views don't exist yet — that's normal. The other tests (`store_*`, `destroy_*`) should pass since they only test redirects + DB state.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/MenuItemController.php routes/admin.php tests/Feature/Admin/MenuItemControllerTest.php
git commit -m "feat(menus): MenuItemController CRUD + reorder + routes admin"
```

---

## Task 5 — Vues admin (index + create + edit + _form)

**Files:**
- Create: `resources/views/admin/menus/index.blade.php`
- Create: `resources/views/admin/menus/create.blade.php`
- Create: `resources/views/admin/menus/edit.blade.php`
- Create: `resources/views/admin/menus/_form.blade.php`
- Modify: `resources/views/layouts/admin.blade.php` — ajout du lien "Menus" dans la sidebar nav après "Événements"

- [ ] **Step 1: Create `index.blade.php`**

Create `resources/views/admin/menus/index.blade.php`:

```blade
@extends('layouts.admin')
@section('title', 'Menus')
@section('breadcrumb')<span>Menus</span>@endsection

@section('content')
    <div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
            <i data-lucide="plus"></i> Nouvel item
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
    @endif

    {{-- Header section --}}
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header"><h3 class="card-title">Menu Header</h3></div>
        <div class="card-body" style="padding: 0;">
            @if($headerItems->isEmpty())
                <div style="padding: 1rem; color: #6b7280;">Aucun item dans le menu header.</div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Ordre</th>
                            <th>Libellé</th>
                            <th>URL</th>
                            <th>Statut</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($headerItems as $item)
                            @include('admin.menus._row', ['item' => $item, 'depth' => 0])
                            @foreach($item->children as $child)
                                @include('admin.menus._row', ['item' => $child, 'depth' => 1])
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Footer section --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Menu Footer</h3></div>
        <div class="card-body" style="padding: 0;">
            @if($footerItems->isEmpty())
                <div style="padding: 1rem; color: #6b7280;">Aucun item dans le menu footer.</div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Ordre</th>
                            <th>Libellé</th>
                            <th>URL</th>
                            <th>Statut</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($footerItems as $item)
                            @include('admin.menus._row', ['item' => $item, 'depth' => 0])
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
```

- [ ] **Step 2: Create `_row.blade.php` partial**

Create `resources/views/admin/menus/_row.blade.php`:

```blade
<tr style="{{ ! $item->is_active ? 'opacity: 0.5;' : '' }}">
    <td>
        <form action="{{ route('admin.menus.reorder', ['menu' => $item->id, 'direction' => 'up']) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary" style="padding: 0.125rem 0.375rem;" title="Monter">↑</button>
        </form>
        <form action="{{ route('admin.menus.reorder', ['menu' => $item->id, 'direction' => 'down']) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary" style="padding: 0.125rem 0.375rem;" title="Descendre">↓</button>
        </form>
    </td>
    <td>
        @if($depth > 0)
            <span style="color: #9ca3af; margin-right: 0.5rem;">└─</span>
        @endif
        <strong>{{ $item->label }}</strong>
    </td>
    <td><code style="background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.8125rem;">{{ $item->url }}</code></td>
    <td>
        @if($item->is_active)
            <span class="badge badge-success">Actif</span>
        @else
            <span class="badge badge-secondary">Inactif</span>
        @endif
        @if($item->open_in_new_tab)
            <span class="badge badge-info" style="margin-left: 0.25rem;">↗</span>
        @endif
    </td>
    <td>
        <a href="{{ route('admin.menus.edit', $item) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem;" title="Modifier">✏</a>
        <form action="{{ route('admin.menus.destroy', $item) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet item{{ $item->children->count() ? ' et ses ' . $item->children->count() . ' sous-items' : '' }} ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; color: #dc2626;" title="Supprimer">🗑</button>
        </form>
    </td>
</tr>
```

- [ ] **Step 3: Create `create.blade.php`**

Create `resources/views/admin/menus/create.blade.php`:

```blade
@extends('layouts.admin')
@section('title', 'Nouvel item de menu')
@section('breadcrumb')
    <a href="{{ route('admin.menus.index') }}">Menus</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div style="max-width: 800px;">
        <form action="{{ route('admin.menus.store') }}" method="POST">
            @csrf
            @include('admin.menus._form', ['menuItem' => null, 'availableParents' => $availableParents, 'defaultLocation' => $defaultLocation])

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">Créer l'item</button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
```

- [ ] **Step 4: Create `edit.blade.php`**

Create `resources/views/admin/menus/edit.blade.php`:

```blade
@extends('layouts.admin')
@section('title', 'Modifier ' . $menuItem->label)
@section('breadcrumb')
    <a href="{{ route('admin.menus.index') }}">Menus</a>
    <span>/</span>
    <span>Modifier "{{ $menuItem->label }}"</span>
@endsection

@section('content')
    <div style="max-width: 800px;">
        <form action="{{ route('admin.menus.update', $menuItem) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.menus._form', ['menuItem' => $menuItem, 'availableParents' => $availableParents, 'defaultLocation' => $menuItem->location])

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
```

- [ ] **Step 5: Create `_form.blade.php` partial**

Create `resources/views/admin/menus/_form.blade.php`:

```blade
@php
    $hubRoutes = [
        '/' => 'Accueil',
        '/a-propos' => 'Association',
        '/actualites' => 'Actualités',
        '/evenements' => 'Événements',
        '/lepis' => 'Lepis',
        '/lepis/bulletins' => 'Lepis — bulletins',
        '/revue' => 'Chersotis (revue)',
        '/adhesion' => 'Adhésion',
        '/contact' => 'Contact',
        '/connexion' => 'Connexion',
        '/inscription' => 'Inscription',
    ];
    $alreadyHasChildren = $menuItem && $menuItem->exists && $menuItem->children()->exists();
@endphp

{{-- Carte 1 — Identité --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Identité</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="label">Libellé *</label>
            <input type="text" name="label" id="label" class="form-input" value="{{ old('label', $menuItem?->label ?? '') }}" required>
            @error('label')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="location">Localisation *</label>
            <select name="location" id="location" class="form-input" required>
                <option value="header" {{ old('location', $menuItem?->location ?? $defaultLocation) === 'header' ? 'selected' : '' }}>Header</option>
                <option value="footer" {{ old('location', $menuItem?->location ?? $defaultLocation) === 'footer' ? 'selected' : '' }}>Footer</option>
            </select>
            @error('location')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        @if(! $alreadyHasChildren)
            <div class="form-group" id="parent-group" style="{{ old('location', $menuItem?->location ?? $defaultLocation) === 'footer' ? 'display: none;' : '' }}">
                <label class="form-label" for="parent_id">Parent (optionnel — uniquement pour le header)</label>
                <select name="parent_id" id="parent_id" class="form-input">
                    <option value="">— Aucun (item racine) —</option>
                    @foreach($availableParents as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $menuItem?->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->label }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        @endif
    </div>
</div>

{{-- Carte 2 — Cible --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Cible</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="hub_route_helper">Page hub (optionnel — pré-remplit l'URL)</label>
            <select id="hub_route_helper" class="form-input">
                <option value="">— Choisir une page... —</option>
                @foreach($hubRoutes as $path => $label)
                    <option value="{{ $path }}">{{ $label }} — {{ $path }}</option>
                @endforeach
            </select>
            <small style="color: #6b7280; font-size: 0.8125rem;">Sélectionne une page pour pré-remplir le champ URL ci-dessous. Tu peux ensuite l'éditer librement.</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="url">URL *</label>
            <input type="text" name="url" id="url" class="form-input" value="{{ old('url', $menuItem?->url ?? '') }}" required maxlength="500">
            <small style="color: #6b7280; font-size: 0.8125rem;">Path interne (<code>/actualites</code>), ancre (<code>#section</code>), ou URL externe (<code>https://...</code>).</small>
            @error('url')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="open_in_new_tab" value="1" {{ old('open_in_new_tab', $menuItem?->open_in_new_tab ?? false) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Ouvrir dans un nouvel onglet</span>
            </label>
        </div>
    </div>
</div>

{{-- Carte 3 — Affichage --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Affichage</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $menuItem?->is_active ?? true) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Item actif (visible côté hub)</span>
            </label>
        </div>

        <div class="form-group">
            <label class="form-label" for="sort_order">Ordre de tri</label>
            <input type="number" name="sort_order" id="sort_order" class="form-input" value="{{ old('sort_order', $menuItem?->sort_order ?? 0) }}" min="0" style="max-width: 120px;">
            <small style="color: #6b7280; font-size: 0.8125rem;">Entier croissant. Les items sont affichés du plus petit au plus grand. Les boutons ↑↓ de l'index permettent de modifier en swap.</small>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hubHelper = document.getElementById('hub_route_helper');
        const urlInput = document.getElementById('url');
        const locationSelect = document.getElementById('location');
        const parentGroup = document.getElementById('parent-group');

        // Pre-fill URL when hub route is chosen
        if (hubHelper && urlInput) {
            hubHelper.addEventListener('change', function () {
                if (this.value) {
                    urlInput.value = this.value;
                }
            });
            // Reset helper if user manually edits URL
            urlInput.addEventListener('input', function () {
                if (hubHelper.value && urlInput.value !== hubHelper.value) {
                    hubHelper.value = '';
                }
            });
        }

        // Hide parent select when location is footer
        if (locationSelect && parentGroup) {
            locationSelect.addEventListener('change', function () {
                parentGroup.style.display = this.value === 'footer' ? 'none' : '';
            });
        }
    });
</script>
@endpush
```

- [ ] **Step 6: Add the "Menus" link to the admin sidebar nav**

In `resources/views/layouts/admin.blade.php`, find line 162 (the `<a href="{{ route('admin.events.index') }}"...>` for Événements). Just AFTER its closing `</a>` (line 165), insert:

```blade
<a href="{{ route('admin.menus.index') }}" class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
    <i data-lucide="menu"></i>
    <span>Menus</span>
</a>
```

The result will be: Articles → Événements → **Menus** → Brevo (Emails) → Import / Export.

- [ ] **Step 7: Run all admin menu tests**

`php artisan test --filter=MenuItemControllerTest`
Expected: 7 passing (the previously failing `index` and `create` tests now pass with views in place).

- [ ] **Step 8: Commit**

```bash
git add resources/views/admin/menus/ resources/views/layouts/admin.blade.php
git commit -m "feat(menus): vues admin (index + create + edit + form) + lien sidebar"
```

---

## Task 6 — Rendu côté hub (View Composer + partials)

**Files:**
- Modify: `app/Providers/AppServiceProvider.php` — ajout View Composer
- Modify: `resources/views/partials/hub/header.blade.php` — boucles Blade (desktop + mobile)
- Modify: `resources/views/partials/hub/footer.blade.php` — boucle Blade
- Create: `tests/Feature/Hub/MenuRenderingTest.php`

- [ ] **Step 1: Write failing tests**

Create `tests/Feature/Hub/MenuRenderingTest.php`:

```php
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
```

- [ ] **Step 2: Run tests, expect failure**

`php artisan test --filter=MenuRenderingTest`
Expected: FAIL — current header has hardcoded items, no `$headerMenu` variable.

- [ ] **Step 3: Add the View Composer in AppServiceProvider**

In `app/Providers/AppServiceProvider.php`, inside the `boot()` method (after the existing `View::composer(...)` calls if any, otherwise just add it), insert:

```php
\Illuminate\Support\Facades\View::composer(
    ['layouts.hub', 'partials.hub.header', 'partials.hub.footer'],
    function ($view) {
        $renderer = app(\App\Services\MenuRenderer::class);
        $view->with('headerMenu', $renderer->forLocation('header'));
        $view->with('footerMenu', $renderer->forLocation('footer'));
    }
);
```

Read the file first to find a logical place. Add it at the end of the `boot()` method body, before the closing `}` of `boot()`.

- [ ] **Step 4: Replace header.blade.php nav blocks**

In `resources/views/partials/hub/header.blade.php`, replace the **desktop nav block** (lines 16-22, the `<nav class="hub-nav">...</nav>` with hardcoded `<a>` tags) with:

```blade
<nav class="hub-nav">
    @foreach($headerMenu as $item)
        @if($item->children->isEmpty())
            <a href="{{ $item->url }}"
               @class(['active' => request()->path() === ltrim($item->url, '/')])
               {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' }}>
                {{ $item->label }}
            </a>
        @else
            <div class="hub-nav-dropdown" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <a href="{{ $item->url }}" class="hub-nav-dropdown-toggle"
                   {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' }}>
                    {{ $item->label }} ▾
                </a>
                <div class="hub-nav-dropdown-menu" x-show="open" x-transition style="display: none;">
                    @foreach($item->children as $child)
                        <a href="{{ $child->url }}"
                           {{ $child->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' }}>
                            {{ $child->label }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</nav>
```

Replace the **mobile nav block** (lines 49-56, the `<nav class="hub-nav-mobile">...</nav>`) with:

```blade
<nav class="hub-nav-mobile">
    @foreach($headerMenu as $item)
        @if($item->children->isEmpty())
            <a href="{{ $item->url }}" {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' }}>
                {{ $item->label }}
            </a>
        @else
            <div x-data="{ subOpen: false }">
                <button type="button" class="hub-nav-mobile-toggle" @click="subOpen = !subOpen" style="display: flex; align-items: center; justify-content: space-between; width: 100%; background: none; border: none; padding: inherit; cursor: pointer; text-align: left; font: inherit; color: inherit;">
                    <span>{{ $item->label }}</span>
                    <span x-text="subOpen ? '▴' : '▾'"></span>
                </button>
                <div x-show="subOpen" x-transition x-cloak style="padding-left: 1rem;">
                    @foreach($item->children as $child)
                        <a href="{{ $child->url }}" {{ $child->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' }}>
                            {{ $child->label }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</nav>
```

- [ ] **Step 5: Replace footer.blade.php links block**

In `resources/views/partials/hub/footer.blade.php`, replace the `<div class="footer-links">...</div>` block (lines 9-16, with hardcoded `<a>` tags) with:

```blade
<div class="footer-links">
    @foreach($footerMenu as $item)
        <a href="{{ $item->url }}" {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' }}>
            {{ $item->label }}
        </a>
    @endforeach
</div>
```

- [ ] **Step 6: Add CSS for `.hub-nav-dropdown-menu`**

The dropdown menu needs CSS to position itself correctly. Read `resources/css/app.css` (or the hub-specific CSS file — check if there's a `resources/css/hub.css` or similar). Find a good place to add:

```css
.hub-nav-dropdown {
    position: relative;
    display: inline-block;
}

.hub-nav-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 0.5rem 0;
    min-width: 200px;
    z-index: 100;
    display: flex;
    flex-direction: column;
}

.hub-nav-dropdown-menu a {
    padding: 0.5rem 1rem;
    color: #1C2B27;
    text-decoration: none;
    font-size: 0.95rem;
}

.hub-nav-dropdown-menu a:hover {
    background: #f9fafb;
}
```

If the app uses Tailwind v4 with no separate CSS file, add this block at the end of `resources/css/app.css` (typically the main stylesheet).

After adding the CSS, run:
```bash
npm run build
```
Expected: Vite rebuilds the CSS bundle without errors. Output mentions `app.css` size.

- [ ] **Step 7: Run all tests**

`php artisan test --filter=MenuRenderingTest`
Expected: 3 passing.

`php artisan test --filter=MenuRenderingTest|MenuItemControllerTest|MenuRendererTest|MenuItemTest`
Expected: 5 + 7 + 4 + 5 = 21 (or whatever cumulative count, all green).

- [ ] **Step 8: Commit**

```bash
git add app/Providers/AppServiceProvider.php resources/views/partials/hub/header.blade.php resources/views/partials/hub/footer.blade.php resources/css/app.css public/build tests/Feature/Hub/MenuRenderingTest.php
git commit -m "feat(menus): rendu côté hub via View Composer + dropdown CSS"
```

NB: don't commit `public/build` if it's gitignored — check `.gitignore` first. If gitignored, just commit the source files.

---

## Task 7 — Cache invalidation tests + smoke + merge

**Files:**
- Create: `tests/Feature/Admin/MenuCacheInvalidationTest.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/Admin/MenuCacheInvalidationTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\MenuItem;
use App\Services\MenuRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MenuCacheInvalidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_item_invalidates_the_cache(): void
    {
        // Prime cache
        $renderer = new MenuRenderer();
        $first = $renderer->forLocation('header');
        $this->assertCount(0, $first);

        // Create an item — observer should invalidate cache
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
```

- [ ] **Step 2: Run, expect pass directly**

`php artisan test --filter=MenuCacheInvalidationTest`
Expected: 2 passing immediately (the observer was set up in Task 1, the renderer in Task 2).

- [ ] **Step 3: Run full suite**

`php -d memory_limit=512M artisan test 2>&1 | tail -4`
Expected: 486 passing (472 previous + 14 new — adjust if some tests overlapped). All green.

- [ ] **Step 4: Manual smoke**

Start server: `php artisan serve`

- Visit `http://localhost:8000/extranet/menus` — see Header and Footer sections with the 12 seeded items, badges Actif/Inactif, boutons ↑↓ ✏ 🗑.
- Click "Nouvel item", remplir, soumettre. L'item doit apparaître dans la liste.
- Editer un item existant : changer le label, sauvegarder, vérifier que le label est mis à jour côté admin ET côté hub (`http://localhost:8000/`).
- Sur le hub `/`, vérifier que la nav header montre les items actifs (Association, Actualités, Chersotis) et pas les placeholders cassés (Projets, Réseau).
- Cliquer sur un item header pour vérifier que la navigation fonctionne.
- Tester le footer en bas de la page (5 items actifs visibles : Association / Portail / Actualités / Réseau / + ceux que tu actives).
- Tester sur mobile (réduire la fenêtre) : hamburger ouvre le menu mobile, items visibles.

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/Admin/MenuCacheInvalidationTest.php
git commit -m "test(menus): tests d'invalidation du cache au save/delete"
```

- [ ] **Step 6: Merge to main + push + cleanup**

```bash
git checkout main
git merge --no-ff feature/admin-menus -m "Merge branch 'feature/admin-menus'

Gestion admin des menus du hub public:
- Table menu_items + modèle MenuItem (hiérarchie 2 niveaux, location header/footer)
- Service MenuRenderer mis en cache 1h, invalidé par observer
- CRUD admin /extranet/menus avec helper Page hub
- Migration des 12 items hardcodés via seeder idempotent
- View Composer injecte headerMenu/footerMenu dans les partials hub
- 14 nouveaux tests, suite full verte"

php artisan migrate
php artisan db:seed --class=MenuItemsSeeder
php artisan view:clear
php artisan cache:clear

git push origin main
git branch -d feature/admin-menus
```

---

## Self-review

**Spec coverage:**

| Spec section | Tasks |
|---|---|
| Migration menu_items + CHECK location | Task 1 |
| Modèle MenuItem (constants, relations, scopes, observer cache) | Task 1 |
| Service MenuRenderer cache 1h | Task 2 |
| Migration des 12 items hardcodés (4 désactivés) | Task 3 |
| Routes admin (resource + reorder) | Task 4 |
| Controller CRUD + reorder + validation custom (parent rules) | Task 4 |
| Vues admin (index avec ↑↓, create/edit, _form avec helper hub) | Task 5 |
| Sidebar admin nav lien "Menus" après Événements | Task 5 (Step 6) |
| View Composer headerMenu/footerMenu | Task 6 |
| header.blade.php desktop + mobile boucle Blade | Task 6 |
| footer.blade.php boucle Blade | Task 6 |
| CSS .hub-nav-dropdown-menu | Task 6 |
| Tests : 14 au total répartis | Tasks 1, 2, 4, 6, 7 |
| Rollout (migrate + seed + cache:clear) | Task 7 (Step 6) |

Toutes les exigences couvertes.

**Type consistency:**
- `MenuItem::LOCATION_HEADER`/`LOCATION_FOOTER` constantes utilisées dans tests Task 1, Service Task 2, Controller Task 4.
- `headerMenu` / `footerMenu` variables Blade cohérentes dans View Composer (Task 6) et utilisations dans header/footer.blade.php (Task 6).
- `MenuRenderer::forLocation(string $location)` signature cohérente entre Task 2 (impl) et Task 6 (View Composer call).
- Cache key `menu.{location}` cohérent : `menu.header` / `menu.footer` dans MenuItem observer (Task 1) + MenuRenderer (Task 2) + tests (Task 1, 7).
- Boucles Blade utilisent toutes le pattern `$item->children->isEmpty()` + `target="_blank" rel="noopener"` quand `open_in_new_tab`.

**Placeholders:** none. Tous les blocs de code sont complets.

**Notes pour l'engineer:**
- Le View Composer fonctionne sur des views Blade ; il s'exécute lors du rendu. Si une page d'admin lit `partials.hub.header`, elle déclenchera aussi la requête menu. Pas grave car cache 1h.
- L'icône `data-lucide="menu"` pour la sidebar est l'icône hamburger générique — adaptable si une autre icône Lucide est plus parlante (`list`, `align-justify`, etc.).
- Le seeder utilise `MenuItem::create()` qui déclenche l'observer → invalide le cache 12 fois. Pas grave (cache vide donc rien à invalider). Si problème, on peut désactiver l'observer pendant le seed.
