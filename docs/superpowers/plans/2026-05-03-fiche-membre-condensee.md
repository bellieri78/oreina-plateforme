# Fiche contact admin condensée — Plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refondre la fiche contact admin `/extranet/members/{id}` pour la rendre plus dense (KPI bar synthétique, cartes raccourcies à 5 items via `<details>`) et plus complète (groupes de travail, publications Chersotis, suggestions Lepis).

**Architecture:** Modifications additives sur `Member` (2 nouvelles relations Eloquent), eager-load étendu dans `MemberController::show`, refonte complète du blade `show.blade.php` en gardant les classes existantes (`.card`, `.dashboard-stat-card`, `.badge`, `.table`) et les icônes Lucide déjà utilisées dans le dashboard. Aucune migration. Aucune nouvelle route.

**Tech Stack:** Laravel 12, Blade, Tailwind v4 (via classes admin custom), Lucide icons, PostgreSQL.

**Spec source:** `docs/superpowers/specs/2026-05-03-fiche-membre-condensee-design.md`

---

## File map

**Modified:**
- `app/Models/Member.php` — ajouter `lepisSuggestions()` HasMany + `submissions()` HasManyThrough.
- `app/Http/Controllers/Admin/MemberController.php` — étendre `$member->load([...])` dans `show()` (ligne 116).
- `resources/views/admin/members/show.blade.php` — refonte complète (228 lignes → ~280 lignes après ajouts).

**Created:**
- `tests/Unit/Models/MemberRelationsTest.php` — tests unitaires des 2 nouvelles relations.
- `tests/Feature/Admin/MemberShowFicheTest.php` — 8 tests feature couvrant la refonte UI.

---

## Task 1 — Relations `lepisSuggestions()` et `submissions()` sur Member

**Files:**
- Modify: `app/Models/Member.php`
- Create: `tests/Unit/Models/MemberRelationsTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Unit/Models/MemberRelationsTest.php`:

```php
<?php

namespace Tests\Unit\Models;

use App\Models\LepisSuggestion;
use App\Models\Member;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_lepis_suggestions_relation_returns_member_suggestions(): void
    {
        $member = $this->makeMember();
        LepisSuggestion::create([
            'member_id' => $member->id,
            'title' => 'Idée 1',
            'content' => 'Contenu',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        LepisSuggestion::create([
            'member_id' => $member->id,
            'title' => 'Idée 2',
            'content' => 'Contenu',
            'status' => 'noted',
            'submitted_at' => now()->subDay(),
        ]);

        $this->assertCount(2, $member->fresh()->lepisSuggestions);
    }

    public function test_submissions_relation_returns_user_submissions(): void
    {
        $member = $this->makeMember();
        Submission::create([
            'author_id' => $member->user_id,
            'title' => 'Article 1',
            'status' => 'submitted',
        ]);
        Submission::create([
            'author_id' => $member->user_id,
            'title' => 'Article 2',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->assertCount(2, $member->fresh()->submissions);
        $this->assertEquals(['Article 1', 'Article 2'], $member->submissions->pluck('title')->sort()->values()->all());
    }

    public function test_submissions_relation_excludes_other_members_submissions(): void
    {
        $alice = $this->makeMember('alice@test.com');
        $bob = $this->makeMember('bob@test.com');
        Submission::create(['author_id' => $alice->user_id, 'title' => 'Alice', 'status' => 'submitted']);
        Submission::create(['author_id' => $bob->user_id, 'title' => 'Bob', 'status' => 'submitted']);

        $this->assertCount(1, $alice->fresh()->submissions);
        $this->assertSame('Alice', $alice->submissions->first()->title);
    }

    private function makeMember(string $email = null): Member
    {
        $email = $email ?: 'm' . random_int(1000, 9999) . '@test.com';
        $user = User::factory()->create(['email' => $email]);
        return Member::create([
            'user_id' => $user->id,
            'member_number' => 'M' . random_int(1000, 9999),
            'email' => $email,
            'first_name' => 'F',
            'last_name' => 'L',
            'joined_at' => now(),
        ]);
    }
}
```

- [ ] **Step 2: Run the test, expect failure**

`php artisan test --filter=MemberRelationsTest`
Expected: FAIL — `Call to undefined method App\Models\Member::lepisSuggestions()` (and submissions).

- [ ] **Step 3: Add the relations on `Member`**

In `app/Models/Member.php`, near the existing relation methods (around line 146 where `memberships()` is defined), add:

```php
public function lepisSuggestions(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(\App\Models\LepisSuggestion::class)->orderByDesc('submitted_at');
}

public function submissions(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
{
    return $this->hasManyThrough(
        \App\Models\Submission::class,
        \App\Models\User::class,
        'id',         // FK on users (= users.id linked from members.user_id)
        'author_id',  // FK on submissions
        'user_id',    // local key on members
        'id'          // local key on users
    )->orderByDesc('created_at');
}
```

- [ ] **Step 4: Run the test, expect pass**

`php artisan test --filter=MemberRelationsTest`
Expected: 3 passing.

- [ ] **Step 5: Commit**

```bash
git add app/Models/Member.php tests/Unit/Models/MemberRelationsTest.php
git commit -m "feat(members): relations lepisSuggestions et submissions sur Member"
```

---

## Task 2 — Étendre eager-load dans `MemberController::show`

**Files:**
- Modify: `app/Http/Controllers/Admin/MemberController.php` (line 116)

- [ ] **Step 1: Modify the show() method**

In `app/Http/Controllers/Admin/MemberController.php`, replace line 116 (the `$member->load([...])`):

```php
$member->load([
    'memberships.membershipType',
    'donations',
    'purchases.product',
    'consents',
    'lepisBulletinRecipients.bulletin',
    'workGroups',
    'lepisSuggestions',
    'user.submissions' => function ($q) {
        $q->orderByDesc('created_at');
    },
]);
```

- [ ] **Step 2: Smoke test that the page still loads**

Run a quick test to ensure the eager-load doesn't crash:

`php artisan test --filter=ExampleTest`
Expected: 2 passing (regression check on a basic test).

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Admin/MemberController.php
git commit -m "feat(members): eager-load des relations engagement dans MemberController::show"
```

---

## Task 3 — KPI bar (`dashboard-stats` grid)

**Files:**
- Modify: `resources/views/admin/members/show.blade.php`
- Create: `tests/Feature/Admin/MemberShowFicheTest.php` (premier test, on l'enrichira)

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/Admin/MemberShowFicheTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Donation;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberShowFicheTest extends TestCase
{
    use RefreshDatabase;

    public function test_kpi_bar_hides_zero_counters(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        // Pas de membership, dons, achats, etc.

        $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

        $response->assertOk()
            ->assertDontSee('Adhésions')   // KPI label invisible because count=0
            ->assertDontSee('Dons cumulés')
            ->assertDontSee('Achats')
            ->assertDontSee('Bulletins reçus')
            ->assertDontSee('Groupes')
            ->assertDontSee('Publications')
            ->assertDontSee('Suggestions');
    }

    public function test_kpi_bar_shows_donation_sum_in_euros(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        Donation::create([
            'member_id' => $member->id,
            'donor_email' => $member->email,
            'donor_name' => $member->full_name,
            'amount' => 100,
            'donation_date' => now(),
            'payment_method' => 'helloasso',
        ]);
        Donation::create([
            'member_id' => $member->id,
            'donor_email' => $member->email,
            'donor_name' => $member->full_name,
            'amount' => 230,
            'donation_date' => now()->subMonth(),
            'payment_method' => 'helloasso',
        ]);

        $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

        $response->assertOk()
            ->assertSee('Dons cumulés')
            ->assertSee('330'); // sum of donations
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    protected function makeMember(): Member
    {
        $u = User::factory()->create();
        return Member::create([
            'user_id' => $u->id,
            'member_number' => 'M' . random_int(1000, 99999),
            'email' => $u->email,
            'first_name' => 'F',
            'last_name' => 'L',
            'joined_at' => now(),
        ]);
    }

    protected function makeActiveMembership(Member $member, string $lepisFormat = 'paper'): Membership
    {
        if (! MembershipType::where('slug', 'standard')->exists()) {
            MembershipType::create([
                'name' => 'Standard', 'slug' => 'standard', 'price' => 30,
                'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
            ]);
        }
        return Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => MembershipType::where('slug', 'standard')->first()->id,
            'status' => 'active',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'amount_paid' => 30,
            'lepis_format' => $lepisFormat,
        ]);
    }
}
```

- [ ] **Step 2: Run the tests, expect failure**

`php artisan test --filter=MemberShowFicheTest`
Expected: FAIL — current page shows "Adhésions" / "Dons" labels in card titles even when empty, no "Dons cumulés" label.

- [ ] **Step 3: Replace `show.blade.php` header section + add KPI bar**

In `resources/views/admin/members/show.blade.php`, replace the `@section('content')` opening block (currently starts at line 10 with the `<div style="display: grid;...">`). Insert a KPI bar BEFORE the existing 2-column grid:

```blade
@section('content')
    @php
        $adhesionsCount = $member->memberships->count();
        $donsTotal = $member->donations->sum('amount');
        $achatsCount = $member->purchases->count();
        $bulletinsCount = $member->lepisBulletinRecipients->count();
        $groupesCount = $member->workGroups->count();
        $publicationsCount = $member->user?->submissions->count() ?? 0;
        $suggestionsCount = $member->lepisSuggestions->count();
        $currentMembership = $member->memberships->where('end_date', '>=', now())->sortByDesc('end_date')->first();
        $lepisFormat = $currentMembership?->lepis_format ?: ($currentMembership ? 'paper' : null);

        $formatDonsTotal = function ($v) {
            if ($v >= 1000) return number_format($v / 1000, 1, ',', ' ') . 'k €';
            return number_format($v, 0, ',', ' ') . ' €';
        };
    @endphp

    {{-- KPI BAR --}}
    @if($adhesionsCount + $donsTotal + $achatsCount + $bulletinsCount + $groupesCount + $publicationsCount + $suggestionsCount > 0)
        <div class="dashboard-stats" style="margin-bottom: 1.5rem;">
            @if($adhesionsCount > 0)
                <a href="#adhesions" class="dashboard-stat-card dashboard-stat-purple" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="id-card" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $adhesionsCount }}</span>
                        <span class="dashboard-stat-label">Adhésions</span>
                    </div>
                </a>
            @endif
            @if($donsTotal > 0)
                <a href="#dons" class="dashboard-stat-card dashboard-stat-green" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="circle-dollar-sign" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $formatDonsTotal($donsTotal) }}</span>
                        <span class="dashboard-stat-label">Dons cumulés</span>
                    </div>
                </a>
            @endif
            @if($achatsCount > 0)
                <a href="#achats" class="dashboard-stat-card dashboard-stat-orange" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="shopping-cart" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $achatsCount }}</span>
                        <span class="dashboard-stat-label">Achats</span>
                    </div>
                </a>
            @endif
            @if($bulletinsCount > 0)
                <a href="#bulletins" class="dashboard-stat-card dashboard-stat-blue" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="mail" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $bulletinsCount }}</span>
                        <span class="dashboard-stat-label">Bulletins reçus</span>
                    </div>
                </a>
            @endif
            @if($groupesCount > 0)
                <a href="#engagement" class="dashboard-stat-card dashboard-stat-purple" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="users-round" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $groupesCount }}</span>
                        <span class="dashboard-stat-label">Groupes</span>
                    </div>
                </a>
            @endif
            @if($publicationsCount > 0)
                <a href="#engagement" class="dashboard-stat-card dashboard-stat-blue" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="book-open" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $publicationsCount }}</span>
                        <span class="dashboard-stat-label">Publications Chersotis</span>
                    </div>
                </a>
            @endif
            @if($suggestionsCount > 0)
                <a href="#engagement" class="dashboard-stat-card dashboard-stat-orange" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="lightbulb" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $suggestionsCount }}</span>
                        <span class="dashboard-stat-label">Suggestions Lepis</span>
                    </div>
                </a>
            @endif
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        {{-- ... le reste de la page (sidebar + body) reste à modifier dans les tasks suivantes ... --}}
```

**NB:** garde le reste de la page tel quel pour cette task. On modifiera la sidebar et les cartes dans les tasks 4-6. Le `@php` block en haut pré-calcule toutes les variables qu'on réutilisera ensuite. Make sure the closing `</div>` and `@endsection` at the bottom of the file remain.

- [ ] **Step 4: Run the tests, expect pass**

`php artisan test --filter=MemberShowFicheTest`
Expected: 2 passing (test_kpi_bar_hides_zero_counters + test_kpi_bar_shows_donation_sum_in_euros).

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/members/show.blade.php tests/Feature/Admin/MemberShowFicheTest.php
git commit -m "feat(members): KPI bar synthétique sur fiche contact admin"
```

---

## Task 4 — Sidebar info enrichie (Format Lepis + Groupes + Engagement)

**Files:**
- Modify: `resources/views/admin/members/show.blade.php`
- Modify: `tests/Feature/Admin/MemberShowFicheTest.php` (ajout de 2 tests)

- [ ] **Step 1: Add the failing tests**

Append these tests inside `MemberShowFicheTest` class in `tests/Feature/Admin/MemberShowFicheTest.php`:

```php
public function test_lepis_format_in_sidebar_when_active_membership(): void
{
    $admin = $this->makeAdmin();
    $member = $this->makeMember();
    $this->makeActiveMembership($member, 'paper');

    $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

    $response->assertOk()
        ->assertSee('Format Lepis')
        ->assertSee('Papier');
}

public function test_sidebar_engagement_block_hidden_when_no_activity(): void
{
    $admin = $this->makeAdmin();
    $member = $this->makeMember();
    // No memberships, no submissions, no suggestions, no work groups
    $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

    $response->assertOk()
        ->assertDontSee('Auteur Chersotis')
        ->assertDontSee('Contributeur Lepis')
        ->assertDontSee('Format Lepis');  // pas de membership active
}
```

- [ ] **Step 2: Run them, expect failure**

`php artisan test --filter=MemberShowFicheTest`
Expected: 2 of the 4 tests fail (the new ones).

- [ ] **Step 3: Modify the sidebar info card in `show.blade.php`**

In `resources/views/admin/members/show.blade.php`, find the existing sidebar `<div class="card">` (the "Informations" card, around line 13). After the existing block that ends with `Inscrit le ... {{ $member->created_at->format('d/m/Y') }}` and its closing `</div>`, but BEFORE the closing `</div>` of `card-body`, add the 3 new sub-blocks. The full updated sidebar card body becomes:

```blade
<div class="card-body">
    {{-- Bloc identité existant (avatar + nom + statut) — INCHANGÉ --}}
    <div style="text-align: center; margin-bottom: 1.5rem;">
        {{-- ... bloc avatar inchangé ... --}}
    </div>

    {{-- Bloc contact existant — INCHANGÉ --}}
    <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
        {{-- email/tél/adresse/inscrit le ... inchangé ... --}}
    </div>

    {{-- NOUVEAU : Format Lepis --}}
    @if($lepisFormat)
        <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 1rem;">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Format Lepis</div>
            <div>{{ $lepisFormat === 'digital' ? 'Numérique' : 'Papier' }}</div>
        </div>
    @endif

    {{-- NOUVEAU : Groupes --}}
    @if($member->workGroups->isNotEmpty())
        <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 1rem;">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Groupes ({{ $member->workGroups->count() }})</div>
            @foreach($member->workGroups->take(5) as $group)
                <div style="margin-bottom: 0.25rem; font-size: 0.875rem;">
                    · {{ $group->name }}
                    @if(($group->pivot->role ?? 'member') !== 'member')
                        <span style="color: #6b7280; font-style: italic;">— {{ $group->pivot->role }}</span>
                    @endif
                </div>
            @endforeach
            @if($member->workGroups->count() > 5)
                <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem;">+ {{ $member->workGroups->count() - 5 }} autres</div>
            @endif
        </div>
    @endif

    {{-- NOUVEAU : Engagement (auteur + contributeur Lepis) --}}
    @php
        $publishedSubmissionsCount = $member->user?->submissions->where('status', \App\Enums\SubmissionStatus::Published)->count() ?? 0;
        $draftSubmissionsCount = ($publicationsCount > 0) ? $publicationsCount - $publishedSubmissionsCount : 0;
    @endphp
    @if($publicationsCount > 0 || $suggestionsCount > 0)
        <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 1rem;">
            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Engagement</div>
            @if($publicationsCount > 0)
                <div style="margin-bottom: 0.5rem; font-size: 0.875rem;">
                    <strong>Auteur Chersotis</strong>
                    <div style="color: #6b7280; font-size: 0.8125rem;">
                        {{ $publishedSubmissionsCount }} publi{{ $publishedSubmissionsCount > 1 ? 's' : '' }}
                        @if($draftSubmissionsCount > 0)
                            · {{ $draftSubmissionsCount }} en cours
                        @endif
                    </div>
                </div>
            @endif
            @if($suggestionsCount > 0)
                <div style="margin-bottom: 0.5rem; font-size: 0.875rem;">
                    <strong>Contributeur Lepis</strong>
                    <div style="color: #6b7280; font-size: 0.8125rem;">
                        {{ $suggestionsCount }} suggestion{{ $suggestionsCount > 1 ? 's' : '' }}
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
```

- [ ] **Step 4: Run the tests, expect pass**

`php artisan test --filter=MemberShowFicheTest`
Expected: 4 passing (the 2 from Task 3 + the 2 new ones).

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/members/show.blade.php tests/Feature/Admin/MemberShowFicheTest.php
git commit -m "feat(members): sidebar enrichie avec format Lepis, groupes et engagement"
```

---

## Task 5 — Cartes raccourcies via `<details>`

**Files:**
- Modify: `resources/views/admin/members/show.blade.php`
- Modify: `tests/Feature/Admin/MemberShowFicheTest.php` (ajout d'un test)

- [ ] **Step 1: Add the failing test**

Append to `tests/Feature/Admin/MemberShowFicheTest`:

```php
public function test_membership_card_truncates_at_5(): void
{
    $admin = $this->makeAdmin();
    $member = $this->makeMember();
    if (! MembershipType::where('slug', 'standard')->exists()) {
        MembershipType::create([
            'name' => 'Standard', 'slug' => 'standard', 'price' => 30,
            'duration_months' => 12, 'is_active' => true, 'sort_order' => 1,
        ]);
    }
    $typeId = MembershipType::where('slug', 'standard')->first()->id;
    // Crée 7 adhésions
    for ($i = 0; $i < 7; $i++) {
        Membership::create([
            'member_id' => $member->id,
            'membership_type_id' => $typeId,
            'status' => 'active',
            'start_date' => now()->subYears(7 - $i),
            'end_date' => now()->subYears(6 - $i),
            'amount_paid' => 30,
            'lepis_format' => 'paper',
        ]);
    }

    $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

    $response->assertOk()
        ->assertSee('Adhésions (7)')
        ->assertSee('Voir tout')
        ->assertSee('<details', escape: false);
}
```

- [ ] **Step 2: Run it, expect failure**

`php artisan test --filter=test_membership_card_truncates_at_5`
Expected: FAIL — current page shows all 7 rows in one table without `<details>`.

- [ ] **Step 3: Refactor the 4 existing cards to use a truncation pattern**

In `resources/views/admin/members/show.blade.php`, replace each of the 4 existing cards in the body (Memberships, Donations, Purchases, Bulletins Lepis) using the same truncation logic. The Bulletins card (which currently uses inline `<div>`-style markup) should be converted to the `.card` pattern for consistency.

Replace the entire body's 2nd column block. Use this template for **each** card (just substitute the relation, columns, and id):

```blade
{{-- Adhésions --}}
@php $adhesions = $member->memberships->sortByDesc('start_date')->values(); @endphp
<div class="card" id="adhesions" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">Adhésions ({{ $adhesions->count() }})</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <table class="table">
            <thead><tr><th>Type</th><th>Période</th><th>Montant</th><th>Statut</th></tr></thead>
            <tbody>
                @forelse($adhesions->take(5) as $membership)
                    <tr>
                        <td>{{ $membership->membershipType->name ?? 'Standard' }}</td>
                        <td>{{ $membership->start_date->format('d/m/Y') }} - {{ $membership->end_date->format('d/m/Y') }}</td>
                        <td>{{ number_format($membership->amount_paid, 2, ',', ' ') }} EUR</td>
                        <td>
                            @if($membership->end_date >= now())
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-warning">Expirée</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align: center; color: #9ca3af;">Aucune adhésion</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($adhesions->count() > 5)
            <details style="border-top: 1px solid #e5e7eb;">
                <summary style="padding: 0.75rem 1rem; cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $adhesions->count() }})</summary>
                <table class="table">
                    <tbody>
                        @foreach($adhesions->slice(5) as $membership)
                            <tr>
                                <td>{{ $membership->membershipType->name ?? 'Standard' }}</td>
                                <td>{{ $membership->start_date->format('d/m/Y') }} - {{ $membership->end_date->format('d/m/Y') }}</td>
                                <td>{{ number_format($membership->amount_paid, 2, ',', ' ') }} EUR</td>
                                <td>
                                    @if($membership->end_date >= now())
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-warning">Expirée</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </details>
        @endif
    </div>
</div>

{{-- Dons --}}
@php $dons = $member->donations->sortByDesc('donation_date')->values(); @endphp
<div class="card" id="dons" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Dons ({{ $dons->count() }})</h3></div>
    <div class="card-body" style="padding: 0;">
        <table class="table">
            <thead><tr><th>Date</th><th>Montant</th><th>Paiement</th><th>Reçu</th></tr></thead>
            <tbody>
                @forelse($dons->take(5) as $donation)
                    <tr>
                        <td>{{ $donation->donation_date->format('d/m/Y') }}</td>
                        <td><span class="badge badge-success">{{ number_format($donation->amount, 0, ',', ' ') }} EUR</span></td>
                        <td>{{ $donation->payment_method ?? '-' }}</td>
                        <td>
                            @if($donation->tax_receipt_sent)
                                <span class="badge badge-info">Envoyé</span>
                            @else
                                <span class="badge badge-warning">À envoyer</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align: center; color: #9ca3af;">Aucun don</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($dons->count() > 5)
            <details style="border-top: 1px solid #e5e7eb;">
                <summary style="padding: 0.75rem 1rem; cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $dons->count() }})</summary>
                <table class="table">
                    <tbody>
                        @foreach($dons->slice(5) as $donation)
                            <tr>
                                <td>{{ $donation->donation_date->format('d/m/Y') }}</td>
                                <td><span class="badge badge-success">{{ number_format($donation->amount, 0, ',', ' ') }} EUR</span></td>
                                <td>{{ $donation->payment_method ?? '-' }}</td>
                                <td>
                                    @if($donation->tax_receipt_sent)
                                        <span class="badge badge-info">Envoyé</span>
                                    @else
                                        <span class="badge badge-warning">À envoyer</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </details>
        @endif
    </div>
</div>

{{-- Achats --}}
@php $achats = $member->purchases->sortByDesc('purchase_date')->values(); @endphp
<div class="card" id="achats" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Achats ({{ $achats->count() }})</h3></div>
    <div class="card-body" style="padding: 0;">
        <table class="table">
            <thead><tr><th>Date</th><th>Produit</th><th>Montant</th><th>Source</th></tr></thead>
            <tbody>
                @forelse($achats->take(5) as $purchase)
                    <tr>
                        <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                        <td>
                            @if($purchase->product)
                                <a href="{{ route('admin.products.show', $purchase->product) }}">{{ $purchase->product->name }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ number_format($purchase->total_amount, 2, ',', ' ') }} EUR</td>
                        <td><span class="badge badge-{{ $purchase->source === 'import' ? 'warning' : 'info' }}">{{ $purchase->getSourceLabel() }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align: center; color: #9ca3af;">Aucun achat</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($achats->count() > 5)
            <details style="border-top: 1px solid #e5e7eb;">
                <summary style="padding: 0.75rem 1rem; cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $achats->count() }})</summary>
                <table class="table">
                    <tbody>
                        @foreach($achats->slice(5) as $purchase)
                            <tr>
                                <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                                <td>
                                    @if($purchase->product)
                                        <a href="{{ route('admin.products.show', $purchase->product) }}">{{ $purchase->product->name }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ number_format($purchase->total_amount, 2, ',', ' ') }} EUR</td>
                                <td><span class="badge badge-{{ $purchase->source === 'import' ? 'warning' : 'info' }}">{{ $purchase->getSourceLabel() }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </details>
        @endif
    </div>
</div>

{{-- Bulletins Lepis (converti au pattern .card) --}}
@php $recipients = $member->lepisBulletinRecipients; @endphp
<div class="card" id="bulletins" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Bulletins Lepis reçus ({{ $recipients->count() }})</h3></div>
    <div class="card-body" style="padding: 0;">
        @if($recipients->isEmpty())
            <div style="padding: 1rem; color: #6b7280;">Aucun envoi de bulletin pour ce contact.</div>
        @else
            <table class="table">
                <thead><tr><th>Bulletin</th><th>Format</th><th>Date d'envoi</th><th>Liste Brevo</th></tr></thead>
                <tbody>
                    @foreach($recipients->take(5) as $r)
                        <tr>
                            <td><a href="{{ route('admin.lepis.edit', $r->bulletin) }}" style="color: #2C5F2D;">{{ $r->bulletin?->title ?? '#' . $r->lepis_bulletin_id }}</a></td>
                            <td>{{ $r->format === 'digital' ? 'Numérique' : 'Papier' }}</td>
                            <td>{{ $r->included_at?->locale('fr')->isoFormat('LL') }}</td>
                            <td style="color: #6b7280; font-size: 0.875rem;">{{ $r->brevo_list_id ? '#' . $r->brevo_list_id : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($recipients->count() > 5)
                <details style="border-top: 1px solid #e5e7eb;">
                    <summary style="padding: 0.75rem 1rem; cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $recipients->count() }})</summary>
                    <table class="table">
                        <tbody>
                            @foreach($recipients->slice(5) as $r)
                                <tr>
                                    <td><a href="{{ route('admin.lepis.edit', $r->bulletin) }}" style="color: #2C5F2D;">{{ $r->bulletin?->title ?? '#' . $r->lepis_bulletin_id }}</a></td>
                                    <td>{{ $r->format === 'digital' ? 'Numérique' : 'Papier' }}</td>
                                    <td>{{ $r->included_at?->locale('fr')->isoFormat('LL') }}</td>
                                    <td style="color: #6b7280; font-size: 0.875rem;">{{ $r->brevo_list_id ? '#' . $r->brevo_list_id : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </details>
            @endif
        @endif
    </div>
</div>
```

This block replaces all 4 of the existing card definitions. The "Engagement OREINA" 5th card is added in the next task.

- [ ] **Step 4: Run the tests, expect pass**

`php artisan test --filter=MemberShowFicheTest`
Expected: 5 passing (the 4 from Tasks 3-4 + the new truncation test).

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/members/show.blade.php tests/Feature/Admin/MemberShowFicheTest.php
git commit -m "feat(members): cartes raccourcies à 5 items avec voir-tout via <details>"
```

---

## Task 6 — Carte "Engagement OREINA"

**Files:**
- Modify: `resources/views/admin/members/show.blade.php`
- Modify: `tests/Feature/Admin/MemberShowFicheTest.php` (ajout de 3 tests)

- [ ] **Step 1: Add the failing tests**

Append to `MemberShowFicheTest`:

```php
public function test_engagement_card_hidden_when_no_activity(): void
{
    $admin = $this->makeAdmin();
    $member = $this->makeMember();
    // No groups, no submissions, no suggestions
    $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

    $response->assertOk()
        ->assertDontSee('Engagement OREINA');
}

public function test_engagement_card_shows_groups_with_role(): void
{
    $admin = $this->makeAdmin();
    $member = $this->makeMember();
    $group = \App\Models\WorkGroup::create(['name' => 'Coléoptères', 'slug' => 'coleopteres']);
    $member->workGroups()->attach($group->id, ['role' => 'coordinator', 'joined_at' => now()->subYear()]);

    $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

    $response->assertOk()
        ->assertSee('Engagement OREINA')
        ->assertSee('Coléoptères')
        ->assertSee('coordinator');
}

public function test_engagement_card_shows_all_submissions_with_status_badges(): void
{
    $admin = $this->makeAdmin();
    $member = $this->makeMember();
    \App\Models\Submission::create([
        'author_id' => $member->user_id,
        'title' => 'Pieridae alpines',
        'status' => 'published',
        'published_at' => now()->subMonths(3),
    ]);
    \App\Models\Submission::create([
        'author_id' => $member->user_id,
        'title' => 'Note Saxifraga',
        'status' => 'under_peer_review',
    ]);

    $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}");

    $response->assertOk()
        ->assertSee('Engagement OREINA')
        ->assertSee('Pieridae alpines')
        ->assertSee('Note Saxifraga')
        ->assertSee('Publié')
        ->assertSee('En relecture');
}
```

- [ ] **Step 2: Run them, expect failure**

`php artisan test --filter=MemberShowFicheTest`
Expected: 3 of 8 fail (the new ones).

- [ ] **Step 3: Add the Engagement card after the 4 existing cards**

In `resources/views/admin/members/show.blade.php`, after the closing `</div>` of the Bulletins Lepis card, add this 5th card:

```blade
{{-- Engagement OREINA --}}
@php
    $submissions = $member->user?->submissions ?? collect();
    $suggestions = $member->lepisSuggestions;
    $groups = $member->workGroups;
@endphp
@if($groups->isNotEmpty() || $submissions->isNotEmpty() || $suggestions->isNotEmpty())
    <div class="card" id="engagement" style="margin-bottom: 1.5rem;">
        <div class="card-header"><h3 class="card-title">Engagement OREINA</h3></div>
        <div class="card-body">

            @if($groups->isNotEmpty())
                <div style="margin-bottom: 1.25rem;">
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">
                        Groupes de travail ({{ $groups->count() }})
                    </div>
                    @foreach($groups as $group)
                        <div style="margin-bottom: 0.5rem;">
                            · <strong>{{ $group->name }}</strong>
                            @if(($group->pivot->role ?? 'member') !== 'member')
                                — {{ $group->pivot->role }}
                            @endif
                            @if($group->pivot->joined_at)
                                <div style="color: #6b7280; font-size: 0.8125rem; margin-left: 0.875rem;">
                                    Membre depuis le {{ \Carbon\Carbon::parse($group->pivot->joined_at)->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if($submissions->isNotEmpty())
                <div style="margin-bottom: 1.25rem;">
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">
                        Chersotis ({{ $submissions->count() }} soumission{{ $submissions->count() > 1 ? 's' : '' }})
                    </div>
                    @foreach($submissions->take(5) as $sub)
                        <div style="margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                            <div style="flex: 1;">
                                · <a href="{{ route('admin.submissions.show', $sub) }}" style="color: #2C5F2D;">{{ $sub->title }}</a>
                                <span class="badge badge-info" style="margin-left: 0.5rem;">{{ $sub->status?->label() ?? $sub->status }}</span>
                            </div>
                            <div style="color: #6b7280; font-size: 0.8125rem;">
                                {{ $sub->published_at?->format('Y') ?? $sub->created_at->format('Y') }}
                            </div>
                        </div>
                    @endforeach
                    @if($submissions->count() > 5)
                        <details style="margin-top: 0.5rem;">
                            <summary style="cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $submissions->count() }})</summary>
                            @foreach($submissions->slice(5) as $sub)
                                <div style="margin: 0.5rem 0; display: flex; justify-content: space-between; align-items: center;">
                                    <div style="flex: 1;">
                                        · <a href="{{ route('admin.submissions.show', $sub) }}" style="color: #2C5F2D;">{{ $sub->title }}</a>
                                        <span class="badge badge-info" style="margin-left: 0.5rem;">{{ $sub->status?->label() ?? $sub->status }}</span>
                                    </div>
                                    <div style="color: #6b7280; font-size: 0.8125rem;">
                                        {{ $sub->published_at?->format('Y') ?? $sub->created_at->format('Y') }}
                                    </div>
                                </div>
                            @endforeach
                        </details>
                    @endif
                </div>
            @endif

            @if($suggestions->isNotEmpty())
                <div>
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">
                        Lepis — Suggestions ({{ $suggestions->count() }})
                    </div>
                    @foreach($suggestions->take(5) as $sug)
                        <div style="margin-bottom: 0.75rem;">
                            · <strong>« {{ $sug->title }} »</strong>
                            <span class="badge badge-{{ $sug->status === 'noted' ? 'success' : 'warning' }}" style="margin-left: 0.5rem;">
                                {{ $sug->status === 'noted' ? 'Notée' : 'En attente' }}
                            </span>
                            @if($sug->submitted_at)
                                <div style="color: #6b7280; font-size: 0.8125rem; margin-left: 0.875rem;">
                                    Soumise le {{ $sug->submitted_at->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                    @if($suggestions->count() > 5)
                        <details>
                            <summary style="cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $suggestions->count() }})</summary>
                            @foreach($suggestions->slice(5) as $sug)
                                <div style="margin: 0.5rem 0;">
                                    · <strong>« {{ $sug->title }} »</strong>
                                    <span class="badge badge-{{ $sug->status === 'noted' ? 'success' : 'warning' }}" style="margin-left: 0.5rem;">
                                        {{ $sug->status === 'noted' ? 'Notée' : 'En attente' }}
                                    </span>
                                    @if($sug->submitted_at)
                                        <div style="color: #6b7280; font-size: 0.8125rem; margin-left: 0.875rem;">
                                            Soumise le {{ $sug->submitted_at->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </details>
                    @endif
                </div>
            @endif

        </div>
    </div>
@endif
```

- [ ] **Step 4: Run the tests, expect pass**

`php artisan test --filter=MemberShowFicheTest`
Expected: 8 passing.

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/members/show.blade.php tests/Feature/Admin/MemberShowFicheTest.php
git commit -m "feat(members): carte Engagement OREINA (groupes, publications, suggestions)"
```

---

## Task 7 — Full test run + smoke + cleanup

**Files:** none (verification only)

- [ ] **Step 1: Run the full test suite**

`php artisan test`
Expected: all tests green. Baseline avant cette feature ≈ 449 tests (441 + ~8 nouveaux). Si une régression apparaît, fixer avant de continuer.

- [ ] **Step 2: Manual smoke test**

Démarrer le serveur :
```bash
php artisan serve
```

Visiter `http://localhost:8000/extranet/members/1` (l'admin par défaut admin@oreina.org). Vérifier visuellement :
- KPI bar visible avec les chiffres-clés (probablement seul "Adhésions" si l'admin n'a aucune autre activité)
- Sidebar info correcte, statut Lepis visible si membership active
- Cartes raccourcies si > 5 items, sinon table complète comme avant
- Carte "Engagement OREINA" masquée si vide, visible avec sections si non-vide
- Click sur les KPIs scrolle vers la bonne section
- Click "Voir tout" déplie la liste

Visiter aussi `/extranet/members/271` (membre régulier vu dans la session précédente) pour valider sur un cas concret.

- [ ] **Step 3: View cache clear (au cas où)**

```bash
php artisan view:clear
```

- [ ] **Step 4: No commit needed if smoke OK**

Si le smoke révèle un problème mineur (ex. couleur de badge), corriger et committer en supplément. Sinon, plan terminé.

---

## Self-review

**Spec coverage check:**

| Spec section | Tasks |
|---|---|
| Layout général (KPI bar + grille 2 cols) | Task 3 |
| KPI bar : 7 KPIs avec masquage si 0, format dons en € | Task 3 |
| KPI mapping icônes Lucide + variants | Task 3 |
| KPIs cliquables avec ancres | Task 3 |
| Sidebar : Format Lepis | Task 4 |
| Sidebar : Groupes (max 5 + truncation) | Task 4 |
| Sidebar : Engagement (Auteur Chersotis, Contributeur Lepis) | Task 4 |
| Cartes raccourcies (Adhésions, Dons, Achats) via `<details>` | Task 5 |
| Bulletin Lepis converti au pattern `.card` | Task 5 |
| Carte Engagement OREINA (3 sous-sections + masquage si vide) | Task 6 |
| Submissions toutes affichées, badge statut | Task 6 |
| Eager-load étendu | Task 2 |
| Relations Member.lepisSuggestions et Member.submissions | Task 1 |
| Tests : 8 tests feature | Tasks 3, 4, 5, 6 (8 tests au total) |

Toutes les exigences de spec sont couvertes.

**Type consistency check:**
- `$member->lepisSuggestions` (HasMany) — utilisé task 6
- `$member->user->submissions` ET `$member->submissions` via HasManyThrough — Task 1 définit les deux, Task 3+ utilisent `$member->user->submissions` (compat avec eager-load `user.submissions`). NB : la HasManyThrough sur Member est définie pour usage futur, pas utilisée dans ce plan. Cohérent.
- `$lepisFormat` calculé en `@php` block au début de la page, réutilisé sidebar (Task 3 l'établit, Task 4 l'utilise).
- Compteurs `$adhesionsCount`, `$donsTotal`, etc. établis Task 3, utilisés Tasks 3-6.

**Placeholder scan:** none. All steps contain actual code.

**Scope check:** Single page, single blade file refactor + 2 model relations + 1 controller change. Approprié pour 1 plan.
