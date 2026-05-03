# Gestion admin des menus du hub public

**Date** : 2026-05-03
**Statut** : design validé, prêt pour planification
**Cibles** : nouvelle section admin `/extranet/menus` + remplacement des menus hardcodés sur le hub public.

## Contexte

Aujourd'hui les menus du hub public (header desktop, header mobile, footer) sont **100% hardcodés** dans :
- `resources/views/partials/hub/header.blade.php` (lignes 16–22 desktop, 49–56 mobile)
- `resources/views/partials/hub/footer.blade.php` (lignes 9–16)

Le header contient 5 items (Association / Projets / Actualités / Réseau / Chersotis), dont **2 placeholders cassés** (`#projets`, `#reseau` qui ne pointent nulle part). Le footer contient 7 items, dont **3 placeholders cassés** (`#projets`, `#mentions-legales`, `#politique-de-donnees`).

L'admin ne peut rien éditer sans toucher le code Blade. Cette refonte permet de gérer les menus depuis l'extranet, avec :
- Hiérarchie 2 niveaux (parent + enfants en dropdown)
- Localisation header / footer dans une seule table
- URL libre avec helper "page hub" pour faciliter la saisie
- Activation/désactivation, ordre, ouverture nouvelle fenêtre

## Décisions de conception

| Décision | Choix retenu | Alternative écartée |
|---|---|---|
| Périmètre | Header + Footer dans **un seul système** (colonne `location`) | 2 tables séparées (rejeté : pas de gain pour la duplication) |
| Profondeur hiérarchie | **2 niveaux fixes** (parent + enfants) | 3 niveaux ou récursif illimité (rejeté : YAGNI, complexifie l'UI admin) |
| Type de cible | **Champ `url` libre + helper dropdown "page hub"** | Champ url libre seul (moins user-friendly) ou typage strict route/url/anchor/external (over-engineering) |
| Reorder | **Boutons ↑↓** (swap voisin) | Drag-and-drop (rejeté : dépendance JS supplémentaire pour 5–10 items max) |
| Footer | **Plat** (les `parent_id` ignorés au rendu) | Hiérarchie footer (rejeté : pratique standard plat en footer) |
| Multilingue | **Non** (FR seulement, aligné avec le projet) | EN (rejeté : aucune infra i18n existante) |

## Modèle de données

### Migration `create_menu_items_table`

```sql
CREATE TABLE menu_items (
    id BIGSERIAL PRIMARY KEY,
    parent_id BIGINT NULL REFERENCES menu_items(id) ON DELETE CASCADE,
    location VARCHAR(20) NOT NULL,
    label VARCHAR(255) NOT NULL,
    url VARCHAR(500) NOT NULL,
    sort_order INTEGER NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    open_in_new_tab BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

ALTER TABLE menu_items ADD CONSTRAINT menu_items_location_check
    CHECK (location IN ('header', 'footer'));

CREATE INDEX idx_menu_items_location_active_order ON menu_items (location, is_active, sort_order);
CREATE INDEX idx_menu_items_parent ON menu_items (parent_id);
```

PG 9.6 compat : tout est en SQL standard. Pas de `->change()`. Création via Schema::create est OK + ajout du CHECK via `DB::statement` brut pour rester cohérent avec les autres migrations du projet.

### Modèle `App\Models\MenuItem`

```php
class MenuItem extends Model
{
    public const LOCATION_HEADER = 'header';
    public const LOCATION_FOOTER = 'footer';

    protected $fillable = [
        'parent_id', 'location', 'label', 'url',
        'sort_order', 'is_active', 'open_in_new_tab',
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
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeForLocation($q, string $loc) { return $q->where('location', $loc); }

    /** Un item dont parent_id != null ne peut pas avoir d'enfants (limite à 2 niveaux). */
    public function canHaveChildren(): bool
    {
        return $this->parent_id === null;
    }

    protected static function booted(): void
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('menu.header');
            \Illuminate\Support\Facades\Cache::forget('menu.footer');
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('menu.header');
            \Illuminate\Support\Facades\Cache::forget('menu.footer');
        });
    }
}
```

### Service `App\Services\MenuRenderer`

```php
class MenuRenderer
{
    public function forLocation(string $location): \Illuminate\Support\Collection
    {
        return \Illuminate\Support\Facades\Cache::remember(
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

Cache TTL 3600s, invalidé immédiatement par l'observer du modèle. La requête est idempotente et ne fait pas de N+1 grâce à `with('children')`.

## Backoffice admin

### Routes

Dans `routes/admin.php` (préfixe `extranet`, middleware `admin`) :

```php
Route::resource('menus', \App\Http\Controllers\Admin\MenuItemController::class);
Route::post('menus/{menu}/reorder/{direction}', [\App\Http\Controllers\Admin\MenuItemController::class, 'reorder'])
    ->whereIn('direction', ['up', 'down'])
    ->name('menus.reorder');
```

URLs résultantes : `/extranet/menus`, `/extranet/menus/create`, `/extranet/menus/{id}/edit`, etc.

### Controller `Admin\MenuItemController`

Méthodes : `index`, `create`, `store`, `edit`, `update`, `destroy`, `reorder`.

**Validation** (store + update identiques sauf parent_id selon location) :

```php
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

// Règles custom
if ($validated['location'] === 'footer') {
    $validated['parent_id'] = null;  // footer plat, pas de parent
}
if (! empty($validated['parent_id'])) {
    $parent = MenuItem::findOrFail($validated['parent_id']);
    if ($parent->parent_id !== null) {
        throw ValidationException::withMessages(['parent_id' => 'Le parent choisi est déjà un sous-item (limite à 2 niveaux).']);
    }
    if ($parent->location !== $validated['location']) {
        throw ValidationException::withMessages(['parent_id' => 'Le parent doit être dans la même localisation.']);
    }
}

// Empêche un item qui a déjà des enfants de devenir lui-même enfant
if ($menuItem->exists && ! empty($validated['parent_id']) && $menuItem->children()->exists()) {
    throw ValidationException::withMessages(['parent_id' => 'Cet item a déjà des sous-items, il ne peut pas devenir lui-même un sous-item.']);
}
```

**Méthode `reorder($menu, $direction)`** : trouve le voisin du même `(location, parent_id)` dans la direction donnée, swap les `sort_order`, save les deux.

### Vues admin

`resources/views/admin/menus/index.blade.php` :
- Bouton "Nouvel item" en haut à droite
- 2 sections empilées (`.card`) : "Menu Header" et "Menu Footer"
- Chaque section liste les items racine + leurs enfants indentés `└─`
- Boutons ↑↓ ✏ 🗑 sur chaque ligne
- Items inactifs grisés (opacity 0.5) avec badge "Inactif"

`resources/views/admin/menus/_form.blade.php` :
- 3 cartes thématiques (cohérent avec le pattern fiche membre/event) : Identité / Cible / Affichage
- **Helper "Page hub"** : `<select>` avec ~10 options des routes hub courantes. Au `change` JS, injecte le path correspondant dans le champ `url`. Si l'admin tape manuellement dans `url`, le select repasse à `-- Choisir... --`
- Liste des routes hub utilisées dans le helper :

| Label | Path |
|---|---|
| Accueil | `/` |
| Association | `/a-propos` |
| Actualités | `/actualites` |
| Événements | `/evenements` |
| Lepis | `/lepis` |
| Lepis — bulletins | `/lepis/bulletins` |
| Chersotis (revue) | `/revue` |
| Adhésion | `/adhesion` |
| Contact | `/contact` |
| Connexion | `/connexion` |
| Inscription | `/inscription` |

Le sélecteur "parent" est masqué quand `location === 'footer'` ou quand l'item édité a déjà des enfants. Filtré pour ne lister que les items racine de la même location.

### Sidebar admin nav

Ajout d'un lien **"Menus"** dans la sidebar nav admin, **à côté des liens Articles et Événements** (section "Contenu" de la sidebar). Icône Lucide `menu` ou `list`. Implémentation : trouver le bloc qui contient les liens Articles/Événements dans `resources/views/layouts/admin.blade.php` (ou son partial nav) et insérer le lien `admin.menus.index` dans le même groupe, juste après "Événements".

## Rendu côté hub

### View Composer

Dans `app/Providers/AppServiceProvider::boot()` :

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

### `partials/hub/header.blade.php`

Remplace le bloc hardcodé :

```blade
<nav class="hub-nav">
    @foreach($headerMenu as $item)
        @if($item->children->isEmpty())
            <a href="{{ $item->url }}" {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' }}>{{ $item->label }}</a>
        @else
            <div class="hub-nav-dropdown" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <a href="{{ $item->url }}" class="hub-nav-dropdown-toggle">{{ $item->label }} ▾</a>
                <div class="hub-nav-dropdown-menu" x-show="open" x-transition style="display: none;">
                    @foreach($item->children as $child)
                        <a href="{{ $child->url }}" {{ $child->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' }}>{{ $child->label }}</a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</nav>
```

Le menu mobile (`.hub-nav-mobile`, même fichier) suit le même pattern mais en accordion. Si un parent a des enfants, on rend une section dépliable avec Alpine.js (cohérent avec l'existant qui utilise déjà Alpine pour le toggle hamburger).

CSS pour `.hub-nav-dropdown-menu` ajouté dans la feuille hub existante (~10 lignes) : position absolute, fond blanc, ombre, padding, `z-index: 100`, items en flex column.

### `partials/hub/footer.blade.php`

```blade
<div class="footer-links">
    @foreach($footerMenu as $item)
        <a href="{{ $item->url }}" {{ $item->open_in_new_tab ? 'target="_blank" rel="noopener"' : '' }}>{{ $item->label }}</a>
    @endforeach
</div>
```

Footer rendu à plat : ignore complètement les `children` même si présents (un anomalie data ne casse pas le rendu).

### Robustesse face à un menu vide

Si la table est vide ou tous les items sont `is_active=false`, la nav s'affiche sans aucun lien. Pas de fallback hardcodé. Cohérent avec "l'admin contrôle tout".

## Migration des items hardcodés existants

`database/seeders/MenuItemsSeeder.php` :

| Location | Label | URL | sort_order | is_active |
|---|---|---|---|---|
| header | Association | `/a-propos` | 10 | ✓ |
| header | Projets | `#projets` | 20 | ✗ (placeholder cassé) |
| header | Actualités | `/actualites` | 30 | ✓ |
| header | Réseau | `#reseau` | 40 | ✗ (placeholder cassé) |
| header | Chersotis | `/revue` | 50 | ✓ |
| footer | Association | `/a-propos` | 10 | ✓ |
| footer | Portail | `/` | 20 | ✓ |
| footer | Projets | `#` | 30 | ✗ (placeholder) |
| footer | Actualités | `/actualites` | 40 | ✓ |
| footer | Réseau | `/contact` | 50 | ✓ |
| footer | Mentions légales | `#` | 60 | ✗ (placeholder) |
| footer | Politique de données | `#` | 70 | ✗ (placeholder) |

Les 4 placeholders cassés sont migrés en **désactivés** pour ne pas se retrouver en front. L'admin pourra les réactiver après avoir corrigé l'URL.

Seeder **idempotent** : `if (MenuItem::count() > 0) return;` au début, pour permettre de re-run sans dupliquer.

Appel : `php artisan db:seed --class=MenuItemsSeeder` lors du déploiement.

## Tests

**`tests/Unit/Services/MenuRendererTest.php`** (4 tests) :
- `test_returns_only_active_items` — items inactifs filtrés
- `test_returns_items_for_correct_location` — header ne contient pas d'items footer
- `test_returns_items_in_sort_order` — tri respecte sort_order ASC puis id ASC
- `test_eager_loads_children_filtered_active` — un parent actif avec 2 enfants dont 1 inactif → seul l'enfant actif visible

**`tests/Feature/Admin/MenuItemControllerTest.php`** (5 tests) :
- `test_index_shows_both_header_and_footer_sections`
- `test_create_form_hides_parent_select_when_location_is_footer`
- `test_store_validates_required_fields`
- `test_store_rejects_parent_assignment_to_already_child_item`
- `test_destroy_cascades_children`

**`tests/Feature/Hub/MenuRenderingTest.php`** (3 tests) :
- `test_header_renders_active_items_with_correct_urls`
- `test_dropdown_appears_for_parent_with_children`
- `test_open_in_new_tab_renders_target_blank_attribute`

**`tests/Feature/Admin/MenuCacheInvalidationTest.php`** (2 tests) :
- `test_saving_menu_item_invalidates_cache`
- `test_deleting_menu_item_invalidates_cache`

**14 tests au total.**

## Rollout

1. **Migration schema** : `php artisan migrate` crée la table `menu_items`.
2. **Migration data** : `php artisan db:seed --class=MenuItemsSeeder` peuple la table avec les items existants (idempotent).
3. **Cache clear** : `php artisan view:clear` + `php artisan cache:clear` (le menu cache est vide à la première requête).
4. Le rendu hub bascule automatiquement sur le nouveau système.
5. **Aucune régression front** : les items actifs migrés rendent à l'identique du hardcodé. Les 4 placeholders cassés disparaissent du front (= amélioration).
6. **Admin reprend la main** : peut éditer chaque item, en désactiver, en ajouter, réactiver les "Projets" / "Réseau" quand les pages existent.

## Hors scope (différé)

- **Drag-and-drop** pour reorder (boutons ↑↓ suffisent en v1)
- **Multilingue** (FR seulement)
- **Icônes par item**
- **Affichage conditionnel par rôle** (visible si auth / membre / etc.)
- **Locations supplémentaires** (sidebar espace membre, footer mobile dédié) — la colonne `location` permet d'étendre, à faire au cas par cas
- **Historique / audit trail** des modifications
- **Scopes temporels** (item actif du X au Y)

## Compatibilité

- **PostgreSQL 9.6 prod** : migration en SQL standard, CHECK constraint via `DB::statement` brut. `JSONB` non utilisé.
- **Cache view** : invalidé par observer modèle.
- **Tests existants** : aucun test ne devrait casser. Le hub rendrait jusqu'à présent les liens hardcodés ; après le seeder le rendu est identique en données mais via DB.
- **Aucune route renommée** : tous les liens existants continuent de fonctionner.

## Référence

- Audit du 2026-05-03
- Pattern de cache existant : `App\Models\Setting::get()` (TTL 3600s)
- Layouts hub existants : `resources/views/layouts/hub.blade.php`, `partials/hub/header.blade.php`, `partials/hub/footer.blade.php`
- Mémoires projet : `feedback_no_filament.md` (admin Blade custom — respecté), `feedback_postgres_96_migrations.md` (PG 9.6 raw SQL pour CHECK)
