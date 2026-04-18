# Spec — Rejet avec recommandation Lepis (Chersotis P1 #B)

**Date** : 2026-04-18
**Item P1** : #2 de la section 17 du doc `implications_dev_reunion_chersotis_20260416.md`
**Origine** : Réunion revue Chersotis du 16 avril 2026, section 8
**Statut** : Spec validée — prêt pour plan d'implémentation

---

## 1. Contexte et motivation

Quand un article est rejeté par Chersotis, l'éditeur peut estimer qu'il est **mieux adapté au bulletin Lepis** (publication interne d'OREINA, format plus court/vulgarisation). Dans ce cas, l'article ne doit **pas** être rejeté frontalement auprès de l'auteur — il faut laisser le rédacteur en chef de Lepis examiner la proposition en amont, avant toute communication à l'auteur.

**Scaffolding existant** :
- Checkbox « Recommander pour le bulletin Lepis » dans la modale Rejeter (`_transition_buttons.blade.php`)
- Champ booléen `redirected_to_lepis` sur `submissions` (migration du 2026-04-15)
- `EditorialQueueController` pose le flag quand la checkbox est cochée

**Gap** :
- Le mail `SubmissionDecision` part quand même à l'auteur dès la transition `→ Rejected` (spec violée)
- Pas de statut intermédiaire : la soumission est immédiatement terminale
- Aucune page pour consulter les articles en attente de décision Lepis
- Aucune notification aux admins quand un article entre en file Lepis

---

## 2. Décisions de design (validées en brainstorming)

| Choix | Option retenue | Raison courte |
|-------|----------------|----------------|
| Cas Lepis accepte | **Nouveau statut terminal `RedirectedToLepis`** | Traçabilité claire, statut sémantique honnête, contact Lepis-auteur reste offline |
| Cas Lepis refuse | **Transition `RejectedPendingLepis → Rejected`** | Déclenche le flow de rejet standard (mail `SubmissionDecision`) |
| Vue auteur pendant `RejectedPendingLepis` | **Auteur voit le statut public précédent** via helper `publicStatus()` | L'auteur n'est pas au courant de l'évaluation Lepis tant que Lepis ne s'est pas prononcé |
| Page dédiée | **`/extranet/revue/file-lepis`** accessible aux admins | Permet de traiter les soumissions en file sans bouton dispersé |
| Permission | **`Gate::isAdmin()`** (role admin OU capability chief_editor) | Pas de nouveau rôle `lepis_editor` pour l'instant — la gestion des droits Lepis sera formalisée plus tard |
| Notification entrée en file | **Mail `LepisQueueNotification`** à tous les admins | Évite que la file Lepis reste non consultée |
| Notification redirection Lepis | **Mail `ArticleRedirectedToLepis`** à l'auteur | Informe poliment sans rentrer dans les motifs |

---

## 3. Machine à états et modèle de données

### 3.1 Enum `SubmissionStatus` — nouveaux cases

```php
// app/Enums/SubmissionStatus.php
case RejectedPendingLepis = 'rejected_pending_lepis';
case RedirectedToLepis    = 'redirected_to_lepis';
```

**Labels** :
- `RejectedPendingLepis` → « Rejet en attente Lepis » (admin only)
- `RedirectedToLepis` → « Transmis au bulletin Lepis » (admin + auteur)

**Couleurs** :
- `RejectedPendingLepis` → `amber`
- `RedirectedToLepis` → `teal`

**`isTerminal()`** : `RedirectedToLepis` oui, `RejectedPendingLepis` non.

### 3.2 Transitions

```php
// SubmissionStateMachine::TRANSITIONS
'under_initial_review'     => ['revision_requested', 'under_peer_review', 'rejected', 'rejected_pending_lepis'],
'under_peer_review'        => ['revision_after_review', 'accepted', 'rejected', 'rejected_pending_lepis'],
'revision_after_review'    => ['under_peer_review', 'accepted', 'rejected', 'rejected_pending_lepis'],
'rejected_pending_lepis'   => ['redirected_to_lepis', 'rejected'],
'redirected_to_lepis'      => [],  // terminal
```

`RejectedPendingLepis → Accepted` est **interdit** (un article parti en file Lepis ne peut pas repasser sur le chemin standard sans un rejet/redirection explicite).

### 3.3 Migration `add_lepis_decision_fields_to_submissions`

```php
Schema::table('submissions', function (Blueprint $table) {
    $table->timestamp('lepis_decision_at')->nullable()->after('redirected_to_lepis');
    $table->foreignId('lepis_decided_by_user_id')->nullable()
          ->after('lepis_decision_at')
          ->constrained('users')
          ->onDelete('set null');
});
```
Trace temporelle et attribution de la décision Lepis. Le statut (`RedirectedToLepis` vs `Rejected` final) suffit à savoir si Lepis a accepté ou refusé.

Le champ existant `redirected_to_lepis` (boolean) est **conservé** : il sert de flag historique « cet article a été à un moment en file Lepis ». Le statut reste la source de vérité pour l'état courant ; le flag permet les requêtes de reporting (`WHERE redirected_to_lepis = true`).

### 3.4 Model `Submission` — fillable et casts

Ajouter `lepis_decision_at`, `lepis_decided_by_user_id` à `$fillable`.
Ajouter `lepis_decision_at => 'datetime'` aux casts.
Ajouter la relation :
```php
public function lepisDecidedBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'lepis_decided_by_user_id');
}
```

### 3.5 Helper `publicStatus()` sur `Submission`

```php
public function publicStatus(): SubmissionStatus
{
    if ($this->status !== SubmissionStatus::RejectedPendingLepis) {
        return $this->status;
    }

    $lastPublicTransition = $this->transitions()
        ->where('action', SubmissionTransition::ACTION_STATUS_CHANGED)
        ->where('to_status', '!=', SubmissionStatus::RejectedPendingLepis->value)
        ->orderByDesc('created_at')
        ->first();

    return $lastPublicTransition?->to_status
        ? SubmissionStatus::from($lastPublicTransition->to_status)
        : SubmissionStatus::UnderInitialReview;  // fallback prudent
}
```

---

## 4. Logique du state machine

### 4.1 Mail `SubmissionDecision` — déclenchement révisé

Dans `SubmissionStateMachine::transition()`, le bloc existant :
```php
if (in_array($target, [SubmissionStatus::Accepted, SubmissionStatus::Rejected], true)) {
    Mail::to($submission->author)->queue(new SubmissionDecision($submission));
}
```
**Reste inchangé**. `SubmissionDecision` part donc sur :
- `→ Accepted` ✅
- `→ Rejected` direct ou via `RejectedPendingLepis → Rejected` ✅ (l'auteur reçoit le mail de rejet au moment où Lepis refuse)

Et ne part **pas** sur `→ RejectedPendingLepis` ou `→ RedirectedToLepis` — conforme à la spec.

### 4.2 Nouveaux blocs dans `SubmissionStateMachine::transition()`

```php
// Entrée en file Lepis : notif aux admins + flag historique
if ($target === SubmissionStatus::RejectedPendingLepis) {
    $submission->redirected_to_lepis = true;
    $submission->save();

    $admins = User::where('role', User::ROLE_ADMIN)
        ->orWhereHas('capabilities', fn ($q) => $q->where('capability', EditorialCapability::CHIEF_EDITOR))
        ->get()
        ->unique('id');

    foreach ($admins as $admin) {
        Mail::to($admin)->queue(new LepisQueueNotification($submission));
    }
}

// Décision Lepis (accept OU refuse) : timestamp + auteur de la décision
if (in_array($target, [SubmissionStatus::RedirectedToLepis, SubmissionStatus::Rejected], true)
    && $current === SubmissionStatus::RejectedPendingLepis
) {
    $submission->lepis_decision_at = now();
    $submission->lepis_decided_by_user_id = $actor->id;
    $submission->save();
}

// Mail à l'auteur quand Lepis accepte (dédié, pas le SubmissionDecision)
if ($target === SubmissionStatus::RedirectedToLepis) {
    Mail::to($submission->author)->queue(new ArticleRedirectedToLepis($submission));
}
```

---

## 5. Controller + Routing — file Lepis

### 5.1 Route

```php
// routes/admin.php, dans le groupe 'web, admin', prefix 'revue'
Route::get('/file-lepis', [LepisQueueController::class, 'index'])
    ->middleware('can:access-lepis-queue')
    ->name('journal.lepis-queue');
```

### 5.2 Gate

Dans `AppServiceProvider::boot()` :
```php
Gate::define('access-lepis-queue', function (User $user) {
    return $user->isAdmin();  // role admin OU chief_editor capability (cf. User::isAdmin())
});
```

### 5.3 Controller

**Fichier** : `app/Http/Controllers/Admin/Journal/LepisQueueController.php`

```php
class LepisQueueController extends Controller
{
    public function index()
    {
        $this->authorize('access-lepis-queue');

        $submissions = Submission::with(['author', 'editor', 'transitions' => fn ($q) =>
                $q->where('to_status', SubmissionStatus::RejectedPendingLepis->value)
                  ->orderByDesc('created_at')
                  ->limit(1)
            ])
            ->where('status', SubmissionStatus::RejectedPendingLepis->value)
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.journal.lepis-queue', compact('submissions'));
    }
}
```

### 5.4 EditorialQueueController — routage de la transition

Dans la méthode `transition()` existante, remplacer le bloc actuel :
```php
if ($target === SubmissionStatus::Rejected && ($validated['redirect_to_lepis'] ?? false)) {
    $submission->update(['redirected_to_lepis' => true]);
}
```

Par :
```php
// Case Lepis cochée : on redirige la transition vers le statut intermédiaire
if ($target === SubmissionStatus::Rejected && ($validated['redirect_to_lepis'] ?? false)) {
    $target = SubmissionStatus::RejectedPendingLepis;
}
```
(Le flag `redirected_to_lepis` est maintenant posé par le state machine au moment de l'entrée en `RejectedPendingLepis`, on supprime le `->update()` ici.)

---

## 6. Vues

### 6.1 `resources/views/admin/journal/lepis-queue.blade.php`

Layout `layouts.admin`, section contenu :
- **Breadcrumb** : « Revue > File Lepis »
- **Header** : titre + compteur « X soumission(s) en attente de décision Lepis »
- **Table** par soumission :
  - Colonne 1 : Titre (lien vers `admin.submissions.show`)
  - Colonne 2 : Auteur (nom + email tronqué)
  - Colonne 3 : Éditeur (nom)
  - Colonne 4 : Date d'entrée en file (= date de la dernière transition `to_status=rejected_pending_lepis`)
  - Colonne 5 : Motifs (extrait des notes de la transition, 150 chars)
  - Colonne 6 : Actions — 2 boutons :
    - « Transmettre à Lepis » (vert/teal) — ouvre la modale transition existante avec `target_status=redirected_to_lepis`, notes optionnelles
    - « Rejeter définitivement » (rouge) — ouvre la modale transition existante avec `target_status=rejected`, notes **requises** (le mail de rejet les inclut)
- Lien depuis le menu admin (sidebar) : « 📋 File Lepis [N] » avec badge numérique du count.

### 6.2 Modifications des vues auteur

**Fichiers à toucher** :
- `resources/views/journal/submissions/index.blade.php` — remplacer `$submission->status` par `$submission->publicStatus()` pour le badge et le label
- `resources/views/journal/submissions/show.blade.php` — idem
- `resources/views/member/dashboard.blade.php` — si le dashboard membre affiche le statut des soumissions, idem

**Note** : la transition `RejectedPendingLepis` apparaît dans `$submission->transitions` (journal complet), mais côté auteur la timeline est déjà filtrée (par pattern P0 pour `awaiting_author_approval`). Pattern à réutiliser : filtrer aussi `rejected_pending_lepis` des transitions côté auteur.

### 6.3 Modifications fiche soumission admin

Sur `/extranet/submissions/{id}` (`show.blade.php`) :
- Si `$submission->status === SubmissionStatus::RejectedPendingLepis` : bandeau info au-dessus de la carte Statut
  ```
  ℹ️ Ce statut est invisible pour l'auteur. Il continue à voir
     "[publicStatus()->label()]". Décision attendue via la File Lepis.
  ```
- Lien « → File Lepis » dans le bandeau

### 6.4 Badge sidebar admin

Dans `resources/views/layouts/admin.blade.php` (ou partial) :
```blade
@can('access-lepis-queue')
<a href="{{ route('admin.journal.lepis-queue') }}" class="nav-item">
    <svg>...</svg>
    File Lepis
    @php $count = \App\Models\Submission::where('status', 'rejected_pending_lepis')->count(); @endphp
    @if($count > 0)
        <span class="badge badge-amber">{{ $count }}</span>
    @endif
</a>
@endcan
```
Pas de cache nécessaire pour un count aussi rare (< 1 requête/seconde en pic).

---

## 7. Mailables

### 7.1 `ArticleRedirectedToLepis` (à l'auteur)

**Fichier** : `app/Mail/ArticleRedirectedToLepis.php`
**Template** : `resources/views/emails/article-redirected-to-lepis.blade.php` (Markdown)
**Sujet** : « Votre article a été transmis au bulletin Lepis »

**Contenu** :
- Salutation
- « Après examen, votre manuscrit *{{ title }}* a été jugé mieux adapté au bulletin **Lepis**, publication interne d'OREINA dédiée aux notes courtes, observations de terrain et vulgarisation. »
- « Le rédacteur en chef de Lepis prendra directement contact avec vous dans les prochains jours pour vous proposer les suites possibles (publication dans Lepis sous une forme adaptée, ou éventuels ajustements). »
- « Vous pouvez consulter cet état depuis votre espace auteur : [lien `/revue/mes-soumissions`] »
- Signature équipe éditoriale Chersotis

### 7.2 `LepisQueueNotification` (aux admins)

**Fichier** : `app/Mail/LepisQueueNotification.php`
**Template** : `resources/views/emails/lepis-queue-notification.blade.php` (Markdown)
**Sujet** : « Nouvelle soumission en file Lepis — Chersotis »

**Contenu** :
- « Une soumission vient d'être proposée pour redirection vers le bulletin Lepis. »
- Titre, auteur, éditeur qui a rejeté, motif/notes extraites de la transition
- Bouton « Voir la file Lepis » → URL absolue vers `/extranet/revue/file-lepis`
- « Action attendue : transmettre à Lepis (accepté) ou rejeter définitivement (l'auteur sera alors notifié). »

---

## 8. Permissions — synthèse

| Action | Qui |
|--------|-----|
| Rejeter avec case Lepis cochée (→ RejectedPendingLepis) | Éditeur + rédacteur en chef (policy existante `transitionTo(Rejected)`) |
| Voir `/extranet/revue/file-lepis` | Admin (role admin OU capability chief_editor) |
| Bouton « Transmettre à Lepis » | Admin (policy `transitionTo(RedirectedToLepis)`) |
| Bouton « Rejeter définitivement » depuis la file | Admin (policy `transitionTo(Rejected)` depuis `RejectedPendingLepis`) |

**Adaptation `SubmissionPolicy::transitionTo`** : ajouter une règle explicite pour les transitions sortantes de `RejectedPendingLepis` (admin only). Les transitions entrantes (vers `RejectedPendingLepis`) suivent la règle existante de `Rejected` (éditeur + rédac chef).

---

## 9. Tests

### 9.1 `tests/Feature/Journal/LepisQueueTest.php`

```
✓ Rejeter avec case Lepis cochée → statut RejectedPendingLepis (pas Rejected)
✓ Pas de mail SubmissionDecision envoyé à l'auteur lors de l'entrée en file Lepis
✓ Mail LepisQueueNotification envoyé à tous les admins + chief_editors
✓ Flag redirected_to_lepis posé à true
✓ GET /extranet/revue/file-lepis accessible à un admin (role admin)
✓ GET /extranet/revue/file-lepis accessible à un chief_editor
✓ GET /extranet/revue/file-lepis renvoie 403 à un simple editor (sans role admin)
✓ La page liste les soumissions RejectedPendingLepis uniquement
✓ Click "Transmettre à Lepis" depuis la file → status RedirectedToLepis, mail ArticleRedirectedToLepis à l'auteur, lepis_decision_at rempli, lepis_decided_by_user_id rempli
✓ Click "Rejeter définitivement" depuis la file → status Rejected, mail SubmissionDecision à l'auteur, lepis_decision_at rempli
```

### 9.2 `tests/Unit/Models/SubmissionPublicStatusTest.php`

```
✓ publicStatus() retourne status direct si pas RejectedPendingLepis
✓ publicStatus() sur RejectedPendingLepis retourne le dernier statut public du log
✓ publicStatus() fallback UnderInitialReview si aucun transition log trouvé
✓ publicStatus() ignore bien la transition vers RejectedPendingLepis elle-même
```

### 9.3 `tests/Unit/Services/SubmissionStateMachineLepisTest.php`

```
✓ Transition UnderInitialReview → RejectedPendingLepis autorisée
✓ Transition UnderPeerReview → RejectedPendingLepis autorisée
✓ Transition RevisionAfterReview → RejectedPendingLepis autorisée
✓ Transition RejectedPendingLepis → RedirectedToLepis autorisée
✓ Transition RejectedPendingLepis → Rejected autorisée
✓ Transition RejectedPendingLepis → Accepted interdite (IllegalTransitionException)
✓ Transition RedirectedToLepis → Published interdite (terminal)
✓ Transition RedirectedToLepis → Rejected interdite (terminal)
✓ Submitted → RejectedPendingLepis interdite (doit passer par UnderInitialReview ou UnderPeerReview)
```

---

## 10. Hors scope (différé)

- **Rôle `lepis_editor` dédié** — sera traité lors de la revue des droits (gestion fine entre revue / vie associative / Lepis)
- **Page Lepis** (leur propre app / UI) — complètement indépendant de Chersotis
- **Transfert automatique de métadonnées** vers Lepis au moment du `RedirectedToLepis` (export JSON, push API, etc.)
- **Relance automatique** si une soumission reste en file Lepis plus de X jours sans décision
- **Statistiques** : combien d'articles redirigés vers Lepis, taux d'acceptation côté Lepis, etc.

---

## 11. Plan d'implémentation (vue d'ensemble)

1. Migration `add_lepis_decision_fields_to_submissions`
2. Enum `SubmissionStatus` : 2 nouveaux cases + labels + colors + isTerminal
3. Submission model : fillable, casts, relation lepisDecidedBy, helper publicStatus()
4. State machine : transitions + logique mail (suppression/ajout) + tests unit
5. Mailables `ArticleRedirectedToLepis` + `LepisQueueNotification` + templates
6. Gate `access-lepis-queue`
7. Route + `LepisQueueController` + vue `lepis-queue.blade.php`
8. Modification `EditorialQueueController::transition()` (routage RejectedPendingLepis)
9. Modification vues auteur (`publicStatus()` partout)
10. Bandeau info sur fiche soumission admin (statut invisible pour l'auteur)
11. Badge sidebar admin « File Lepis [N] »
12. Tests feature + unit
13. Smoke test manuel + merge

---

## 12. Critères de succès

- [ ] Cliquer "Rejeter" avec case Lepis cochée envoie la soumission en `RejectedPendingLepis`, pas en `Rejected`
- [ ] L'auteur ne voit aucun changement sur son dashboard à ce moment-là (statut reste "En évaluation" ou équivalent)
- [ ] Les admins reçoivent un mail `LepisQueueNotification` dans la minute
- [ ] La page `/extranet/revue/file-lepis` liste la soumission, accessible à l'admin
- [ ] « Transmettre à Lepis » : soumission passe à `RedirectedToLepis`, l'auteur reçoit `ArticleRedirectedToLepis`, voit le statut final sur son dashboard
- [ ] « Rejeter définitivement » : soumission passe à `Rejected`, l'auteur reçoit `SubmissionDecision` avec les motifs
- [ ] Aucune régression sur le flow rejet sans case Lepis (reject direct fonctionne comme avant)
- [ ] Coverage tests ≥ 90% sur les nouvelles lignes

---

*Spec validée le 2026-04-18 — brainstorming session avec David*
