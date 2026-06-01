# Agenda — événements ciblés — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Permettre des événements ciblés par profil (public / adhérents / fonctions CA-Bureau-validateur) créés en extranet, et des réunions de groupe créées par les coordinateurs, le tout filtré dans l'agenda de l'espace membre.

**Architecture:** Table `events` unifiée étendue d'un champ `visibility` + `audience_roles` (JSON) + `work_group_id` + `meeting_url`. Les `members` portent un champ multi-valeur `adherent_roles` (JSON). Un scope `Event::visibleToMember()` et une méthode `isVisibleToMember()` centralisent la résolution de visibilité (cascade bureau⊇CA, validateur orthogonal). Le hub public n'affiche que `public`.

**Tech Stack:** Laravel 12, PostgreSQL (json + `whereJsonContains`), Blade + Alpine.js, PHPUnit (DB de test `oreina_test`).

**Spec:** `docs/superpowers/specs/2026-06-01-agenda-evenements-cibles-design.md`

**Conventions du projet :**
- Tests : `php artisan test --filter=NomDuTest` (DB `oreina_test`, jamais `migrate:fresh` sur `oreina_local`).
- Accents FR : conserver tels quels dans les vues (ne pas les retirer).
- Pas de `Co-Authored-By` dans les commits.

---

## LOT 1 — Socle & ciblage admin

### Task 1: Migration `members.adherent_roles` + constantes/casts Member

**Files:**
- Create: `database/migrations/2026_06_01_100001_add_adherent_roles_to_members.php`
- Modify: `app/Models/Member.php`

- [ ] **Step 1: Créer la migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->json('adherent_roles')->nullable()->after('interests');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('adherent_roles');
        });
    }
};
```

- [ ] **Step 2: Lancer la migration**

Run: `php artisan migrate`
Expected: `DONE` sur `..._add_adherent_roles_to_members`.

- [ ] **Step 3: Ajouter constantes, cast et fillable dans `Member`**

Dans `app/Models/Member.php`, après les constantes `TYPE_*` (vers la ligne 22), ajouter :

```php
    public const ADHERENT_ROLE_CA = 'ca';
    public const ADHERENT_ROLE_BUREAU = 'bureau';
    public const ADHERENT_ROLE_VALIDATEUR = 'validateur';

    public const ADHERENT_ROLES = [
        self::ADHERENT_ROLE_CA => "Conseil d'administration",
        self::ADHERENT_ROLE_BUREAU => 'Bureau',
        self::ADHERENT_ROLE_VALIDATEUR => 'Validateur',
    ];

    /** Cascade : une clé donne accès aux clés listées (gouvernance). */
    protected const ADHERENT_ROLE_CASCADE = [
        self::ADHERENT_ROLE_BUREAU => [self::ADHERENT_ROLE_CA],
    ];
```

Ajouter `'adherent_roles'` au tableau `$fillable` (après `'interests'`).

Ajouter `'adherent_roles' => 'array',` au tableau `$casts`.

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_06_01_100001_add_adherent_roles_to_members.php app/Models/Member.php
git commit -m "feat(members): colonne adherent_roles + constantes roles adherent"
```

---

### Task 2: Helpers `Member::effectiveAdherentRoles` / `hasAdherentRole`

**Files:**
- Modify: `app/Models/Member.php`
- Test: `tests/Unit/Models/MemberAdherentRolesTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Unit\Models;

use App\Models\Member;
use Tests\TestCase;

class MemberAdherentRolesTest extends TestCase
{
    public function test_bureau_cascades_to_ca(): void
    {
        $m = new Member(['adherent_roles' => ['bureau']]);

        $eff = $m->effectiveAdherentRoles();

        $this->assertContains('bureau', $eff);
        $this->assertContains('ca', $eff);
        $this->assertTrue($m->hasAdherentRole('ca'));
        $this->assertTrue($m->hasAdherentRole('bureau'));
    }

    public function test_validateur_is_orthogonal(): void
    {
        $m = new Member(['adherent_roles' => ['validateur']]);

        $this->assertTrue($m->hasAdherentRole('validateur'));
        $this->assertFalse($m->hasAdherentRole('ca'));
    }

    public function test_simple_member_has_no_roles(): void
    {
        $m = new Member(['adherent_roles' => null]);

        $this->assertSame([], $m->effectiveAdherentRoles());
        $this->assertFalse($m->hasAdherentRole('ca'));
    }
}
```

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=MemberAdherentRolesTest`
Expected: FAIL (`Call to undefined method ... effectiveAdherentRoles()`).

- [ ] **Step 3: Implémenter les helpers dans `Member`**

Ajouter ces méthodes dans `app/Models/Member.php` (près de `isInDirectory`) :

```php
    public function effectiveAdherentRoles(): array
    {
        $roles = collect($this->adherent_roles ?? []);

        foreach ($roles->all() as $r) {
            foreach (self::ADHERENT_ROLE_CASCADE[$r] ?? [] as $implied) {
                $roles->push($implied);
            }
        }

        return $roles->unique()->values()->all();
    }

    public function hasAdherentRole(string $role): bool
    {
        return in_array($role, $this->effectiveAdherentRoles(), true);
    }
```

- [ ] **Step 4: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=MemberAdherentRolesTest`
Expected: PASS (3 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Models/Member.php tests/Unit/Models/MemberAdherentRolesTest.php
git commit -m "feat(members): effectiveAdherentRoles + hasAdherentRole (cascade bureau>ca)"
```

---

### Task 3: Migration `events` (visibility, audience_roles, work_group_id, meeting_url) + Event model

**Files:**
- Create: `database/migrations/2026_06_01_100002_add_visibility_to_events.php`
- Modify: `app/Models/Event.php`

- [ ] **Step 1: Créer la migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('visibility')->default('public')->after('event_type');
            $table->json('audience_roles')->nullable()->after('visibility');
            $table->foreignId('work_group_id')->nullable()->after('audience_roles')
                ->constrained('work_groups')->nullOnDelete();
            $table->string('meeting_url')->nullable()->after('location_city');
            $table->index(['visibility', 'start_date'], 'events_visibility_start_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_visibility_start_date_index');
            $table->dropConstrainedForeignId('work_group_id');
            $table->dropColumn(['visibility', 'audience_roles', 'meeting_url']);
        });
    }
};
```

- [ ] **Step 2: Lancer la migration**

Run: `php artisan migrate`
Expected: `DONE` sur `..._add_visibility_to_events`.

- [ ] **Step 3: Étendre `Event`**

Dans `app/Models/Event.php` :

Ajouter `use Illuminate\Database\Eloquent\Relations\BelongsTo;` (déjà présent — vérifier).

Ajouter au `$fillable` : `'visibility'`, `'audience_roles'`, `'work_group_id'`, `'meeting_url'`.

Ajouter au `$casts` : `'audience_roles' => 'array',`.

Ajouter les constantes (en tête de classe) et la relation :

```php
    public const VIS_PUBLIC = 'public';
    public const VIS_MEMBERS = 'members';
    public const VIS_RESTRICTED = 'restricted';
    public const VIS_GROUP = 'group';

    public function workGroup(): BelongsTo
    {
        return $this->belongsTo(WorkGroup::class);
    }
```

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_06_01_100002_add_visibility_to_events.php app/Models/Event.php
git commit -m "feat(events): visibility + audience_roles + work_group_id + meeting_url"
```

---

### Task 4: Résolution de visibilité (`scopeVisibleToMember`, `scopePublicOnly`, `isVisibleToMember`)

**Files:**
- Modify: `app/Models/Event.php`
- Test: `tests/Feature/Member/EventVisibilityTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Member;

use App\Models\Event;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use App\Models\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventVisibilityTest extends TestCase
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

    private function event(array $attrs = []): Event
    {
        return Event::create(array_merge([
            'title' => 'E'.uniqid(), 'slug' => 'e'.uniqid(),
            'start_date' => now()->addWeek(), 'status' => 'published', 'visibility' => Event::VIS_PUBLIC,
        ], $attrs));
    }

    public function test_simple_member_sees_public_and_members_only(): void
    {
        $m = $this->currentMember();
        $pub = $this->event(['visibility' => Event::VIS_PUBLIC]);
        $mem = $this->event(['visibility' => Event::VIS_MEMBERS]);
        $res = $this->event(['visibility' => Event::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        $ids = Event::visibleToMember($m)->pluck('id');

        $this->assertTrue($ids->contains($pub->id));
        $this->assertTrue($ids->contains($mem->id));
        $this->assertFalse($ids->contains($res->id));
    }

    public function test_bureau_sees_ca_restricted_event(): void
    {
        $m = $this->currentMember(['adherent_roles' => ['bureau']]);
        $caEvent = $this->event(['visibility' => Event::VIS_RESTRICTED, 'audience_roles' => ['ca']]);
        $valEvent = $this->event(['visibility' => Event::VIS_RESTRICTED, 'audience_roles' => ['validateur']]);

        $ids = Event::visibleToMember($m)->pluck('id');

        $this->assertTrue($ids->contains($caEvent->id));
        $this->assertFalse($ids->contains($valEvent->id));
    }

    public function test_group_event_visible_only_to_active_group_member(): void
    {
        $member = $this->currentMember();
        $outsider = $this->currentMember();
        $wg = WorkGroup::create(['name' => 'GT Zyg', 'is_active' => true]);
        $wg->members()->attach($member->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);

        $ev = $this->event(['visibility' => Event::VIS_GROUP, 'work_group_id' => $wg->id]);

        $this->assertTrue(Event::visibleToMember($member)->pluck('id')->contains($ev->id));
        $this->assertFalse(Event::visibleToMember($outsider)->pluck('id')->contains($ev->id));
    }

    public function test_public_only_scope_excludes_non_public(): void
    {
        $this->event(['visibility' => Event::VIS_PUBLIC]);
        $this->event(['visibility' => Event::VIS_MEMBERS]);

        $this->assertSame(1, Event::publicOnly()->count());
    }
}
```

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=EventVisibilityTest`
Expected: FAIL (`Call to undefined method ... visibleToMember()`).

- [ ] **Step 3: Implémenter les scopes + méthode dans `Event`**

```php
    public function scopePublicOnly($query)
    {
        return $query->where('visibility', self::VIS_PUBLIC);
    }

    public function scopeVisibleToMember($query, Member $member)
    {
        $roles = $member->effectiveAdherentRoles();
        $groupIds = $member->workGroups()->wherePivot('status', 'active')
            ->pluck('work_groups.id')->all();

        return $query->where(function ($q) use ($roles, $groupIds) {
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

            if (! empty($groupIds)) {
                $q->orWhere(function ($g) use ($groupIds) {
                    $g->where('visibility', self::VIS_GROUP)
                      ->whereIn('work_group_id', $groupIds);
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
            self::VIS_GROUP => $member->workGroups()
                ->wherePivot('status', 'active')
                ->where('work_groups.id', $this->work_group_id)
                ->exists(),
            default => false,
        };
    }
```

Ajouter `use App\Models\Member;` n'est pas nécessaire (même namespace `App\Models`).

- [ ] **Step 4: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=EventVisibilityTest`
Expected: PASS (4 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Models/Event.php tests/Feature/Member/EventVisibilityTest.php
git commit -m "feat(events): scopes visibleToMember/publicOnly + isVisibleToMember (cumulatif)"
```

---

### Task 5: Hub — index `publicOnly` + garde de la page détail

**Files:**
- Modify: `app/Http/Controllers/Hub/EventController.php`
- Test: `tests/Feature/Hub/HubEventVisibilityTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Hub;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubEventVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function event(array $attrs = []): Event
    {
        return Event::create(array_merge([
            'title' => 'E'.uniqid(), 'slug' => 'e'.uniqid(),
            'start_date' => now()->addWeek(), 'status' => 'published', 'visibility' => Event::VIS_PUBLIC,
        ], $attrs));
    }

    public function test_hub_index_lists_only_public_events(): void
    {
        $pub = $this->event(['title' => 'Sortie publique', 'visibility' => Event::VIS_PUBLIC]);
        $mem = $this->event(['title' => 'Reunion adherents', 'visibility' => Event::VIS_MEMBERS]);

        $this->get(route('hub.events.index'))
            ->assertOk()
            ->assertSee('Sortie publique')
            ->assertDontSee('Reunion adherents');
    }

    public function test_hub_show_404_for_members_event_as_guest(): void
    {
        $mem = $this->event(['visibility' => Event::VIS_MEMBERS]);

        $this->get(route('hub.events.show', $mem))->assertNotFound();
    }

    public function test_hub_show_ok_for_public_event(): void
    {
        $pub = $this->event(['visibility' => Event::VIS_PUBLIC]);

        $this->get(route('hub.events.show', $pub))->assertOk();
    }
}
```

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=HubEventVisibilityTest`
Expected: FAIL (`test_hub_show_404_for_members_event_as_guest` échoue car la page s'affiche).

- [ ] **Step 3: Modifier `Hub\EventController`**

Dans `index()`, contraindre la requête au public. Repérer la construction de la liste (ex. `Event::published()...`) et ajouter `->publicOnly()`. Si le code fait `Event::query()->where('status','published')`, ajouter `->where('visibility', Event::VIS_PUBLIC)`.

Dans `show(Event $event)`, ajouter en tête :

```php
$member = auth()->check()
    ? \App\Models\Member::where('user_id', auth()->id())->first()
    : null;

abort_unless($event->status === 'published' && $event->isVisibleToMember($member), 404);
```

(Importer `use App\Models\Event;` si nécessaire pour la constante dans `index`.)

- [ ] **Step 4: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=HubEventVisibilityTest`
Expected: PASS (3 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Hub/EventController.php tests/Feature/Hub/HubEventVisibilityTest.php
git commit -m "feat(hub): agenda public uniquement + garde page detail evenement non-public"
```

---

### Task 6: Agenda espace membre filtré par membre

**Files:**
- Modify: `app/Http/Controllers/Member/DashboardController.php:51-55`
- Modify: `resources/views/member/partials/_agenda.blade.php`
- Test: `tests/Feature/Member/DashboardAgendaTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Member;

use App\Models\Event;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAgendaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Member $member;

    protected function setUp(): void
    {
        parent::setUp();
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $this->user = User::factory()->create();
        $this->member = Member::create([
            'user_id' => $this->user->id, 'member_number' => 'MA', 'email' => $this->user->email,
            'first_name' => 'Ada', 'last_name' => 'L', 'joined_at' => now(), 'is_active' => true,
        ]);
        Membership::create([
            'member_id' => $this->member->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
    }

    public function test_member_dashboard_shows_members_event_but_not_restricted(): void
    {
        Event::create(['title' => 'Atelier adherents', 'slug' => 'atelier-adh',
            'start_date' => now()->addDays(3), 'status' => 'published', 'visibility' => Event::VIS_MEMBERS]);
        Event::create(['title' => 'Reunion CA secrete', 'slug' => 'ca-secret',
            'start_date' => now()->addDays(4), 'status' => 'published',
            'visibility' => Event::VIS_RESTRICTED, 'audience_roles' => ['ca']]);

        $this->actingAs($this->user)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Atelier adherents')
            ->assertDontSee('Reunion CA secrete');
    }
}
```

> Note : vérifier le nom exact de la route du dashboard membre (`member.dashboard`) dans `routes/web.php` et l'ajuster si besoin.

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=DashboardAgendaTest`
Expected: FAIL (l'événement restreint s'affiche, ou les deux s'affichent).

- [ ] **Step 3: Filtrer l'agenda dans `DashboardController`**

Remplacer le bloc actuel (lignes ~51-55) :

```php
        $upcomingEvents = Event::where('status', 'published')
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();
```

par :

```php
        $upcomingEvents = collect();
        if ($member) {
            $upcomingEvents = Event::with('workGroup')
                ->visibleToMember($member)
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->limit(5)
                ->get();
        } else {
            $upcomingEvents = Event::publicOnly()
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->limit(5)
                ->get();
        }
```

- [ ] **Step 4: Ajouter le repère de source dans `_agenda.blade.php`**

Dans `resources/views/member/partials/_agenda.blade.php`, remplacer la ligne du chip statut :

```blade
                <span class="space-row-chip gold">À venir</span>
```

par :

```blade
                @php
                    $aud = $event->audience_roles ?? [];
                    if ($event->visibility === \App\Models\Event::VIS_GROUP) {
                        $repere = $event->workGroup?->name ?? 'Groupe';
                    } elseif ($event->meeting_url) {
                        $repere = 'Visio';
                    } elseif ($event->visibility === \App\Models\Event::VIS_RESTRICTED && $aud) {
                        $repere = implode(' · ', array_map(fn ($r) => \App\Models\Member::ADHERENT_ROLES[$r] ?? $r, $aud));
                    } elseif ($event->visibility === \App\Models\Event::VIS_MEMBERS) {
                        $repere = 'Adhérents';
                    } else {
                        $repere = 'À venir';
                    }
                @endphp
                <span class="space-row-chip gold">{{ $repere }}</span>
```

- [ ] **Step 5: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=DashboardAgendaTest`
Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Member/DashboardController.php resources/views/member/partials/_agenda.blade.php tests/Feature/Member/DashboardAgendaTest.php
git commit -m "feat(espace-membre): agenda filtre par visibilite + repere de source"
```

---

### Task 7: Formulaire admin — sélecteur de visibilité + rôles ciblés

**Files:**
- Modify: `resources/views/admin/events/_form.blade.php`
- Modify: `app/Http/Controllers/Admin/EventController.php` (`store` ~83-104, `update` ~144-165)
- Test: `tests/Feature/Admin/AdminEventVisibilityTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_restricted_event_with_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.events.store'), [
                'title' => 'Reunion bureau',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'status' => 'published',
                'visibility' => 'restricted',
                'audience_roles' => ['ca', 'bureau'],
            ])->assertRedirect();

        $event = Event::where('title', 'Reunion bureau')->firstOrFail();
        $this->assertSame('restricted', $event->visibility);
        $this->assertEqualsCanonicalizing(['ca', 'bureau'], $event->audience_roles);
    }

    public function test_restricted_requires_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.events.store'), [
                'title' => 'Sans cible',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'status' => 'published',
                'visibility' => 'restricted',
            ])->assertSessionHasErrors('audience_roles');
    }

    public function test_members_visibility_clears_roles(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.events.store'), [
                'title' => 'Pour adherents',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'status' => 'published',
                'visibility' => 'members',
                'audience_roles' => ['ca'],
            ])->assertRedirect();

        $event = Event::where('title', 'Pour adherents')->firstOrFail();
        $this->assertNull($event->audience_roles);
    }
}
```

> Note : vérifier que `User::factory()->create(['role' => 'admin'])` passe la garde d'accès admin (middleware des routes `admin.*`). Ajuster le rôle si la constante diffère (`User::ROLE_ADMIN`).

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=AdminEventVisibilityTest`
Expected: FAIL (pas de validation `visibility`, `audience_roles` non persisté).

- [ ] **Step 3: Ajouter le bloc Visibilité au formulaire**

Dans `resources/views/admin/events/_form.blade.php`, juste après le `<div class="form-group">` du `status` (vers la ligne 95, avant le bloc `event_type`), insérer :

```blade
        <div class="form-group">
            <label class="form-label" for="visibility">Visibilité *</label>
            <select name="visibility" id="visibility" class="form-input" required
                    x-data x-init="$watch('$el.value', () => {})"
                    onchange="document.getElementById('audience-roles-block').style.display = this.value === 'restricted' ? 'block' : 'none'">
                @php $vis = old('visibility', $event->visibility ?? 'public'); @endphp
                <option value="public" {{ $vis === 'public' ? 'selected' : '' }}>Public (site + espace membre)</option>
                <option value="members" {{ $vis === 'members' ? 'selected' : '' }}>Adhérents (espace membre)</option>
                <option value="restricted" {{ $vis === 'restricted' ? 'selected' : '' }}>Restreint (fonctions)</option>
            </select>
            @error('visibility')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <div class="form-group" id="audience-roles-block" style="{{ old('visibility', $event->visibility ?? 'public') === 'restricted' ? '' : 'display:none;' }}">
            <label class="form-label">Fonctions ciblées *</label>
            @php $selectedRoles = old('audience_roles', $event->audience_roles ?? []); @endphp
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

- [ ] **Step 4: Valider + persister dans `store()` et `update()`**

Dans les DEUX méthodes `store()` et `update()` de `Admin\EventController`, ajouter dans le tableau de `validate()` (après la règle `status`) :

```php
            'visibility' => 'required|in:public,members,restricted',
            'audience_roles' => 'nullable|array|required_if:visibility,restricted',
            'audience_roles.*' => 'in:ca,bureau,validateur',
```

Puis, juste après le `$validated = $request->validate([...]);` (dans les deux méthodes), ajouter :

```php
        if (($validated['visibility'] ?? 'public') !== 'restricted') {
            $validated['audience_roles'] = null;
        }
```

(Pas besoin de toucher `work_group_id`/`meeting_url` : les événements admin restent non-groupe.)

- [ ] **Step 5: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=AdminEventVisibilityTest`
Expected: PASS (3 tests).

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/events/_form.blade.php app/Http/Controllers/Admin/EventController.php tests/Feature/Admin/AdminEventVisibilityTest.php
git commit -m "feat(admin): visibilite evenement (public/adherents/restreint) + roles cibles"
```

> **Polish optionnel (hors TDD)** — la spec mentionne un badge de visibilité + un
> filtre `visibility` dans la liste admin (`admin/events/index.blade.php` +
> `Admin\EventController::index`). Cosmétique : à ajouter après coup si souhaité
> (afficher `Member::ADHERENT_ROLES`/libellé visibilité par ligne, et un `<select>`
> de filtre passé à la requête comme les filtres `status`/`event_type` existants).

---

### Task 8: Fiche membre admin — `adherent_roles`

**Files:**
- Modify: `resources/views/admin/members/_form.blade.php`
- Modify: `app/Http/Controllers/Admin/MemberController.php` (`store` ~93, `update` ~151)
- Modify: `resources/views/admin/members/show.blade.php`
- Test: `tests/Feature/Admin/AdminMemberRolesTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMemberRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_update_persists_adherent_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $u = User::factory()->create();
        $member = Member::create([
            'user_id' => $u->id, 'member_number' => 'MZ', 'email' => $u->email,
            'first_name' => 'Zoe', 'last_name' => 'M', 'joined_at' => now(), 'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.members.update', $member), [
                'first_name' => 'Zoe', 'last_name' => 'M', 'email' => $u->email,
                'contact_type' => 'individuel',
                'adherent_roles' => ['ca', 'validateur'],
            ])->assertRedirect();

        $this->assertEqualsCanonicalizing(['ca', 'validateur'], $member->fresh()->adherent_roles);
    }
}
```

> Note : compléter le payload `put` avec les champs requis par la validation `update()` de `MemberController` (lire la méthode ~151-170 et inclure les champs `required`).

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=AdminMemberRolesTest`
Expected: FAIL (`adherent_roles` null après update).

- [ ] **Step 3: Ajouter le bloc de cases dans `_form.blade.php`**

Dans `resources/views/admin/members/_form.blade.php`, ajouter un bloc (dans une carte thématique cohérente, ex. après les centres d'intérêt) :

```blade
<div class="form-group">
    <label class="form-label">Rôles adhérent</label>
    @php $roles = old('adherent_roles', $member->adherent_roles ?? []); @endphp
    @foreach(\App\Models\Member::ADHERENT_ROLES as $key => $label)
        <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; margin-bottom:0.25rem;">
            <input type="checkbox" name="adherent_roles[]" value="{{ $key }}"
                   {{ in_array($key, $roles) ? 'checked' : '' }} style="width:auto;">
            <span>{{ $label }}</span>
        </label>
    @endforeach
    <p style="color:#6b7280; font-size:0.75rem; margin-top:0.25rem;">Le bureau voit aussi les événements ciblés CA.</p>
</div>
```

- [ ] **Step 4: Valider + persister dans `store()` et `update()`**

Dans `store()` et `update()` de `MemberController`, ajouter à `validate()` :

```php
            'adherent_roles' => 'nullable|array',
            'adherent_roles.*' => 'in:ca,bureau,validateur',
```

Puis après `$validated = $request->validate([...]);` :

```php
        $validated['adherent_roles'] = $request->input('adherent_roles', []);
```

(Garantit la mise à zéro quand toutes les cases sont décochées.)

- [ ] **Step 5: Afficher les rôles sur la fiche (show)**

Dans `resources/views/admin/members/show.blade.php`, à un endroit cohérent (en-tête de fiche) :

```blade
@if(!empty($member->adherent_roles))
    <div style="display:flex; gap:0.4rem; flex-wrap:wrap; margin-top:0.5rem;">
        @foreach($member->adherent_roles as $r)
            <span class="badge" style="background:#e8f0ea; color:#2C5F2D; padding:2px 8px; border-radius:9999px; font-size:0.75rem;">
                {{ \App\Models\Member::ADHERENT_ROLES[$r] ?? $r }}
            </span>
        @endforeach
    </div>
@endif
```

- [ ] **Step 6: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=AdminMemberRolesTest`
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add resources/views/admin/members/_form.blade.php resources/views/admin/members/show.blade.php app/Http/Controllers/Admin/MemberController.php tests/Feature/Admin/AdminMemberRolesTest.php
git commit -m "feat(admin): roles adherent (CA/Bureau/Validateur) sur la fiche membre"
```

---

## LOT 2 — Événements de groupe

### Task 9: Relation `WorkGroup::events()`

**Files:**
- Modify: `app/Models/WorkGroup.php`

- [ ] **Step 1: Ajouter la relation**

Dans `app/Models/WorkGroup.php`, après `projects()` :

```php
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
```

(`use Illuminate\Database\Eloquent\Relations\HasMany;` est déjà importé.)

- [ ] **Step 2: Vérifier rapidement (tinker)**

Run: `php artisan tinker --execute="echo App\Models\WorkGroup::first()?->events()->count() ?? 0;"`
Expected: `0` (ou un nombre), sans erreur.

- [ ] **Step 3: Commit**

```bash
git add app/Models/WorkGroup.php
git commit -m "feat(groupe): relation WorkGroup hasMany events"
```

---

### Task 10: `WorkGroupEventController` + routes + garde coordinateur

**Files:**
- Create: `app/Http/Controllers/Member/WorkGroupEventController.php`
- Modify: `routes/web.php` (groupe `current_member`, ~ligne 188 après les projets)
- Test: `tests/Feature/Member/WorkGroupEventTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Member;

use App\Models\Event;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use App\Models\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkGroupEventTest extends TestCase
{
    use RefreshDatabase;

    private function member(): array
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
        return [$u, $m];
    }

    public function test_coordinator_can_create_online_group_event(): void
    {
        [$u, $m] = $this->member();
        $wg = WorkGroup::create(['name' => 'GT Micro', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'coordinator', 'status' => 'active', 'joined_at' => now()]);

        $this->actingAs($u)
            ->post(route('member.work-groups.events.store', $wg), [
                'title' => 'Visio mensuelle',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'mode' => 'online',
                'meeting_url' => 'https://meet.example.org/abc',
            ])->assertRedirect();

        $event = Event::where('title', 'Visio mensuelle')->firstOrFail();
        $this->assertSame(Event::VIS_GROUP, $event->visibility);
        $this->assertSame($wg->id, $event->work_group_id);
        $this->assertSame('https://meet.example.org/abc', $event->meeting_url);
        $this->assertSame('published', $event->status);
    }

    public function test_non_coordinator_cannot_create(): void
    {
        [$u, $m] = $this->member();
        $wg = WorkGroup::create(['name' => 'GT Macro', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);

        $this->actingAs($u)
            ->post(route('member.work-groups.events.store', $wg), [
                'title' => 'Tentative',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'mode' => 'online',
                'meeting_url' => 'https://meet.example.org/x',
            ])->assertForbidden();

        $this->assertDatabaseMissing('events', ['title' => 'Tentative']);
    }

    public function test_online_mode_requires_meeting_url(): void
    {
        [$u, $m] = $this->member();
        $wg = WorkGroup::create(['name' => 'GT Zyg', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'coordinator', 'status' => 'active', 'joined_at' => now()]);

        $this->actingAs($u)
            ->post(route('member.work-groups.events.store', $wg), [
                'title' => 'Sans lien',
                'start_date' => now()->addWeek()->format('Y-m-d\TH:i'),
                'mode' => 'online',
            ])->assertSessionHasErrors('meeting_url');
    }
}
```

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=WorkGroupEventTest`
Expected: FAIL (route inexistante).

- [ ] **Step 3: Créer le contrôleur**

```php
<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\WorkGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkGroupEventController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'event_type' => 'nullable|string|max:100',
            'mode' => 'required|in:onsite,online',
            'location_name' => 'nullable|string|max:255|required_if:mode,onsite',
            'location_address' => 'nullable|string|max:255',
            'location_city' => 'nullable|string|max:100',
            'meeting_url' => 'nullable|url|max:500|required_if:mode,online',
            'description' => 'nullable|string',
        ];
    }

    private function payload(array $data, WorkGroup $workGroup, int $userId): array
    {
        $onsite = $data['mode'] === 'onsite';

        return [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'event_type' => $data['event_type'] ?? 'reunion',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'location_name' => $onsite ? ($data['location_name'] ?? null) : null,
            'location_address' => $onsite ? ($data['location_address'] ?? null) : null,
            'location_city' => $onsite ? ($data['location_city'] ?? null) : null,
            'meeting_url' => $onsite ? null : ($data['meeting_url'] ?? null),
            'visibility' => Event::VIS_GROUP,
            'organizer_id' => $userId,
            'status' => 'published',
            'published_at' => now(),
        ];
    }

    public function store(Request $request, WorkGroup $workGroup)
    {
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $data = $request->validate($this->rules());

        $payload = $this->payload($data, $workGroup, $request->user()->id);
        $payload['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(6));

        $workGroup->events()->create($payload);

        return back()->with('success', 'Réunion planifiée.');
    }

    public function update(Request $request, WorkGroup $workGroup, Event $event)
    {
        abort_unless($event->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $data = $request->validate($this->rules());
        $event->update($this->payload($data, $workGroup, $event->organizer_id ?? $request->user()->id));

        return back()->with('success', 'Réunion mise à jour.');
    }

    public function destroy(Request $request, WorkGroup $workGroup, Event $event)
    {
        abort_unless($event->work_group_id === $workGroup->id, 404);
        abort_unless($request->user()->can('manage', $workGroup), 403);

        $event->delete();

        return back()->with('success', 'Réunion supprimée.');
    }
}
```

- [ ] **Step 4: Déclarer les routes**

Dans `routes/web.php`, dans le groupe `current_member` (après les routes `work-groups.projects.*`, ~ligne 188), ajouter :

```php
        // Work Groups — événements / réunions (gestion par coordinateur)
        Route::post('/groupes-de-travail/{workGroup:slug}/evenements', [\App\Http\Controllers\Member\WorkGroupEventController::class, 'store'])->name('work-groups.events.store');
        Route::put('/groupes-de-travail/{workGroup:slug}/evenements/{event}', [\App\Http\Controllers\Member\WorkGroupEventController::class, 'update'])->name('work-groups.events.update');
        Route::delete('/groupes-de-travail/{workGroup:slug}/evenements/{event}', [\App\Http\Controllers\Member\WorkGroupEventController::class, 'destroy'])->name('work-groups.events.destroy');
```

- [ ] **Step 5: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=WorkGroupEventTest`
Expected: PASS (3 tests).

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Member/WorkGroupEventController.php routes/web.php tests/Feature/Member/WorkGroupEventTest.php
git commit -m "feat(groupe): creation reunion par coordinateur (presentiel ou visio)"
```

---

### Task 11: UI page groupe — « Prochaines réunions » + formulaire coordinateur

**Files:**
- Create: `resources/views/member/work-groups/partials/_events.blade.php`
- Modify: `resources/views/member/work-groups/show.blade.php:124` (tab accueil)
- Modify: `app/Http/Controllers/Member/WorkGroupController.php` (méthode `show`)
- Test: `tests/Feature/Member/WorkGroupEventsDisplayTest.php`

- [ ] **Step 1: Écrire le test qui échoue**

```php
<?php

namespace Tests\Feature\Member;

use App\Models\Event;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use App\Models\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkGroupEventsDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_page_shows_upcoming_meeting(): void
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );
        $u = User::factory()->create();
        $m = Member::create([
            'user_id' => $u->id, 'member_number' => 'MD', 'email' => $u->email,
            'first_name' => 'D', 'last_name' => 'E', 'joined_at' => now(), 'is_active' => true,
        ]);
        Membership::create([
            'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
            'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
            'amount_paid' => 30, 'lepis_format' => 'paper',
        ]);
        $wg = WorkGroup::create(['name' => 'GT Reunion', 'is_active' => true]);
        $wg->members()->attach($m->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);
        $wg->events()->create([
            'title' => 'Reunion de cadrage', 'slug' => 'reunion-cadrage',
            'start_date' => now()->addDays(5), 'status' => 'published',
            'visibility' => Event::VIS_GROUP, 'organizer_id' => $u->id,
        ]);

        $this->actingAs($u)
            ->get(route('member.work-groups.show', $wg))
            ->assertOk()
            ->assertSee('Reunion de cadrage');
    }
}
```

- [ ] **Step 2: Lancer le test pour vérifier l'échec**

Run: `php artisan test --filter=WorkGroupEventsDisplayTest`
Expected: FAIL (le titre n'apparaît pas).

- [ ] **Step 3: Passer les événements depuis `WorkGroupController::show`**

Dans la méthode `show()` de `Member\WorkGroupController`, avant le `return view(...)`, ajouter :

```php
        $upcomingGroupEvents = $workGroup->events()
            ->where('status', 'published')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->get();
```

Ajouter `'upcomingGroupEvents'` à la liste `compact(...)` (ou au tableau de données passé à la vue).

- [ ] **Step 4: Créer le partial `_events.blade.php`**

```blade
@php
    $cm = $currentMember ?? \App\Models\Member::where('user_id', auth()->id())->first();
    $canManage = $workGroup->isCoordinator($cm);
@endphp
<section class="wg-card" style="margin-top:18px;">
    <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0;">Prochaines réunions</h3>
        @if($canManage)
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('wg-event-form').style.display = document.getElementById('wg-event-form').style.display==='block' ? 'none' : 'block';">
            <i data-lucide="calendar-plus"></i>Planifier
        </button>
        @endif
    </div>

    @if($canManage)
    <form id="wg-event-form" method="POST" action="{{ route('member.work-groups.events.store', $workGroup) }}"
          style="display:none; margin:12px 0; padding:14px; border:1px solid var(--border); border-radius:14px;"
          x-data="{ mode: 'online' }">
        @csrf
        <div class="form-group">
            <label>Titre</label>
            <input type="text" name="title" required class="form-input" value="{{ old('title') }}">
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div class="form-group">
                <label>Début</label>
                <input type="datetime-local" name="start_date" required class="form-input" value="{{ old('start_date') }}">
            </div>
            <div class="form-group">
                <label>Fin (optionnel)</label>
                <input type="datetime-local" name="end_date" class="form-input" value="{{ old('end_date') }}">
            </div>
        </div>
        <div class="form-group">
            <label>Mode</label>
            <select name="mode" x-model="mode" class="form-input">
                <option value="online">Visio</option>
                <option value="onsite">Présentiel</option>
            </select>
        </div>
        <div class="form-group" x-show="mode === 'online'">
            <label>Lien visio</label>
            <input type="url" name="meeting_url" class="form-input" placeholder="https://..." value="{{ old('meeting_url') }}">
        </div>
        <template x-if="mode === 'onsite'">
            <div>
                <div class="form-group"><label>Lieu</label><input type="text" name="location_name" class="form-input" value="{{ old('location_name') }}"></div>
                <div class="form-group"><label>Ville</label><input type="text" name="location_city" class="form-input" value="{{ old('location_city') }}"></div>
            </div>
        </template>
        <div class="form-group">
            <label>Description (optionnel)</label>
            <textarea name="description" rows="2" class="form-input">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary"><i data-lucide="check"></i>Enregistrer</button>
    </form>
    @endif

    @forelse($upcomingGroupEvents as $ev)
        <div class="agenda-item" style="grid-template-columns:56px 1fr auto; padding:10px 0; border-bottom:1px solid var(--border);">
            <div class="agenda-date">
                <small>{{ $ev->start_date->translatedFormat('M') }}</small>
                <strong>{{ $ev->start_date->format('d') }}</strong>
            </div>
            <div class="agenda-item-body">
                <strong>{{ $ev->title }}</strong>
                <small>
                    {{ $ev->start_date->format('H\hi') }}
                    @if($ev->meeting_url) · <a href="{{ $ev->meeting_url }}" target="_blank" rel="noopener">Lien visio</a>
                    @elseif($ev->location_city) · {{ $ev->location_city }}@endif
                </small>
            </div>
            @if($canManage)
            <form method="POST" action="{{ route('member.work-groups.events.destroy', [$workGroup, $ev]) }}"
                  onsubmit="return confirm('Supprimer cette réunion ?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn-icon" title="Supprimer"><i data-lucide="trash-2"></i></button>
            </form>
            @endif
        </div>
    @empty
        <p style="color:var(--muted); padding:10px 0;">Aucune réunion programmée.</p>
    @endforelse
</section>
```

- [ ] **Step 5: Inclure le partial dans l'onglet accueil**

Dans `resources/views/member/work-groups/show.blade.php`, juste après l'include des projets de l'onglet accueil (ligne 124, `@include('member.work-groups.partials._projects')`), ajouter :

```blade
            @include('member.work-groups.partials._events')
```

- [ ] **Step 6: Lancer le test pour vérifier le succès**

Run: `php artisan test --filter=WorkGroupEventsDisplayTest`
Expected: PASS.

- [ ] **Step 7: Rebuild assets (classes Tailwind éventuelles) + commit**

Run: `npm run build`
Expected: build OK.

```bash
git add resources/views/member/work-groups/partials/_events.blade.php resources/views/member/work-groups/show.blade.php app/Http/Controllers/Member/WorkGroupController.php tests/Feature/Member/WorkGroupEventsDisplayTest.php public/build
git commit -m "feat(groupe): section Prochaines reunions + formulaire coordinateur"
```

---

### Task 12: Test bout-en-bout — réunion de groupe dans l'agenda membre

**Files:**
- Test: `tests/Feature/Member/GroupEventInAgendaTest.php`

- [ ] **Step 1: Écrire le test**

```php
<?php

namespace Tests\Feature\Member;

use App\Models\Event;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use App\Models\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupEventInAgendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_meeting_appears_in_member_agenda_for_group_member_only(): void
    {
        $type = MembershipType::firstOrCreate(
            ['slug' => 'std'],
            ['name' => 'Std', 'price' => 30, 'duration_months' => 12, 'is_active' => true, 'sort_order' => 1]
        );

        $make = function (string $num) use ($type) {
            $u = User::factory()->create();
            $m = Member::create([
                'user_id' => $u->id, 'member_number' => $num, 'email' => $u->email,
                'first_name' => 'A', 'last_name' => 'B', 'joined_at' => now(), 'is_active' => true,
            ]);
            Membership::create([
                'member_id' => $m->id, 'membership_type_id' => $type->id, 'status' => 'active',
                'start_date' => now()->subMonth(), 'end_date' => now()->addMonth(),
                'amount_paid' => 30, 'lepis_format' => 'paper',
            ]);
            return [$u, $m];
        };

        [$uIn, $mIn] = $make('IN');
        [$uOut, $mOut] = $make('OUT');
        $wg = WorkGroup::create(['name' => 'GT X', 'is_active' => true]);
        $wg->members()->attach($mIn->id, ['role' => 'member', 'status' => 'active', 'joined_at' => now()]);
        $wg->events()->create([
            'title' => 'Reunion interne X', 'slug' => 'reunion-interne-x',
            'start_date' => now()->addDays(2), 'status' => 'published',
            'visibility' => Event::VIS_GROUP, 'organizer_id' => $uIn->id,
        ]);

        $this->actingAs($uIn)->get(route('member.dashboard'))->assertSee('Reunion interne X');
        $this->actingAs($uOut)->get(route('member.dashboard'))->assertDontSee('Reunion interne X');
    }
}
```

- [ ] **Step 2: Lancer le test**

Run: `php artisan test --filter=GroupEventInAgendaTest`
Expected: PASS (la logique existe déjà via Task 4/6 ; ce test la verrouille).

- [ ] **Step 3: Lancer toute la suite ciblée**

Run: `php artisan test --filter="Event|Agenda|WorkGroupEvent|AdherentRoles|MemberRoles"`
Expected: tous PASS.

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/Member/GroupEventInAgendaTest.php
git commit -m "test(agenda): reunion de groupe visible uniquement par les membres du groupe"
```

---

## Vérification finale

- [ ] **Suite complète**

Run: `php artisan test`
Expected: vert (aucune régression).

- [ ] **Grep accents** (les vues modifiées ne doivent pas avoir perdu leurs accents)

Run: `git diff --stat main`
Vérifier visuellement les libellés FR des vues touchées.

- [ ] **Nettoyage caches**

Run: `php artisan config:clear; php artisan view:clear`
