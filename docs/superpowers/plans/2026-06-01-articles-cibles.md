# Articles ciblés par rôle adhérent — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Taguer les articles par visibilité (public / adhérents / fonctions CA-Bureau-Validateur) et les surfacer dans l'espace membre (aperçu tableau de bord + page dédiée), sans jamais exposer les articles non-publics sur le hub ni l'API.

**Architecture:** Décalque la fonctionnalité « événements ciblés ». On étend la table `articles` (`visibility` + `audience_roles` JSONB), on ajoute des scopes `publicOnly`/`visibleToMember` + `isVisibleToMember(?Member)` sur `Article` (mêmes sémantiques : cascade bureau⊇ca, validateur orthogonal — réutilise `Member::effectiveAdherentRoles()`). Toutes les surfaces publiques filtrent `publicOnly`; les surfaces membre filtrent `visibleToMember` en flux unifié, avec gating « membre à jour ».

**Tech Stack:** Laravel 12, PostgreSQL (JSONB + `whereJsonContains`), Blade + Alpine, PHPUnit (DB `oreina_test`).

**Spec:** `docs/superpowers/specs/2026-06-01-articles-cibles-design.md`

**Conventions projet :**
- Tests : `php artisan test --filter=NomDuTest` (DB `oreina_test`, jamais `migrate:fresh`).
- Migrations : `DB::statement` + `JSONB` (compat PostgreSQL 9.6), index nommés.
- Accents FR : conserver tels quels dans les vues.
- Pas de `Co-Authored-By` dans les commits.
- `Member::ADHERENT_ROLES` (clés `ca`/`bureau`/`validateur`), `Member::effectiveAdherentRoles()` et `Member::hasAdherentRole()` existent déjà.

---

## LOT 1 — Socle & sécurité

### Task 1: Migration `articles.visibility` + constantes/casts Article

**Files:**
- Create: `database/migrations/2026_06_01_110001_add_visibility_to_articles.php`
- Modify: `app/Models/Article.php`

- [ ] **Step 1: Créer la migration (raw SQL, compat PG 9.6)**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL 9.6 compatible : raw SQL.
        DB::statement("ALTER TABLE articles ADD COLUMN visibility VARCHAR(255) NOT NULL DEFAULT 'public'");
        DB::statement("ALTER TABLE articles ADD COLUMN audience_roles JSONB NULL");
        DB::statement("CREATE INDEX articles_visibility_published_at_index ON articles (visibility, published_at)");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS articles_visibility_published_at_index");
        Schema::table('articles', function ($table) {
            $table->dropColumn(['visibility', 'audience_roles']);
        });
    }
};
```

- [ ] **Step 2: Lancer la migration**

Run: `php artisan migrate`
Expected: DONE sur `..._add_visibility_to_articles`.

- [ ] **Step 3: Étendre `app/Models/Article.php`**

Ajouter les constantes (en tête de classe, avant `$fillable`) :

```php
    public const VIS_PUBLIC = 'public';
    public const VIS_MEMBERS = 'members';
    public const VIS_RESTRICTED = 'restricted';
```

Ajouter `'visibility'` et `'audience_roles'` au tableau `$fillable`.
Ajouter `'audience_roles' => 'array',` au tableau `$casts`.

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_06_01_110001_add_visibility_to_articles.php app/Models/Article.php
git commit -m "feat(articles): colonnes visibility + audience_roles"
```

---

### Task 2: Scopes `visibleToMember`/`publicOnly` + `isVisibleToMember`

**Files:**
- Modify: `app/Models/Article.php`
- Test: `tests/Feature/Member/ArticleVisibilityTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Member;

use App\Models\Article;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function currentMember(array $attrs = []): Member
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create(array_merge([
            'user_id' => $u->id, 'member_number' => 'M'.uniqid(), 'email' => $u->email,
            'first_name' => 'A', 'last_name' => 'B', 'joined_at' => now(), 'is_active' => true,
        ], $attrs));
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return $m;
    }

    private function article(array $attrs = []): Article
    {
        return Article::create(array_merge([
            'title' => 'A'.uniqid(), 'slug' => 'a'.uniqid(), 'content' => 'x',
            'status' => 'published', 'published_at' => now()->subDay(), 'visibility' => Article::VIS_PUBLIC,
        ], $attrs));
    }

    public function test_simple_member_sees_public_and_members_only(): void
    {
        $m = $this->currentMember();
        $pub = $this->article(['visibility' => Article::VIS_PUBLIC]);
        $mem = $this->article(['visibility' => Article::VIS_MEMBERS]);
        $res = $this->article(['visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        $ids = Article::visibleToMember($m)->pluck('id');

        $this->assertTrue($ids->contains($pub->id));
        $this->assertTrue($ids->contains($mem->id));
        $this->assertFalse($ids->contains($res->id));
    }

    public function test_bureau_sees_ca_restricted_article(): void
    {
        $m = $this->currentMember(['adherent_roles' => ['bureau']]);
        $ca = $this->article(['visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['ca']]);
        $val = $this->article(['visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['validateur']]);

        $ids = Article::visibleToMember($m)->pluck('id');

        $this->assertTrue($ids->contains($ca->id));
        $this->assertFalse($ids->contains($val->id));
    }

    public function test_public_only_scope_excludes_non_public(): void
    {
        $this->article(['visibility' => Article::VIS_PUBLIC]);
        $this->article(['visibility' => Article::VIS_MEMBERS]);

        $this->assertSame(1, Article::publicOnly()->count());
    }

    public function test_is_visible_to_member_guards_null_and_non_public(): void
    {
        $pub = $this->article(['visibility' => Article::VIS_PUBLIC]);
        $mem = $this->article(['visibility' => Article::VIS_MEMBERS]);

        $this->assertTrue($pub->isVisibleToMember(null));
        $this->assertFalse($mem->isVisibleToMember(null));
        $this->assertTrue($mem->isVisibleToMember($this->currentMember()));
    }
}
```

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=ArticleVisibilityTest`
Expected: FAIL (`Call to undefined method ... visibleToMember()`).

- [ ] **Step 3: Implémenter dans `app/Models/Article.php`**

Ajouter (les types `Member` sont dans le même namespace `App\Models`) :

```php
    public function scopePublicOnly($query)
    {
        return $query->where('visibility', self::VIS_PUBLIC);
    }

    public function scopeVisibleToMember($query, Member $member)
    {
        $roles = $member->effectiveAdherentRoles();

        return $query->where(function ($q) use ($roles) {
            $q->whereIn('visibility', [self::VIS_PUBLIC, self::VIS_MEMBERS]);

            if (! empty($roles)) {
                $q->orWhere(function ($r) use ($roles) {
                    $r->where('visibility', self::VIS_RESTRICTED)
                      ->where(function ($rr) use ($roles) {
                          foreach ($roles as $role) {
                              $rr->orWhereJsonContains('audience_roles', $role);
                          }
                      });
                });
            }
        });
    }

    public function isVisibleToMember(?Member $member): bool
    {
        if ($this->visibility === self::VIS_PUBLIC) {
            return true;
        }
        if (! $member || ! $member->isCurrentMember()) {
            return false;
        }

        return match ($this->visibility) {
            self::VIS_MEMBERS => true,
            self::VIS_RESTRICTED => (bool) array_intersect(
                $this->audience_roles ?? [], $member->effectiveAdherentRoles()
            ),
            default => false,
        };
    }
```

Ajouter `use App\Models\Member;` seulement si le linter l'exige (même namespace : normalement inutile).

- [ ] **Step 4: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=ArticleVisibilityTest`
Expected: PASS (4 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Models/Article.php tests/Feature/Member/ArticleVisibilityTest.php
git commit -m "feat(articles): scopes visibleToMember/publicOnly + isVisibleToMember"
```

---

### Task 3: Hub — articles publics uniquement + garde détail + audit fuites

**Files:**
- Modify: `app/Http/Controllers/Hub/ArticleController.php`
- Modify: `app/Http/Controllers/Hub/HomeController.php:13-22`
- Test: `tests/Feature/Hub/HubArticleVisibilityTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Hub;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubArticleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function article(array $attrs = []): Article
    {
        return Article::create(array_merge([
            'title' => 'A'.uniqid(), 'slug' => 'a'.uniqid(), 'content' => 'x',
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
```

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=HubArticleVisibilityTest`
Expected: FAIL (l'article `members` apparaît / show 200 au lieu de 404).

- [ ] **Step 3: Filtrer `Hub\ArticleController`**

Dans `index()` : remplacer `Article::published()` par `Article::published()->publicOnly()`.

Dans `category()` : remplacer `Article::published()` (la requête `$articles`) par `Article::published()->publicOnly()`.

Dans `show(Article $article)` : remplacer le bloc d'entrée

```php
        if ($article->status !== 'published') {
            abort(404);
        }
```

par

```php
        $member = auth()->check()
            ? \App\Models\Member::where('user_id', auth()->id())->first()
            : null;

        abort_unless($article->isPublished() && $article->isVisibleToMember($member), 404);
```

et, dans le même `show()`, ajouter `->publicOnly()` à la requête `$relatedArticles` (entre `Article::published()` et `->where('id', ...)`).

- [ ] **Step 4: Filtrer `Hub\HomeController`**

Ajouter `->publicOnly()` aux DEUX requêtes (`$featuredArticles` et `$latestArticles`) — juste après `Article::published()`.

- [ ] **Step 5: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=HubArticleVisibilityTest`
Expected: PASS (4 tests).
No-regression : `php artisan test --filter=ArticleVisibility`

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Hub/ArticleController.php app/Http/Controllers/Hub/HomeController.php tests/Feature/Hub/HubArticleVisibilityTest.php
git commit -m "feat(hub): articles publics uniquement (index/categorie/lies/accueil) + garde page detail"
```

---

### Task 4: API publique — articles publics uniquement

**Files:**
- Modify: `app/Http/Controllers/Api/ArticleController.php`
- Test: `tests/Feature/Api/ArticleApiVisibilityTest.php`

Routes : `GET /api/v1/articles` (index), `GET /api/v1/articles/{article:slug}` (show), `GET /api/v1/articles/category/{category}` (byCategory).

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleApiVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function article(array $attrs = []): Article
    {
        return Article::create(array_merge([
            'title' => 'A'.uniqid(), 'slug' => 'a'.uniqid(), 'content' => 'x',
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
```

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=ArticleApiVisibilityTest`
Expected: FAIL (les articles `members` fuient).

- [ ] **Step 3: Filtrer `Api\ArticleController`**

- `index()` : `Article::published()` → `Article::published()->publicOnly()`.
- `byCategory()` : `Article::published()` → `Article::published()->publicOnly()`.
- `show(Article $article)` : remplacer

```php
        if (!$article->isPublished()) {
            abort(404);
        }
```

par

```php
        if (!$article->isPublished() || $article->visibility !== Article::VIS_PUBLIC) {
            abort(404);
        }
```

- [ ] **Step 4: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=ArticleApiVisibilityTest`
Expected: PASS (3 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Api/ArticleController.php tests/Feature/Api/ArticleApiVisibilityTest.php
git commit -m "feat(api): articles publics uniquement (index/categorie/detail)"
```

---

### Task 5: Formulaire admin — visibilité + rôles ciblés

**Files:**
- Modify: `resources/views/admin/articles/_form.blade.php`
- Modify: `app/Http/Controllers/Admin/ArticleController.php` (`store` ~77-89, `update`)
- Test: `tests/Feature/Admin/AdminArticleVisibilityTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminArticleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_restricted_article_with_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.articles.store'), [
                'title' => 'Note interne CA',
                'content' => 'corps',
                'status' => 'draft',
                'visibility' => 'restricted',
                'audience_roles' => ['ca', 'bureau'],
            ])->assertRedirect();

        $article = Article::where('title', 'Note interne CA')->firstOrFail();
        $this->assertSame('restricted', $article->visibility);
        $this->assertEqualsCanonicalizing(['ca', 'bureau'], $article->audience_roles);
    }

    public function test_restricted_requires_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.articles.store'), [
                'title' => 'Sans cible', 'content' => 'corps', 'status' => 'draft',
                'visibility' => 'restricted',
            ])->assertSessionHasErrors('audience_roles');
    }

    public function test_members_visibility_clears_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.articles.store'), [
                'title' => 'Pour adherents', 'content' => 'corps', 'status' => 'draft',
                'visibility' => 'members', 'audience_roles' => ['ca'],
            ])->assertRedirect();

        $article = Article::where('title', 'Pour adherents')->firstOrFail();
        $this->assertNull($article->audience_roles);
    }
}
```

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=AdminArticleVisibilityTest`
Expected: FAIL (pas de validation/persistance `visibility`).

- [ ] **Step 3: Ajouter le bloc Visibilité au formulaire**

Ouvrir `resources/views/admin/articles/_form.blade.php`, repérer le champ **`status`** (select Brouillon/…); insérer **juste après** ce form-group le bloc suivant (KEEP ACCENTS). Il est identique à celui des événements (`resources/views/admin/events/_form.blade.php`) mais utilise `$article` :

```blade
        <div class="form-group">
            <label class="form-label" for="visibility">Visibilité *</label>
            <select name="visibility" id="visibility" class="form-input" required
                    onchange="document.getElementById('audience-roles-block').style.display = this.value === 'restricted' ? 'block' : 'none'">
                @php $vis = old('visibility', $article->visibility ?? 'public'); @endphp
                <option value="public" {{ $vis === 'public' ? 'selected' : '' }}>Public (site + espace membre)</option>
                <option value="members" {{ $vis === 'members' ? 'selected' : '' }}>Adhérents (espace membre)</option>
                <option value="restricted" {{ $vis === 'restricted' ? 'selected' : '' }}>Restreint (fonctions)</option>
            </select>
            @error('visibility')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group" id="audience-roles-block" style="{{ old('visibility', $article->visibility ?? 'public') === 'restricted' ? '' : 'display:none;' }}">
            <label class="form-label">Fonctions ciblées *</label>
            @php $selectedRoles = old('audience_roles', $article->audience_roles ?? []); @endphp
            @foreach(\App\Models\Member::ADHERENT_ROLES as $key => $label)
                <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; margin-bottom:0.25rem;">
                    <input type="checkbox" name="audience_roles[]" value="{{ $key }}"
                           {{ in_array($key, $selectedRoles) ? 'checked' : '' }} style="width:auto;">
                    <span>{{ $label }}</span>
                </label>
            @endforeach
            @error('audience_roles')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
```

(Si le `_form` n'a pas de variable `$article` sur la page de création, garder la forme null-safe `$article->visibility ?? 'public'` — adapter au nom réel de la variable utilisée par ce formulaire.)

- [ ] **Step 4: Valider + persister dans `store()` ET `update()`**

Dans les DEUX méthodes, ajouter dans le tableau `validate([...])` (après la règle `status`) :

```php
            'visibility' => 'required|in:public,members,restricted',
            'audience_roles' => 'nullable|array|required_if:visibility,restricted',
            'audience_roles.*' => 'in:ca,bureau,validateur',
```

Puis, juste après `$validated = $request->validate([...]);` dans chaque méthode :

```php
        if (($validated['visibility'] ?? 'public') !== 'restricted') {
            $validated['audience_roles'] = null;
        }
```

- [ ] **Step 5: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=AdminArticleVisibilityTest`
Expected: PASS (3 tests).

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/articles/_form.blade.php app/Http/Controllers/Admin/ArticleController.php tests/Feature/Admin/AdminArticleVisibilityTest.php
git commit -m "feat(admin): visibilite article (public/adherents/restreint) + roles cibles"
```

---

## LOT 2 — Surfaces membre

### Task 6: Aperçu tableau de bord (feed actualités réel)

**Files:**
- Modify: `app/Http/Controllers/Member/DashboardController.php`
- Rename: `resources/views/member/partials/_actualites_demo.blade.php` → `_actualites.blade.php`
- Modify: `resources/views/member/dashboard.blade.php` (l'`@include`)
- Test: `tests/Feature/Member/DashboardArticlesTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Member;

use App\Models\Article;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_members_article_not_restricted(): void
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create([
            'user_id' => $u->id, 'member_number' => 'MA', 'email' => $u->email,
            'first_name' => 'Ada', 'last_name' => 'L', 'joined_at' => now(), 'is_active' => true,
        ]);
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);

        Article::create(['title' => 'Actu adherents', 'slug' => 'actu-adh', 'content' => 'x',
            'status' => 'published', 'published_at' => now()->subDay(), 'visibility' => Article::VIS_MEMBERS]);
        Article::create(['title' => 'Note CA confidentielle', 'slug' => 'note-ca', 'content' => 'x',
            'status' => 'published', 'published_at' => now()->subDay(),
            'visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        $this->actingAs($u)->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Actu adherents')
            ->assertDontSee('Note CA confidentielle');
    }
}
```

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=DashboardArticlesTest`
Expected: FAIL (le bloc actualités est en démo, n'affiche pas l'article réel).

- [ ] **Step 3: Calculer `$memberArticles` dans `DashboardController::index()`**

Après le calcul de `$member`, ajouter (gating cotisation) :

```php
        if ($member && $member->isCurrentMember()) {
            $memberArticles = \App\Models\Article::visibleToMember($member)->published()
                ->latest('published_at')->limit(4)->get();
        } else {
            $memberArticles = \App\Models\Article::publicOnly()->published()
                ->latest('published_at')->limit(4)->get();
        }
```

Ajouter `'memberArticles'` à la liste `compact(...)` passée à la vue.

- [ ] **Step 4: Renommer + réécrire le partial**

Renommer le fichier :
```bash
git mv resources/views/member/partials/_actualites_demo.blade.php resources/views/member/partials/_actualites.blade.php
```

Remplacer **tout** le contenu de `resources/views/member/partials/_actualites.blade.php` par (KEEP ACCENTS) :

```blade
<article class="card panel">
    <div class="panel-head">
        <div>
            <h2>Actualités du réseau</h2>
        </div>
        <a href="{{ route('member.articles.index') }}" class="text-link"><i data-lucide="arrow-right"></i>Voir toutes les actualités</a>
    </div>

    @if($memberArticles->isEmpty())
        <p style="color:var(--muted);padding:16px 0;">Aucune actualité pour le moment.</p>
    @else
        <div class="news-feed">
            @foreach($memberArticles as $a)
            <a href="{{ route('hub.articles.show', $a) }}" class="news-feed-item">
                <img src="{{ $a->featured_image ? \Storage::url($a->featured_image) : asset('images/magazine/oreina-n68.jpg') }}"
                     alt="" class="news-feed-thumb" onerror="this.style.visibility='hidden'">
                <div>
                    @if($a->visibility !== \App\Models\Article::VIS_PUBLIC)
                        <span class="news-feed-type gold">
                            @if($a->visibility === \App\Models\Article::VIS_MEMBERS)Adhérents@else{{ implode(' · ', array_map(fn ($r) => \App\Models\Member::ADHERENT_ROLES[$r] ?? $r, $a->audience_roles ?? [])) }}@endif
                        </span>
                    @elseif($a->category)
                        <span class="news-feed-type sage">{{ $a->category }}</span>
                    @endif
                    <strong>{{ $a->title }}</strong>
                    <p>{{ $a->published_at?->translatedFormat('d M Y') }}</p>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</article>
```

> Note : ce markup réutilise les classes existantes `news-feed` / `news-feed-item` / `news-feed-thumb` / `news-feed-type` (déjà stylées dans le layout, utilisées par l'ancien partial démo).

- [ ] **Step 5: Mettre à jour l'include dans `dashboard.blade.php`**

Dans `resources/views/member/dashboard.blade.php`, remplacer
`@include('member.partials._actualites_demo')` par
`@include('member.partials._actualites')`.

- [ ] **Step 6: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=DashboardArticlesTest`
Expected: PASS.
No-regression : `php artisan test --filter="ArticleVisibility|DashboardAgenda"`

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/Member/DashboardController.php resources/views/member/partials/_actualites.blade.php resources/views/member/partials/_actualites_demo.blade.php resources/views/member/dashboard.blade.php tests/Feature/Member/DashboardArticlesTest.php
git commit -m "feat(espace-membre): bloc actualites reel filtre par visibilite (remplace la demo)"
```

---

### Task 7: Page dédiée « Actualités » + entrée sidebar

**Files:**
- Create: `app/Http/Controllers/Member/ArticleController.php`
- Create: `resources/views/member/articles/index.blade.php`
- Modify: `routes/web.php` (groupe espace membre)
- Modify: `resources/views/layouts/member.blade.php` (entrée sidebar)
- Test: `tests/Feature/Member/MemberArticlesPageTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Member;

use App\Models\Article;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberArticlesPageTest extends TestCase
{
    use RefreshDatabase;

    private function currentUser(): User
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create([
            'user_id' => $u->id, 'member_number' => 'M'.uniqid(), 'email' => $u->email,
            'first_name' => 'A', 'last_name' => 'B', 'joined_at' => now(), 'is_active' => true,
        ]);
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return $u;
    }

    public function test_member_articles_page_lists_visible_excludes_restricted(): void
    {
        $u = $this->currentUser();
        Article::create(['title' => 'Visible adherents', 'slug' => 'vis-adh', 'content' => 'x',
            'status' => 'published', 'published_at' => now()->subDay(), 'visibility' => Article::VIS_MEMBERS]);
        Article::create(['title' => 'Reserve CA', 'slug' => 'res-ca', 'content' => 'x',
            'status' => 'published', 'published_at' => now()->subDay(),
            'visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        $this->actingAs($u)->get(route('member.articles.index'))
            ->assertOk()
            ->assertSee('Visible adherents')
            ->assertDontSee('Reserve CA');
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get(route('member.articles.index'))->assertRedirect(route('login'));
    }
}
```

> Note : vérifier le nom de la route de login (`login`) ; l'ajuster si le projet utilise `connexion`/un autre nom.

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=MemberArticlesPageTest`
Expected: FAIL (route `member.articles.index` inexistante).

- [ ] **Step 3: Créer le contrôleur**

```php
<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Member;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $member = Member::where('user_id', auth()->id())->first();

        $base = ($member && $member->isCurrentMember())
            ? Article::visibleToMember($member)
            : Article::publicOnly();

        $query = $base->published()->with('author');

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        $articles = $query->latest('published_at')->paginate(12)->withQueryString();

        $categories = Article::whereNotNull('category')->distinct()->pluck('category')->sort();

        return view('member.articles.index', compact('articles', 'categories'));
    }
}
```

- [ ] **Step 4: Déclarer la route**

Dans `routes/web.php`, dans le groupe espace membre (le groupe `Route::prefix('espace-membre')->name('member.')...`, au niveau des routes accessibles à tout compte connecté — PAS dans le sous-groupe `current_member`), ajouter près des autres routes de contenu :

```php
    Route::get('/actualites', [\App\Http\Controllers\Member\ArticleController::class, 'index'])->name('articles.index');
```

- [ ] **Step 5: Créer la vue `resources/views/member/articles/index.blade.php`**

```blade
@extends('layouts.member')

@section('title', 'Actualités')

@section('content')
<section>
    <div class="panel-head" style="margin-bottom:16px;">
        <div>
            <h2 style="margin:0;">Actualités du réseau</h2>
            <p style="color:var(--muted);margin:4px 0 0;">Les actualités qui vous concernent : publiques et réservées à votre profil.</p>
        </div>
    </div>

    @if($categories->isNotEmpty())
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
        <a href="{{ route('member.articles.index') }}" class="space-row-chip {{ request('category') ? '' : 'gold' }}">Toutes</a>
        @foreach($categories as $cat)
            <a href="{{ route('member.articles.index', ['category' => $cat]) }}" class="space-row-chip {{ request('category') === $cat ? 'gold' : '' }}">{{ $cat }}</a>
        @endforeach
    </div>
    @endif

    @if($articles->isEmpty())
        <div class="card panel"><p style="color:var(--muted);">Aucune actualité pour le moment.</p></div>
    @else
        <div class="news-feed">
            @foreach($articles as $a)
            <a href="{{ route('hub.articles.show', $a) }}" class="news-feed-item">
                <img src="{{ $a->featured_image ? \Storage::url($a->featured_image) : asset('images/magazine/oreina-n68.jpg') }}"
                     alt="" class="news-feed-thumb" onerror="this.style.visibility='hidden'">
                <div>
                    @if($a->visibility !== \App\Models\Article::VIS_PUBLIC)
                        <span class="news-feed-type gold">
                            @if($a->visibility === \App\Models\Article::VIS_MEMBERS)Adhérents@else{{ implode(' · ', array_map(fn ($r) => \App\Models\Member::ADHERENT_ROLES[$r] ?? $r, $a->audience_roles ?? [])) }}@endif
                        </span>
                    @elseif($a->category)
                        <span class="news-feed-type sage">{{ $a->category }}</span>
                    @endif
                    <strong>{{ $a->title }}</strong>
                    <p>{{ $a->published_at?->translatedFormat('d M Y') }}</p>
                </div>
            </a>
            @endforeach
        </div>
        <div style="margin-top:18px;">{{ $articles->links() }}</div>
    @endif
</section>
@endsection
```

- [ ] **Step 6: Ajouter l'entrée de sidebar**

Dans `resources/views/layouts/member.blade.php`, dans la famille « Tableau de bord », **juste après** le `<a>` « Accueil » (le bloc `route('member.dashboard')`), insérer :

```blade
                <a href="{{ route('member.articles.index') }}" class="nav-item {{ request()->routeIs('member.articles*') ? 'active' : '' }}">
                    <i data-lucide="newspaper" class="icon"></i>
                    <span class="nav-label">Actualités</span>
                </a>
```

- [ ] **Step 7: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=MemberArticlesPageTest`
Expected: PASS (2 tests).

- [ ] **Step 8: Rebuild assets + commit**

Run: `npm run build`
```bash
git add app/Http/Controllers/Member/ArticleController.php resources/views/member/articles/index.blade.php routes/web.php resources/views/layouts/member.blade.php tests/Feature/Member/MemberArticlesPageTest.php public/build
git commit -m "feat(espace-membre): page Actualites filtree + entree sidebar"
```

---

### Task 8: Test bout-en-bout — article réservé visible au bon profil

**Files:**
- Test: `tests/Feature/Member/RestrictedArticleAccessTest.php`

- [ ] **Step 1: Écrire le test**

```php
<?php

namespace Tests\Feature\Member;

use App\Models\Article;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestrictedArticleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $num, array $attrs = []): User
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create(array_merge([
            'user_id' => $u->id, 'member_number' => $num, 'email' => $u->email,
            'first_name' => 'A', 'last_name' => 'B', 'joined_at' => now(), 'is_active' => true,
        ], $attrs));
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        return $u;
    }

    public function test_ca_article_detail_visible_to_bureau_not_to_simple_member(): void
    {
        $bureau = $this->makeUser('BUR', ['adherent_roles' => ['bureau']]);
        $simple = $this->makeUser('SIM');

        $article = Article::create(['title' => 'Compte rendu CA', 'slug' => 'cr-ca', 'content' => 'x',
            'status' => 'published', 'published_at' => now()->subDay(),
            'visibility' => Article::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        // Bureau (cascade ⊇ ca) : 200 sur le détail
        $this->actingAs($bureau)->get(route('hub.articles.show', $article))->assertOk()->assertSee('Compte rendu CA');
        // Adhérent simple : 404
        $this->actingAs($simple)->get(route('hub.articles.show', $article))->assertNotFound();
    }
}
```

- [ ] **Step 2: Lancer le test**

Run: `php artisan test --filter=RestrictedArticleAccessTest`
Expected: PASS (la logique existe déjà ; ce test la verrouille).

- [ ] **Step 3: Suite ciblée complète**

Run: `php artisan test --filter="Article"`
Expected: tous PASS.

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/Member/RestrictedArticleAccessTest.php
git commit -m "test(articles): detail article CA visible bureau, 404 adherent simple"
```

---

## Vérification finale

- [ ] **Suite complète** : `php -d memory_limit=-1 vendor/bin/phpunit` → vert (les 2 « risky » connus sur HubEventVisibilityTest sont tolérés).
- [ ] **Accents** : `git diff --stat main` puis vérifier les libellés FR des vues touchées.
- [ ] **Caches** : `php artisan config:clear; php artisan view:clear`.
