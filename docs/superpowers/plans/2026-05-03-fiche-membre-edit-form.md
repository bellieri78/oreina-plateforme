# Formulaire d'édition contact admin — Plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refondre le formulaire d'édition `/extranet/members/{id}/edit` (et `create`) en 5 cartes thématiques (Identité / Contact / Adresse / Préférences / Statut & RGPD), couvrir tous les champs métier (civilité, mobile, profession, etc.), et consolider la redondance `phone` en supprimant la colonne.

**Architecture:** 1 migration drop column `members.phone` (backfill mobile inclus). Refonte de `resources/views/admin/members/_form.blade.php` en 5 cartes via les classes `.card` existantes. Mise à jour de `MemberController::store/update` (validation enrichie). Sidebar récap ajoutée dans `edit.blade.php`. Corrections collatérales des références à `phone` dans webhook HelloAsso, espace membre profil, seeder, import legacy.

**Tech Stack:** Laravel 12, Blade, PostgreSQL (compat 9.6 prod), classes `.card`/`.form-group`/`.form-input` existantes.

**Spec source:** `docs/superpowers/specs/2026-05-03-fiche-membre-edit-form-design.md`

**Note de déviation positive vs spec :** Le spec mentionne 3 valeurs de civilité (`M.`/`Mme`/`Autre`). Le modèle `Member` a déjà la constante `Member::CIVILITES = ['M.', 'Mme', 'Dr', 'Pr']`. On utilise cette constante existante — `Autre` est remplacé par les valeurs déjà permises (Dr/Pr) qui couvrent les cas réels.

---

## File map

**Created:**
- `database/migrations/2026_05_03_120000_drop_phone_from_members.php` — migration backfill + drop.
- `tests/Feature/Admin/MemberEditFormTest.php` — 9 tests feature.

**Modified:**
- `app/Models/Member.php` — retirer `'phone'` du `$fillable` (ligne 39).
- `app/Http/Controllers/Admin/MemberController.php` — validation enrichie sur `store` + `update`, presence-based booléens.
- `resources/views/admin/members/_form.blade.php` — refonte complète en 5 cartes.
- `resources/views/admin/members/edit.blade.php` — grille 1fr 2fr, sidebar récap à gauche, form à droite.
- `resources/views/admin/members/create.blade.php` — pas de sidebar, form en single column max-width 800px.
- `resources/views/admin/members/show.blade.php` — sidebar info : remplacer `$member->phone` par mobile/téléphone fixe.
- `resources/views/member/profile.blade.php` — input `phone` renommé `mobile`.
- `app/Http/Controllers/Member/ProfileController.php` — validation `phone` → `mobile`.
- `app/Http/Controllers/Api/WebhookController.php` — ligne 166 : `'phone'` → `'mobile'` dans `Member::create`.
- `app/Services/LegacyMembershipImportService.php` — ligne 246 : `'phone'` → `'mobile'` dans le mapping import.
- `database/seeders/MemberSeeder.php` — 4 occurrences `'phone'` → `'mobile'` (lignes 19, 33, 47, 61).

---

## Task 1 — Migration drop phone + cleanup Member fillable

**Files:**
- Create: `database/migrations/2026_05_03_120000_drop_phone_from_members.php`
- Modify: `app/Models/Member.php` (ligne 39)
- Test: ajout d'un test dans Task 6

- [ ] **Step 1: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Backfill: copier les rares phone-only vers mobile (4 cas en prod selon vérification)
        DB::statement("UPDATE members SET mobile = phone WHERE phone IS NOT NULL AND mobile IS NULL");

        // Drop column (PG 9.6 compat — DB::statement brut)
        DB::statement("ALTER TABLE members DROP COLUMN phone");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE members ADD COLUMN phone VARCHAR(255) NULL");
        // Note: down() ne re-renseigne pas les valeurs (perte irréversible côté rollback).
        // Acceptable car mobile contient toute l'info utile.
    }
};
```

- [ ] **Step 2: Run the migration**

`php artisan migrate`
Expected: `2026_05_03_120000_drop_phone_from_members ... DONE`.

- [ ] **Step 3: Verify in DB**

`php artisan tinker --execute="echo \Illuminate\Support\Facades\Schema::hasColumn('members', 'phone') ? 'PRESENT' : 'DROPPED';"`
Expected: `DROPPED`.

`php artisan tinker --execute="echo \App\Models\Member::whereNotNull('mobile')->count();"`
Expected: ≥ 283 (la valeur initiale + les 4 backfillés depuis phone-only).

- [ ] **Step 4: Remove `'phone'` from Member fillable**

In `app/Models/Member.php`, line 39, remove the line `'phone',` from the `$fillable` array. The block becomes (extract):

```php
'first_name',
'last_name',
'email',
'telephone_fixe',
'mobile',
'address',
```

(Where the previous version was `'email',\n'phone',\n'telephone_fixe',`.)

- [ ] **Step 5: Smoke test that the model still works**

`php artisan test --filter=MemberRelationsTest`
Expected: 3 passing (no regression).

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_05_03_120000_drop_phone_from_members.php app/Models/Member.php
git commit -m "feat(members): drop colonne phone (backfill mobile) et nettoyage fillable"
```

---

## Task 2 — Corrections collatérales des références à `phone`

**Files:**
- Modify: `app/Http/Controllers/Api/WebhookController.php` (line 166)
- Modify: `app/Services/LegacyMembershipImportService.php` (line 246)
- Modify: `database/seeders/MemberSeeder.php` (lines 19, 33, 47, 61)
- Modify: `resources/views/member/profile.blade.php` (line 75)
- Modify: `app/Http/Controllers/Member/ProfileController.php` (line 36)
- Modify: `resources/views/admin/members/show.blade.php` (sidebar info ligne Téléphone)

- [ ] **Step 1: Update WebhookController processMembership**

In `app/Http/Controllers/Api/WebhookController.php` line 166, change:

```php
'phone' => $payer['phone'] ?? null,
```

to:

```php
'mobile' => $payer['phone'] ?? null,
```

- [ ] **Step 2: Update LegacyMembershipImportService**

In `app/Services/LegacyMembershipImportService.php` line 246, change:

```php
'phone' => $this->cleanPhone($row['Tél. portables'] ?? $row['Tél. fixes'] ?? ''),
```

to:

```php
'mobile' => $this->cleanPhone($row['Tél. portables'] ?? ''),
'telephone_fixe' => $this->cleanPhone($row['Tél. fixes'] ?? ''),
```

(On profite du split pour mapper séparément mobile et fixe selon la colonne CSV — plus correct que de tout pousser dans un seul champ.)

- [ ] **Step 3: Update MemberSeeder**

In `database/seeders/MemberSeeder.php`, replace ALL 4 occurrences of `'phone' => '...'` with `'mobile' => '...'`. Lines 19, 33, 47, 61. Use a global find-replace within the file (not in the project, just this file).

After:
```php
'mobile' => '06 12 34 56 78',
// ...
'mobile' => '06 98 76 54 32',
// ...
'mobile' => '07 11 22 33 44',
// ...
'mobile' => '06 55 44 33 22',
```

- [ ] **Step 4: Update Member ProfileController validation**

In `app/Http/Controllers/Member/ProfileController.php` line 36, change:

```php
'phone' => 'nullable|string|max:20',
```

to:

```php
'mobile' => 'nullable|string|max:20',
'telephone_fixe' => 'nullable|string|max:20',
```

(Both nullable, members can fill either or both.)

- [ ] **Step 5: Update member profile view**

In `resources/views/member/profile.blade.php` line 75, change:

```blade
<input type="tel" name="phone" value="{{ old('phone', $member?->phone) }}" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
```

to:

```blade
<input type="tel" name="mobile" value="{{ old('mobile', $member?->mobile) }}" placeholder="06 XX XX XX XX" class="w-full px-3 py-2 border rounded-lg" style="border-color:var(--border)">
```

If the page also has a label or wrapping div referring to "Téléphone" that should be split into "Mobile" / "Téléphone fixe", add a second tel input for `telephone_fixe` adjacent. Read the surrounding ~10 lines first to find the right wrapper.

- [ ] **Step 6: Update show.blade.php sidebar info**

In `resources/views/admin/members/show.blade.php`, find the sidebar info block (the existing card that shows Email, Téléphone, Adresse). The current code (around lines 38-43 of the original file before Tasks 1-6 of the previous feature) likely shows:

```blade
@if($member->phone)
    <div style="margin-bottom: 1rem;">
        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Telephone</div>
        <div>{{ $member->phone }}</div>
    </div>
@endif
```

Replace with:

```blade
@if($member->mobile)
    <div style="margin-bottom: 1rem;">
        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Mobile</div>
        <div>{{ $member->mobile }}</div>
    </div>
@endif
@if($member->telephone_fixe)
    <div style="margin-bottom: 1rem;">
        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Téléphone fixe</div>
        <div>{{ $member->telephone_fixe }}</div>
    </div>
@endif
```

Read the file before editing — exact lines may have shifted from the previous refactor. Search for `Telephone` (without accent, project convention) to find the right block.

- [ ] **Step 7: Run all tests**

`php artisan test`
Expected: all green. The previous baseline was 452 — we've not added tests yet, just changes that should be transparent to existing tests.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/Api/WebhookController.php app/Services/LegacyMembershipImportService.php database/seeders/MemberSeeder.php resources/views/member/profile.blade.php app/Http/Controllers/Member/ProfileController.php resources/views/admin/members/show.blade.php
git commit -m "feat(members): remplacement phone par mobile/telephone_fixe dans les usages collatéraux"
```

---

## Task 3 — MemberController validation enrichie

**Files:**
- Modify: `app/Http/Controllers/Admin/MemberController.php` (méthodes `store` et `update`)
- Test: `tests/Feature/Admin/MemberEditFormTest.php` (créé en Task 6 mais on prépare 3 tests de validation ici)

- [ ] **Step 1: Write failing tests for validation rejections**

Create `tests/Feature/Admin/MemberEditFormTest.php` (premier embryon, on l'enrichira en Tasks 4-6) :

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberEditFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_validation_rejects_future_birth_date(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->put("/extranet/members/{$member->id}", [
            'first_name' => 'Marie',
            'last_name' => 'Durand',
            'email' => $member->email,
            'birth_date' => now()->addYear()->format('Y-m-d'),
            'contact_type' => 'individuel',
        ]);

        $response->assertSessionHasErrors('birth_date');
    }

    public function test_update_validation_rejects_invalid_civilite(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->put("/extranet/members/{$member->id}", [
            'first_name' => 'Marie',
            'last_name' => 'Durand',
            'email' => $member->email,
            'civilite' => 'Mlle',  // not in Member::CIVILITES
            'contact_type' => 'individuel',
        ]);

        $response->assertSessionHasErrors('civilite');
    }

    public function test_update_validation_rejects_invalid_contact_type(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->put("/extranet/members/{$member->id}", [
            'first_name' => 'Marie',
            'last_name' => 'Durand',
            'email' => $member->email,
            'contact_type' => 'famille',  // not allowed
        ]);

        $response->assertSessionHasErrors('contact_type');
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
}
```

- [ ] **Step 2: Run, expect failure**

`php artisan test --filter=MemberEditFormTest`
Expected: 3 fail (current `update` validation doesn't include `birth_date`, `civilite`, `contact_type` rules).

- [ ] **Step 3: Update `MemberController::update`**

In `app/Http/Controllers/Admin/MemberController.php`, replace the validation block in `update()` with:

```php
$validated = $request->validate([
    'civilite' => ['nullable', 'in:' . implode(',', \App\Models\Member::CIVILITES)],
    'first_name' => 'required|string|max:255',
    'last_name' => 'required|string|max:255',
    'birth_date' => 'nullable|date|before:today',
    'profession' => 'nullable|string|max:255',
    'email' => 'required|email|unique:members,email,' . $member->id,
    'mobile' => 'nullable|string|max:20',
    'telephone_fixe' => 'nullable|string|max:20',
    'address' => 'nullable|string|max:255',
    'postal_code' => 'nullable|string|max:10',
    'city' => 'nullable|string|max:255',
    'country' => 'nullable|string|max:255',
    'contact_type' => ['required', 'in:individuel,organisation'],
    'interests' => 'nullable|string|max:2000',
    'is_active' => 'boolean',
    'newsletter_subscribed' => 'boolean',
    'consent_communication' => 'boolean',
    'consent_image' => 'boolean',
]);

$validated['is_active'] = $request->has('is_active');
$validated['newsletter_subscribed'] = $request->has('newsletter_subscribed');
$validated['consent_communication'] = $request->has('consent_communication');
$validated['consent_image'] = $request->has('consent_image');

$member->update($validated);
```

- [ ] **Step 4: Update `MemberController::store`**

Find the `store()` method (it should be around line 90 — read the file first). Replace its validation with the SAME ruleset, except `email` rule becomes `'email' => 'required|email|unique:members,email'` (no `,$member->id` suffix).

The presence-based block for booleans is the same.

- [ ] **Step 5: Run tests, expect pass**

`php artisan test --filter=MemberEditFormTest`
Expected: 3 passing.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/MemberController.php tests/Feature/Admin/MemberEditFormTest.php
git commit -m "feat(members): validation enrichie store/update avec civilité, RGPD, contact_type"
```

---

## Task 4 — Refonte `_form.blade.php` en 5 cartes

**Files:**
- Modify: `resources/views/admin/members/_form.blade.php` (réécriture complète)
- Test: `tests/Feature/Admin/MemberEditFormTest.php` (ajout d'1 test)

- [ ] **Step 1: Add failing test**

Append to `MemberEditFormTest`:

```php
public function test_edit_form_renders_5_cards(): void
{
    $admin = $this->makeAdmin();
    $member = $this->makeMember();

    $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}/edit");

    $response->assertOk()
        ->assertSee('Identité')
        ->assertSee('Contact')
        ->assertSee('Adresse')
        ->assertSee('Préférences')
        ->assertSee('Statut & RGPD');
}
```

- [ ] **Step 2: Run, expect failure**

`php artisan test --filter=test_edit_form_renders_5_cards`
Expected: FAIL — current form has no card titles.

- [ ] **Step 3: Replace `_form.blade.php` content entirely**

Replace the entire content of `resources/views/admin/members/_form.blade.php` with:

```blade
{{-- Carte 1 — Identité --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Identité</h3></div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="civilite">Civilité</label>
                <select name="civilite" id="civilite" class="form-input">
                    <option value="">--</option>
                    @foreach(\App\Models\Member::CIVILITES as $c)
                        <option value="{{ $c }}" {{ old('civilite', $member?->civilite ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
                @error('civilite')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="first_name">Prénom *</label>
                <input type="text" name="first_name" id="first_name" class="form-input" value="{{ old('first_name', $member?->first_name ?? '') }}" required>
                @error('first_name')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="last_name">Nom *</label>
                <input type="text" name="last_name" id="last_name" class="form-input" value="{{ old('last_name', $member?->last_name ?? '') }}" required>
                @error('last_name')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="birth_date">Date de naissance</label>
                <input type="date" name="birth_date" id="birth_date" class="form-input" value="{{ old('birth_date', $member?->birth_date?->format('Y-m-d') ?? '') }}">
                @error('birth_date')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="profession">Profession</label>
                <input type="text" name="profession" id="profession" class="form-input" value="{{ old('profession', $member?->profession ?? '') }}">
                @error('profession')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>

{{-- Carte 2 — Contact --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Contact</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="email">Email *</label>
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $member?->email ?? '') }}" required>
            @error('email')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="mobile">Mobile</label>
                <input type="tel" name="mobile" id="mobile" class="form-input" placeholder="06 XX XX XX XX" value="{{ old('mobile', $member?->mobile ?? '') }}">
                @error('mobile')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="telephone_fixe">Téléphone fixe</label>
                <input type="tel" name="telephone_fixe" id="telephone_fixe" class="form-input" placeholder="01 XX XX XX XX" value="{{ old('telephone_fixe', $member?->telephone_fixe ?? '') }}">
                @error('telephone_fixe')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>

{{-- Carte 3 — Adresse --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Adresse</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="address">Adresse</label>
            <input type="text" name="address" id="address" class="form-input" value="{{ old('address', $member?->address ?? '') }}">
            @error('address')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
        <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="postal_code">Code postal</label>
                <input type="text" name="postal_code" id="postal_code" class="form-input" value="{{ old('postal_code', $member?->postal_code ?? '') }}">
                @error('postal_code')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="city">Ville</label>
                <input type="text" name="city" id="city" class="form-input" value="{{ old('city', $member?->city ?? '') }}">
                @error('city')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="country">Pays</label>
                <input type="text" name="country" id="country" class="form-input" value="{{ old('country', $member?->country ?? 'France') }}">
                @error('country')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>

{{-- Carte 4 — Préférences --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Préférences</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label" for="contact_type">Type de contact *</label>
            <select name="contact_type" id="contact_type" class="form-input" required>
                <option value="individuel" {{ old('contact_type', $member?->contact_type ?? 'individuel') === 'individuel' ? 'selected' : '' }}>Individuel</option>
                <option value="organisation" {{ old('contact_type', $member?->contact_type ?? '') === 'organisation' ? 'selected' : '' }}>Organisation</option>
            </select>
            @error('contact_type')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="newsletter_subscribed" value="1" {{ old('newsletter_subscribed', $member?->newsletter_subscribed ?? true) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Inscrit à la newsletter</span>
            </label>
        </div>
        <div class="form-group">
            <label class="form-label" for="interests">Intérêts</label>
            <textarea name="interests" id="interests" rows="3" class="form-input">{{ old('interests', $member?->interests ?? '') }}</textarea>
            @error('interests')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- Carte 5 — Statut & RGPD --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header"><h3 class="card-title">Statut & RGPD</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $member?->is_active ?? true) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Contact actif</span>
            </label>
        </div>
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="consent_communication" value="1" {{ old('consent_communication', $member?->consent_communication ?? false) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Autorise les communications associatives</span>
            </label>
        </div>
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="consent_image" value="1" {{ old('consent_image', $member?->consent_image ?? false) ? 'checked' : '' }} style="width: 1rem; height: 1rem;">
                <span class="form-label" style="margin-bottom: 0;">Autorise l'utilisation de son image</span>
            </label>
        </div>
    </div>
</div>
```

- [ ] **Step 4: Run test, expect pass**

`php artisan test --filter=test_edit_form_renders_5_cards`
Expected: 1 passing (in addition to 3 from Task 3).

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/members/_form.blade.php tests/Feature/Admin/MemberEditFormTest.php
git commit -m "feat(members): formulaire édition refait en 5 cartes thématiques"
```

---

## Task 5 — `edit.blade.php` (sidebar récap) + `create.blade.php` (sans sidebar)

**Files:**
- Modify: `resources/views/admin/members/edit.blade.php`
- Modify: `resources/views/admin/members/create.blade.php`
- Test: `tests/Feature/Admin/MemberEditFormTest.php` (ajout d'1 test)

- [ ] **Step 1: Add failing test**

Append to `MemberEditFormTest`:

```php
public function test_create_form_does_not_render_sidebar_recap(): void
{
    $admin = $this->makeAdmin();

    $response = $this->actingAs($admin)->get('/extranet/members/create');

    $response->assertOk()
        ->assertDontSee('Retour à la fiche');
}
```

- [ ] **Step 2: Run, expect pass or fail (depending on existing create.blade.php)**

`php artisan test --filter=test_create_form_does_not_render_sidebar_recap`
Expected: PASS already if `create.blade.php` doesn't have a "Retour à la fiche" link. If it currently does (unlikely), the test fails and we fix in Step 4.

- [ ] **Step 3: Update `edit.blade.php` to add sidebar récap**

Replace the entire `resources/views/admin/members/edit.blade.php` with:

```blade
@extends('layouts.admin')

@section('title', 'Modifier ' . $member->full_name)
@section('breadcrumb')
    <a href="{{ route('admin.members.index') }}">Contacts</a>
    <span>/</span>
    <a href="{{ route('admin.members.show', $member) }}">{{ $member->full_name }}</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        {{-- SIDEBAR RÉCAP --}}
        <div class="card">
            <div class="card-body">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background-color: #356B8A; color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 600; margin: 0 auto;">
                        {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                    </div>
                    <h2 style="margin-top: 1rem; font-size: 1.25rem; font-weight: 600;">{{ $member->full_name }}</h2>
                </div>
                <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">N° adhérent</div>
                        <div>{{ $member->member_number }}</div>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Inscrit le</div>
                        <div>{{ $member->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
                <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 1rem;">
                    <a href="{{ route('admin.members.show', $member) }}" class="btn btn-secondary" style="width: 100%; text-align: center;">
                        ← Retour à la fiche
                    </a>
                </div>
            </div>
        </div>

        {{-- FORMULAIRE --}}
        <div>
            <form action="{{ route('admin.members.update', $member) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.members._form', ['member' => $member])

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.members.show', $member) }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
```

- [ ] **Step 4: Update `create.blade.php` (sans sidebar)**

Read current `resources/views/admin/members/create.blade.php` first. If it already follows the same minimal pattern (just `extends + section + form + include _form`), update it to ensure:
- No "Retour à la fiche" link (the test enforces this).
- The cards structure works without a `$member` variable: in create, `$member` is undefined and `_form.blade.php` uses `$member->X ?? ''` patterns, so it should fall back to defaults.

The recommended replacement:

```blade
@extends('layouts.admin')

@section('title', 'Nouveau contact')
@section('breadcrumb')
    <a href="{{ route('admin.members.index') }}">Contacts</a>
    <span>/</span>
    <span>Nouveau</span>
@endsection

@section('content')
    <div style="max-width: 800px;">
        <form action="{{ route('admin.members.store') }}" method="POST">
            @csrf
            @include('admin.members._form', ['member' => null])

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">Créer le contact</button>
                <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
```

NB: passing `'member' => null` lets the `_form.blade.php` use `$member?->X ?? ''` patterns — make sure the partial uses `?->` (nullsafe) on access. Re-read the `_form.blade.php` you wrote in Task 4 — replace `$member?->civilite ?? ''` with `$member?->civilite ?? ''` if needed for null safety. (The `?->` operator returns null if `$member` is null, then the `??` fallback kicks in.)

If you find any `$member->X` without `?->` in the partial, fix them by adding the nullsafe operator.

- [ ] **Step 5: Run tests, expect pass**

`php artisan test --filter=MemberEditFormTest`
Expected: 5 passing (3 from Task 3 + 1 from Task 4 + 1 from Task 5).

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/members/edit.blade.php resources/views/admin/members/create.blade.php resources/views/admin/members/_form.blade.php
git commit -m "feat(members): edit avec sidebar récap, create simple, partial null-safe"
```

(`_form.blade.php` is in the commit too if you had to add `?->` for null-safety.)

---

## Task 6 — Tests pre-fill, persist, uncheck

**Files:**
- Modify: `tests/Feature/Admin/MemberEditFormTest.php` (ajout de 4 tests)

- [ ] **Step 1: Add the failing tests**

Append to `MemberEditFormTest`:

```php
public function test_edit_form_pre_fills_existing_values(): void
{
    $admin = $this->makeAdmin();
    $u = User::factory()->create(['email' => 'marie@test.com']);
    $member = Member::create([
        'user_id' => $u->id,
        'member_number' => 'M5555',
        'email' => 'marie@test.com',
        'civilite' => 'Mme',
        'first_name' => 'Marie',
        'last_name' => 'Durand',
        'profession' => 'Botaniste',
        'mobile' => '06 12 34 56 78',
        'consent_image' => true,
        'interests' => 'papillons alpins',
        'joined_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get("/extranet/members/{$member->id}/edit");

    $response->assertOk()
        ->assertSee('Mme', escape: false)
        ->assertSee('Marie')
        ->assertSee('Botaniste')
        ->assertSee('06 12 34 56 78')
        ->assertSee('papillons alpins');
}

public function test_update_persists_new_fields(): void
{
    $admin = $this->makeAdmin();
    $member = $this->makeMember();

    $response = $this->actingAs($admin)->put("/extranet/members/{$member->id}", [
        'civilite' => 'Mme',
        'first_name' => 'Marie',
        'last_name' => 'Durand',
        'birth_date' => '1980-05-15',
        'profession' => 'Botaniste',
        'email' => $member->email,
        'mobile' => '06 12 34 56 78',
        'telephone_fixe' => '01 23 45 67 89',
        'address' => '1 rue Test',
        'postal_code' => '75001',
        'city' => 'Paris',
        'country' => 'France',
        'contact_type' => 'individuel',
        'interests' => 'papillons alpins',
        'newsletter_subscribed' => '1',
        'is_active' => '1',
        'consent_communication' => '1',
        'consent_image' => '1',
    ]);

    $response->assertRedirect();
    $member->refresh();
    $this->assertSame('Mme', $member->civilite);
    $this->assertSame('1980-05-15', $member->birth_date->format('Y-m-d'));
    $this->assertSame('Botaniste', $member->profession);
    $this->assertSame('06 12 34 56 78', $member->mobile);
    $this->assertSame('01 23 45 67 89', $member->telephone_fixe);
    $this->assertSame('papillons alpins', $member->interests);
    $this->assertTrue((bool) $member->newsletter_subscribed);
    $this->assertTrue((bool) $member->consent_communication);
    $this->assertTrue((bool) $member->consent_image);
}

public function test_update_uncheck_unchecks_booleans(): void
{
    $admin = $this->makeAdmin();
    $u = User::factory()->create();
    $member = Member::create([
        'user_id' => $u->id,
        'member_number' => 'M' . random_int(1000, 99999),
        'email' => $u->email,
        'first_name' => 'F',
        'last_name' => 'L',
        'newsletter_subscribed' => true,
        'consent_image' => true,
        'is_active' => true,
        'joined_at' => now(),
    ]);

    // POST without checkbox names → presence-based pattern unsets them
    $this->actingAs($admin)->put("/extranet/members/{$member->id}", [
        'first_name' => 'F',
        'last_name' => 'L',
        'email' => $member->email,
        'contact_type' => 'individuel',
    ])->assertRedirect();

    $member->refresh();
    $this->assertFalse((bool) $member->newsletter_subscribed);
    $this->assertFalse((bool) $member->consent_image);
    $this->assertFalse((bool) $member->is_active);
}

public function test_phone_column_dropped_after_migration(): void
{
    $this->assertFalse(\Illuminate\Support\Facades\Schema::hasColumn('members', 'phone'));
}
```

- [ ] **Step 2: Run, expect pass**

`php artisan test --filter=MemberEditFormTest`
Expected: 9 passing (3 + 1 + 1 + 4 = 9).

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/Admin/MemberEditFormTest.php
git commit -m "test(members): tests feature pre-fill, persistence, uncheck booleans, phone dropped"
```

---

## Task 7 — Full test suite + smoke + merge to main

**Files:** none (verification only)

- [ ] **Step 1: Run full suite**

`php artisan test`
Expected: 461 passing (452 previous + 9 new). Investigate and fix any regression.

If a regression appears in tests that previously created Members with `'phone' => '...'`, update the test to use `'mobile'` instead (the fillable no longer has `phone`).

- [ ] **Step 2: Manual smoke**

Start server: `php artisan serve`

Visit:
- `http://localhost:8000/extranet/members/1/edit` — sidebar récap visible à gauche, formulaire en 5 cartes à droite
- Fill some fields (civilité, mobile, profession, RGPD checkboxes), submit, verify redirect to `show` with success message
- `http://localhost:8000/extranet/members/create` — formulaire en single column, pas de sidebar, lien "Retour fiche" absent
- Try to submit with invalid civilité or future birth_date → see validation errors

- [ ] **Step 3: Clear caches**

```bash
php artisan view:clear
```

- [ ] **Step 4: Commit any tweaks from smoke (if applicable)**

If smoke reveals minor issues (label typos, layout glitches), fix and commit individually.

- [ ] **Step 5: Merge feature branch to main**

The work is on `main` directly per user choice (no worktree, no feature branch for this small scope). All commits are on `main`. No merge needed — the task is complete once all commits are in place.

- [ ] **Step 6: Update memory**

Mark the feature livré in memory after smoke OK.

---

## Self-Review

**Spec coverage:**

| Spec section | Tasks |
|---|---|
| Migration drop phone (backfill mobile) | Task 1 |
| Member fillable cleanup (drop 'phone') | Task 1 |
| MemberController validation enrichie (store + update, presence-based booléens) | Task 3 |
| 5 cartes Identité/Contact/Adresse/Préférences/Statut RGPD | Task 4 |
| edit.blade.php avec sidebar récap | Task 5 |
| create.blade.php sans sidebar | Task 5 |
| _form.blade.php null-safe pour create | Task 5 |
| Impacts collatéraux phone → mobile (Webhook, Profile, Seeder, Import, show, profile views) | Task 2 |
| 9 tests feature | Tasks 3 (3 validation), 4 (1 render), 5 (1 create), 6 (4 pre-fill/persist/uncheck/dropped) |

Toutes les exigences couvertes.

**Type consistency:**
- Validation rules in Task 3 use `Member::CIVILITES` constant — consistent with the SELECT in Task 4.
- All booleans (`is_active`, `newsletter_subscribed`, `consent_communication`, `consent_image`) handled with `$request->has(...)` presence-based pattern — consistent across Task 3 and Task 6 tests.
- `$member?->X ?? ''` null-safe pattern in Task 5 matches the partial `_form.blade.php` in Task 4 (NB: the partial code in Task 4 uses `$member->X ?? ''` without `?->`. Task 5 fixes this with `?->`. The fix in Task 5 is essential — flag in self-review and confirm.)

**Placeholders:** none.

**Adjustment from spec:** civilités `M./Mme/Autre` → `Member::CIVILITES = ['M.', 'Mme', 'Dr', 'Pr']` (existing constant) — documented at top of plan.

**Note importante** : la Task 4 utilise `$member->X` sans `?->` dans le `_form.blade.php`, et la Task 5 doit le corriger en `$member?->X` pour que `create.blade.php` (qui passe `'member' => null`) fonctionne. **Adjustment** : on peut écrire le partial avec `?->` dès la Task 4 pour éviter cette double édition. Je recommande à l'engineer d'utiliser `$member?->X ?? ''` partout dès Task 4 pour anticiper Task 5.
