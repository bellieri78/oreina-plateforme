# Spec — Soumission backoffice pour le compte d'un auteur (Chersotis P1)

**Date** : 2026-04-18
**Item P1** : #1 de la section 17 du doc `implications_dev_reunion_chersotis_20260416.md`
**Origine** : Réunion revue Chersotis du 16 avril 2026, section 7
**Statut** : Spec validée — prêt pour plan d'implémentation

---

## 1. Contexte et motivation

Le comité de rédaction doit pouvoir **créer une soumission pour le compte d'un auteur** qui n'a pas encore de compte sur la plateforme. Cas d'usage immédiat : Greg doit saisir les **7-8 articles en transition** depuis le magazine (déposés sur le Drive Oreina, dossier « Revue »), dont la plupart des auteurs ne sont pas encore inscrits.

Usage récurrent attendu : articles reçus par email, articles transmis par un ancien canal, articles de contributeurs externes identifiés par le comité.

Le formulaire admin `admin/submissions/create` existe déjà et fonctionne pour les auteurs **déjà inscrits** (FK `author_id` obligatoire). **Gap à combler** : supporter le cas « auteur sans compte » et garantir la traçabilité (qui a saisi pour qui).

---

## 2. Décisions de design (validées en brainstorming)

| Choix | Option retenue | Raison courte |
|-------|----------------|----------------|
| Modèle d'identité auteur | **Pré-créer un User « ghost »** avec `password=null`, `invited_at=now` | Zéro casse sur les flux existants (`->author` reste un User valide partout) |
| UX sélection auteur | **Radio toggle** « Auteur existant / Nouvel auteur » | Lisible pour un saisisseur non-dev, pas de JS lourd |
| Invitation | **Mail immédiat** à la création, signed URL 14 jours | L'auteur peut suivre vite, pas de gestion manuelle |
| Fields "nouvel auteur" | **Nom + email uniquement** | Pas ralentir Greg, l'auteur complétera son profil lors du claim |
| Fichier manuscrit | **Requis** (30 Mo max, cohérent réunion) | Évite les stubs vides, Greg a les fichiers sur le Drive |
| Token d'invitation | **Signed URL Laravel** (HMAC natif sur `APP_KEY`) | Stateless, pas de nouvelle table |
| Permissions | **Rédacteur en chef + Éditeur** uniquement | Aligné sur la spec réunion (point 7) |

---

## 3. Modifications du modèle de données

### 3.1 Migration — `add_submitted_by_to_submissions`
```php
Schema::table('submissions', function (Blueprint $table) {
    $table->foreignId('submitted_by_user_id')
          ->nullable()
          ->after('author_id')
          ->constrained('users')
          ->onDelete('set null');
    $table->index('submitted_by_user_id');
});
```
**Sémantique** : `null` = auto-soumission par l'auteur. Rempli = création backoffice par `submitted_by_user_id` pour le compte de `author_id`.

### 3.2 Migration — `add_invitation_fields_to_users`
```php
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
```
**Définition d'un User « ghost »** : `password IS NULL` AND `invited_at IS NOT NULL` AND `claimed_at IS NULL`.

Un User réclamé a `password IS NOT NULL` AND `claimed_at IS NOT NULL`.

### 3.3 Model `Submission` — relations et accessors
Ajouter :
```php
public function submittedBy(): BelongsTo {
    return $this->belongsTo(User::class, 'submitted_by_user_id');
}

public function wasSubmittedOnBehalf(): bool {
    return $this->submitted_by_user_id !== null
        && $this->submitted_by_user_id !== $this->author_id;
}
```
Ajouter `submitted_by_user_id` à `$fillable`.

### 3.4 Model `User` — scopes et helpers
```php
public function scopeGhost($query) {
    return $query->whereNull('password')
                 ->whereNotNull('invited_at')
                 ->whereNull('claimed_at');
}

public function scopeClaimed($query) {
    return $query->whereNotNull('claimed_at');
}

public function isGhost(): bool {
    return $this->password === null
        && $this->invited_at !== null
        && $this->claimed_at === null;
}

public function invitedBy(): BelongsTo {
    return $this->belongsTo(User::class, 'invited_by_user_id');
}
```
Ajouter `invited_at`, `claimed_at`, `invited_by_user_id` à `$fillable` et aux casts datetime.

### 3.5 Impact sur les listings existants
**À vérifier / filtrer** :
- Scope `withCapability()` (dropdown éditeurs/relecteurs) → les ghosts n'ont aucune capability, donc invisibles naturellement.
- Annuaire membres extranet → filtrer `claimed()` pour ne pas exposer des ghosts.
- Dropdown « Auteur existant » (create submission) → peut inclure les ghosts (utile pour recréer une 2e soumission d'un auteur pas encore actif).

---

## 4. UX — Formulaire `admin/submissions/create`

### 4.1 Modification du partial `resources/views/admin/submissions/_form.blade.php`

Bloc actuel (lignes 163-174) :
```blade
<label>Auteur *</label>
<select name="author_id">...</select>
```

**Remplacé par** un bloc Alpine.js :
```blade
<div x-data="{ mode: '{{ old('author_mode', 'existing') }}' }">
    <label class="form-label">Auteur *</label>
    <div style="display: flex; gap: 1rem; margin-bottom: 0.75rem;">
        <label><input type="radio" name="author_mode" value="existing" x-model="mode"> Auteur existant</label>
        <label><input type="radio" name="author_mode" value="new" x-model="mode"> Nouvel auteur</label>
    </div>

    <div x-show="mode === 'existing'">
        <select name="author_id" class="form-input" x-bind:required="mode === 'existing'">
            <option value="">-- Sélectionner --</option>
            @foreach($authors as $a)
                <option value="{{ $a->id }}">{{ $a->name }}{{ $a->isGhost() ? ' (compte non activé)' : '' }}</option>
            @endforeach
        </select>
    </div>

    <div x-show="mode === 'new'" x-cloak>
        <input type="text" name="author_name" placeholder="Nom complet"
               class="form-input" x-bind:required="mode === 'new'">
        <input type="email" name="author_email" placeholder="Email"
               class="form-input" x-bind:required="mode === 'new'" style="margin-top: 0.5rem;">
        <p style="font-size: 0.8rem; color: #6b7280; margin-top: 0.5rem;">
            Un compte sera créé pour l'auteur. Une invitation lui sera envoyée à cette adresse pour activer son accès.
        </p>
    </div>
</div>
```

Les erreurs de validation sont affichées sous le bloc concerné.

### 4.2 Validation côté `store()` du controller

```php
$validated = $request->validate([
    // ... champs existants
    'author_mode'    => 'required|in:existing,new',
    'author_id'      => 'required_if:author_mode,existing|nullable|exists:users,id',
    'author_name'    => 'required_if:author_mode,new|nullable|string|max:255',
    'author_email'   => [
        'required_if:author_mode,new',
        'nullable',
        'email',
        Rule::unique('users', 'email'), // → message custom "Ce compte existe déjà, utilisez Auteur existant"
    ],
    'manuscript_file' => 'required|file|mimes:doc,docx,pdf,odt|max:30720',
    // ... reste inchangé
]);
```

Message custom pour `author_email.unique` :
```php
'author_email.unique' => 'Un compte existe déjà pour cet email. Sélectionnez « Auteur existant » dans la liste déroulante.',
```

### 4.3 Extraction dans `SubmissionCreationService`

Nouveau fichier `app/Services/SubmissionCreationService.php` :
```php
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
            $submission = Submission::create([
                ...$data,
                'author_id' => $author->id,
                'submitted_by_user_id' => $submittedBy->id === $author->id
                    ? null
                    : $submittedBy->id,
                'status' => SubmissionStatus::Submitted,
                'submitted_at' => now(),
            ]);
            // L'accusé de réception existant (SubmissionReceivedNotification) part par event/listener déjà en place
            return $submission;
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

            $submission = Submission::create([
                ...$data,
                'author_id' => $author->id,
                'submitted_by_user_id' => $submittedBy->id,
                'status' => SubmissionStatus::Submitted,
                'submitted_at' => now(),
            ]);

            $this->mailer->to($author)->send(new AccountInvitation($author, $submission, $submittedBy));
            // L'accusé de réception part via l'event existant (listener → SubmissionReceivedNotification)
            return $submission;
        });
    }
}
```

Le controller `store()` délègue ensuite selon `author_mode` — la logique de gestion des fichiers uploadés reste dans le controller (inchangée) et le résultat est passé en `$data` au service.

### 4.4 Routes et permissions

**Aucune nouvelle route.** Les routes `admin.submissions.create` et `admin.submissions.store` existent déjà.

**Gate `create-submission-for-author`** dans `app/Providers/AuthServiceProvider.php` :
```php
Gate::define('create-submission-for-author', function (User $user) {
    return $user->hasEditorialCapability(EditorialCapability::CHIEF_EDITOR)
        || $user->hasEditorialCapability(EditorialCapability::EDITOR);
});
```

**Application** :
- `SubmissionController::create()` et `store()` → `$this->authorize('create-submission-for-author')` en première ligne
- Dans `resources/views/admin/submissions/index.blade.php`, entourer le bouton « Nouvelle soumission » par `@can('create-submission-for-author') ... @endcan`

Les autres admins (trésorier, secrétaire, etc.) perdent l'accès au bouton et reçoivent 403 sur `/extranet/submissions/create`. **C'est un changement de comportement** par rapport à l'existant (tout admin accédait) — accepté car aligné avec la séparation des rôles éditoriaux.

---

## 5. Invitation & claim flow

### 5.1 Mail `AccountInvitation`

Fichier : `app/Mail/AccountInvitation.php` (Mailable)

**Sujet** : « Un article vous concernant a été déposé sur Chersotis »

**Template** : `resources/views/emails/account-invitation.blade.php` (Markdown mail Laravel)

**Contenu** :
- Salutation avec `$author->name`
- « {{ invitedBy->name }} a déposé une soumission pour la revue Chersotis en votre nom : *{{ submission->title }}* »
- Bouton « Activer mon compte » → signed URL (valide 14 jours)
- Texte : « Ce lien vous permettra de définir votre mot de passe et d'accéder à l'espace auteur pour suivre votre soumission. »
- Récapitulatif du process éditorial en 7 étapes (copié du template P0 accusé de réception)
- Mention : « Si le lien est expiré, contactez chersotis-revue@oreina.org »
- Expéditeur : `config('journal.contact_email')` (chersotis-revue@oreina.org déjà existant)

### 5.2 Signed URL

Génération dans le Mailable :
```php
$claimUrl = URL::temporarySignedRoute(
    'account.claim',
    now()->addDays(config('journal.invitation_expiration_days', 14)),
    ['user' => $this->author->id]
);
```

Ajouter dans `config/journal.php` : `'invitation_expiration_days' => env('JOURNAL_INVITATION_EXPIRATION_DAYS', 14)`.

### 5.3 Routes publiques

Dans `routes/web.php` (hors groupe admin, hors groupe auth) :
```php
Route::get('/claim-account/{user}', [ClaimAccountController::class, 'show'])
    ->name('account.claim')
    ->middleware('signed');

Route::post('/claim-account/{user}', [ClaimAccountController::class, 'store'])
    ->name('account.claim.store')
    ->middleware('signed');
```

Le middleware `signed` de Laravel vérifie la signature et l'expiration — 403 si invalide ou expiré.

### 5.4 `ClaimAccountController`

Fichier : `app/Http/Controllers/Auth/ClaimAccountController.php`

```php
class ClaimAccountController extends Controller
{
    public function show(User $user)
    {
        if ($user->claimed_at !== null) {
            return redirect()->route('login')
                ->with('info', 'Ce compte a déjà été activé. Connectez-vous.');
        }
        return view('auth.claim-account', ['user' => $user]);
    }

    public function store(Request $request, User $user)
    {
        if ($user->claimed_at !== null) {
            return redirect()->route('login')
                ->with('info', 'Ce compte a déjà été activé.');
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
            'claimed_at' => now(),
            'email_verified_at' => now(), // l'invitation reçue par mail vaut vérification
        ]);

        Auth::login($user);

        return redirect()->route('submissions.my')
            ->with('success', 'Bienvenue ! Votre compte est activé, vous pouvez suivre vos soumissions.');
    }
}
```

### 5.5 Vue `auth/claim-account.blade.php`

Form simple :
- Affichage de `$user->name` et `$user->email` (non modifiables, informatifs)
- Champs `password` + `password_confirmation` avec indicateur de robustesse
- Bouton « Activer mon compte »
- Mention RGPD : « En activant votre compte, vous acceptez les conditions d'utilisation et la politique de confidentialité (liens) »

---

## 6. Tests (TDD obligatoire)

### 6.1 `tests/Feature/Admin/BackofficeSubmissionTest.php`

```
✓ Un Chief editor peut créer une soumission pour un auteur existant
✓ Un Editor peut créer une soumission pour un auteur existant
✓ Un Chief editor peut créer une soumission pour un nouvel auteur
  → User ghost créé (password null, invited_at rempli, invited_by=chief)
  → Submission créée (status=submitted, submitted_by=chief, author_id=nouveau user)
  → Mail AccountInvitation envoyé à l'auteur
  → Mail SubmissionReceived envoyé à l'auteur
✓ Un user sans capability chief/editor reçoit 403 sur create et store
✓ Un admin sans capability éditoriale ne voit pas le bouton "Nouvelle soumission"
✓ Email de nouvel auteur déjà existant → erreur validation avec message "Utilisez Auteur existant"
✓ Manuscrit absent → erreur validation
✓ author_mode=new sans author_name → erreur validation
✓ author_mode=existing sans author_id → erreur validation
✓ Quand l'auteur et submitted_by sont le même user (auto-soumission via admin), submitted_by_user_id est null
```

### 6.2 `tests/Feature/Auth/ClaimAccountTest.php`

```
✓ Signed URL valide → form de claim accessible
✓ Signed URL avec signature falsifiée → 403
✓ Signed URL expirée (> 14 jours) → 403
✓ POST password valide → user activé (claimed_at, email_verified_at, password hashé) + login auto + redirect my-submissions
✓ POST password non-confirmé → erreur validation
✓ POST password trop court → erreur validation
✓ Accès à /claim-account/{user} pour un user déjà claimed → redirect login avec message
```

### 6.3 `tests/Unit/Services/SubmissionCreationServiceTest.php`

```
✓ createForExistingAuthor → Submission en base, submitted_by correct, status=submitted
✓ createForExistingAuthor avec author === submittedBy → submitted_by_user_id = null
✓ createForNewAuthor → User ghost + Submission + 2 mails (invitation + accusé)
✓ createForNewAuthor → transaction atomique (si mail échoue, rien n'est persisté)
```

### 6.4 `tests/Unit/Models/UserGhostTest.php`

```
✓ scopeGhost filtre les users password=null + invited_at ≠ null + claimed_at=null
✓ scopeClaimed filtre les users claimed_at ≠ null
✓ isGhost() retourne true/false correctement
✓ withCapability() n'inclut jamais un ghost (ghost a zéro capability par définition)
```

---

## 7. Sécurité et RGPD

- Signed URL HMAC sur `APP_KEY` → pas de token à persister, pas d'attaque par rejeu sur un token périmé
- Expiration 14 jours (configurable)
- Un User ghost n'a aucune capability → il est invisible des dropdowns éditoriaux
- **RGPD audit trail** : `invited_at`, `invited_by_user_id`, `claimed_at` servent de preuve qu'un compte a été créé par un tiers avec consentement implicite (l'auteur a soumis son article à la revue). Le mail d'invitation documente explicitement la création du compte.
- **Suppression d'un ghost non réclamé** : si l'auteur ne réclame jamais son compte, l'admin peut supprimer le User. Sur `users` la FK `author_id` sur `submissions` est `onDelete('restrict')` — on ne peut pas supprimer un User avec des soumissions associées. Solution : supprimer d'abord la soumission, ou garder le ghost indéfiniment (pas de problème de sécurité).

---

## 8. Hors scope (différé)

- **Relance automatique d'invitation** si non réclamée après N jours (à ajouter P2 si observé)
- **Bouton « Renvoyer l'invitation »** sur la page show de la soumission (utile si le mail est perdu avant expiration) — peut être ajouté rapidement post-MVP si besoin remonte
- **Interface de gestion des ghost users** (liste, bulk resend) — à réserver pour le volume
- **Recherche avancée dans le dropdown « Auteur existant »** (autocomplete) — pertinent seulement si la liste devient très longue (>200 users)
- **Co-auteurs** : restent en champ JSON `co_authors` (texte libre), pas de ghost user automatique pour eux — un co-auteur devient auteur principal uniquement si resoumis comme tel

---

## 9. Plan d'implémentation (vue d'ensemble, détail dans le plan séparé)

1. Migrations + Models (User scopes, Submission relation)
2. Gate + middleware de permission
3. `SubmissionCreationService` + tests unitaires
4. Modification du partial `_form.blade.php` + controller (validation, dispatch)
5. Mailable `AccountInvitation` + template Markdown
6. `ClaimAccountController` + route + vue `auth/claim-account.blade.php`
7. Tests Feature (backoffice + claim)
8. Seeder/factory : `User::factory()->ghost()` pour faciliter les tests
9. Mise à jour doc `config/journal.php` (expiration config)
10. Masquage conditionnel du bouton « Nouvelle soumission » (index admin)

---

## 10. Critères de succès

- [ ] Un éditeur peut saisir un article d'un auteur sans compte en moins d'1 minute
- [ ] L'auteur reçoit un mail avec lien, clique, définit son mot de passe, arrive sur son tableau de bord avec sa soumission visible — en moins d'1 minute
- [ ] Tous les flux existants (timeline, notifications, peer review, approbation auteur P0) fonctionnent sans modification sur une soumission créée par le backoffice
- [ ] Les 7-8 articles en transition peuvent être saisis sans créer manuellement les comptes auteurs
- [ ] Aucune régression sur le formulaire de création de soumission pour un auteur existant
- [ ] Coverage tests ≥ 90% sur les nouvelles lignes

---

*Spec validée le 2026-04-18 — brainstorming session avec David*
