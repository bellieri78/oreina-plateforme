# Chersotis — Rejet avec recommandation Lepis — Plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ajouter un statut intermédiaire `rejected_pending_lepis` invisible à l'auteur quand un éditeur rejette un manuscrit avec recommandation pour le bulletin Lepis, et une page admin dédiée pour décider (accepter → `redirected_to_lepis` avec mail dédié, ou rejeter définitivement → flow de rejet standard).

**Architecture:** Extension de l'enum `SubmissionStatus` (2 cases) + state machine (3 nouveaux arcs + logique mail/timestamp) + page admin dédiée + helper `publicStatus()` pour cacher le statut à l'auteur. Pas de nouveau rôle : `Gate::isAdmin()` couvre l'accès à la file Lepis jusqu'à la revue des droits.

**Tech Stack:** Laravel 12, Blade, Alpine, PostgreSQL, Mail (Markdown template).

**Spec source:** `docs/superpowers/specs/2026-04-18-chersotis-rejet-lepis-design.md`

---

## File Structure

**New files**
- `database/migrations/2026_04_18_140000_add_lepis_decision_fields_to_submissions.php`
- `app/Mail/ArticleRedirectedToLepis.php`
- `app/Mail/LepisQueueNotification.php`
- `resources/views/emails/article-redirected-to-lepis.blade.php`
- `resources/views/emails/lepis-queue-notification.blade.php`
- `app/Http/Controllers/Admin/Journal/LepisQueueController.php`
- `resources/views/admin/journal/lepis-queue.blade.php`
- `tests/Unit/Models/SubmissionPublicStatusTest.php`
- `tests/Unit/Services/SubmissionStateMachineLepisTest.php`
- `tests/Feature/Journal/LepisQueueTest.php`

**Files to modify**
- `app/Enums/SubmissionStatus.php` — 2 cases + labels + colors + isTerminal
- `app/Models/Submission.php` — fillable, casts, relation `lepisDecidedBy`, helper `publicStatus()`
- `app/Services/SubmissionStateMachine.php` — TRANSITIONS map + transition() logic (mail suppression, timestamps, admin notification)
- `app/Http/Controllers/Admin/Journal/EditorialQueueController.php` — route `Rejected + checkbox` vers `RejectedPendingLepis`
- `app/Policies/SubmissionPolicy.php` — règle pour transitions sortantes de `RejectedPendingLepis` (admin only)
- `app/Providers/AppServiceProvider.php` — Gate `access-lepis-queue`
- `routes/admin.php` — route `file-lepis`
- `resources/views/admin/submissions/show.blade.php` — bandeau info si `RejectedPendingLepis`
- `resources/views/journal/submissions/index.blade.php` — badge via `publicStatus()`
- `resources/views/journal/submissions/show.blade.php` — badge via `publicStatus()`
- `resources/views/layouts/admin.blade.php` — entrée nav « File Lepis » avec badge numérique

---

## Task 1: Migration lepis_decision_fields

**Files:**
- Create: `database/migrations/2026_04_18_140000_add_lepis_decision_fields_to_submissions.php`

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
            $table->timestamp('lepis_decision_at')->nullable()->after('redirected_to_lepis');
            $table->foreignId('lepis_decided_by_user_id')->nullable()
                  ->after('lepis_decision_at')
                  ->constrained('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['lepis_decided_by_user_id']);
            $table->dropColumn(['lepis_decision_at', 'lepis_decided_by_user_id']);
        });
    }
};
```

- [ ] **Step 2: Exécuter la migration**

Run: `php artisan migrate`
Expected: « Migrated: 2026_04_18_140000_add_lepis_decision_fields_to_submissions »

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_04_18_140000_add_lepis_decision_fields_to_submissions.php
git commit -m "feat(db): add lepis_decision_at + lepis_decided_by_user_id to submissions"
```

IMPORTANT: DO NOT add `Co-Authored-By: Claude` in commit messages.

---

## Task 2: Enum SubmissionStatus — 2 nouveaux cases

**Files:**
- Modify: `app/Enums/SubmissionStatus.php`

- [ ] **Step 1: Ajouter les cases**

Remplacer le bloc `case` existant (lignes 7-16) par :

```php
    case Submitted           = 'submitted';
    case UnderInitialReview  = 'under_initial_review';
    case RevisionRequested   = 'revision_requested';
    case UnderPeerReview     = 'under_peer_review';
    case RevisionAfterReview = 'revision_after_review';
    case Accepted                 = 'accepted';
    case InProduction             = 'in_production';
    case AwaitingAuthorApproval   = 'awaiting_author_approval';
    case Published                = 'published';
    case Rejected                 = 'rejected';
    case RejectedPendingLepis     = 'rejected_pending_lepis';
    case RedirectedToLepis        = 'redirected_to_lepis';
```

- [ ] **Step 2: Mettre à jour `label()`**

Dans la méthode `label()`, ajouter les 2 nouveaux cas :

```php
    public function label(): string
    {
        return match ($this) {
            self::Submitted           => 'Soumis',
            self::UnderInitialReview  => 'Évaluation initiale',
            self::RevisionRequested   => 'Retour auteur (avant relecture)',
            self::UnderPeerReview     => 'En relecture',
            self::RevisionAfterReview => 'Révision demandée (après relecture)',
            self::Accepted                => 'Accepté',
            self::InProduction            => 'En maquettage',
            self::AwaitingAuthorApproval  => 'En attente d\'approbation auteur',
            self::Published               => 'Publié',
            self::Rejected                => 'Rejeté',
            self::RejectedPendingLepis    => 'Rejet en attente Lepis',
            self::RedirectedToLepis       => 'Transmis au bulletin Lepis',
        };
    }
```

- [ ] **Step 3: Mettre à jour `color()`**

```php
    public function color(): string
    {
        return match ($this) {
            self::Submitted           => 'blue',
            self::UnderInitialReview  => 'amber',
            self::RevisionRequested   => 'orange',
            self::UnderPeerReview     => 'indigo',
            self::RevisionAfterReview => 'orange',
            self::Accepted                => 'green',
            self::InProduction            => 'teal',
            self::AwaitingAuthorApproval  => 'purple',
            self::Published               => 'emerald',
            self::Rejected                => 'red',
            self::RejectedPendingLepis    => 'amber',
            self::RedirectedToLepis       => 'teal',
        };
    }
```

- [ ] **Step 4: Mettre à jour `isTerminal()`**

```php
    public function isTerminal(): bool
    {
        return in_array($this, [self::Published, self::Rejected, self::RedirectedToLepis], true);
    }
```

- [ ] **Step 5: Vérifier la suite de tests ne régresse pas**

Run: `php artisan test`
Expected: 260 tests passent (aucune régression — les nouveaux cases ne sont pas encore utilisés).

- [ ] **Step 6: Commit**

```bash
git add app/Enums/SubmissionStatus.php
git commit -m "feat(submission): add RejectedPendingLepis + RedirectedToLepis enum cases"
```

---

## Task 3: Submission model — fillable + relation + casts

**Files:**
- Modify: `app/Models/Submission.php`

- [ ] **Step 1: Ajouter les champs au fillable**

Dans `$fillable`, ajouter `'lepis_decision_at'` et `'lepis_decided_by_user_id'` juste après `'redirected_to_lepis'` :

Le bloc existant contient `'redirected_to_lepis'` (ligne ~46). Remplacer :

```php
        'redirected_to_lepis',
        'author_approved_at',
```

Par :

```php
        'redirected_to_lepis',
        'lepis_decision_at',
        'lepis_decided_by_user_id',
        'author_approved_at',
```

- [ ] **Step 2: Ajouter le cast datetime**

Dans `$casts`, ajouter après `'author_approval_requested_at' => 'datetime'` :

```php
        'lepis_decision_at' => 'datetime',
```

- [ ] **Step 3: Ajouter la relation `lepisDecidedBy()`**

Après la méthode `layoutEditor()` (ou après `submittedBy()` selon l'ordre actuel), ajouter :

```php
    public function lepisDecidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lepis_decided_by_user_id');
    }
```

- [ ] **Step 4: Vérifier non-régression**

Run: `php artisan test`
Expected: 260 tests passent.

- [ ] **Step 5: Commit**

```bash
git add app/Models/Submission.php
git commit -m "feat(submission): fillable + casts + lepisDecidedBy relation"
```

---

## Task 4: Helper `publicStatus()` sur Submission + tests

**Files:**
- Modify: `app/Models/Submission.php`
- Create: `tests/Unit/Models/SubmissionPublicStatusTest.php`

- [ ] **Step 1: Écrire le test**

Créer `tests/Unit/Models/SubmissionPublicStatusTest.php` :

```php
<?php

namespace Tests\Unit\Models;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\SubmissionTransition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionPublicStatusTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(SubmissionStatus $status, User $author): Submission
    {
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => $status,
        ]);
    }

    private function logTransition(Submission $submission, string $to, User $actor): void
    {
        $submission->transitions()->create([
            'action' => SubmissionTransition::ACTION_STATUS_CHANGED,
            'actor_id' => $actor->id,
            'from_status' => null,
            'to_status' => $to,
            'notes' => null,
        ]);
    }

    public function test_public_status_returns_direct_status_if_not_lepis_pending(): void
    {
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::UnderInitialReview, $author);

        $this->assertEquals(SubmissionStatus::UnderInitialReview, $sub->publicStatus());
    }

    public function test_public_status_returns_last_public_status_when_rejected_pending_lepis(): void
    {
        $author = User::factory()->create();
        $editor = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        // Log transitions dans l'ordre : submitted → under_initial_review → under_peer_review → rejected_pending_lepis
        $this->logTransition($sub, SubmissionStatus::UnderInitialReview->value, $editor);
        $this->logTransition($sub, SubmissionStatus::UnderPeerReview->value, $editor);
        $this->logTransition($sub, SubmissionStatus::RejectedPendingLepis->value, $editor);

        // publicStatus doit retourner UnderPeerReview (la dernière transition publique, pas la lepis)
        $this->assertEquals(SubmissionStatus::UnderPeerReview, $sub->publicStatus());
    }

    public function test_public_status_fallback_to_under_initial_review_if_no_transitions(): void
    {
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        // Aucune transition log — fallback
        $this->assertEquals(SubmissionStatus::UnderInitialReview, $sub->publicStatus());
    }

    public function test_public_status_ignores_transition_to_rejected_pending_lepis_itself(): void
    {
        $author = User::factory()->create();
        $editor = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        // Uniquement la transition vers RejectedPendingLepis (pas de public status avant)
        $this->logTransition($sub, SubmissionStatus::RejectedPendingLepis->value, $editor);

        $this->assertEquals(SubmissionStatus::UnderInitialReview, $sub->publicStatus());
    }
}
```

- [ ] **Step 2: Lancer les tests — ils doivent échouer**

Run: `php artisan test --filter=SubmissionPublicStatusTest`
Expected: FAIL (méthode publicStatus non définie).

- [ ] **Step 3: Ajouter le helper `publicStatus()` sur Submission**

Dans `app/Models/Submission.php`, ajouter (par exemple après la relation `lepisDecidedBy()`) :

```php
    public function publicStatus(): SubmissionStatus
    {
        if ($this->status !== SubmissionStatus::RejectedPendingLepis) {
            return $this->status instanceof SubmissionStatus
                ? $this->status
                : SubmissionStatus::from($this->status);
        }

        $lastPublic = $this->transitions()
            ->where('action', SubmissionTransition::ACTION_STATUS_CHANGED)
            ->where('to_status', '!=', SubmissionStatus::RejectedPendingLepis->value)
            ->whereNotNull('to_status')
            ->orderByDesc('created_at')
            ->first();

        return $lastPublic?->to_status
            ? SubmissionStatus::from($lastPublic->to_status)
            : SubmissionStatus::UnderInitialReview;
    }
```

**Note** : ajouter l'import de `SubmissionTransition` en tête du fichier si pas déjà présent : `use App\Models\SubmissionTransition;`.

- [ ] **Step 4: Lancer les tests — ils passent**

Run: `php artisan test --filter=SubmissionPublicStatusTest`
Expected: PASS (4 tests)

- [ ] **Step 5: Commit**

```bash
git add app/Models/Submission.php tests/Unit/Models/SubmissionPublicStatusTest.php
git commit -m "feat(submission): publicStatus() helper to hide Lepis pending status from authors"
```

---

## Task 5: State machine — transitions + tests unit

**Files:**
- Modify: `app/Services/SubmissionStateMachine.php` (TRANSITIONS map uniquement pour cette tâche — la logique mail vient en Task 7)
- Create: `tests/Unit/Services/SubmissionStateMachineLepisTest.php`

- [ ] **Step 1: Écrire les tests**

Créer `tests/Unit/Services/SubmissionStateMachineLepisTest.php` :

```php
<?php

namespace Tests\Unit\Services;

use App\Enums\SubmissionStatus;
use App\Services\SubmissionStateMachine;
use App\Services\SubmissionTransitionLogger;
use Tests\TestCase;

class SubmissionStateMachineLepisTest extends TestCase
{
    private SubmissionStateMachine $sm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sm = new SubmissionStateMachine(app(SubmissionTransitionLogger::class));
    }

    public function test_under_initial_review_to_rejected_pending_lepis_allowed(): void
    {
        $this->assertTrue($this->sm->canTransition(
            SubmissionStatus::UnderInitialReview,
            SubmissionStatus::RejectedPendingLepis
        ));
    }

    public function test_under_peer_review_to_rejected_pending_lepis_allowed(): void
    {
        $this->assertTrue($this->sm->canTransition(
            SubmissionStatus::UnderPeerReview,
            SubmissionStatus::RejectedPendingLepis
        ));
    }

    public function test_revision_after_review_to_rejected_pending_lepis_allowed(): void
    {
        $this->assertTrue($this->sm->canTransition(
            SubmissionStatus::RevisionAfterReview,
            SubmissionStatus::RejectedPendingLepis
        ));
    }

    public function test_rejected_pending_lepis_to_redirected_to_lepis_allowed(): void
    {
        $this->assertTrue($this->sm->canTransition(
            SubmissionStatus::RejectedPendingLepis,
            SubmissionStatus::RedirectedToLepis
        ));
    }

    public function test_rejected_pending_lepis_to_rejected_allowed(): void
    {
        $this->assertTrue($this->sm->canTransition(
            SubmissionStatus::RejectedPendingLepis,
            SubmissionStatus::Rejected
        ));
    }

    public function test_rejected_pending_lepis_to_accepted_forbidden(): void
    {
        $this->assertFalse($this->sm->canTransition(
            SubmissionStatus::RejectedPendingLepis,
            SubmissionStatus::Accepted
        ));
    }

    public function test_redirected_to_lepis_is_terminal(): void
    {
        $this->assertFalse($this->sm->canTransition(
            SubmissionStatus::RedirectedToLepis,
            SubmissionStatus::Rejected
        ));
        $this->assertFalse($this->sm->canTransition(
            SubmissionStatus::RedirectedToLepis,
            SubmissionStatus::Published
        ));
    }

    public function test_submitted_to_rejected_pending_lepis_forbidden(): void
    {
        // Doit passer par UnderInitialReview d'abord
        $this->assertFalse($this->sm->canTransition(
            SubmissionStatus::Submitted,
            SubmissionStatus::RejectedPendingLepis
        ));
    }
}
```

- [ ] **Step 2: Lancer les tests — ils doivent échouer**

Run: `php artisan test --filter=SubmissionStateMachineLepisTest`
Expected: FAIL (transitions pas encore définies).

- [ ] **Step 3: Modifier la `TRANSITIONS` map**

Dans `app/Services/SubmissionStateMachine.php`, remplacer le bloc `private const TRANSITIONS` par :

```php
    private const TRANSITIONS = [
        'submitted'                    => ['under_initial_review', 'rejected'],
        'under_initial_review'         => ['revision_requested', 'under_peer_review', 'rejected', 'rejected_pending_lepis'],
        'revision_requested'           => ['under_initial_review'],
        'under_peer_review'            => ['revision_after_review', 'accepted', 'rejected', 'rejected_pending_lepis'],
        'revision_after_review'        => ['under_peer_review', 'accepted', 'rejected', 'rejected_pending_lepis'],
        'accepted'                     => ['in_production'],
        'in_production'                => ['awaiting_author_approval'],
        'awaiting_author_approval'     => ['published', 'in_production'],
        'published'                    => [],
        'rejected'                     => [],
        'rejected_pending_lepis'       => ['redirected_to_lepis', 'rejected'],
        'redirected_to_lepis'          => [],
    ];
```

- [ ] **Step 4: Lancer les tests — ils passent**

Run: `php artisan test --filter=SubmissionStateMachineLepisTest`
Expected: PASS (9 tests)

- [ ] **Step 5: Commit**

```bash
git add app/Services/SubmissionStateMachine.php tests/Unit/Services/SubmissionStateMachineLepisTest.php
git commit -m "feat(state-machine): add Lepis transitions (pending, redirected)"
```

---

## Task 6: Mailables ArticleRedirectedToLepis + LepisQueueNotification

**Files:**
- Create: `app/Mail/ArticleRedirectedToLepis.php`
- Create: `app/Mail/LepisQueueNotification.php`
- Create: `resources/views/emails/article-redirected-to-lepis.blade.php`
- Create: `resources/views/emails/lepis-queue-notification.blade.php`

- [ ] **Step 1: Générer les Mailables**

Run: `php artisan make:mail ArticleRedirectedToLepis --markdown=emails.article-redirected-to-lepis`
Run: `php artisan make:mail LepisQueueNotification --markdown=emails.lepis-queue-notification`

- [ ] **Step 2: Implémenter `ArticleRedirectedToLepis`**

Remplacer le contenu de `app/Mail/ArticleRedirectedToLepis.php` par :

```php
<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticleRedirectedToLepis extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre article a été transmis au bulletin Lepis',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.article-redirected-to-lepis',
            with: [
                'submission' => $this->submission,
                'author' => $this->submission->author,
            ],
        );
    }
}
```

- [ ] **Step 3: Écrire le template `article-redirected-to-lepis.blade.php`**

Remplacer `resources/views/emails/article-redirected-to-lepis.blade.php` par :

```blade
<x-mail::message>
# Bonjour {{ $author->name }},

Après examen attentif, votre manuscrit

> *{{ $submission->title }}*

a été jugé mieux adapté au bulletin **Lepis**, publication interne d'OREINA dédiée aux notes courtes, observations de terrain et vulgarisation.

Le rédacteur en chef de Lepis prendra directement contact avec vous dans les prochains jours pour vous proposer la suite : publication dans Lepis sous une forme adaptée, ou éventuels ajustements avant publication.

<x-mail::button :url="config('app.url').'/revue/mes-soumissions'">
Voir mes soumissions
</x-mail::button>

Merci de votre contribution à OREINA.

Cordialement,
L'équipe éditoriale **Chersotis**
{{ config('app.url') }}
</x-mail::message>
```

- [ ] **Step 4: Implémenter `LepisQueueNotification`**

Remplacer le contenu de `app/Mail/LepisQueueNotification.php` par :

```php
<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LepisQueueNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle soumission en file Lepis — Chersotis',
        );
    }

    public function content(): Content
    {
        // On récupère la dernière transition vers RejectedPendingLepis pour extraire notes + éditeur
        $transition = $this->submission->transitions()
            ->where('to_status', 'rejected_pending_lepis')
            ->orderByDesc('created_at')
            ->first();

        return new Content(
            markdown: 'emails.lepis-queue-notification',
            with: [
                'submission' => $this->submission,
                'author' => $this->submission->author,
                'notes' => $transition?->notes,
                'actor' => $transition?->actor,
            ],
        );
    }
}
```

- [ ] **Step 5: Écrire le template `lepis-queue-notification.blade.php`**

Remplacer `resources/views/emails/lepis-queue-notification.blade.php` par :

```blade
<x-mail::message>
# Nouvelle soumission en file Lepis

Une soumission vient d'être proposée pour redirection vers le bulletin **Lepis**. Elle est actuellement en attente de décision (transmission ou rejet définitif).

**Titre :** {{ $submission->title }}
**Auteur :** {{ $author->name }} ({{ $author->email }})
@if($actor)
**Proposé par :** {{ $actor->name }}
@endif

@if($notes)
**Motifs / commentaires :**
> {{ $notes }}
@endif

<x-mail::button :url="config('app.url').'/extranet/revue/file-lepis'">
Voir la file Lepis
</x-mail::button>

**Action attendue :** transmettre à Lepis (l'auteur recevra un message l'informant du transfert) ou rejeter définitivement (l'auteur recevra le mail de rejet avec motifs).

Cordialement,
Plateforme OREINA
</x-mail::message>
```

- [ ] **Step 6: Commit**

```bash
git add app/Mail/ArticleRedirectedToLepis.php app/Mail/LepisQueueNotification.php \
        resources/views/emails/article-redirected-to-lepis.blade.php \
        resources/views/emails/lepis-queue-notification.blade.php
git commit -m "feat(mail): ArticleRedirectedToLepis + LepisQueueNotification mailables"
```

---

## Task 7: State machine — logique mails + timestamps Lepis

**Files:**
- Modify: `app/Services/SubmissionStateMachine.php` (méthode `transition()`)

- [ ] **Step 1: Ajouter les imports nécessaires**

En tête du fichier `app/Services/SubmissionStateMachine.php`, ajouter :

```php
use App\Mail\ArticleRedirectedToLepis;
use App\Mail\LepisQueueNotification;
```

(`SubmissionDecision` et `User` sont déjà importés, `EditorialCapability` aussi.)

- [ ] **Step 2: Ajouter les blocs de logique dans `transition()`**

Dans la méthode `transition()`, **juste après** le bloc existant :

```php
        if (in_array($target, [SubmissionStatus::Accepted, SubmissionStatus::Rejected], true)) {
            $submission->load('author');
            if ($submission->author) {
                Mail::to($submission->author)->queue(new SubmissionDecision($submission));
            }
        }
```

Ajouter ces nouveaux blocs :

```php
        // Entrée en file Lepis : flag historique + notif aux admins/chief_editors
        if ($target === SubmissionStatus::RejectedPendingLepis) {
            $submission->redirected_to_lepis = true;
            $submission->save();

            $admins = User::query()
                ->where('role', User::ROLE_ADMIN)
                ->orWhereHas('capabilities', fn ($q) => $q->where('capability', EditorialCapability::CHIEF_EDITOR))
                ->get()
                ->unique('id');

            foreach ($admins as $admin) {
                Mail::to($admin)->queue(new LepisQueueNotification($submission));
            }
        }

        // Décision Lepis (accepte OU refuse) : timestamp + auteur de la décision
        if ($current === SubmissionStatus::RejectedPendingLepis
            && in_array($target, [SubmissionStatus::RedirectedToLepis, SubmissionStatus::Rejected], true)
        ) {
            $submission->lepis_decision_at = now();
            $submission->lepis_decided_by_user_id = $actor->id;
            $submission->save();
        }

        // Mail à l'auteur quand Lepis accepte (mail dédié, pas SubmissionDecision)
        if ($target === SubmissionStatus::RedirectedToLepis) {
            $submission->load('author');
            if ($submission->author) {
                Mail::to($submission->author)->queue(new ArticleRedirectedToLepis($submission));
            }
        }
```

**Note importante** : le bloc `SubmissionDecision` existant reste inchangé. Il ne déclenche que sur `Accepted`/`Rejected` — donc il part bien sur `RejectedPendingLepis → Rejected` (l'auteur reçoit son mail de rejet quand Lepis refuse). Et il ne part PAS sur `→ RejectedPendingLepis` ni `→ RedirectedToLepis`.

- [ ] **Step 3: Vérifier la suite existante**

Run: `php artisan test --filter="SubmissionStateMachine|AuthorApproval"`
Expected: tous passent (pas de régression sur le flow standard).

- [ ] **Step 4: Commit**

```bash
git add app/Services/SubmissionStateMachine.php
git commit -m "feat(state-machine): Lepis queue mails + decision timestamps"
```

---

## Task 8: SubmissionPolicy — règles de transition Lepis

**Files:**
- Modify: `app/Policies/SubmissionPolicy.php`

- [ ] **Step 1: Lire le code actuel de `transitionTo()`**

Ouvrir `app/Policies/SubmissionPolicy.php` et localiser la méthode `transitionTo(User $user, Submission $submission, SubmissionStatus $target)`. Elle gère les règles par paire `(from → to)`.

- [ ] **Step 2: Ajouter la règle pour les transitions sortantes de `RejectedPendingLepis`**

Dans `transitionTo`, ajouter les cas suivants (à placer avant le `return false` final, ou dans la structure match/switch existante) :

```php
        // Transitions depuis RejectedPendingLepis : admin only (gestion de la file Lepis)
        if ($submission->status === SubmissionStatus::RejectedPendingLepis
            && in_array($target, [SubmissionStatus::RedirectedToLepis, SubmissionStatus::Rejected], true)
        ) {
            return $user->isAdmin();  // role admin OU capability chief_editor
        }

        // Transition vers RejectedPendingLepis : mêmes droits que Reject (éditeur ou chief_editor)
        if ($target === SubmissionStatus::RejectedPendingLepis) {
            return $user->hasCapability(\App\Models\EditorialCapability::EDITOR)
                || $user->hasCapability(\App\Models\EditorialCapability::CHIEF_EDITOR);
        }
```

**Note sur le placement** : si `transitionTo` utilise un `match` ou un ensemble de `if`/`return`, ajuster pour que ces règles soient évaluées avant la règle générique "éditeur peut rejeter". Les tests Feature (Task 12) valident le comportement final.

- [ ] **Step 3: Vérifier non-régression**

Run: `php artisan test --filter="SubmissionPolicy|SubmissionTransitionRoute"`
Expected: tous passent.

- [ ] **Step 4: Commit**

```bash
git add app/Policies/SubmissionPolicy.php
git commit -m "feat(policy): Lepis queue transitions (admin only for outbound)"
```

---

## Task 9: Gate `access-lepis-queue`

**Files:**
- Modify: `app/Providers/AppServiceProvider.php`

- [ ] **Step 1: Ajouter la définition**

Dans `app/Providers/AppServiceProvider.php`, méthode `boot()`, ajouter **après** la ligne `Gate::define('create-submission-for-author', ...)` (cf. P1 #1) :

```php
        \Illuminate\Support\Facades\Gate::define('access-lepis-queue', function (\App\Models\User $user) {
            return $user->isAdmin();  // role admin OR chief_editor capability
        });
```

- [ ] **Step 2: Vérifier non-régression**

Run: `php artisan test`
Expected: 260+ tests passent.

- [ ] **Step 3: Commit**

```bash
git add app/Providers/AppServiceProvider.php
git commit -m "feat(auth): gate access-lepis-queue (admin / chief_editor)"
```

---

## Task 10: EditorialQueueController — routage RejectedPendingLepis

**Files:**
- Modify: `app/Http/Controllers/Admin/Journal/EditorialQueueController.php`

- [ ] **Step 1: Localiser le bloc à remplacer**

Dans `EditorialQueueController`, localiser la méthode `transition()`, spécifiquement le bloc actuel (autour de la ligne 124) :

```php
            if ($target === SubmissionStatus::Rejected && ($validated['redirect_to_lepis'] ?? false)) {
                $submission->update(['redirected_to_lepis' => true]);
            }
```

- [ ] **Step 2: Remplacer ce bloc**

**Déplacer** la logique EN AMONT de l'appel `$this->stateMachine->transition(...)` et modifier `$target` directement :

```php
        // Case Lepis cochée : on redirige la transition vers le statut intermédiaire
        if ($target === SubmissionStatus::Rejected && ($validated['redirect_to_lepis'] ?? false)) {
            $target = SubmissionStatus::RejectedPendingLepis;
        }
```

Et **supprimer** l'ancien bloc (celui qui faisait `update(['redirected_to_lepis' => true])` après la transition — c'est désormais fait par le state machine à l'entrée en `RejectedPendingLepis`).

**Le code final devrait ressembler à** (approximatif, à adapter selon le contexte exact de la méthode) :

```php
    public function transition(Request $request, Submission $submission)
    {
        $validated = $request->validate([
            'target_status' => ['required', Rule::enum(SubmissionStatus::class)],
            'notes' => 'nullable|string|max:2000',
            'redirect_to_lepis' => 'sometimes|boolean',
        ]);

        $target = SubmissionStatus::from($validated['target_status']);

        // Case Lepis cochée : on redirige la transition vers le statut intermédiaire
        if ($target === SubmissionStatus::Rejected && ($validated['redirect_to_lepis'] ?? false)) {
            $target = SubmissionStatus::RejectedPendingLepis;
        }

        $this->authorize('transitionTo', [$submission, $target]);

        try {
            $this->stateMachine->transition(
                $submission,
                $target,
                $request->user(),
                $validated['notes'] ?? null,
            );
        } catch (IllegalTransitionException $e) {
            return back()->withErrors(['transition' => $e->getMessage()]);
        }

        return back()->with('success', 'Transition effectuée.');
    }
```

**Important** : si la structure exacte de la méthode diffère, préserver la logique d'authorize + try/catch existante et ne modifier que le calcul de `$target` + la suppression du bloc `$submission->update(['redirected_to_lepis' => true])`.

- [ ] **Step 3: Vérifier non-régression**

Run: `php artisan test`
Expected: 260+ tests passent. Le flow sans case Lepis doit continuer à fonctionner.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Admin/Journal/EditorialQueueController.php
git commit -m "feat(editorial-queue): route rejection+lepis checkbox to RejectedPendingLepis"
```

---

## Task 11: LepisQueueController + route + vue

**Files:**
- Create: `app/Http/Controllers/Admin/Journal/LepisQueueController.php`
- Create: `resources/views/admin/journal/lepis-queue.blade.php`
- Modify: `routes/admin.php` (ajouter la route)

- [ ] **Step 1: Créer le controller**

Créer `app/Http/Controllers/Admin/Journal/LepisQueueController.php` :

```php
<?php

namespace App\Http\Controllers\Admin\Journal;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Submission;

class LepisQueueController extends Controller
{
    public function index()
    {
        $this->authorize('access-lepis-queue');

        $submissions = Submission::with([
                'author',
                'editor',
                'transitions' => fn ($q) => $q
                    ->where('to_status', SubmissionStatus::RejectedPendingLepis->value)
                    ->orderByDesc('created_at')
                    ->with('actor'),
            ])
            ->where('status', SubmissionStatus::RejectedPendingLepis->value)
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.journal.lepis-queue', compact('submissions'));
    }
}
```

- [ ] **Step 2: Ajouter la route**

Ouvrir `routes/admin.php`. Dans le groupe `Route::middleware(['web', 'admin'])->prefix('extranet')->name('admin.')->group(function () { ... })`, à l'intérieur du sous-ensemble Revue (autour des routes existantes `admin.journal.queue`), ajouter :

```php
    Route::get('revue/file-lepis', [\App\Http\Controllers\Admin\Journal\LepisQueueController::class, 'index'])
        ->name('journal.lepis-queue');
```

- [ ] **Step 3: Créer la vue**

Créer `resources/views/admin/journal/lepis-queue.blade.php` :

```blade
@extends('layouts.admin')
@section('title', 'File Lepis')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span>/</span>
    <span>Revue</span>
    <span>/</span>
    <span>File Lepis</span>
@endsection

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #16302B; margin: 0 0 0.25rem 0;">File Lepis</h1>
    <p style="color: #6b7280; margin: 0;">
        {{ $submissions->total() }} soumission(s) en attente de décision Lepis. L'auteur ne voit aucun changement de statut tant qu'une décision n'a pas été prise.
    </p>
</div>

@if($submissions->isEmpty())
    <div class="card">
        <div class="card-body" style="text-align: center; padding: 3rem;">
            <p style="color: #6b7280; margin: 0;">Aucune soumission en attente de décision Lepis.</p>
        </div>
    </div>
@else
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Proposé par</th>
                    <th>Date</th>
                    <th>Motif</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $submission)
                    @php
                        $transition = $submission->transitions->first();
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('admin.submissions.show', $submission) }}" style="color: #356B8A; font-weight: 500;">
                                {{ \Illuminate\Support\Str::limit($submission->title, 60) }}
                            </a>
                        </td>
                        <td>
                            <div>{{ $submission->author?->name ?? '—' }}</div>
                            <div style="font-size: 0.75rem; color: #9ca3af;">{{ $submission->author?->email ?? '' }}</div>
                        </td>
                        <td>{{ $transition?->actor?->name ?? '—' }}</td>
                        <td>{{ $transition?->created_at?->format('d/m/Y') ?? '—' }}</td>
                        <td style="max-width: 20rem; color: #6b7280; font-size: 0.875rem;">
                            {{ \Illuminate\Support\Str::limit($transition?->notes ?? '—', 150) }}
                        </td>
                        <td style="text-align: right;">
                            @include('admin.journal._transition_buttons', ['submission' => $submission])
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1rem;">
        {{ $submissions->links() }}
    </div>
@endif
@endsection
```

**Note** : le partial `_transition_buttons.blade.php` (mis à jour dans la session précédente) calcule dynamiquement les actions disponibles via la policy. Depuis `RejectedPendingLepis`, il affichera « Transmettre à Lepis » (teal) et « Rejeter » (rouge, notes required) — sous condition que l'utilisateur soit admin.

**IMPORTANT pour `_transition_buttons.blade.php`** : actuellement le tableau `$candidates` (lignes 19-29) contient uniquement les transitions classiques. Il faut y ajouter la transition `RedirectedToLepis`. Modification à faire en Task 11 également.

- [ ] **Step 4: Ajouter la transition RedirectedToLepis dans `_transition_buttons.blade.php`**

Dans `resources/views/admin/journal/_transition_buttons.blade.php`, dans le tableau `$candidates`, ajouter une entrée **après** `Published` :

```php
        ['target' => SubmissionStatus::RedirectedToLepis,      'label' => 'Transmettre à Lepis',              'color' => 'teal',   'needsNotes' => false, 'notesRequired' => false],
```

Le filtre `$policy->transitionTo(...)` se chargera de n'afficher le bouton que pour les admins depuis `RejectedPendingLepis`.

- [ ] **Step 5: Vérifier la route enregistrée**

Run: `php artisan route:list --name=journal.lepis-queue`
Expected: une ligne `GET /extranet/revue/file-lepis ... admin.journal.lepis-queue › ...LepisQueueController@index`

- [ ] **Step 6: Smoke test rapide**

Se logger en tant qu'admin, aller sur `http://localhost:8000/extranet/revue/file-lepis`. La page doit charger avec « Aucune soumission en attente » si la base est vide.

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/Admin/Journal/LepisQueueController.php \
        resources/views/admin/journal/lepis-queue.blade.php \
        routes/admin.php \
        resources/views/admin/journal/_transition_buttons.blade.php
git commit -m "feat(lepis): LepisQueueController + /file-lepis page + transition button"
```

---

## Task 12: Feature tests LepisQueueTest (E2E)

**Files:**
- Create: `tests/Feature/Journal/LepisQueueTest.php`

- [ ] **Step 1: Écrire la suite feature**

Créer `tests/Feature/Journal/LepisQueueTest.php` :

```php
<?php

namespace Tests\Feature\Journal;

use App\Enums\SubmissionStatus;
use App\Mail\ArticleRedirectedToLepis;
use App\Mail\LepisQueueNotification;
use App\Mail\SubmissionDecision;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LepisQueueTest extends TestCase
{
    use RefreshDatabase;

    private function makeEditor(string $capability = EditorialCapability::EDITOR): User
    {
        $user = User::factory()->create();
        $user->grantCapability($capability);
        return $user;
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function makeSubmission(SubmissionStatus $status, User $author): Submission
    {
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Test Lepis',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => $status,
        ]);
    }

    public function test_rejection_with_lepis_checkbox_routes_to_rejected_pending_lepis(): void
    {
        Mail::fake();
        $editor = $this->makeEditor(EditorialCapability::EDITOR);
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::UnderPeerReview, $author);

        $this->actingAs($editor)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => SubmissionStatus::Rejected->value,
                'notes' => 'Ce travail conviendrait mieux à Lepis.',
                'redirect_to_lepis' => '1',
            ]);

        $sub->refresh();
        $this->assertEquals(SubmissionStatus::RejectedPendingLepis, $sub->status);
        $this->assertTrue((bool) $sub->redirected_to_lepis);

        Mail::assertNotQueued(SubmissionDecision::class);
        Mail::assertQueued(LepisQueueNotification::class);
    }

    public function test_lepis_queue_notification_sent_to_admins_and_chief_editors(): void
    {
        Mail::fake();
        $editor = $this->makeEditor(EditorialCapability::EDITOR);
        $chief = $this->makeEditor(EditorialCapability::CHIEF_EDITOR);
        $admin = $this->makeAdmin();
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::UnderPeerReview, $author);

        $this->actingAs($editor)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => SubmissionStatus::Rejected->value,
                'notes' => 'Motif',
                'redirect_to_lepis' => '1',
            ]);

        Mail::assertQueued(LepisQueueNotification::class, fn ($m) => $m->hasTo($chief->email));
        Mail::assertQueued(LepisQueueNotification::class, fn ($m) => $m->hasTo($admin->email));
    }

    public function test_admin_can_access_lepis_queue_page(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.journal.lepis-queue'));

        $response->assertOk();
        $response->assertSee('File Lepis');
    }

    public function test_chief_editor_can_access_lepis_queue_page(): void
    {
        $chief = $this->makeEditor(EditorialCapability::CHIEF_EDITOR);

        $response = $this->actingAs($chief)->get(route('admin.journal.lepis-queue'));

        $response->assertOk();
    }

    public function test_simple_editor_cannot_access_lepis_queue_page(): void
    {
        $editor = $this->makeEditor(EditorialCapability::EDITOR);

        $response = $this->actingAs($editor)->get(route('admin.journal.lepis-queue'));

        $response->assertForbidden();
    }

    public function test_lepis_queue_lists_only_pending_submissions(): void
    {
        $admin = $this->makeAdmin();
        $author = User::factory()->create();

        $pending = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);
        $rejected = $this->makeSubmission(SubmissionStatus::Rejected, $author);
        $peer = $this->makeSubmission(SubmissionStatus::UnderPeerReview, $author);

        $response = $this->actingAs($admin)->get(route('admin.journal.lepis-queue'));

        $response->assertSee($pending->title);
        // Les 2 autres sont créées avec le même titre 'Test Lepis' — on teste via le count
        $this->assertEquals(1, $response->viewData('submissions')->total());
    }

    public function test_transmit_to_lepis_sends_mail_and_sets_decision_timestamp(): void
    {
        Mail::fake();
        $admin = $this->makeAdmin();
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        $this->actingAs($admin)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => SubmissionStatus::RedirectedToLepis->value,
            ]);

        $sub->refresh();
        $this->assertEquals(SubmissionStatus::RedirectedToLepis, $sub->status);
        $this->assertNotNull($sub->lepis_decision_at);
        $this->assertEquals($admin->id, $sub->lepis_decided_by_user_id);

        Mail::assertQueued(ArticleRedirectedToLepis::class, fn ($m) => $m->hasTo($author->email));
        Mail::assertNotQueued(SubmissionDecision::class);
    }

    public function test_reject_from_lepis_queue_sends_submission_decision_mail(): void
    {
        Mail::fake();
        $admin = $this->makeAdmin();
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        $this->actingAs($admin)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => SubmissionStatus::Rejected->value,
                'notes' => 'Motifs de rejet détaillés',
            ]);

        $sub->refresh();
        $this->assertEquals(SubmissionStatus::Rejected, $sub->status);
        $this->assertNotNull($sub->lepis_decision_at);
        $this->assertEquals($admin->id, $sub->lepis_decided_by_user_id);

        Mail::assertQueued(SubmissionDecision::class, fn ($m) => $m->hasTo($author->email));
        Mail::assertNotQueued(ArticleRedirectedToLepis::class);
    }

    public function test_simple_editor_cannot_transmit_to_lepis(): void
    {
        $editor = $this->makeEditor(EditorialCapability::EDITOR);
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        $response = $this->actingAs($editor)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => SubmissionStatus::RedirectedToLepis->value,
            ]);

        $response->assertForbidden();
        $sub->refresh();
        $this->assertEquals(SubmissionStatus::RejectedPendingLepis, $sub->status);
    }
}
```

- [ ] **Step 2: Lancer les tests**

Run: `php artisan test --filter=LepisQueueTest`
Expected: PASS (9 tests)

Si un test échoue, lire le message et ajuster :
- Les erreurs de permission peuvent indiquer que la Policy n'est pas bien branchée — revoir Task 8
- Les erreurs `Route not defined` indiquent un problème d'enregistrement de la route — revoir Task 11
- Les erreurs d'assertion mail indiquent un oubli dans la state machine — revoir Task 7

- [ ] **Step 3: Vérifier la suite globale**

Run: `php artisan test`
Expected: tous les tests passent (260 existants + nouveaux).

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/Journal/LepisQueueTest.php
git commit -m "test(lepis): E2E coverage of Lepis queue flow (9 scenarios)"
```

---

## Task 13: Vues auteur — publicStatus()

**Files:**
- Modify: `resources/views/journal/submissions/index.blade.php`
- Modify: `resources/views/journal/submissions/show.blade.php`

- [ ] **Step 1: Modifier `index.blade.php`**

Dans `resources/views/journal/submissions/index.blade.php` :

Remplacer (ligne ~54) :
```php
$submissionStatusValue = $submission->status instanceof \App\Enums\SubmissionStatus ? $submission->status->value : $submission->status;
```
Par :
```php
$publicStatus = $submission->publicStatus();
$submissionStatusValue = $publicStatus->value;
```

Et remplacer (ligne ~57) :
```blade
{{ $submission->status instanceof \App\Enums\SubmissionStatus ? $submission->status->label() : (\App\Models\Submission::getStatuses()[$submission->status] ?? $submission->status) }}
```
Par :
```blade
{{ $publicStatus->label() }}
```

Et dans la ligne 100 :
```php
@if($submission->status?->value === 'revision_after_review')
```
Par :
```php
@if($publicStatus->value === 'revision_after_review')
```

- [ ] **Step 2: Modifier `show.blade.php`**

Dans `resources/views/journal/submissions/show.blade.php` :

Remplacer (ligne ~32) :
```php
$submissionStatusValue = $submission->status instanceof \App\Enums\SubmissionStatus ? $submission->status->value : $submission->status;
```
Par :
```php
$publicStatus = $submission->publicStatus();
$submissionStatusValue = $publicStatus->value;
```

Remplacer (ligne ~35) :
```blade
{{ $submission->status instanceof \App\Enums\SubmissionStatus ? $submission->status->label() : (\App\Models\Submission::getStatuses()[$submission->status] ?? $submission->status) }}
```
Par :
```blade
{{ $publicStatus->label() }}
```

Remplacer (ligne ~40) :
```php
@if(in_array($submission->status?->value, ['revision_requested', 'revision_after_review']))
```
Par :
```php
@if(in_array($publicStatus->value, ['revision_requested', 'revision_after_review']))
```

**Important** : **NE PAS** remplacer `$submission->status === \App\Enums\SubmissionStatus::AwaitingAuthorApproval` (ligne 180) — cette comparaison doit rester sur le statut réel car elle détermine si l'auteur peut cliquer sur les boutons d'approbation. `AwaitingAuthorApproval` est un statut public, donc `publicStatus() === status` dans ce cas.

- [ ] **Step 3: Ajouter un test d'intégration rapide**

Ajouter dans `tests/Feature/Journal/LepisQueueTest.php` :

```php
    public function test_author_sees_public_status_not_lepis_pending_on_show_page(): void
    {
        $author = User::factory()->create();
        $editor = $this->makeEditor(EditorialCapability::EDITOR);
        $sub = $this->makeSubmission(SubmissionStatus::UnderPeerReview, $author);

        // Log manuellement les transitions pour simuler le passage par UnderPeerReview
        $sub->transitions()->create([
            'action' => 'status_changed',
            'actor_id' => $editor->id,
            'from_status' => 'submitted',
            'to_status' => 'under_peer_review',
            'notes' => null,
        ]);

        // Passage en RejectedPendingLepis
        $sub->update(['status' => SubmissionStatus::RejectedPendingLepis]);
        $sub->transitions()->create([
            'action' => 'status_changed',
            'actor_id' => $editor->id,
            'from_status' => 'under_peer_review',
            'to_status' => 'rejected_pending_lepis',
            'notes' => 'reco Lepis',
        ]);

        $response = $this->actingAs($author)->get(route('journal.submissions.show', $sub));

        $response->assertOk();
        $response->assertSee('En relecture');  // label de UnderPeerReview
        $response->assertDontSee('Rejet en attente Lepis');
        $response->assertDontSee('Transmis au bulletin Lepis');
    }
```

- [ ] **Step 4: Lancer les tests**

Run: `php artisan test --filter=LepisQueueTest`
Expected: 10 tests passent (9 + 1 nouveau).

- [ ] **Step 5: Commit**

```bash
git add resources/views/journal/submissions/index.blade.php \
        resources/views/journal/submissions/show.blade.php \
        tests/Feature/Journal/LepisQueueTest.php
git commit -m "feat(journal): author views use publicStatus() to hide Lepis pending"
```

---

## Task 14: Bandeau info sur fiche soumission admin

**Files:**
- Modify: `resources/views/admin/submissions/show.blade.php`

- [ ] **Step 1: Ajouter le bandeau au-dessus de la card Statut**

Dans `resources/views/admin/submissions/show.blade.php`, **juste avant** le début de la card Statut (autour de la ligne 163), ajouter :

```blade
            {{-- Bandeau info si statut caché à l'auteur (RejectedPendingLepis) --}}
            @if($submissionStatusValue === 'rejected_pending_lepis')
                <div style="margin-bottom: 1rem; padding: 1rem; background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%); border: 1px solid #fde68a; border-left: 4px solid #d97706; border-radius: 0.5rem;">
                    <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20" style="color: #d97706; flex-shrink: 0; margin-top: 2px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <div>
                            <div style="font-weight: 600; color: #92400e; margin-bottom: 0.25rem;">Statut invisible pour l'auteur</div>
                            <div style="font-size: 0.875rem; color: #78350f;">
                                Cette soumission est en file Lepis. L'auteur voit toujours le statut « <strong>{{ $submission->publicStatus()->label() }}</strong> » sur son espace.
                                <a href="{{ route('admin.journal.lepis-queue') }}" style="color: #92400e; text-decoration: underline; font-weight: 500;">→ Aller à la file Lepis</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
```

- [ ] **Step 2: Vérifier non-régression et smoke rapide**

Run: `php artisan test`
Expected: tous les tests passent.

Se logger en admin, créer une soumission → la passer manuellement en `rejected_pending_lepis` (via DB ou via le flow normal) → ouvrir `/extranet/submissions/{id}` → le bandeau doit apparaître.

- [ ] **Step 3: Commit**

```bash
git add resources/views/admin/submissions/show.blade.php
git commit -m "feat(admin): banner on submission page when status is hidden from author"
```

---

## Task 15: Badge sidebar « File Lepis [N] »

**Files:**
- Modify: `resources/views/layouts/admin.blade.php` (bloc nav-section « Revue »)

- [ ] **Step 1: Ajouter l'entrée de navigation**

Dans `resources/views/layouts/admin.blade.php`, dans le bloc `<div class="nav-section">` intitulé « Revue » (autour des lignes 108-137), après le lien `admin.journal.mine` et avant `@endif ... @endif`, ajouter :

```blade
                        @can('access-lepis-queue')
                            @php
                                $lepisQueueCount = \App\Models\Submission::where('status', 'rejected_pending_lepis')->count();
                            @endphp
                            <a href="{{ route('admin.journal.lepis-queue') }}" class="nav-link {{ request()->routeIs('admin.journal.lepis-queue') ? 'active' : '' }}" style="display:flex;align-items:center;justify-content:space-between;">
                                <span style="display:flex;align-items:center;gap:0.75rem;">
                                    <i data-lucide="file-warning"></i>
                                    <span>File Lepis</span>
                                </span>
                                @if($lepisQueueCount > 0)
                                    <span style="background:#d97706;color:white;font-size:0.7rem;font-weight:600;padding:2px 8px;border-radius:10px;">{{ $lepisQueueCount }}</span>
                                @endif
                            </a>
                        @endcan
```

**Structure exacte du placement** (contexte) : cette entrée doit apparaître DANS le `@if($authUser && ($authUser->hasCapability(EDITOR) || ...))` existant, **après** le lien « Mes articles » (ligne ~131-134) et **avant** les deux `@endif` qui ferment ce bloc.

- [ ] **Step 2: Vérifier visuellement**

Run: `php artisan serve` + aller sur le dashboard admin. Un item « File Lepis » doit apparaître dans la section Revue de la sidebar (visible pour admin/chief_editor, invisible pour simple editor).

Si `$lepisQueueCount > 0`, un badge orange numérique doit s'afficher à droite.

- [ ] **Step 3: Vérifier non-régression**

Run: `php artisan test`
Expected: tous verts.

- [ ] **Step 4: Commit**

```bash
git add resources/views/layouts/admin.blade.php
git commit -m "feat(admin): sidebar link to Lepis queue with pending count badge"
```

---

## Task 16: Smoke test + final review + cleanup

- [ ] **Step 1: Lancer la suite complète**

Run: `php artisan test`
Expected: tous les tests passent (260 existants + ~24 nouveaux = ~284).

- [ ] **Step 2: Smoke test manuel bout-en-bout**

1. Run `php artisan serve` + `php artisan queue:work` (ou `QUEUE_CONNECTION=sync` dans `.env`)
2. Se logger en tant qu'**éditeur** (capability EDITOR, pas admin)
3. Aller sur la fiche d'une soumission en `under_peer_review` (en créer une si besoin via seeder ou tinker)
4. Cliquer **Rejeter** → cocher **Recommander pour le bulletin Lepis** → saisir des motifs → Confirmer
5. Vérifier :
    - Le statut est `rejected_pending_lepis` (badge « Rejet en attente Lepis » en amber)
    - Bandeau orange affiché : « Statut invisible pour l'auteur… »
    - `laravel.log` : **pas** de `SubmissionDecision` mail envoyé
    - `laravel.log` : **un** mail `LepisQueueNotification` envoyé à l'admin et aux chief_editors
6. Se déconnecter, se logger en tant qu'**auteur** de la soumission
7. Aller sur `/revue/mes-soumissions/{id}` → le statut affiché doit être le précédent (ex: « En relecture »), pas « Rejet en attente Lepis »
8. Se déconnecter, se logger en tant qu'**admin** (role admin OU chief_editor)
9. Cliquer sur « File Lepis » dans la sidebar → la soumission apparaît dans la table
10. Cliquer **Transmettre à Lepis** → modale → Confirmer
11. Vérifier :
    - Statut = `redirected_to_lepis` (badge teal « Transmis au bulletin Lepis »)
    - `lepis_decision_at` rempli, `lepis_decided_by_user_id` = admin
    - `laravel.log` : mail `ArticleRedirectedToLepis` envoyé à l'auteur
12. Se logger en auteur → le statut affiché est maintenant « Transmis au bulletin Lepis »
13. **Bonus** — rejouer le flow mais cliquer **Rejeter définitivement** depuis la file Lepis :
    - Statut = `rejected`
    - `laravel.log` : mail `SubmissionDecision` envoyé à l'auteur (avec les motifs)
    - L'auteur voit « Rejeté » sur son espace

- [ ] **Step 3: Vérifier la non-régression du flow Reject standard (sans checkbox Lepis)**

1. En éditeur, rejeter une autre soumission en décochant la case Lepis
2. Vérifier :
    - Statut = `rejected` directement (pas `rejected_pending_lepis`)
    - `SubmissionDecision` envoyé à l'auteur immédiatement
    - Pas de `LepisQueueNotification` envoyé
    - `redirected_to_lepis` = false sur la soumission

- [ ] **Step 4: Mettre à jour la documentation (facultatif si temps)**

Dans `resources/views/admin/documentation/index.blade.php`, compléter la section Workflow editorial avec une sous-section sur la file Lepis (court paragraphe expliquant le flow).

- [ ] **Step 5: Commit final si ajustements**

Si des ajustements ont été faits pendant le smoke test :

```bash
git status
git add .
git commit -m "chore(lepis): smoke test fixes"
```

- [ ] **Step 6: Mettre à jour la mémoire projet**

Mettre à jour `C:\Users\ddemerges\.claude\projects\C--xampp-htdocs-oreina-plateforme\memory\MEMORY.md` pour ajouter une ligne pointant vers un nouveau fichier mémoire `project_chersotis_p1_lepis.md` résumant la livraison de ce P1 #B.

---

## Critères de succès (rappel du spec §12)

- [ ] Cliquer "Rejeter" avec case Lepis cochée envoie la soumission en `RejectedPendingLepis`, pas en `Rejected`
- [ ] L'auteur ne voit aucun changement sur son dashboard à ce moment-là
- [ ] Les admins reçoivent `LepisQueueNotification`
- [ ] La page `/extranet/revue/file-lepis` liste la soumission, accessible admin
- [ ] « Transmettre à Lepis » → `RedirectedToLepis` + mail `ArticleRedirectedToLepis`
- [ ] « Rejeter définitivement » → `Rejected` + mail `SubmissionDecision`
- [ ] Aucune régression sur le flow rejet sans case Lepis
- [ ] Coverage tests ≥ 90% sur les nouvelles lignes
