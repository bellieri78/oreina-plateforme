# Rattachement manuel User ↔ Member — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Permettre à un admin de rattacher/détacher manuellement un compte `User` à une fiche `Member` existante depuis le back-office des utilisateurs, avec suggestions automatiques et recherche libre.

**Architecture:** Aucune migration — on réutilise `members.user_id`. La logique est centralisée dans un service `MemberUserLinkService` (suggestions + link atomique + unlink + audit). Un `AccountLinkController` dédié expose 3 routes (recherche JSON, lier, détacher). L'UI ajoute une carte « Fiche contact » sur la page user et une colonne + filtre sur la liste des users.

**Tech Stack:** Laravel 12, PostgreSQL 17, Blade + Alpine.js, PHPUnit (DB de test `oreina_test` forcée par `phpunit.xml`).

---

## Conventions importantes (lire avant de commencer)

- **Base de test :** la suite tourne sur `oreina_test` (forcée par `phpunit.xml`). Lancer les tests avec `php artisan test`. **NE JAMAIS** lancer `migrate:fresh` à la main sur `.env` (garde-fou anti-wipe, base `oreina_local` protégée).
- **Pas de `MemberFactory`** : créer les `Member` dans les tests via `Member::create([...])`.
- **`Member::booted()` met `last_name` en MAJUSCULES** au save : en assertion, comparer avec la valeur en majuscules (ex. `DUPONT`).
- **Accents français :** ce projet a un historique de sous-agents qui suppriment les accents dans les vues. Après toute écriture de Blade, **vérifier que les accents sont intacts** (Rattachée, détachée, Fiche, etc.).
- Suivre le style admin existant : Blade + styles inline, controllers fins, pas de Livewire pour cette feature.

---

## File Structure

- **Créer** `app/Services/MemberUserLinkService.php` — suggestions, link, unlink (+ audit).
- **Créer** `app/Http/Controllers/Admin/AccountLinkController.php` — routes search/link/unlink.
- **Créer** `resources/views/admin/users/partials/_member-link-card.blade.php` — carte sidebar.
- **Créer** `tests/Unit/Services/MemberUserLinkServiceTest.php` — tests service.
- **Créer** `tests/Feature/Admin/AccountLinkTest.php` — tests HTTP.
- **Modifier** `app/Models/Member.php` — scope `withoutAccount`.
- **Modifier** `app/Models/User.php` — scope `withoutMember`.
- **Modifier** `app/Http/Controllers/Admin/UserController.php` — filtre liste + chargement suggestions sur show.
- **Modifier** `routes/admin.php` — 3 routes + import controller.
- **Modifier** `resources/views/admin/users/show.blade.php` — inclure la carte.
- **Modifier** `resources/views/admin/users/index.blade.php` — colonne + filtre.
- **Modifier** `resources/views/admin/documentation/index.blade.php` — doc.

---

## Task 1: Scopes Eloquent

**Files:**
- Modify: `app/Models/Member.php` (zone des scopes, après `scopeIndividuels`)
- Modify: `app/Models/User.php` (zone des scopes, après `scopeClaimed`)
- Test: `tests/Unit/Services/MemberUserLinkServiceTest.php`

- [ ] **Step 1: Écrire le test des scopes**

Créer `tests/Unit/Services/MemberUserLinkServiceTest.php` :

```php
<?php

namespace Tests\Unit\Services;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberUserLinkServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(array $attrs = []): Member
    {
        return Member::create(array_merge([
            'contact_type' => 'individuel',
            'first_name'   => 'Jean',
            'last_name'    => 'Dupont',
            'email'        => 'jean.dupont@example.com',
        ], $attrs));
    }

    public function test_scopes_filter_linked_and_unlinked(): void
    {
        $user = User::factory()->create();
        $linked = $this->makeMember(['email' => 'a@example.com', 'user_id' => $user->id]);
        $orphan = $this->makeMember(['email' => 'b@example.com']);

        $this->assertEqualsCanonicalizing(
            [$orphan->id],
            Member::withoutAccount()->pluck('id')->all()
        );
        $this->assertEqualsCanonicalizing(
            [$user->id],
            User::query()->whereHas('member')->pluck('id')->all()
        );
        $this->assertTrue(User::withoutMember()->where('id', $user->id)->doesntExist());
    }
}
```

- [ ] **Step 2: Lancer le test (échec attendu)**

Run: `php artisan test --filter=test_scopes_filter_linked_and_unlinked`
Expected: FAIL — `Call to undefined method ...withoutAccount()`.

- [ ] **Step 3: Ajouter `scopeWithoutAccount` dans `Member.php`**

Dans `app/Models/Member.php`, après la méthode `scopeIndividuels` (vers la ligne 653) :

```php
    public function scopeWithoutAccount($query)
    {
        return $query->whereNull('user_id');
    }
```

- [ ] **Step 4: Ajouter `scopeWithoutMember` dans `User.php`**

Dans `app/Models/User.php`, après la méthode `scopeClaimed` (vers la ligne 307) :

```php
    public function scopeWithoutMember($query)
    {
        return $query->whereDoesntHave('member');
    }
```

- [ ] **Step 5: Lancer le test (succès attendu)**

Run: `php artisan test --filter=test_scopes_filter_linked_and_unlinked`
Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Models/Member.php app/Models/User.php tests/Unit/Services/MemberUserLinkServiceTest.php
git commit -m "feat(account-link): scopes withoutAccount / withoutMember"
```

---

## Task 2: Service — `suggestionsFor`

**Files:**
- Create: `app/Services/MemberUserLinkService.php`
- Test: `tests/Unit/Services/MemberUserLinkServiceTest.php`

- [ ] **Step 1: Écrire le test des suggestions**

Ajouter ces méthodes dans `MemberUserLinkServiceTest` :

```php
    public function test_suggestions_match_email_then_name_and_exclude_linked_and_anonymized(): void
    {
        $user = User::factory()->create([
            'name'  => 'Marie Martin',
            'email' => 'marie@perso.fr',
        ]);

        // Correspondance email exacte (email de fiche different du name)
        $byEmail = $this->makeMember(['first_name' => 'Autre', 'last_name' => 'Nom', 'email' => 'marie@perso.fr']);
        // Correspondance nom + prenom (email different)
        $byName = $this->makeMember(['first_name' => 'Marie', 'last_name' => 'Martin', 'email' => 'm.martin@asso.org']);
        // Deja rattachee -> exclue
        $linked = $this->makeMember(['first_name' => 'Marie', 'last_name' => 'Martin', 'email' => 'x@example.com', 'user_id' => $user->id]);
        // Anonymisee -> exclue
        $anon = $this->makeMember(['first_name' => 'Marie', 'last_name' => 'Martin', 'email' => 'marie@perso.fr', 'anonymise' => true]);
        // Sans rapport
        $this->makeMember(['first_name' => 'Paul', 'last_name' => 'Durand', 'email' => 'paul@example.com']);

        $service = new \App\Services\MemberUserLinkService();
        $ids = $service->suggestionsFor($user)->pluck('id')->all();

        $this->assertSame([$byEmail->id, $byName->id], array_slice($ids, 0, 2));
        $this->assertNotContains($linked->id, $ids);
        $this->assertNotContains($anon->id, $ids);
    }
```

- [ ] **Step 2: Lancer le test (échec attendu)**

Run: `php artisan test --filter=test_suggestions_match_email_then_name`
Expected: FAIL — classe `MemberUserLinkService` introuvable.

- [ ] **Step 3: Créer le service avec `suggestionsFor`**

Créer `app/Services/MemberUserLinkService.php` :

```php
<?php

namespace App\Services;

use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Collection;

class MemberUserLinkService
{
    /**
     * Fiches Member sans compte susceptibles de correspondre a $user.
     * Classees : correspondance email exacte d'abord, puis nom + prenom.
     */
    public function suggestionsFor(User $user, int $limit = 5): Collection
    {
        $emailMatches = Member::query()
            ->withoutAccount()
            ->where('anonymise', false)
            ->whereRaw('lower(email) = ?', [mb_strtolower((string) $user->email)])
            ->get();

        $name = mb_strtolower(trim((string) $user->name));

        $nameMatches = collect();
        if ($name !== '') {
            $nameMatches = Member::query()
                ->withoutAccount()
                ->where('anonymise', false)
                ->where(function ($q) use ($name) {
                    $q->whereRaw("lower(trim(coalesce(first_name,'') || ' ' || coalesce(last_name,''))) = ?", [$name])
                      ->orWhereRaw("lower(trim(coalesce(last_name,'') || ' ' || coalesce(first_name,''))) = ?", [$name]);
                })
                ->get();
        }

        return $emailMatches->concat($nameMatches)
            ->unique('id')
            ->take($limit)
            ->values();
    }
}
```

- [ ] **Step 4: Lancer le test (succès attendu)**

Run: `php artisan test --filter=test_suggestions_match_email_then_name`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Services/MemberUserLinkService.php tests/Unit/Services/MemberUserLinkServiceTest.php
git commit -m "feat(account-link): service suggestionsFor"
```

---

## Task 3: Service — `link` / `unlink`

**Files:**
- Modify: `app/Services/MemberUserLinkService.php`
- Test: `tests/Unit/Services/MemberUserLinkServiceTest.php`

- [ ] **Step 1: Écrire les tests link/unlink**

Ajouter dans `MemberUserLinkServiceTest` :

```php
    public function test_link_sets_user_id_atomically_and_audits(): void
    {
        $user = User::factory()->create();
        $member = $this->makeMember();

        $service = new \App\Services\MemberUserLinkService();
        $ok = $service->link($user, $member);

        $this->assertTrue($ok);
        $this->assertSame($user->id, $member->fresh()->user_id);
        $this->assertDatabaseHas('audit_logs', [
            'table_name' => 'members',
            'record_id'  => $member->id,
            'action'     => 'UPDATE',
        ]);
    }

    public function test_link_refused_when_member_already_taken(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $member = $this->makeMember(['user_id' => $owner->id]);

        $service = new \App\Services\MemberUserLinkService();
        $ok = $service->link($other, $member);

        $this->assertFalse($ok);
        $this->assertSame($owner->id, $member->fresh()->user_id);
    }

    public function test_link_refused_when_user_already_has_member(): void
    {
        $user = User::factory()->create();
        $this->makeMember(['email' => 'first@example.com', 'user_id' => $user->id]);
        $candidate = $this->makeMember(['email' => 'second@example.com']);

        $service = new \App\Services\MemberUserLinkService();
        $ok = $service->link($user, $candidate);

        $this->assertFalse($ok);
        $this->assertNull($candidate->fresh()->user_id);
    }

    public function test_unlink_clears_user_id_and_audits(): void
    {
        $user = User::factory()->create();
        $member = $this->makeMember(['user_id' => $user->id]);

        $service = new \App\Services\MemberUserLinkService();
        $service->unlink($member);

        $this->assertNull($member->fresh()->user_id);
        $this->assertDatabaseHas('audit_logs', [
            'table_name' => 'members',
            'record_id'  => $member->id,
            'action'     => 'UPDATE',
        ]);
    }
```

- [ ] **Step 2: Lancer les tests (échec attendu)**

Run: `php artisan test --filter=MemberUserLinkServiceTest`
Expected: FAIL — `link` / `unlink` non définis.

- [ ] **Step 3: Implémenter `link` et `unlink`**

Dans `app/Services/MemberUserLinkService.php`, ajouter l'import et les méthodes :

En haut, après les `use` existants :

```php
use App\Models\AuditLog;
```

Dans la classe, après `suggestionsFor` :

```php
    /**
     * Rattache $member a $user de facon atomique.
     * Retourne false si le user a deja une fiche, ou si la fiche est deja prise.
     */
    public function link(User $user, Member $member): bool
    {
        if ($user->member()->exists()) {
            return false;
        }

        $affected = Member::whereKey($member->id)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        if ($affected === 0) {
            return false;
        }

        AuditLog::log(
            'members',
            $member->id,
            AuditLog::ACTION_UPDATE,
            ['user_id' => null],
            ['user_id' => $user->id],
            "Rattachement manuel au compte #{$user->id} ({$user->email})"
        );

        return true;
    }

    /**
     * Detache la fiche $member de son compte.
     */
    public function unlink(Member $member): void
    {
        $oldUserId = $member->user_id;

        Member::whereKey($member->id)->update(['user_id' => null]);

        AuditLog::log(
            'members',
            $member->id,
            AuditLog::ACTION_UPDATE,
            ['user_id' => $oldUserId],
            ['user_id' => null],
            "Detachement manuel du compte #{$oldUserId}"
        );
    }
```

- [ ] **Step 4: Lancer les tests (succès attendu)**

Run: `php artisan test --filter=MemberUserLinkServiceTest`
Expected: PASS (tous les tests du fichier).

- [ ] **Step 5: Commit**

```bash
git add app/Services/MemberUserLinkService.php tests/Unit/Services/MemberUserLinkServiceTest.php
git commit -m "feat(account-link): service link/unlink atomique + audit"
```

---

## Task 4: Controller + routes (link / unlink)

**Files:**
- Create: `app/Http/Controllers/Admin/AccountLinkController.php`
- Modify: `routes/admin.php`
- Test: `tests/Feature/Admin/AccountLinkTest.php`

- [ ] **Step 1: Écrire le test HTTP link/unlink**

Créer `tests/Feature/Admin/AccountLinkTest.php` :

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountLinkTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    }

    private function makeMember(array $attrs = []): Member
    {
        return Member::create(array_merge([
            'contact_type' => 'individuel',
            'first_name'   => 'Jean',
            'last_name'    => 'Dupont',
            'email'        => 'jean.dupont@example.com',
        ], $attrs));
    }

    public function test_admin_can_link_member_to_user(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['email' => 'login@perso.fr']);
        $member = $this->makeMember(['email' => 'contact@asso.org']);

        $this->actingAs($admin)
            ->post(route('admin.users.link-member', $target), ['member_id' => $member->id])
            ->assertRedirect(route('admin.users.show', $target))
            ->assertSessionHas('success');

        $this->assertSame($target->id, $member->fresh()->user_id);
    }

    public function test_link_refused_when_member_already_taken(): void
    {
        $admin = $this->admin();
        $owner = User::factory()->create();
        $target = User::factory()->create();
        $member = $this->makeMember(['user_id' => $owner->id]);

        $this->actingAs($admin)
            ->post(route('admin.users.link-member', $target), ['member_id' => $member->id])
            ->assertSessionHas('error');

        $this->assertSame($owner->id, $member->fresh()->user_id);
    }

    public function test_admin_can_unlink_member(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $member = $this->makeMember(['user_id' => $target->id]);

        $this->actingAs($admin)
            ->post(route('admin.users.unlink-member', $target))
            ->assertRedirect(route('admin.users.show', $target))
            ->assertSessionHas('success');

        $this->assertNull($member->fresh()->user_id);
    }

    public function test_guest_cannot_link(): void
    {
        $target = User::factory()->create();
        $member = $this->makeMember();

        $this->post(route('admin.users.link-member', $target), ['member_id' => $member->id])
            ->assertRedirect();

        $this->assertNull($member->fresh()->user_id);
    }
}
```

- [ ] **Step 2: Lancer le test (échec attendu)**

Run: `php artisan test --filter=AccountLinkTest`
Expected: FAIL — route `admin.users.link-member` inexistante.

- [ ] **Step 3: Créer le `AccountLinkController`**

Créer `app/Http/Controllers/Admin/AccountLinkController.php` :

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use App\Services\MemberUserLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountLinkController extends Controller
{
    public function __construct(private MemberUserLinkService $linker)
    {
    }

    /**
     * Recherche JSON de fiches Member sans compte.
     */
    public function search(Request $request, User $user): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = Member::query()
            ->withoutAccount()
            ->where('anonymise', false)
            ->where(function ($builder) use ($q) {
                $builder->where('first_name', 'ilike', "%{$q}%")
                    ->orWhere('last_name', 'ilike', "%{$q}%")
                    ->orWhere('email', 'ilike', "%{$q}%")
                    ->orWhere('member_number', 'ilike', "%{$q}%");
            })
            ->orderBy('last_name')
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'member_number'])
            ->map(fn (Member $m) => [
                'id'            => $m->id,
                'name'          => trim($m->first_name . ' ' . $m->last_name),
                'email'         => $m->email,
                'member_number' => $m->member_number,
            ]);

        return response()->json(['results' => $results]);
    }

    /**
     * Rattache une fiche au compte.
     */
    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
        ]);

        $member = Member::findOrFail($validated['member_id']);
        $ok = $this->linker->link($user, $member);

        return redirect()
            ->route('admin.users.show', $user)
            ->with(
                $ok ? 'success' : 'error',
                $ok
                    ? 'Fiche contact rattachée au compte.'
                    : 'Rattachement impossible : ce compte a déjà une fiche, ou la fiche est déjà rattachée à un autre compte.'
            );
    }

    /**
     * Détache la fiche du compte.
     */
    public function destroy(User $user)
    {
        if ($user->member) {
            $this->linker->unlink($user->member);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Fiche contact détachée du compte.');
    }
}
```

- [ ] **Step 4: Déclarer les routes**

Dans `routes/admin.php`, ajouter l'import en haut (après la ligne 15, `use ...UserController;`) :

```php
use App\Http\Controllers\Admin\AccountLinkController;
```

Puis, **juste avant** `Route::resource('users', UserController::class);` (ligne 188) :

```php
    Route::get('users/{user}/member-search', [AccountLinkController::class, 'search'])->name('users.member-search');
    Route::post('users/{user}/link-member', [AccountLinkController::class, 'store'])->name('users.link-member');
    Route::post('users/{user}/unlink-member', [AccountLinkController::class, 'destroy'])->name('users.unlink-member');
```

- [ ] **Step 5: Lancer le test (succès attendu)**

Run: `php artisan test --filter=AccountLinkTest`
Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/AccountLinkController.php routes/admin.php tests/Feature/Admin/AccountLinkTest.php
git commit -m "feat(account-link): controller + routes search/link/unlink"
```

---

## Task 5: Recherche JSON — test d'intégration

**Files:**
- Test: `tests/Feature/Admin/AccountLinkTest.php`

- [ ] **Step 1: Ajouter le test de recherche**

Ajouter dans `AccountLinkTest` :

```php
    public function test_member_search_returns_only_unlinked_candidates(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $linkedUser = User::factory()->create();

        $hit = $this->makeMember(['first_name' => 'Camille', 'last_name' => 'Bernard', 'email' => 'camille@example.com']);
        $this->makeMember(['first_name' => 'Camille', 'last_name' => 'Linked', 'email' => 'cl@example.com', 'user_id' => $linkedUser->id]);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.users.member-search', $target) . '?q=camille');

        $response->assertOk();
        $ids = collect($response->json('results'))->pluck('id')->all();

        $this->assertContains($hit->id, $ids);
        $this->assertCount(1, $ids);
    }

    public function test_member_search_ignores_short_queries(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $this->makeMember();

        $this->actingAs($admin)
            ->getJson(route('admin.users.member-search', $target) . '?q=a')
            ->assertOk()
            ->assertJson(['results' => []]);
    }
```

- [ ] **Step 2: Lancer le test (succès attendu — endpoint déjà créé en Task 4)**

Run: `php artisan test --filter=AccountLinkTest`
Expected: PASS (tous, y compris les 2 nouveaux).

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/Admin/AccountLinkTest.php
git commit -m "test(account-link): couverture endpoint member-search"
```

---

## Task 6: UI — carte « Fiche contact » sur la page user

**Files:**
- Create: `resources/views/admin/users/partials/_member-link-card.blade.php`
- Modify: `app/Http/Controllers/Admin/UserController.php:76-81` (méthode `show`)
- Modify: `resources/views/admin/users/show.blade.php` (sidebar, après la carte « Actions rapides »)

- [ ] **Step 1: Charger member + suggestions dans `UserController::show`**

Remplacer la méthode `show` (lignes 76-81) par :

```php
    public function show(User $user)
    {
        $user->loadCount(['articles', 'submissions', 'assignedReviews']);
        $user->load('member');

        $memberSuggestions = $user->member
            ? collect()
            : app(\App\Services\MemberUserLinkService::class)->suggestionsFor($user);

        return view('admin.users.show', compact('user', 'memberSuggestions'));
    }
```

- [ ] **Step 2: Créer le partial de la carte**

Créer `resources/views/admin/users/partials/_member-link-card.blade.php` :

```blade
{{-- Carte Fiche contact (rattachement User <-> Member) --}}
<div class="card" style="margin-bottom: 1.5rem;" x-data="memberLink({{ $user->id }})">
    <div class="card-header">
        <h3 class="card-title">Fiche contact</h3>
    </div>
    <div class="card-body">
        @if($user->member)
            <div style="margin-bottom: 0.75rem;">
                <a href="{{ route('admin.members.show', $user->member) }}" style="color: var(--color-primary); font-weight: 600;">
                    {{ $user->member->full_name }}
                </a>
                @if($user->member->member_number)
                    <span class="badge badge-secondary" style="margin-left: 0.5rem;">{{ $user->member->member_number }}</span>
                @endif
            </div>
            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
                {{ $user->member->email }}
            </div>
            <form action="{{ route('admin.users.unlink-member', $user) }}" method="POST"
                  onsubmit="return confirm('Détacher cette fiche du compte ?');">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">Détacher</button>
            </form>
        @else
            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
                Ce compte n'est rattaché à aucune fiche contact.
            </p>

            @if($memberSuggestions->isNotEmpty())
                <h4 style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Suggestions</h4>
                @foreach($memberSuggestions as $candidate)
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="min-width: 0;">
                            <div style="font-weight: 600; font-size: 0.875rem;">{{ $candidate->full_name }}</div>
                            <div style="font-size: 0.75rem; color: #6b7280; overflow: hidden; text-overflow: ellipsis;">{{ $candidate->email }}</div>
                        </div>
                        <form action="{{ route('admin.users.link-member', $user) }}" method="POST">
                            @csrf
                            <input type="hidden" name="member_id" value="{{ $candidate->id }}">
                            <button type="submit" class="btn btn-primary btn-sm">Rattacher</button>
                        </form>
                    </div>
                @endforeach
            @endif

            <div style="margin-top: 1rem;">
                <input type="text" x-model="query" @input.debounce.300ms="search()"
                       placeholder="Rechercher une fiche (nom, email, n°)..."
                       class="form-input" style="width: 100%; margin-bottom: 0.5rem;">
                <template x-if="loading">
                    <p style="font-size: 0.75rem; color: #6b7280;">Recherche...</p>
                </template>
                <template x-for="result in results" :key="result.id">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="min-width: 0;">
                            <div style="font-weight: 600; font-size: 0.875rem;" x-text="result.name"></div>
                            <div style="font-size: 0.75rem; color: #6b7280;" x-text="result.email"></div>
                        </div>
                        <form :action="linkUrl" method="POST">
                            @csrf
                            <input type="hidden" name="member_id" :value="result.id">
                            <button type="submit" class="btn btn-primary btn-sm">Rattacher</button>
                        </form>
                    </div>
                </template>
                <template x-if="!loading && query.length >= 2 && results.length === 0">
                    <p style="font-size: 0.75rem; color: #6b7280;">Aucune fiche sans compte ne correspond.</p>
                </template>
            </div>
        @endif
    </div>
</div>

@once
@push('scripts')
<script>
function memberLink(userId) {
    return {
        query: '',
        results: [],
        loading: false,
        searchUrl: '{{ url('extranet/users') }}/' + userId + '/member-search',
        linkUrl: '{{ url('extranet/users') }}/' + userId + '/link-member',
        async search() {
            if (this.query.length < 2) { this.results = []; return; }
            this.loading = true;
            try {
                const res = await fetch(this.searchUrl + '?q=' + encodeURIComponent(this.query), {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.results = data.results || [];
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endpush
@endonce
```

> **Note Alpine :** vérifier que `@push('scripts')` correspond bien à une `@stack('scripts')` dans `layouts/admin.blade.php`. Si la stack n'existe pas, l'ajouter avant `</body>` : `@stack('scripts')`. Vérifier aussi que la classe `form-input` existe dans le CSS admin ; sinon utiliser le style inline des autres `<input>` du back-office.

- [ ] **Step 3: Inclure la carte dans `show.blade.php`**

Dans `resources/views/admin/users/show.blade.php`, dans la colonne sidebar (la `<div>` ouverte ligne 98), **juste après** la fermeture de la carte « Actions rapides » (après le `</div>` de la ligne 145, avant la carte « Permissions ») :

```blade
            @include('admin.users.partials._member-link-card')
```

- [ ] **Step 4: Vérification manuelle**

Run: `php artisan serve` puis ouvrir `/extranet/users/{id}` pour :
- un compte sans fiche dont l'email/nom matche une fiche → voir les suggestions + bouton Rattacher.
- tester la recherche libre (taper ≥ 2 caractères).
- rattacher, puis vérifier l'affichage « lié » + bouton Détacher.

Vérifier les accents dans le rendu (Rattacher, Détacher, rattaché).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Admin/UserController.php resources/views/admin/users/show.blade.php resources/views/admin/users/partials/_member-link-card.blade.php
git commit -m "feat(account-link): carte Fiche contact sur la page user"
```

---

## Task 7: UI — colonne + filtre dans la liste des users

**Files:**
- Modify: `app/Http/Controllers/Admin/UserController.php:18-48` (méthode `index`)
- Modify: `resources/views/admin/users/index.blade.php` (filtres + colonne tableau)
- Test: `tests/Feature/Admin/AccountLinkTest.php`

- [ ] **Step 1: Écrire le test du filtre**

Ajouter dans `AccountLinkTest` :

```php
    public function test_index_filter_none_shows_only_unlinked_users(): void
    {
        $admin = $this->admin();
        $linkedUser = User::factory()->create(['name' => 'Compte Lie']);
        $this->makeMember(['email' => 'lie@example.com', 'user_id' => $linkedUser->id]);
        $orphanUser = User::factory()->create(['name' => 'Compte Orphelin']);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['member_link' => 'none']))
            ->assertOk()
            ->assertSee('Compte Orphelin')
            ->assertDontSee('Compte Lie');
    }
```

- [ ] **Step 2: Lancer le test (échec attendu)**

Run: `php artisan test --filter=test_index_filter_none_shows_only_unlinked_users`
Expected: FAIL — le filtre n'existe pas, « Compte Lie » est visible.

- [ ] **Step 3: Ajouter le filtre dans `UserController::index`**

Dans `app/Http/Controllers/Admin/UserController.php`, méthode `index`, **après** le bloc `if ($request->filled('status')) { ... }` (vers la ligne 36) et **avant** `$users = $query->orderBy(...)` (ligne 38) :

```php
        if ($request->filled('member_link')) {
            if ($request->get('member_link') === 'linked') {
                $query->whereHas('member');
            } elseif ($request->get('member_link') === 'none') {
                $query->whereDoesntHave('member');
            }
        }
```

Puis modifier la ligne 38 pour eager-load la fiche (évite N+1 dans la colonne) :

```php
        $users = $query->with('member')->orderBy('name')->paginate(20)->withQueryString();
```

- [ ] **Step 4: Ajouter le filtre + la colonne dans `index.blade.php`**

Dans `resources/views/admin/users/index.blade.php` :

(a) Dans le formulaire de filtres (là où se trouvent les `<select name="role">` / `<select name="status">`), ajouter un `<select>` :

```blade
                <select name="member_link" class="form-select" onchange="this.form.submit()">
                    <option value="">Fiche : toutes</option>
                    <option value="linked" {{ request('member_link') === 'linked' ? 'selected' : '' }}>Avec fiche</option>
                    <option value="none" {{ request('member_link') === 'none' ? 'selected' : '' }}>Sans fiche</option>
                </select>
```

> Adapter les classes (`form-select`) et le mécanisme de soumission à ce qu'utilisent déjà les autres `<select>` de filtre de cette vue.

(b) Dans le `<thead>` du tableau, ajouter une colonne après l'en-tête « Rôle » (ou équivalent) :

```blade
                    <th>Fiche contact</th>
```

(c) Dans le `<tbody>`, dans la boucle `@foreach($users as $user)`, ajouter la cellule correspondante (même position que l'en-tête) :

```blade
                    <td>
                        @if($user->member)
                            <a href="{{ route('admin.members.show', $user->member) }}" class="badge badge-success" style="text-decoration: none;">
                                {{ $user->member->full_name }}
                            </a>
                        @else
                            <span style="color: #9ca3af;">—</span>
                        @endif
                    </td>
```

> **Important :** vérifier le nombre de colonnes d'un éventuel `<td colspan="...">` de l'état vide (« Aucun utilisateur ») et l'incrémenter de 1.

- [ ] **Step 5: Lancer le test (succès attendu)**

Run: `php artisan test --filter=test_index_filter_none_shows_only_unlinked_users`
Expected: PASS.

- [ ] **Step 6: Vérification manuelle + accents**

Ouvrir `/extranet/users`, tester le filtre « Sans fiche » / « Avec fiche », vérifier la colonne et les accents (« Fiche contact »).

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/Admin/UserController.php resources/views/admin/users/index.blade.php tests/Feature/Admin/AccountLinkTest.php
git commit -m "feat(account-link): colonne + filtre fiche contact dans la liste des users"
```

---

## Task 8: Documentation + suite complète

**Files:**
- Modify: `resources/views/admin/documentation/index.blade.php`

- [ ] **Step 1: Mettre à jour la doc extranet**

Dans `resources/views/admin/documentation/index.blade.php`, repérer la section traitant des **Utilisateurs** ou des **Contacts/Membres** et ajouter un paragraphe décrivant la fonctionnalité (style cohérent avec le reste de la page) :

```blade
                <h3>Rattacher un compte à une fiche contact</h3>
                <p>
                    Depuis <strong>Utilisateurs</strong>, la colonne « Fiche contact » indique si un
                    compte est relié à une fiche adhérent. Le filtre « Sans fiche » liste les comptes
                    orphelins. Sur la fiche d'un utilisateur, la carte « Fiche contact » propose des
                    suggestions automatiques (même email ou même nom) et une recherche libre pour
                    rattacher la bonne fiche en un clic. Un bouton « Détacher » permet de corriger une
                    erreur. Le rattachement ne modifie jamais les adresses email.
                </p>
```

- [ ] **Step 2: Lancer toute la suite de tests**

Run: `php artisan test --filter='AccountLinkTest|MemberUserLinkServiceTest'`
Expected: PASS (tous).

Puis, pour confirmer l'absence de régression sur l'auth/admin :

Run: `php artisan test --testsuite=Feature`
Expected: PASS (ou inchangé par rapport à l'état initial — noter tout test déjà rouge avant cette feature).

- [ ] **Step 3: Vérifier les accents sur tous les fichiers Blade créés/modifiés**

Run: `git diff --stat HEAD~7` puis relire les vues touchées pour confirmer qu'aucun accent n'a été supprimé.

- [ ] **Step 4: Commit**

```bash
git add resources/views/admin/documentation/index.blade.php
git commit -m "docs(extranet): documente le rattachement compte <-> fiche contact"
```

---

## Self-review (effectué)

- **Couverture spec :** service (suggestions/link/unlink) → Tasks 2-3 ; règle atomique `whereNull('user_id')` → Task 3 ; controller 3 routes → Task 4 ; recherche JSON → Tasks 4-5 ; carte page user (suggestions + recherche + détacher) → Task 6 ; colonne + filtre liste → Task 7 ; email jamais modifié → garanti par link/unlink (Task 3) ; garde-fous (user déjà lié, fiche prise, anonymisée) → Tasks 3-5 ; doc → Task 8. ✓
- **Pas de placeholder :** tout le code est fourni. ✓
- **Cohérence des noms :** `withoutAccount`, `withoutMember`, `suggestionsFor`, `link`, `unlink`, routes `users.member-search` / `users.link-member` / `users.unlink-member` utilisés de façon identique partout. ✓
- **Points de vigilance signalés :** existence de `@stack('scripts')` dans le layout admin, classes CSS `form-input`/`form-select` à aligner sur l'existant, `colspan` de l'état vide à incrémenter, accents à revérifier après génération.
