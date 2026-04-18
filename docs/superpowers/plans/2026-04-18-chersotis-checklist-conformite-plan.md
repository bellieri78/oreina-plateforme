# Chersotis — Checklist conformité éditeur avant maquettage — Plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Offrir à l'éditeur Chersotis une checklist de 9 items qu'il coche au fil de sa relecture avant de passer l'article en maquettage, avec sauvegarde Ajax instantanée et trace figée une fois en production.

**Architecture:** Colonne JSON `conformity_checklist` sur `submissions` + enum `ConformityChecklistItem` fermé (9 cases) + route PATCH par toggle + card Blade dans la colonne droite de la fiche admin. Non-bloquant pour la transition vers maquettage.

**Tech Stack:** Laravel 12, Blade, Alpine, PostgreSQL jsonb, fetch API pour l'Ajax.

**Spec source:** `docs/superpowers/specs/2026-04-18-chersotis-checklist-conformite-design.md`

---

## File Structure

**New files**
- `database/migrations/2026_04_18_160000_add_conformity_checklist_to_submissions.php`
- `app/Enums/ConformityChecklistItem.php`
- `tests/Unit/Enums/ConformityChecklistItemTest.php`
- `tests/Unit/Models/ConformityChecklistTest.php`
- `tests/Feature/Admin/ConformityChecklistTest.php`

**Files to modify**
- `app/Models/Submission.php` — fillable, casts, helpers `conformityChecked()` + `conformityProgress()`
- `app/Policies/SubmissionPolicy.php` — méthode `updateConformity()`
- `app/Http/Controllers/Admin/SubmissionController.php` — méthode `updateConformity()`
- `routes/admin.php` — route PATCH `submissions.conformity.update`
- `resources/views/admin/submissions/show.blade.php` — card checklist entre editorial sidebar et carte Informations + script `toggleConformityItem`

---

## Task 1: Migration `add_conformity_checklist_to_submissions`

**Files:**
- Create: `database/migrations/2026_04_18_160000_add_conformity_checklist_to_submissions.php`

- [ ] **Step 1: Créer le fichier de migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->jsonb('conformity_checklist')->nullable()->after('editor_notes');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('conformity_checklist');
        });
    }
};
```

- [ ] **Step 2: Exécuter la migration**

Run: `php artisan migrate`
Expected: « Migrated: 2026_04_18_160000_add_conformity_checklist_to_submissions »

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_04_18_160000_add_conformity_checklist_to_submissions.php
git commit -m "feat(db): add conformity_checklist jsonb column on submissions"
```

IMPORTANT : DO NOT add `Co-Authored-By: Claude` in commit messages.

---

## Task 2: Enum `ConformityChecklistItem`

**Files:**
- Create: `app/Enums/ConformityChecklistItem.php`
- Create: `tests/Unit/Enums/ConformityChecklistItemTest.php`

- [ ] **Step 1: Écrire les tests**

Créer `tests/Unit/Enums/ConformityChecklistItemTest.php` :

```php
<?php

namespace Tests\Unit\Enums;

use App\Enums\ConformityChecklistItem;
use PHPUnit\Framework\TestCase;

class ConformityChecklistItemTest extends TestCase
{
    public function test_enum_has_exactly_nine_cases(): void
    {
        $this->assertCount(9, ConformityChecklistItem::cases());
    }

    public function test_all_cases_have_non_empty_labels(): void
    {
        foreach (ConformityChecklistItem::cases() as $case) {
            $this->assertNotEmpty($case->label(), "Case {$case->value} has empty label");
        }
    }

    public function test_all_cases_have_non_empty_descriptions(): void
    {
        foreach (ConformityChecklistItem::cases() as $case) {
            $this->assertNotEmpty($case->description(), "Case {$case->value} has empty description");
        }
    }

    public function test_values_are_snake_case_strings(): void
    {
        foreach (ConformityChecklistItem::cases() as $case) {
            $this->assertMatchesRegularExpression('/^[a-z_]+$/', $case->value);
        }
    }
}
```

- [ ] **Step 2: Lancer les tests — doivent échouer**

Run: `php artisan test --filter=ConformityChecklistItemTest`
Expected: FAIL (classe inexistante).

- [ ] **Step 3: Créer l'enum**

Créer `app/Enums/ConformityChecklistItem.php` :

```php
<?php

namespace App\Enums;

enum ConformityChecklistItem: string
{
    case BiblioFormat         = 'biblio_format';
    case AuthorAffiliations   = 'author_affiliations';
    case Correspondence       = 'correspondence';
    case FiguresNumbered      = 'figures_numbered';
    case Acknowledgements     = 'acknowledgements';
    case AbstractsKeywords    = 'abstracts_keywords';
    case ImageRights          = 'image_rights';
    case ConflictsOfInterest  = 'conflicts_of_interest';
    case SupplementaryData    = 'supplementary_data';

    public function label(): string
    {
        return match ($this) {
            self::BiblioFormat        => 'Format bibliographique',
            self::AuthorAffiliations  => 'Affiliations complètes',
            self::Correspondence      => 'Coordonnées de correspondance',
            self::FiguresNumbered     => 'Figures numérotées et légendées',
            self::Acknowledgements    => 'Remerciements présents',
            self::AbstractsKeywords   => 'Résumé FR + EN + mots-clés',
            self::ImageRights         => 'Droits images / copyright',
            self::ConflictsOfInterest => 'Conflits d\'intérêt déclarés',
            self::SupplementaryData   => 'Données supplémentaires identifiées',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::BiblioFormat        => 'Harvard, ordre alphabétique, cohérence citations/biblio',
            self::AuthorAffiliations  => 'Institution + adresse postale pour chaque auteur',
            self::Correspondence      => 'Email de l\'auteur référent renseigné',
            self::FiguresNumbered     => 'Figure 1, 2a, 2b… avec légendes sous l\'image',
            self::Acknowledgements    => 'Financements, aide terrain, permissions de capture',
            self::AbstractsKeywords   => 'Les 3 champs remplis, ≥ 100 caractères',
            self::ImageRights         => 'Images originales, CC, ou autorisation écrite',
            self::ConflictsOfInterest => 'Déclaration présente (même si « aucun »)',
            self::SupplementaryData   => 'Fichiers supplémentaires bien séparés du corps',
        };
    }
}
```

- [ ] **Step 4: Lancer les tests — doivent passer**

Run: `php artisan test --filter=ConformityChecklistItemTest`
Expected: PASS (4 tests)

- [ ] **Step 5: Commit**

```bash
git add app/Enums/ConformityChecklistItem.php tests/Unit/Enums/ConformityChecklistItemTest.php
git commit -m "feat(submission): ConformityChecklistItem enum (9 items)"
```

---

## Task 3: Submission model — fillable, casts, helpers + tests

**Files:**
- Modify: `app/Models/Submission.php`
- Create: `tests/Unit/Models/ConformityChecklistTest.php`

- [ ] **Step 1: Écrire les tests**

Créer `tests/Unit/Models/ConformityChecklistTest.php` :

```php
<?php

namespace Tests\Unit\Models;

use App\Enums\ConformityChecklistItem;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConformityChecklistTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(?array $checklist = null): Submission
    {
        $author = User::factory()->create();
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => 'submitted',
            'conformity_checklist' => $checklist,
        ]);
    }

    public function test_conformity_checked_returns_false_when_checklist_is_null(): void
    {
        $sub = $this->makeSubmission(null);
        $this->assertFalse($sub->conformityChecked(ConformityChecklistItem::BiblioFormat));
    }

    public function test_conformity_checked_returns_false_when_item_not_in_checklist(): void
    {
        $sub = $this->makeSubmission(['biblio_format']);
        $this->assertFalse($sub->conformityChecked(ConformityChecklistItem::FiguresNumbered));
    }

    public function test_conformity_checked_returns_true_when_item_in_checklist(): void
    {
        $sub = $this->makeSubmission(['biblio_format', 'figures_numbered']);
        $this->assertTrue($sub->conformityChecked(ConformityChecklistItem::FiguresNumbered));
    }

    public function test_conformity_progress_returns_zero_over_nine_by_default(): void
    {
        $sub = $this->makeSubmission(null);
        $progress = $sub->conformityProgress();
        $this->assertEquals(0, $progress['checked']);
        $this->assertEquals(9, $progress['total']);
    }

    public function test_conformity_progress_reflects_checklist_count(): void
    {
        $sub = $this->makeSubmission(['biblio_format', 'author_affiliations', 'figures_numbered']);
        $progress = $sub->conformityProgress();
        $this->assertEquals(3, $progress['checked']);
        $this->assertEquals(9, $progress['total']);
    }

    public function test_checklist_persists_as_array_cast(): void
    {
        $sub = $this->makeSubmission(['biblio_format']);
        $fresh = Submission::find($sub->id);
        $this->assertIsArray($fresh->conformity_checklist);
        $this->assertEquals(['biblio_format'], $fresh->conformity_checklist);
    }
}
```

- [ ] **Step 2: Lancer les tests — doivent échouer**

Run: `php artisan test --filter="Tests\\Unit\\Models\\ConformityChecklistTest"`
Expected: FAIL — `conformity_checklist` absent du fillable, helpers inexistants.

- [ ] **Step 3: Modifier le fillable**

Dans `app/Models/Submission.php`, ajouter `'conformity_checklist'` dans `$fillable` juste après `'editor_notes'` :

Le bloc actuel (lignes ~35-36) contient `'editor_notes',`. Ajouter juste après :

```php
        'editor_notes',
        'conformity_checklist',
        'decision',
```

(insérer entre `editor_notes` et `decision` — adapte si l'ordre réel diffère).

- [ ] **Step 4: Ajouter le cast array**

Dans `$casts`, ajouter :

```php
        'conformity_checklist' => 'array',
```

(N'importe où dans le tableau casts, préférer regrouper avec les autres casts `array` déjà existants comme `co_authors`, `keywords`, `supplementary_files`.)

- [ ] **Step 5: Ajouter les helpers**

À l'endroit logique dans la classe (par exemple après les autres helpers existants type `wasSubmittedOnBehalf` / `publicStatus`), ajouter :

```php
    public function conformityChecked(\App\Enums\ConformityChecklistItem $item): bool
    {
        return in_array($item->value, $this->conformity_checklist ?? [], true);
    }

    public function conformityProgress(): array
    {
        return [
            'checked' => count($this->conformity_checklist ?? []),
            'total'   => count(\App\Enums\ConformityChecklistItem::cases()),
        ];
    }
```

- [ ] **Step 6: Lancer les tests — doivent passer**

Run: `php artisan test --filter="Tests\\Unit\\Models\\ConformityChecklistTest"`
Expected: PASS (6 tests)

- [ ] **Step 7: Commit**

```bash
git add app/Models/Submission.php tests/Unit/Models/ConformityChecklistTest.php
git commit -m "feat(submission): fillable + casts + conformity helpers"
```

---

## Task 4: Policy `updateConformity`

**Files:**
- Modify: `app/Policies/SubmissionPolicy.php`

- [ ] **Step 1: Ajouter la méthode**

Dans `app/Policies/SubmissionPolicy.php`, ajouter la méthode `updateConformity` après `manageCapabilities` (ou à un endroit logique dans la classe) :

```php
    public function updateConformity(User $user, Submission $submission): bool
    {
        return $user->isAdmin()
            || $user->hasCapability(EditorialCapability::CHIEF_EDITOR)
            || $submission->editor_id === $user->id;
    }
```

Les imports `User`, `Submission`, `EditorialCapability` sont déjà présents — pas de nouveaux `use` à ajouter.

- [ ] **Step 2: Vérifier non-régression**

Run: `php artisan test`
Expected: tous les tests passent (293+ précédents).

- [ ] **Step 3: Commit**

```bash
git add app/Policies/SubmissionPolicy.php
git commit -m "feat(policy): updateConformity (admin, chief_editor, assigned editor)"
```

---

## Task 5: Controller + route + tests feature

**Files:**
- Modify: `app/Http/Controllers/Admin/SubmissionController.php`
- Modify: `routes/admin.php`
- Create: `tests/Feature/Admin/ConformityChecklistTest.php`

- [ ] **Step 1: Écrire les tests feature**

Créer `tests/Feature/Admin/ConformityChecklistTest.php` :

```php
<?php

namespace Tests\Feature\Admin;

use App\Enums\ConformityChecklistItem;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConformityChecklistTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(?User $editor = null): Submission
    {
        $author = User::factory()->create();
        return Submission::create([
            'author_id' => $author->id,
            'editor_id' => $editor?->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => 'under_peer_review',
        ]);
    }

    private function makeEditor(string $cap = EditorialCapability::EDITOR): User
    {
        $u = User::factory()->create();
        $u->grantCapability($cap);
        return $u;
    }

    public function test_assigned_editor_can_check_item(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);

        $response = $this->actingAs($editor)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::BiblioFormat->value,
                'checked' => true,
            ]);

        $response->assertOk();
        $response->assertJson(['checked' => 1, 'total' => 9]);
        $this->assertEquals(['biblio_format'], $sub->fresh()->conformity_checklist);
    }

    public function test_assigned_editor_can_uncheck_item(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);
        $sub->update(['conformity_checklist' => ['biblio_format', 'figures_numbered']]);

        $response = $this->actingAs($editor)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::BiblioFormat->value,
                'checked' => false,
            ]);

        $response->assertOk();
        $response->assertJson(['checked' => 1, 'total' => 9]);
        $this->assertEquals(['figures_numbered'], array_values($sub->fresh()->conformity_checklist));
    }

    public function test_chief_editor_can_check_without_being_assigned(): void
    {
        $chief = $this->makeEditor(EditorialCapability::CHIEF_EDITOR);
        $sub = $this->makeSubmission(); // pas d'éditeur assigné

        $response = $this->actingAs($chief)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::AuthorAffiliations->value,
                'checked' => true,
            ]);

        $response->assertOk();
        $this->assertTrue(in_array('author_affiliations', $sub->fresh()->conformity_checklist));
    }

    public function test_admin_can_check(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $sub = $this->makeSubmission();

        $response = $this->actingAs($admin)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::FiguresNumbered->value,
                'checked' => true,
            ]);

        $response->assertOk();
    }

    public function test_non_assigned_editor_gets_403(): void
    {
        $assigned = $this->makeEditor();
        $stranger = $this->makeEditor();
        $sub = $this->makeSubmission($assigned);

        $response = $this->actingAs($stranger)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::BiblioFormat->value,
                'checked' => true,
            ]);

        $response->assertForbidden();
    }

    public function test_reviewer_gets_403(): void
    {
        $reviewer = $this->makeEditor(EditorialCapability::REVIEWER);
        $sub = $this->makeSubmission();

        $response = $this->actingAs($reviewer)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => ConformityChecklistItem::BiblioFormat->value,
                'checked' => true,
            ]);

        $response->assertForbidden();
    }

    public function test_toggle_is_idempotent(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);

        // Deux coches consécutives sur le même item
        $this->actingAs($editor)->patch(route('admin.submissions.conformity.update', $sub), [
            'item' => 'biblio_format',
            'checked' => true,
        ]);
        $this->actingAs($editor)->patch(route('admin.submissions.conformity.update', $sub), [
            'item' => 'biblio_format',
            'checked' => true,
        ]);

        $this->assertEquals(['biblio_format'], $sub->fresh()->conformity_checklist);
    }

    public function test_invalid_item_returns_422(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);

        $response = $this->actingAs($editor)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => 'not_a_real_item',
                'checked' => true,
            ]);

        $response->assertStatus(422);
    }

    public function test_missing_checked_returns_422(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);

        $response = $this->actingAs($editor)
            ->patch(route('admin.submissions.conformity.update', $sub), [
                'item' => 'biblio_format',
            ]);

        $response->assertStatus(422);
    }

    public function test_checklist_persists_across_status_change(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission($editor);
        $sub->update(['conformity_checklist' => ['biblio_format', 'figures_numbered']]);

        $sub->update(['status' => 'revision_requested']);
        $sub->update(['status' => 'under_initial_review']);

        $this->assertEquals(['biblio_format', 'figures_numbered'], $sub->fresh()->conformity_checklist);
    }
}
```

- [ ] **Step 2: Lancer les tests — doivent échouer**

Run: `php artisan test --filter=ConformityChecklistTest`
Expected: FAIL (route inexistante).

- [ ] **Step 3: Ajouter la route**

Dans `routes/admin.php`, dans le groupe admin (après les autres routes `submissions/...`, avant `Route::resource('submissions', ...)` pour éviter le conflit avec le paramètre dynamique) :

```php
    Route::patch('submissions/{submission}/conformity', [\App\Http\Controllers\Admin\SubmissionController::class, 'updateConformity'])
        ->name('submissions.conformity.update');
```

**Important** : placer cette ligne AVANT `Route::resource('submissions', SubmissionController::class);`, sinon Laravel routera vers l'action resource `show`.

- [ ] **Step 4: Ajouter la méthode du controller**

Dans `app/Http/Controllers/Admin/SubmissionController.php`, ajouter à un endroit logique (par ex. après la méthode `update` ou en fin de controller) :

```php
    public function updateConformity(\Illuminate\Http\Request $request, \App\Models\Submission $submission)
    {
        $this->authorize('updateConformity', $submission);

        $validated = $request->validate([
            'item' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\ConformityChecklistItem::class)],
            'checked' => 'required|boolean',
        ]);

        $current = $submission->conformity_checklist ?? [];
        if ($validated['checked']) {
            $current = array_values(array_unique(array_merge($current, [$validated['item']])));
        } else {
            $current = array_values(array_diff($current, [$validated['item']]));
        }

        $submission->update(['conformity_checklist' => $current]);

        return response()->json($submission->fresh()->conformityProgress());
    }
```

- [ ] **Step 5: Lancer les tests — doivent passer**

Run: `php artisan test --filter=ConformityChecklistTest`
Expected: PASS (10 tests)

Run: `php artisan test`
Expected: toute la suite passe.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/SubmissionController.php routes/admin.php tests/Feature/Admin/ConformityChecklistTest.php
git commit -m "feat(admin): PATCH submissions/{id}/conformity + E2E tests"
```

---

## Task 6: Card Blade + toggle JS

**Files:**
- Modify: `resources/views/admin/submissions/show.blade.php`

La card vient s'insérer entre l'include `_editorial_sidebar` (ligne ~272-277) et la card « Informations » (ligne ~279).

- [ ] **Step 1: Insérer la card checklist dans la colonne droite**

Dans `resources/views/admin/submissions/show.blade.php`, localiser le bloc (autour de la ligne 277) :

```blade
            @include('admin.submissions.partials._editorial_sidebar', [
                'submission' => $submission,
                'eligibleReviewers' => $eligibleReviewers ?? collect(),
                'eligibleEditors' => $eligibleEditors ?? collect(),
                'eligibleLayoutEditors' => $eligibleLayoutEditors ?? collect(),
            ])

            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Informations</h3>
                </div>
```

Insérer **entre** le `@include(..._editorial_sidebar)` et la card Informations :

```blade
            {{-- Checklist conformité éditeur (avant maquettage) --}}
            @php
                $conformityStages = ['submitted', 'under_initial_review', 'under_peer_review', 'revision_requested', 'revision_after_review', 'accepted'];
                $conformityVisible = true; // toujours visible (read-only après production)
                $conformityEditable = in_array($submissionStatusValue, $conformityStages, true)
                    && app(\App\Policies\SubmissionPolicy::class)->updateConformity(auth()->user(), $submission);
                $conformityProgress = $submission->conformityProgress();
            @endphp
            <div class="card" style="margin-bottom: 1.5rem; border-left: 4px solid #d97706;"
                 x-data="{ progress: @js($conformityProgress), editable: @js($conformityEditable) }">
                <div class="card-header">
                    <h3 class="card-title" style="display:flex; align-items:center; justify-content:space-between;">
                        <span style="display:flex; align-items:center; gap:0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="18" height="18" style="color:#d97706;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Checklist conformité
                        </span>
                        <span style="font-size:0.8rem; font-weight:600; color:#d97706;"
                              x-text="progress.checked + '/' + progress.total"></span>
                    </h3>
                </div>
                <div class="card-body">
                    <p style="font-size:0.8rem; color:#6b7280; margin:0 0 0.75rem 0;">
                        À vérifier avant de passer l'article en maquettage.
                        <strong x-show="!editable" style="color:#d97706;">Figée (stade post-éditorial).</strong>
                    </p>
                    @foreach(\App\Enums\ConformityChecklistItem::cases() as $item)
                        <label style="display:flex; gap:0.5rem; align-items:flex-start; padding:0.5rem 0; border-bottom:1px dashed #f3f4f6;"
                               x-bind:style="editable ? 'cursor:pointer' : 'cursor:not-allowed; opacity:0.8'">
                            <input type="checkbox"
                                   value="{{ $item->value }}"
                                   {{ $submission->conformityChecked($item) ? 'checked' : '' }}
                                   {{ $conformityEditable ? '' : 'disabled' }}
                                   x-on:change="toggleConformityItem($event, '{{ $item->value }}')"
                                   style="margin-top:3px; accent-color:#d97706; flex-shrink:0;">
                            <span style="font-size:0.85rem; line-height:1.3;">
                                <strong>{{ $item->label() }}</strong><br>
                                <em style="color:#6b7280; font-size:0.75rem;">{{ $item->description() }}</em>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Informations</h3>
                </div>
```

La card Informations existante reste en place, on l'a juste déplacée d'une position.

- [ ] **Step 2: Ajouter le script toggleConformityItem**

Dans le même fichier `show.blade.php`, tout en bas de la section `@section('content')` (avant `@endsection`), ajouter :

```blade
<script>
window.toggleConformityItem = async function(event, item) {
    const checkbox = event.target;
    const url = '{{ route("admin.submissions.conformity.update", $submission) }}';
    checkbox.disabled = true;
    try {
        const res = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ item, checked: checkbox.checked }),
        });
        if (!res.ok) throw new Error('HTTP '+res.status);
        const data = await res.json();
        const card = checkbox.closest('[x-data]');
        if (card && window.Alpine) {
            Alpine.$data(card).progress = data;
        }
    } catch (e) {
        checkbox.checked = !checkbox.checked;
        alert('Erreur lors de la sauvegarde — réessayez.');
    } finally {
        checkbox.disabled = false;
    }
};
</script>
```

Le meta csrf-token est déjà présent dans `resources/views/layouts/admin.blade.php` ligne 6 — rien à ajouter.

- [ ] **Step 3: Smoke test rapide**

Run: `php artisan serve` puis se connecter en admin, aller sur `/extranet/submissions/{id}` (id d'une soumission en `under_peer_review` par exemple). Vérifier :
- La card « Checklist conformité » apparaît en colonne droite entre l'équipe éditoriale et les infos
- Le compteur affiche « 0/9 »
- Cocher un item → la checkbox reste cochée, le compteur passe à « 1/9 » sans recharger la page
- Décocher → retour à « 0/9 »
- En base : `SELECT conformity_checklist FROM submissions WHERE id = {id};` renvoie le tableau JSON mis à jour

Si erreur JavaScript : ouvrir la console navigateur. Suspects : CSRF manquant, route mal routée (faire `php artisan route:list | grep conformity`), Alpine non chargé.

- [ ] **Step 4: Commit**

```bash
git add resources/views/admin/submissions/show.blade.php
git commit -m "feat(admin): conformity checklist card on submission show page + Ajax toggle"
```

---

## Task 7: Final review + smoke + memory

- [ ] **Step 1: Suite de tests complète**

Run: `php artisan test`
Expected: 293 + 6 (unit model) + 10 (feature) + 4 (enum) = ~313 tests passent.

- [ ] **Step 2: Smoke test manuel bout-en-bout**

1. Connexion en admin
2. Aller sur une soumission en stade éditorial (`under_peer_review` typiquement)
3. Card « Checklist conformité » présente, 0/9
4. Cocher 3 items → compteur 3/9, chaque coche → pas de reload, toggle instantané
5. Aller sur une soumission en `in_production` → card présente mais grisée/read-only, mention « Figée (stade post-éditorial) »
6. Se déconnecter, re-connecter en tant que reviewer → card visible mais checkboxes désactivées (ou cliquer envoie 403)
7. Se re-connecter en admin → cocher/décocher sur une soumission en `under_peer_review` fonctionne. Rafraîchir la page → l'état persiste.

- [ ] **Step 3: Mettre à jour la mémoire projet**

Créer `C:\Users\ddemerges\.claude\projects\C--xampp-htdocs-oreina-plateforme\memory\project_chersotis_p1_checklist.md` avec un résumé de la livraison (9 items, card permanente, Ajax toggle, permissions, non-bloquant). Ajouter une ligne dans `MEMORY.md`.

- [ ] **Step 4: Commit final si ajustements**

```bash
git status
# si rien à commit → rien à faire
# sinon :
git add .
git commit -m "chore(chersotis): fixes after smoke test on conformity checklist"
```

---

## Critères de succès (rappel du spec §9)

- [ ] L'éditeur voit la card checklist dans la colonne droite de la fiche soumission
- [ ] Il peut cocher/décocher n'importe quel item, la sauvegarde est instantanée
- [ ] Le compteur « X/9 » se met à jour sans recharger la page
- [ ] Quand la soumission passe en maquettage, la card devient read-only avec l'état gelé
- [ ] Les reviewers et l'auteur ne peuvent pas modifier la checklist (403)
- [ ] Aucune régression sur le reste du workflow éditorial
- [ ] Suite de tests ≥ 90% de couverture sur le nouveau code
