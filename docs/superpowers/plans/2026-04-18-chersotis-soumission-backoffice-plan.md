# Chersotis — Soumission backoffice pour compte d'un auteur — Plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Permettre à un rédacteur en chef / éditeur de saisir une soumission pour le compte d'un auteur qui n'a pas encore de compte, avec création automatique d'un User "ghost" et mail d'invitation signé.

**Architecture:** Extension du formulaire admin existant (`admin.submissions.create`) avec un radio toggle "Auteur existant / Nouvel auteur". Un User ghost (password null, invited_at rempli) est créé en transaction avec la Submission. Un mail est envoyé avec une signed URL Laravel (14j) menant à un form "définir mon mot de passe" qui active le compte.

**Tech Stack:** Laravel 12, Blade, Alpine.js, PostgreSQL, Mail (Markdown template), Laravel signed routes.

**Spec source:** `docs/superpowers/specs/2026-04-18-chersotis-soumission-backoffice-design.md`

---

## File Structure

**New files**
- `database/migrations/2026_04_18_120000_add_submitted_by_to_submissions.php`
- `database/migrations/2026_04_18_120001_add_invitation_fields_to_users.php`
- `app/Services/SubmissionCreationService.php`
- `app/Mail/AccountInvitation.php`
- `resources/views/emails/account-invitation.blade.php`
- `app/Http/Controllers/Auth/ClaimAccountController.php`
- `resources/views/auth/claim-account.blade.php`
- `tests/Unit/Models/UserGhostTest.php`
- `tests/Unit/Services/SubmissionCreationServiceTest.php`
- `tests/Feature/Admin/BackofficeSubmissionTest.php`
- `tests/Feature/Auth/ClaimAccountTest.php`

**Files to modify**
- `app/Models/User.php` — scopes `ghost`/`claimed`, `isGhost()`, `invitedBy()` relation, fillable+casts
- `app/Models/Submission.php` — `submittedBy()` relation, `wasSubmittedOnBehalf()`, fillable
- `app/Http/Controllers/Admin/SubmissionController.php` — authorize, validation, service delegation
- `resources/views/admin/submissions/_form.blade.php` — radio toggle (remplace le dropdown auteur)
- `resources/views/admin/submissions/index.blade.php` — `@can` autour du bouton « Nouvelle soumission »
- `app/Providers/AppServiceProvider.php` — Gate `create-submission-for-author`
- `routes/web.php` — 2 routes `account.claim*` avec middleware `signed`
- `config/journal.php` — `invitation_expiration_days`
- `database/factories/UserFactory.php` — state `ghost()`

---

## Task 1: Migration `submitted_by_user_id` sur `submissions`

**Files:**
- Create: `database/migrations/2026_04_18_120000_add_submitted_by_to_submissions.php`

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
            $table->foreignId('submitted_by_user_id')
                  ->nullable()
                  ->after('author_id')
                  ->constrained('users')
                  ->onDelete('set null');
            $table->index('submitted_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['submitted_by_user_id']);
            $table->dropIndex(['submitted_by_user_id']);
            $table->dropColumn('submitted_by_user_id');
        });
    }
};
```

- [ ] **Step 2: Exécuter la migration**

Run: `php artisan migrate`
Expected: « Migrated: 2026_04_18_120000_add_submitted_by_to_submissions »

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_04_18_120000_add_submitted_by_to_submissions.php
git commit -m "feat(db): add submitted_by_user_id to submissions"
```

---

## Task 2: Migration `invitation_fields` sur `users`

**Files:**
- Create: `database/migrations/2026_04_18_120001_add_invitation_fields_to_users.php`

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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('invited_at')->nullable()->after('remember_token');
            $table->timestamp('claimed_at')->nullable()->after('invited_at');
            $table->foreignId('invited_by_user_id')->nullable()
                  ->after('claimed_at')
                  ->constrained('users')
                  ->onDelete('set null');
            $table->index('invited_at');
            $table->index('claimed_at');
        });

        // Le password peut être null pour les users ghost : on rend la colonne nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['invited_by_user_id']);
            $table->dropIndex(['invited_at']);
            $table->dropIndex(['claimed_at']);
            $table->dropColumn(['invited_at', 'claimed_at', 'invited_by_user_id']);
        });
    }
};
```

- [ ] **Step 2: Exécuter la migration**

Run: `php artisan migrate`
Expected: « Migrated: 2026_04_18_120001_add_invitation_fields_to_users »

Si PostgreSQL râle sur le `->change()` → vérifier que `doctrine/dbal` est installé : `composer require doctrine/dbal --dev` puis re-run.

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_04_18_120001_add_invitation_fields_to_users.php
git commit -m "feat(db): add invitation fields to users + make password nullable"
```

---

## Task 3: Model `User` — scopes et relations ghost

**Files:**
- Modify: `app/Models/User.php:26-47` (fillable + casts)
- Modify: `app/Models/User.php:280` (ajout fin de classe)
- Test: `tests/Unit/Models/UserGhostTest.php`

- [ ] **Step 1: Écrire les tests avant**

Créer `tests/Unit/Models/UserGhostTest.php` :

```php
<?php

namespace Tests\Unit\Models;

use App\Models\EditorialCapability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGhostTest extends TestCase
{
    use RefreshDatabase;

    public function test_ghost_scope_returns_only_uninvited_unclaimed_users(): void
    {
        $ghost = User::factory()->ghost()->create();
        $claimed = User::factory()->ghost()->claimed()->create();
        $normal = User::factory()->create();

        $results = User::ghost()->pluck('id');

        $this->assertTrue($results->contains($ghost->id));
        $this->assertFalse($results->contains($claimed->id));
        $this->assertFalse($results->contains($normal->id));
    }

    public function test_claimed_scope_returns_only_claimed_users(): void
    {
        $ghost = User::factory()->ghost()->create();
        $claimed = User::factory()->ghost()->claimed()->create();
        $normal = User::factory()->create();

        $results = User::claimed()->pluck('id');

        $this->assertFalse($results->contains($ghost->id));
        $this->assertTrue($results->contains($claimed->id));
        $this->assertTrue($results->contains($normal->id)); // un user normal est claimé par défaut (claimed_at=now)
    }

    public function test_is_ghost_returns_true_for_ghost_user(): void
    {
        $ghost = User::factory()->ghost()->create();
        $this->assertTrue($ghost->isGhost());
    }

    public function test_is_ghost_returns_false_for_claimed_user(): void
    {
        $claimed = User::factory()->ghost()->claimed()->create();
        $this->assertFalse($claimed->isGhost());
    }

    public function test_is_ghost_returns_false_for_regular_user(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->isGhost());
    }

    public function test_with_capability_scope_does_not_include_ghosts(): void
    {
        $ghost = User::factory()->ghost()->create();
        $editor = User::factory()->create();
        $editor->capabilities()->create([
            'capability' => EditorialCapability::EDITOR,
            'granted_at' => now(),
        ]);

        $results = User::withCapability(EditorialCapability::EDITOR)->pluck('id');

        $this->assertFalse($results->contains($ghost->id));
        $this->assertTrue($results->contains($editor->id));
    }

    public function test_invited_by_relation_returns_the_inviter(): void
    {
        $inviter = User::factory()->create();
        $ghost = User::factory()->ghost()->create(['invited_by_user_id' => $inviter->id]);

        $this->assertEquals($inviter->id, $ghost->invitedBy->id);
    }
}
```

- [ ] **Step 2: Lancer les tests pour vérifier qu'ils échouent**

Run: `php artisan test --filter=UserGhostTest`
Expected: FAIL avec messages « Call to undefined method scopeGhost » ou « Call to undefined method scopeClaimed », etc.

- [ ] **Step 3: Modifier le model `User` — ajouter fillable et casts**

Dans `app/Models/User.php`, remplacer le bloc `$fillable` (ligne 26-33) par :

```php
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'invited_at',
        'claimed_at',
        'invited_by_user_id',
    ];
```

Remplacer la méthode `casts()` (ligne 40-47) par :

```php
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'invited_at' => 'datetime',
            'claimed_at' => 'datetime',
        ];
    }
```

- [ ] **Step 4: Ajouter scopes, relations, helpers**

À la fin de la classe `User` (avant le `}` final de la ligne 280), ajouter :

```php
    // ===== GHOST USERS (invitation flow) =====

    public function invitedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    public function scopeGhost($query)
    {
        return $query->whereNull('password')
                     ->whereNotNull('invited_at')
                     ->whereNull('claimed_at');
    }

    public function scopeClaimed($query)
    {
        return $query->whereNotNull('claimed_at');
    }

    public function isGhost(): bool
    {
        return $this->password === null
            && $this->invited_at !== null
            && $this->claimed_at === null;
    }
```

- [ ] **Step 5: Ajouter la factory state `ghost()` et `claimed()`**

Modifier `database/factories/UserFactory.php` en ajoutant à la fin de la classe (avant le `}` de fermeture de classe) :

```php
    public function ghost(): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => null,
            'email_verified_at' => null,
            'invited_at' => now(),
            'claimed_at' => null,
        ]);
    }

    public function claimed(): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => static::$password ??= \Illuminate\Support\Facades\Hash::make('password'),
            'claimed_at' => now(),
            'email_verified_at' => now(),
        ]);
    }
```

Note : les users normaux créés par `User::factory()->create()` ont `claimed_at = null` par défaut mais `password != null`. Pour que le test `test_claimed_scope_returns_only_claimed_users` passe, il faut que les users normaux soient considérés comme « claimed » — on ajoute `claimed_at => now()` dans le state par défaut :

Modifier `definition()` de `UserFactory` (ligne 24) pour ajouter `claimed_at => now()` :

```php
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'claimed_at' => now(),
        ];
    }
```

- [ ] **Step 6: Lancer les tests — ils passent**

Run: `php artisan test --filter=UserGhostTest`
Expected: PASS (6 tests, 6 assertions OK)

- [ ] **Step 7: Commit**

```bash
git add app/Models/User.php database/factories/UserFactory.php tests/Unit/Models/UserGhostTest.php
git commit -m "feat(user): add ghost scopes, relations, factory states + tests"
```

---

## Task 4: Model `Submission` — relation `submittedBy`

**Files:**
- Modify: `app/Models/Submission.php:12-48` (fillable)
- Modify: `app/Models/Submission.php:100-118` (relations)

- [ ] **Step 1: Écrire le test avant**

Ajouter dans `tests/Unit/Models/` un nouveau fichier `SubmissionSubmittedByTest.php` :

```php
<?php

namespace Tests\Unit\Models;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionSubmittedByTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitted_by_relation_returns_the_creator(): void
    {
        $author = User::factory()->create();
        $editor = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'submitted_by_user_id' => $editor->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => 'submitted',
        ]);

        $this->assertEquals($editor->id, $submission->submittedBy->id);
    }

    public function test_was_submitted_on_behalf_true_when_different_from_author(): void
    {
        $author = User::factory()->create();
        $editor = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'submitted_by_user_id' => $editor->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => 'submitted',
        ]);

        $this->assertTrue($submission->wasSubmittedOnBehalf());
    }

    public function test_was_submitted_on_behalf_false_when_null(): void
    {
        $author = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'submitted_by_user_id' => null,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => 'submitted',
        ]);

        $this->assertFalse($submission->wasSubmittedOnBehalf());
    }

    public function test_was_submitted_on_behalf_false_when_same_as_author(): void
    {
        $author = User::factory()->create();

        $submission = Submission::create([
            'author_id' => $author->id,
            'submitted_by_user_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => 'submitted',
        ]);

        $this->assertFalse($submission->wasSubmittedOnBehalf());
    }
}
```

- [ ] **Step 2: Lancer les tests pour vérifier qu'ils échouent**

Run: `php artisan test --filter=SubmissionSubmittedByTest`
Expected: FAIL avec « Call to undefined method submittedBy » ou « field not in fillable »

- [ ] **Step 3: Modifier le fillable**

Dans `app/Models/Submission.php`, ajouter `'submitted_by_user_id'` à `$fillable` (entre `'author_id'` ligne 13 et `'journal_issue_id'` ligne 14) :

```php
    protected $fillable = [
        'author_id',
        'submitted_by_user_id',
        'journal_issue_id',
        // ... reste inchangé
```

- [ ] **Step 4: Ajouter la relation et le helper**

Après la méthode `layoutEditor()` (ligne 113) et avant `journalIssue()` (ligne 115), ajouter :

```php
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function wasSubmittedOnBehalf(): bool
    {
        return $this->submitted_by_user_id !== null
            && $this->submitted_by_user_id !== $this->author_id;
    }
```

- [ ] **Step 5: Lancer les tests — ils passent**

Run: `php artisan test --filter=SubmissionSubmittedByTest`
Expected: PASS (4 tests OK)

- [ ] **Step 6: Commit**

```bash
git add app/Models/Submission.php tests/Unit/Models/SubmissionSubmittedByTest.php
git commit -m "feat(submission): add submittedBy relation + wasSubmittedOnBehalf helper"
```

---

## Task 5: Mail `AccountInvitation`

**Files:**
- Create: `app/Mail/AccountInvitation.php`
- Create: `resources/views/emails/account-invitation.blade.php`
- Modify: `config/journal.php` (ajouter `invitation_expiration_days`)

- [ ] **Step 1: Ajouter la config d'expiration**

Dans `config/journal.php`, ajouter à la fin (avant le `];` final) :

```php
    /*
    |--------------------------------------------------------------------------
    | Invitation Flow
    |--------------------------------------------------------------------------
    */
    'invitation_expiration_days' => env('JOURNAL_INVITATION_EXPIRATION_DAYS', 14),
```

- [ ] **Step 2: Créer le Mailable**

Run: `php artisan make:mail AccountInvitation --markdown=emails.account-invitation`

Remplacer le contenu de `app/Mail/AccountInvitation.php` par :

```php
<?php

namespace App\Mail;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class AccountInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $author,
        public Submission $submission,
        public User $invitedBy,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Un article vous concernant a été déposé sur Chersotis',
        );
    }

    public function content(): Content
    {
        $claimUrl = URL::temporarySignedRoute(
            'account.claim',
            now()->addDays(config('journal.invitation_expiration_days', 14)),
            ['user' => $this->author->id]
        );

        return new Content(
            markdown: 'emails.account-invitation',
            with: [
                'author' => $this->author,
                'submission' => $this->submission,
                'invitedBy' => $this->invitedBy,
                'claimUrl' => $claimUrl,
                'expirationDate' => now()->addDays(config('journal.invitation_expiration_days', 14))->format('d/m/Y'),
            ],
        );
    }
}
```

- [ ] **Step 3: Écrire le template Markdown**

Remplacer `resources/views/emails/account-invitation.blade.php` par :

```blade
<x-mail::message>
# Bonjour {{ $author->name }},

**{{ $invitedBy->name }}** a déposé sur la revue **Chersotis** une soumission en votre nom :

> *{{ $submission->title }}*

Afin de pouvoir suivre le processus éditorial et échanger avec l'équipe, un compte a été créé pour vous sur la plateforme. Il vous suffit de définir votre mot de passe pour l'activer.

<x-mail::button :url="$claimUrl" color="success">
Activer mon compte
</x-mail::button>

Ce lien est valide jusqu'au **{{ $expirationDate }}**. Passé cette date, contactez [chersotis-revue@oreina.org](mailto:chersotis-revue@oreina.org) pour obtenir un nouveau lien.

---

## Processus éditorial de Chersotis

Votre soumission va suivre ces étapes :

1. **Accusé de réception** — l'équipe a bien reçu votre manuscrit
2. **Évaluation initiale** — un éditeur vérifie l'adéquation au périmètre de la revue
3. **Relecture par les pairs** — deux relecteurs spécialisés examinent le manuscrit
4. **Décision** — l'éditeur synthétise les retours et vous transmet la décision
5. **Révisions éventuelles** — vous pouvez être invité à amender le manuscrit
6. **Maquettage** — la revue prépare la mise en page
7. **Approbation finale** — vous validez la version maquettée avant publication

Vous pourrez suivre l'avancement depuis votre espace auteur.

---

Cordialement,
L'équipe éditoriale **Chersotis**
{{ config('app.url') }}
</x-mail::message>
```

- [ ] **Step 4: Lancer un test rapide de rendu**

Créer `tests/Unit/Mail/AccountInvitationTest.php` :

```php
<?php

namespace Tests\Unit\Mail;

use App\Mail\AccountInvitation;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_contains_subject_and_signed_claim_url(): void
    {
        $author = User::factory()->ghost()->create(['name' => 'Jean Test']);
        $editor = User::factory()->create(['name' => 'Greg']);
        $submission = Submission::create([
            'author_id' => $author->id,
            'submitted_by_user_id' => $editor->id,
            'title' => 'Mon super papier',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => 'submitted',
        ]);

        $mail = new AccountInvitation($author, $submission, $editor);
        $mail->assertSeeInHtml('Jean Test');
        $mail->assertSeeInHtml('Greg');
        $mail->assertSeeInHtml('Mon super papier');
        $mail->assertSeeInHtml('/claim-account/'.$author->id);
        $mail->assertSeeInHtml('signature='); // signed URL
        $this->assertSame('Un article vous concernant a été déposé sur Chersotis', $mail->envelope()->subject);
    }
}
```

Run: `php artisan test --filter=AccountInvitationTest`

Expected : FAIL à cause de la route `account.claim` qui n'existe pas encore → **on crée les routes avant de re-lancer**. Continue à la Task 6 avant de valider ce test.

- [ ] **Step 5: Commit (test rouge temporaire, sera vert après Task 6)**

```bash
git add app/Mail/AccountInvitation.php resources/views/emails/account-invitation.blade.php config/journal.php tests/Unit/Mail/AccountInvitationTest.php
git commit -m "feat(mail): AccountInvitation mailable + markdown template"
```

---

## Task 6: `ClaimAccountController` + routes signées

**Files:**
- Create: `app/Http/Controllers/Auth/ClaimAccountController.php`
- Create: `resources/views/auth/claim-account.blade.php`
- Modify: `routes/web.php` (ajouter routes claim)
- Test: `tests/Feature/Auth/ClaimAccountTest.php`

- [ ] **Step 1: Créer le dossier Auth s'il n'existe pas**

Run: `mkdir -p app/Http/Controllers/Auth`

- [ ] **Step 2: Écrire les tests**

Créer `tests/Feature/Auth/ClaimAccountTest.php` :

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ClaimAccountTest extends TestCase
{
    use RefreshDatabase;

    private function signedUrl(User $user, int $minutes = 60): string
    {
        return URL::temporarySignedRoute(
            'account.claim',
            now()->addMinutes($minutes),
            ['user' => $user->id]
        );
    }

    public function test_valid_signed_url_shows_claim_form(): void
    {
        $ghost = User::factory()->ghost()->create();

        $response = $this->get($this->signedUrl($ghost));

        $response->assertOk();
        $response->assertSee('mot de passe');
        $response->assertSee($ghost->email);
    }

    public function test_tampered_signature_returns_403(): void
    {
        $ghost = User::factory()->ghost()->create();
        $url = $this->signedUrl($ghost).'&tampered=1';

        $response = $this->get($url);

        $response->assertStatus(403);
    }

    public function test_expired_url_returns_403(): void
    {
        $ghost = User::factory()->ghost()->create();
        $url = URL::temporarySignedRoute(
            'account.claim',
            now()->subMinute(),
            ['user' => $ghost->id]
        );

        $response = $this->get($url);

        $response->assertStatus(403);
    }

    public function test_post_password_activates_account_and_logs_in(): void
    {
        $ghost = User::factory()->ghost()->create();
        $signedPost = URL::temporarySignedRoute(
            'account.claim.store',
            now()->addMinutes(60),
            ['user' => $ghost->id]
        );

        $response = $this->post($signedPost, [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('submissions.index'));
        $response->assertSessionHas('success');

        $ghost->refresh();
        $this->assertNotNull($ghost->claimed_at);
        $this->assertNotNull($ghost->email_verified_at);
        $this->assertTrue(Hash::check('password123', $ghost->password));
        $this->assertAuthenticatedAs($ghost);
    }

    public function test_post_password_unconfirmed_fails_validation(): void
    {
        $ghost = User::factory()->ghost()->create();
        $signedPost = URL::temporarySignedRoute(
            'account.claim.store',
            now()->addMinutes(60),
            ['user' => $ghost->id]
        );

        $response = $this->post($signedPost, [
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
        $ghost->refresh();
        $this->assertNull($ghost->claimed_at);
    }

    public function test_post_password_too_short_fails_validation(): void
    {
        $ghost = User::factory()->ghost()->create();
        $signedPost = URL::temporarySignedRoute(
            'account.claim.store',
            now()->addMinutes(60),
            ['user' => $ghost->id]
        );

        $response = $this->post($signedPost, [
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_claiming_an_already_claimed_account_redirects_to_login(): void
    {
        $claimed = User::factory()->ghost()->claimed()->create();

        $response = $this->get($this->signedUrl($claimed));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('info');
    }
}
```

- [ ] **Step 3: Lancer les tests → FAIL attendu**

Run: `php artisan test --filter=ClaimAccountTest`
Expected: FAIL « Route [account.claim] not defined »

- [ ] **Step 4: Créer le controller**

Créer `app/Http/Controllers/Auth/ClaimAccountController.php` :

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ClaimAccountController extends Controller
{
    public function show(User $user)
    {
        if ($user->claimed_at !== null) {
            return redirect()
                ->route('login')
                ->with('info', 'Ce compte a déjà été activé. Connectez-vous.');
        }

        return view('auth.claim-account', ['user' => $user]);
    }

    public function store(Request $request, User $user)
    {
        if ($user->claimed_at !== null) {
            return redirect()
                ->route('login')
                ->with('info', 'Ce compte a déjà été activé.');
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'claimed_at' => now(),
            'email_verified_at' => now(),
        ])->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('submissions.index')
            ->with('success', 'Bienvenue ! Votre compte est activé, vous pouvez suivre vos soumissions.');
    }
}
```

- [ ] **Step 5: Ajouter les routes dans `routes/web.php`**

Ouvrir `routes/web.php` et ajouter à la fin du fichier (hors de tout groupe existant) :

```php
use App\Http\Controllers\Auth\ClaimAccountController;

Route::middleware('signed')->group(function () {
    Route::get('/claim-account/{user}', [ClaimAccountController::class, 'show'])
        ->name('account.claim');
    Route::post('/claim-account/{user}', [ClaimAccountController::class, 'store'])
        ->name('account.claim.store');
});
```

- [ ] **Step 6: Créer la vue Blade**

Créer `resources/views/auth/claim-account.blade.php` :

```blade
@extends('layouts.guest')

@section('title', 'Activer mon compte — Chersotis')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold text-oreina-dark mb-2">Activer mon compte</h1>
        <p class="text-gray-600 text-sm mb-6">
            Bienvenue {{ $user->name }} ! Définissez votre mot de passe pour accéder à votre espace auteur.
        </p>

        <div class="mb-6 p-3 bg-gray-50 rounded border border-gray-200 text-sm">
            <div><strong>Nom :</strong> {{ $user->name }}</div>
            <div><strong>Email :</strong> {{ $user->email }}</div>
        </div>

        <form method="POST" action="{{ url()->current() }}">
            @csrf

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <input type="password" name="password" id="password" required
                       autocomplete="new-password" minlength="8"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-oreina-green">
                <p class="text-xs text-gray-500 mt-1">8 caractères minimum.</p>
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       autocomplete="new-password" minlength="8"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-oreina-green">
            </div>

            <button type="submit"
                    class="w-full bg-oreina-green text-white py-2 px-4 rounded-md hover:bg-oreina-dark transition">
                Activer mon compte
            </button>
        </form>

        <p class="text-xs text-gray-500 mt-6 text-center">
            En activant votre compte, vous acceptez les
            <a href="{{ url('/cgu') }}" class="text-oreina-green hover:underline">conditions d'utilisation</a>
            et la <a href="{{ url('/confidentialite') }}" class="text-oreina-green hover:underline">politique de confidentialité</a>.
        </p>
    </div>
</div>
@endsection
```

**Note** : vérifier que `layouts.guest` existe. Sinon utiliser `layouts.app` ou un layout vide existant. Run:
`find resources/views/layouts -name "*.blade.php"` pour voir les layouts dispos.

Si `layouts.guest` n'existe pas, remplacer `@extends('layouts.guest')` par `@extends('layouts.app')` (ou layout équivalent présent dans le repo).

- [ ] **Step 7: Lancer les tests — ils passent**

Run: `php artisan test --filter=ClaimAccountTest`
Expected: PASS (7 tests)

- [ ] **Step 8: Lancer aussi le test AccountInvitation (dépendait de `account.claim`)**

Run: `php artisan test --filter=AccountInvitationTest`
Expected: PASS

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/Auth/ClaimAccountController.php \
        resources/views/auth/claim-account.blade.php \
        routes/web.php \
        tests/Feature/Auth/ClaimAccountTest.php
git commit -m "feat(auth): claim account flow via signed URL + tests"
```

---

## Task 7: `SubmissionCreationService`

**Files:**
- Create: `app/Services/SubmissionCreationService.php`
- Test: `tests/Unit/Services/SubmissionCreationServiceTest.php`

- [ ] **Step 1: Écrire les tests**

Créer `tests/Unit/Services/SubmissionCreationServiceTest.php` :

```php
<?php

namespace Tests\Unit\Services;

use App\Enums\SubmissionStatus;
use App\Mail\AccountInvitation;
use App\Models\Submission;
use App\Models\User;
use App\Services\SubmissionCreationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SubmissionCreationServiceTest extends TestCase
{
    use RefreshDatabase;

    private function data(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Un papier test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
        ], $overrides);
    }

    public function test_create_for_existing_author_persists_submission(): void
    {
        Mail::fake();
        $author = User::factory()->create();
        $editor = User::factory()->create();

        $service = app(SubmissionCreationService::class);
        $sub = $service->createForExistingAuthor($author, $this->data(), $editor);

        $this->assertEquals($author->id, $sub->author_id);
        $this->assertEquals($editor->id, $sub->submitted_by_user_id);
        $this->assertEquals(SubmissionStatus::Submitted, $sub->status);
        $this->assertNotNull($sub->submitted_at);
    }

    public function test_create_for_existing_author_sets_submitted_by_null_when_author_creates_self(): void
    {
        Mail::fake();
        $author = User::factory()->create();

        $service = app(SubmissionCreationService::class);
        $sub = $service->createForExistingAuthor($author, $this->data(), $author);

        $this->assertNull($sub->submitted_by_user_id);
    }

    public function test_create_for_new_author_creates_ghost_user_and_sends_invitation(): void
    {
        Mail::fake();
        $editor = User::factory()->create();

        $service = app(SubmissionCreationService::class);
        $sub = $service->createForNewAuthor(
            'Jean Nouveau',
            'jean@example.com',
            $this->data(),
            $editor,
        );

        $author = User::where('email', 'jean@example.com')->first();
        $this->assertNotNull($author);
        $this->assertTrue($author->isGhost());
        $this->assertEquals($editor->id, $author->invited_by_user_id);
        $this->assertEquals($author->id, $sub->author_id);
        $this->assertEquals($editor->id, $sub->submitted_by_user_id);

        Mail::assertQueued(AccountInvitation::class, function ($mail) use ($author) {
            return $mail->hasTo($author->email);
        });
    }

    public function test_create_for_new_author_is_atomic_on_mail_failure(): void
    {
        // On simule un échec mail en bindant un mailer qui throw
        $this->app->bind(\Illuminate\Contracts\Mail\Mailer::class, function () {
            return new class implements \Illuminate\Contracts\Mail\Mailer {
                public function to($users, $name = null) { throw new \RuntimeException('mail down'); }
                public function bcc($users, $name = null) { return $this; }
                public function cc($users, $name = null) { return $this; }
                public function raw($text, $callback) {}
                public function send($view, array $data = [], $callback = null) {}
                public function sendNow($mailable, array $data = [], $callback = null) {}
                public function queue($view, $queue = null) {}
                public function later($delay, $view, array $data = [], $callback = null) {}
                public function mailer($name = null) { return $this; }
            };
        });

        $editor = User::factory()->create();
        $service = app(SubmissionCreationService::class);

        try {
            $service->createForNewAuthor('Jean', 'jean@example.com', $this->data(), $editor);
            $this->fail('Exception attendue');
        } catch (\RuntimeException $e) {
            // OK
        }

        $this->assertDatabaseMissing('users', ['email' => 'jean@example.com']);
        $this->assertDatabaseMissing('submissions', ['title' => 'Un papier test']);
    }
}
```

- [ ] **Step 2: Lancer les tests → FAIL attendu**

Run: `php artisan test --filter=SubmissionCreationServiceTest`
Expected: FAIL « Class App\Services\SubmissionCreationService not found »

- [ ] **Step 3: Créer le service**

Créer `app/Services/SubmissionCreationService.php` :

```php
<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Mail\AccountInvitation;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\DB;

class SubmissionCreationService
{
    public function __construct(
        private Mailer $mailer,
    ) {}

    public function createForExistingAuthor(
        User $author,
        array $data,
        User $submittedBy,
    ): Submission {
        return DB::transaction(function () use ($author, $data, $submittedBy) {
            return Submission::create(array_merge($data, [
                'author_id' => $author->id,
                'submitted_by_user_id' => $submittedBy->id === $author->id
                    ? null
                    : $submittedBy->id,
                'status' => SubmissionStatus::Submitted,
                'submitted_at' => now(),
            ]));
        });
    }

    public function createForNewAuthor(
        string $name,
        string $email,
        array $data,
        User $submittedBy,
    ): Submission {
        return DB::transaction(function () use ($name, $email, $data, $submittedBy) {
            $author = User::create([
                'name' => $name,
                'email' => $email,
                'password' => null,
                'invited_at' => now(),
                'invited_by_user_id' => $submittedBy->id,
            ]);

            $submission = Submission::create(array_merge($data, [
                'author_id' => $author->id,
                'submitted_by_user_id' => $submittedBy->id,
                'status' => SubmissionStatus::Submitted,
                'submitted_at' => now(),
            ]));

            $this->mailer
                ->to($author->email)
                ->queue(new AccountInvitation($author, $submission, $submittedBy));

            return $submission;
        });
    }
}
```

- [ ] **Step 4: Lancer les tests — ils passent**

Run: `php artisan test --filter=SubmissionCreationServiceTest`
Expected: PASS (4 tests)

- [ ] **Step 5: Commit**

```bash
git add app/Services/SubmissionCreationService.php tests/Unit/Services/SubmissionCreationServiceTest.php
git commit -m "feat(submissions): SubmissionCreationService for existing + new authors"
```

---

## Task 8: Gate `create-submission-for-author`

**Files:**
- Modify: `app/Providers/AppServiceProvider.php:23-38` (méthode `boot`)

- [ ] **Step 1: Ajouter la définition du gate**

Dans `app/Providers/AppServiceProvider.php`, méthode `boot()`, ajouter après la policy `Submission` (ligne 30) :

```php
        \Illuminate\Support\Facades\Gate::define('create-submission-for-author', function (\App\Models\User $user) {
            return $user->hasCapability(\App\Models\EditorialCapability::CHIEF_EDITOR)
                || $user->hasCapability(\App\Models\EditorialCapability::EDITOR);
        });
```

Le contexte complet du `boot()` doit ressembler à :

```php
    public function boot(): void
    {
        View::composer('layouts.member', MemberLayoutComposer::class);

        \Illuminate\Support\Facades\Gate::policy(
            \App\Models\Submission::class,
            \App\Policies\SubmissionPolicy::class
        );

        \Illuminate\Support\Facades\Gate::define('create-submission-for-author', function (\App\Models\User $user) {
            return $user->hasCapability(\App\Models\EditorialCapability::CHIEF_EDITOR)
                || $user->hasCapability(\App\Models\EditorialCapability::EDITOR);
        });

        \Illuminate\Support\Facades\Blade::directive('turnstile', function () {
            // ... code existant inchangé
        });
    }
```

- [ ] **Step 2: Test rapide manuel via Tinker**

Run: `php artisan tinker`

Dans tinker :
```php
$u = \App\Models\User::factory()->create();
\Illuminate\Support\Facades\Gate::forUser($u)->allows('create-submission-for-author'); // false
$u->capabilities()->create(['capability' => \App\Models\EditorialCapability::EDITOR, 'granted_at' => now()]);
\Illuminate\Support\Facades\Gate::forUser($u)->allows('create-submission-for-author'); // true
exit
```

Expected : false puis true.

- [ ] **Step 3: Commit**

```bash
git add app/Providers/AppServiceProvider.php
git commit -m "feat(auth): gate create-submission-for-author (chief/editor only)"
```

---

## Task 9: Modifier `_form.blade.php` — radio toggle auteur

**Files:**
- Modify: `resources/views/admin/submissions/_form.blade.php:163-174`

- [ ] **Step 1: Remplacer le bloc auteur**

Ouvrir `resources/views/admin/submissions/_form.blade.php`. Remplacer le bloc lignes 163-174 (celui qui commence par `<div class="form-group">` contenant `<label>Auteur *</label>`) par :

```blade
        <div class="form-group" x-data="{ mode: '{{ old('author_mode', isset($submission) ? 'existing' : 'existing') }}' }">
            <label class="form-label">Auteur *</label>

            @if(!isset($submission))
                <div style="display: flex; gap: 1rem; margin-bottom: 0.75rem; font-size: 0.875rem;">
                    <label style="display: flex; align-items: center; gap: 0.25rem; cursor: pointer;">
                        <input type="radio" name="author_mode" value="existing" x-model="mode"> Auteur existant
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.25rem; cursor: pointer;">
                        <input type="radio" name="author_mode" value="new" x-model="mode"> Nouvel auteur
                    </label>
                </div>
            @else
                <input type="hidden" name="author_mode" value="existing">
            @endif

            <div x-show="mode === 'existing'">
                <select name="author_id" id="author_id" class="form-input" x-bind:required="mode === 'existing'">
                    <option value="">-- Selectionner --</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}" {{ old('author_id', $submission->author_id ?? '') == $author->id ? 'selected' : '' }}>
                            {{ $author->name }}{{ $author->isGhost() ? ' (compte non activé)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('author_id')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div x-show="mode === 'new'" x-cloak>
                <input type="text" name="author_name" id="author_name"
                       class="form-input" placeholder="Nom complet"
                       value="{{ old('author_name') }}"
                       x-bind:required="mode === 'new'">
                @error('author_name')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror

                <input type="email" name="author_email" id="author_email"
                       class="form-input" placeholder="Email"
                       value="{{ old('author_email') }}"
                       style="margin-top: 0.5rem;"
                       x-bind:required="mode === 'new'">
                @error('author_email')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror

                <p style="font-size: 0.8rem; color: #6b7280; margin-top: 0.5rem;">
                    Un compte sera créé pour l'auteur. Une invitation lui sera envoyée à cette adresse pour activer son accès.
                </p>
            </div>
        </div>
```

- [ ] **Step 2: Vérifier qu'Alpine est chargé dans le layout admin**

Run: `grep -r "alpinejs\|defer.*alpine" resources/views/layouts/admin*`

Si Alpine n'est pas chargé : ajouter dans `resources/views/layouts/admin.blade.php` juste avant `</head>` :
```html
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
```
Si Alpine est déjà chargé (probable vu les autres templates) : rien à faire.

- [ ] **Step 3: Ajouter `x-cloak` dans le CSS global (si pas déjà présent)**

Run: `grep -r "x-cloak" resources/views/layouts/ resources/css/`

Si absent, ajouter dans `resources/views/layouts/admin.blade.php` dans le `<head>` :
```html
<style>[x-cloak] { display: none !important; }</style>
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/admin/submissions/_form.blade.php
git commit -m "feat(admin): radio toggle existing/new author in submission form"
```

---

## Task 10: Modifier `SubmissionController::store()` — validation + délégation service

**Files:**
- Modify: `app/Http/Controllers/Admin/SubmissionController.php:75-135`

- [ ] **Step 1: Ajouter authorize + injection service dans `create()` et `store()`**

Ouvrir `app/Http/Controllers/Admin/SubmissionController.php`. Modifier `create()` (ligne 75) — ajouter `$this->authorize(...)` au début :

```php
    public function create(Request $request)
    {
        $this->authorize('create-submission-for-author');

        $authors = User::orderBy('name')->get();
        $issues = JournalIssue::orderBy('volume_number', 'desc')
            ->orderBy('issue_number', 'desc')
            ->get();

        $selectedIssue = $request->get('journal_issue_id');

        return view('admin.submissions.create', compact('authors', 'issues', 'selectedIssue'));
    }
```

- [ ] **Step 2: Remplacer la méthode `store()` existante (ligne 87-135)**

Remplacer **intégralement** la méthode `store()` par :

```php
    public function store(Request $request, \App\Services\SubmissionCreationService $creation)
    {
        $this->authorize('create-submission-for-author');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'nullable|string',
            'keywords' => 'nullable|string|max:500',
            'author_mode' => 'required|in:existing,new',
            'author_id' => 'required_if:author_mode,existing|nullable|exists:users,id',
            'author_name' => 'required_if:author_mode,new|nullable|string|max:255',
            'author_email' => [
                'required_if:author_mode,new',
                'nullable',
                'email',
                \Illuminate\Validation\Rule::unique('users', 'email'),
            ],
            'journal_issue_id' => 'nullable|exists:journal_issues,id',
            'editor_id' => 'nullable|exists:users,id',
            'editor_notes' => 'nullable|string',
            'doi' => 'nullable|string|max:255',
            'start_page' => 'nullable|integer|min:1',
            'end_page' => 'nullable|integer|min:1',
            'manuscript_file' => 'required|file|mimes:doc,docx,pdf,odt|max:30720',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
            'featured_image' => 'nullable|image|max:5120',
        ], [
            'author_email.unique' => 'Un compte existe déjà pour cet email. Sélectionnez « Auteur existant » dans la liste déroulante.',
        ]);

        // Handle file uploads
        $data = collect($validated)->only([
            'title', 'abstract', 'keywords', 'journal_issue_id',
            'editor_id', 'editor_notes', 'doi', 'start_page', 'end_page',
        ])->toArray();

        $data['manuscript_file'] = $request->file('manuscript_file')
            ->store('submissions/manuscripts', 'public');

        if ($request->hasFile('pdf_file')) {
            $data['pdf_file'] = $request->file('pdf_file')
                ->store('submissions/pdfs', 'public');
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')
                ->store('submissions/images', 'public');
        }

        $submittedBy = $request->user();

        if ($validated['author_mode'] === 'existing') {
            $author = User::findOrFail($validated['author_id']);
            $submission = $creation->createForExistingAuthor($author, $data, $submittedBy);
        } else {
            $submission = $creation->createForNewAuthor(
                $validated['author_name'],
                $validated['author_email'],
                $data,
                $submittedBy,
            );
        }

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('success', 'Soumission creee avec succes.');
    }
```

- [ ] **Step 3: Lancer les tests existants pour vérifier zéro régression**

Run: `php artisan test --filter=SubmissionTransitionRouteTest`
Run: `php artisan test --filter=EditorialShowTest`
Expected: tous passent (aucune régression sur l'existant).

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Admin/SubmissionController.php
git commit -m "feat(admin): delegate submission creation to service, support new author mode"
```

---

## Task 11: Masquer le bouton « Nouvelle soumission » si pas de permission

**Files:**
- Modify: `resources/views/admin/submissions/index.blade.php`

- [ ] **Step 1: Repérer le bouton "Nouvelle soumission"**

Run: `grep -n "submissions.create" resources/views/admin/submissions/index.blade.php`
Expected : 1 ou 2 matches avec un `href` ou un `route(...)`.

- [ ] **Step 2: Entourer le(s) bouton(s) avec `@can`**

Remplacer le bloc qui contient l'anchor/bouton vers `admin.submissions.create` par :

```blade
@can('create-submission-for-author')
    {{-- bloc existant du bouton inchangé --}}
@endcan
```

(Sans modifier le HTML interne du bouton — juste l'entourer du `@can`/`@endcan`.)

- [ ] **Step 3: Test manuel via Tinker + curl**

Run: `php artisan serve` en tâche de fond.

Depuis un shell séparé, se logger en session web (ou utiliser un test feature — voir Task 12). Pour ce step, on se contente d'un smoke test manuel :

1. Connexion en admin sans capability éditoriale → vérifier que le bouton est absent sur `/extranet/submissions`
2. Grant EDITOR capability via Tinker : `\App\Models\User::find(1)->capabilities()->create(['capability' => 'editor', 'granted_at' => now()])`
3. Reload → le bouton apparaît.

- [ ] **Step 4: Commit**

```bash
git add resources/views/admin/submissions/index.blade.php
git commit -m "feat(admin): hide submission create button without editorial capability"
```

---

## Task 12: Tests feature — BackofficeSubmission end-to-end

**Files:**
- Create: `tests/Feature/Admin/BackofficeSubmissionTest.php`

- [ ] **Step 1: Écrire les tests feature**

Créer `tests/Feature/Admin/BackofficeSubmissionTest.php` :

```php
<?php

namespace Tests\Feature\Admin;

use App\Mail\AccountInvitation;
use App\Models\EditorialCapability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackofficeSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private function makeEditor(string $capability = EditorialCapability::EDITOR): User
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user->capabilities()->create(['capability' => $capability, 'granted_at' => now()]);
        return $user;
    }

    private function basePayload(): array
    {
        return [
            'title' => 'Un article sur les Chersotis',
            'abstract' => str_repeat('x', 150),
            'keywords' => 'chersotis, noctuidae',
            'manuscript_file' => UploadedFile::fake()->create('manuscrit.docx', 500),
        ];
    }

    public function test_chief_editor_can_create_submission_for_existing_author(): void
    {
        Storage::fake('public');
        Mail::fake();

        $chief = $this->makeEditor(EditorialCapability::CHIEF_EDITOR);
        $author = User::factory()->create();

        $response = $this->actingAs($chief)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            ['author_mode' => 'existing', 'author_id' => $author->id],
        ));

        $response->assertRedirect();
        $this->assertDatabaseHas('submissions', [
            'author_id' => $author->id,
            'submitted_by_user_id' => $chief->id,
            'title' => 'Un article sur les Chersotis',
        ]);
        Mail::assertNothingQueued(); // pas d'invitation quand auteur existe
    }

    public function test_editor_can_create_submission_for_new_author(): void
    {
        Storage::fake('public');
        Mail::fake();

        $editor = $this->makeEditor(EditorialCapability::EDITOR);

        $response = $this->actingAs($editor)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            [
                'author_mode' => 'new',
                'author_name' => 'Jean Nouveau',
                'author_email' => 'jean@example.com',
            ],
        ));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'jean@example.com',
            'name' => 'Jean Nouveau',
            'invited_by_user_id' => $editor->id,
        ]);

        $author = User::where('email', 'jean@example.com')->first();
        $this->assertTrue($author->isGhost());

        $this->assertDatabaseHas('submissions', [
            'author_id' => $author->id,
            'submitted_by_user_id' => $editor->id,
            'status' => 'submitted',
        ]);

        Mail::assertQueued(AccountInvitation::class, fn ($m) => $m->hasTo('jean@example.com'));
    }

    public function test_user_without_editorial_capability_gets_403(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]); // admin mais sans capability
        $author = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            ['author_mode' => 'existing', 'author_id' => $author->id],
        ));

        $response->assertForbidden();
    }

    public function test_get_create_without_capability_gets_403(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($user)->get(route('admin.submissions.create'));

        $response->assertForbidden();
    }

    public function test_duplicate_email_in_new_author_mode_fails_validation(): void
    {
        Storage::fake('public');
        $editor = $this->makeEditor();
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($editor)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            [
                'author_mode' => 'new',
                'author_name' => 'Existing Dude',
                'author_email' => 'existing@example.com',
            ],
        ));

        $response->assertSessionHasErrors('author_email');
    }

    public function test_missing_manuscript_fails_validation(): void
    {
        Storage::fake('public');
        $editor = $this->makeEditor();
        $author = User::factory()->create();

        $payload = $this->basePayload();
        unset($payload['manuscript_file']);

        $response = $this->actingAs($editor)->post(
            route('admin.submissions.store'),
            array_merge($payload, ['author_mode' => 'existing', 'author_id' => $author->id])
        );

        $response->assertSessionHasErrors('manuscript_file');
    }

    public function test_new_author_mode_without_name_fails_validation(): void
    {
        Storage::fake('public');
        $editor = $this->makeEditor();

        $response = $this->actingAs($editor)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            ['author_mode' => 'new', 'author_email' => 'x@example.com'],
        ));

        $response->assertSessionHasErrors('author_name');
    }

    public function test_existing_mode_without_author_id_fails_validation(): void
    {
        Storage::fake('public');
        $editor = $this->makeEditor();

        $response = $this->actingAs($editor)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            ['author_mode' => 'existing'],
        ));

        $response->assertSessionHasErrors('author_id');
    }

    public function test_author_creating_own_submission_via_admin_gets_null_submitted_by(): void
    {
        Storage::fake('public');
        Mail::fake();

        // Hypothétique cas : un chief_editor soumet SA propre soumission depuis le backoffice
        $chief = $this->makeEditor(EditorialCapability::CHIEF_EDITOR);

        $this->actingAs($chief)->post(route('admin.submissions.store'), array_merge(
            $this->basePayload(),
            ['author_mode' => 'existing', 'author_id' => $chief->id],
        ));

        $this->assertDatabaseHas('submissions', [
            'author_id' => $chief->id,
            'submitted_by_user_id' => null,
        ]);
    }
}
```

- [ ] **Step 2: Lancer les tests**

Run: `php artisan test --filter=BackofficeSubmissionTest`
Expected: PASS (9 tests)

Si un test échoue sur « `admin` middleware », vérifier comment l'authentification admin est gérée dans les tests existants (voir `tests/Feature/Admin/EditorialShowTest.php` pour le pattern) et ajuster le `role` ou les claims en conséquence.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/Admin/BackofficeSubmissionTest.php
git commit -m "test(admin): backoffice submission creation end-to-end"
```

---

## Task 13: Smoke test manuel + commit final

- [ ] **Step 1: Lancer la suite complète**

Run: `php artisan test`
Expected: tous les tests passent (anciens + nouveaux).

- [ ] **Step 2: Smoke test manuel du flow complet**

1. Run: `php artisan serve` + `php artisan queue:work` (si queue pas sync)
2. Dans le navigateur, se connecter en admin avec EDITOR capability
3. Aller sur `/extranet/submissions/create`
4. Vérifier que le radio "Nouvel auteur" s'affiche et bascule les champs
5. Remplir nom/email d'un auteur fictif + titre + upload manuscrit → Submit
6. Vérifier :
    - Redirection sur la page show de la soumission
    - User ghost créé en base (`select id, name, email, invited_at, claimed_at, password from users where email = 'le-mail-saisi'`)
    - Mail d'invitation présent dans Mailpit/log mail
7. Cliquer sur le lien « Activer mon compte » dans le mail
8. Vérifier :
    - Form de claim s'affiche avec nom + email corrects
    - Soumettre password + confirmation → redirection vers `/mes-soumissions`
    - La soumission apparaît bien dans la liste
9. Se déconnecter, retester le lien d'invitation → doit rediriger vers login avec message « Déjà activé »

- [ ] **Step 3: Vérifier la régression sur l'existant**

1. Connexion en tant qu'auteur normal → créer une soumission via `/mes-soumissions/nouvelle` → OK
2. Vérifier qu'un admin non-editor reçoit un 403 sur `/extranet/submissions/create`
3. Vérifier que le bouton « Nouvelle soumission » est masqué pour un admin non-editor
4. Vérifier que le workflow P0 d'approbation auteur fonctionne toujours (aller jusqu'à `awaiting_author_approval` sur une soumission existante)

- [ ] **Step 4: Commit final si des ajustements ont été faits**

```bash
git status
# si rien à commit → rien à faire
# sinon :
git add .
git commit -m "chore(chersotis): fix smoke test findings on backoffice submission flow"
```

- [ ] **Step 5: Mettre à jour la mémoire du projet**

Mettre à jour `C:\Users\ddemerges\.claude\projects\C--xampp-htdocs-oreina-plateforme\memory\project_chersotis_p0.md` pour marquer l'item P1 #1 comme livré, et pointer vers ce plan.

---

## Critères de succès (rappel du spec §10)

- [ ] Un éditeur peut saisir un article d'un auteur sans compte en moins d'1 minute (smoke test ci-dessus)
- [ ] L'auteur reçoit un mail, clique, définit son mot de passe, arrive sur son tableau de bord — moins d'1 minute
- [ ] Tous les flux existants (timeline, notifications, peer review, approbation auteur P0) fonctionnent sans modification sur une soumission créée par le backoffice
- [ ] Les 7-8 articles en transition peuvent être saisis sans créer manuellement les comptes auteurs
- [ ] Aucune régression sur le formulaire de création de soumission pour un auteur existant
- [ ] Suite de tests complète passe (unit + feature)
