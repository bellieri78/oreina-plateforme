# Spec — Checklist conformité éditeur avant maquettage (Chersotis P1 #C)

**Date** : 2026-04-18
**Item P1** : #3 de la section 17 du doc `implications_dev_reunion_chersotis_20260416.md`
**Origine** : Réunion Chersotis du 16 avril 2026, section 4 (clarification du rôle de l'éditeur)
**Statut** : Spec validée — prêt pour plan d'implémentation

---

## 1. Contexte et motivation

La réunion du 16 avril a clarifié que l'éditeur doit **vérifier la conformité formelle** du manuscrit avant de le passer en maquettage, pour ne pas surcharger le maquettiste : format biblio respecté, affiliations complètes, figures numérotées, remerciements présents, etc.

Cette vérification est aujourd'hui **mentale et non traçée**. L'objectif : donner à l'éditeur une **checklist de 9 items** qu'il coche au fil de sa relecture, visible sur la fiche soumission admin, pour servir de pense-bête et laisser une trace de la vérification effectuée.

La checklist est **optionnelle et non-bloquante** : l'éditeur peut passer en maquettage sans avoir tout coché. Elle sert d'aide-mémoire, pas de garde-fou dur.

---

## 2. Décisions de design (validées en brainstorming)

| Choix | Option retenue | Raison |
|-------|----------------|--------|
| Items | **9 items figés** (liste fermée, enum) | Périmètre bien défini après échange avec David |
| Stockage | **Colonne JSON array** `conformity_checklist` | Simple, extensible, requêtable |
| Emplacement UI | **Card permanente** sur la fiche soumission admin, colonne droite | Coche incrémentale au fil de la relecture |
| Visibilité | **Stades éditoriaux** (submitted → accepted) puis **read-only** à partir de `in_production` | Témoin de ce qui a été validé avant maquettage |
| Sauvegarde | **PATCH Ajax par toggle** (pas de bouton « Enregistrer ») | Évite le piège UX qu'on vient de corriger sur capabilities |
| Bloquant au passage en maquettage | **Non** | Conforme à l'intention du comité éditorial (aide-mémoire) |
| Permissions | Éditeur assigné + rédac chef + admin | Les reviewers / auteurs n'y touchent pas |

---

## 3. Modèle de données

### 3.1 Migration `add_conformity_checklist_to_submissions`

```php
Schema::table('submissions', function (Blueprint $table) {
    $table->jsonb('conformity_checklist')->nullable()->after('editor_notes');
});
```

Format stocké : tableau JSON des clés d'items cochés, ex. :
```json
["biblio_format", "author_affiliations", "figures_numbered"]
```

Un item absent = non coché. `null` et `[]` sont équivalents (rien coché).

### 3.2 Enum `ConformityChecklistItem`

Nouveau fichier `app/Enums/ConformityChecklistItem.php` :

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

### 3.3 Model `Submission` — fillable, casts, helpers

```php
// $fillable : ajouter 'conformity_checklist'
// $casts : 'conformity_checklist' => 'array'

public function conformityChecked(ConformityChecklistItem $item): bool
{
    return in_array($item->value, $this->conformity_checklist ?? [], true);
}

public function conformityProgress(): array
{
    return [
        'checked' => count($this->conformity_checklist ?? []),
        'total'   => count(ConformityChecklistItem::cases()),
    ];
}
```

---

## 4. Controller + route

### 4.1 Route

```php
// routes/admin.php, dans le groupe admin
Route::patch('submissions/{submission}/conformity', [SubmissionController::class, 'updateConformity'])
    ->name('submissions.conformity.update');
```

### 4.2 Controller `Admin\SubmissionController::updateConformity`

```php
public function updateConformity(Request $request, Submission $submission)
{
    $this->authorize('updateConformity', $submission);

    $validated = $request->validate([
        'item' => ['required', Rule::enum(ConformityChecklistItem::class)],
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

Réponse : `{ "checked": 3, "total": 9 }` pour que le frontend mette à jour le compteur sans reload.

### 4.3 Policy — `SubmissionPolicy::updateConformity`

```php
public function updateConformity(User $user, Submission $submission): bool
{
    return $user->isAdmin()
        || $user->hasCapability(EditorialCapability::CHIEF_EDITOR)
        || $submission->editor_id === $user->id;
}
```

Reviewers et auteurs : interdit. La Policy renvoie false → 403 côté HTTP.

---

## 5. UI

### 5.1 Card dans `/extranet/submissions/{id}` (admin show)

Emplacement : dans la colonne droite, **après la card « Équipe éditoriale »**, **avant la card « Informations »**.

Visible quand `$submission->status->value` est dans :
- `submitted`, `under_initial_review`, `under_peer_review`
- `revision_requested`, `revision_after_review`
- `accepted`

À partir de `in_production`, `awaiting_author_approval`, `published`, `rejected`, `rejected_pending_lepis`, `redirected_to_lepis` → **affichage read-only** (checkboxes désactivées, pas d'Ajax, résumé « X/9 items validés avant maquettage »).

### 5.2 Rendu Blade

```blade
@php
    $conformityStages = ['submitted','under_initial_review','under_peer_review','revision_requested','revision_after_review','accepted'];
    $conformityEditable = in_array($submissionStatusValue, $conformityStages, true)
        && $policy->updateConformity($authUser, $submission);
    $progress = $submission->conformityProgress();
@endphp

<div class="card" style="margin-bottom: 1.5rem; border-left: 4px solid #d97706;" x-data="{ progress: @js($progress) }">
    <div class="card-header">
        <h3 class="card-title" style="display:flex; align-items:center; justify-content:space-between;">
            <span>
                <svg>...</svg>
                Checklist conformité
            </span>
            <span style="font-size:0.8rem;font-weight:500;color:#d97706;"
                  x-text="progress.checked + '/' + progress.total"></span>
        </h3>
    </div>
    <div class="card-body">
        <p style="font-size:0.8rem;color:#6b7280;margin:0 0 0.75rem 0;">
            À vérifier avant de passer l'article en maquettage. Non bloquant.
        </p>
        @foreach(\App\Enums\ConformityChecklistItem::cases() as $item)
            <label style="display:flex;gap:0.5rem;align-items:flex-start;padding:0.375rem 0;border-bottom:1px dashed #f3f4f6;cursor:{{ $conformityEditable ? 'pointer' : 'not-allowed' }};">
                <input type="checkbox"
                       value="{{ $item->value }}"
                       {{ $submission->conformityChecked($item) ? 'checked' : '' }}
                       {{ $conformityEditable ? '' : 'disabled' }}
                       x-on:change="toggleConformityItem($event, '{{ $item->value }}')"
                       style="margin-top:3px;accent-color:#d97706;flex-shrink:0;">
                <span style="font-size:0.85rem;">
                    <strong>{{ $item->label() }}</strong><br>
                    <em style="color:#6b7280;font-size:0.75rem;">{{ $item->description() }}</em>
                </span>
            </label>
        @endforeach
    </div>
</div>
```

### 5.3 JS (Alpine / vanilla) — toggle Ajax

Dans `resources/views/admin/submissions/show.blade.php` à la fin, ajouter un bloc script (ou dans un composant réutilisable) :

```html
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
        // Mise à jour Alpine du compteur
        const card = checkbox.closest('[x-data]');
        if (card && card._x_dataStack) {
            Alpine.$data(card).progress = data;
        }
    } catch (e) {
        checkbox.checked = !checkbox.checked; // rollback
        alert('Erreur lors de la sauvegarde — réessayez.');
    } finally {
        checkbox.disabled = false;
    }
};
</script>
```

Pré-requis : le layout admin a déjà `<meta name="csrf-token" content="{{ csrf_token() }}">` dans le `<head>` — **à vérifier et ajouter si absent**.

---

## 6. Tests

### 6.1 `tests/Feature/Admin/ConformityChecklistTest.php`

```
✓ Éditeur assigné peut cocher un item (retourne progress 1/9)
✓ Éditeur assigné peut décocher un item (retourne progress 0/9)
✓ Chief editor peut cocher même sans être assigné
✓ Admin peut cocher
✓ Reviewer ne peut pas cocher (403)
✓ Auteur ne peut pas cocher (403)
✓ Toggle idempotent : cocher 2× = même état (1 seule occurrence dans le tableau)
✓ Décocher un item non présent reste à []
✓ Validation : item invalide → 422
✓ Validation : checked manquant → 422
✓ Checklist persiste à travers revision_requested → under_initial_review
```

### 6.2 `tests/Unit/Models/ConformityChecklistTest.php`

```
✓ conformityChecked() retourne false quand checklist est null
✓ conformityChecked() retourne true/false correctement après update
✓ conformityProgress() retourne 0/9 par défaut
✓ conformityProgress() reflète le nombre d'items cochés
✓ cast array fonctionne (persiste et relit un tableau)
```

### 6.3 `tests/Unit/Enums/ConformityChecklistItemTest.php`

```
✓ Tous les cases ont un label non vide
✓ Tous les cases ont une description non vide
✓ La liste contient exactement 9 cases
```

---

## 7. Hors scope (différé)

- **Rappel doux** dans la modale « Passer en maquettage » (« 7/9 validés — continuer ? ») — pas bloquant, sera ajouté si besoin signalé en usage réel
- **Audit fin par item** (qui a coché quoi, quand) — over-engineering pour un outil de confort
- **Export / statistiques** des checklists — aucun besoin actuel
- **Items configurables via UI** — la liste fermée suffit, toute modification passe par code
- **Blocage dur de la transition** en maquettage si < 9/9 — le comité a explicitement demandé que ce soit optionnel

---

## 8. Plan d'implémentation (vue d'ensemble)

1. Migration `add_conformity_checklist_to_submissions`
2. Enum `ConformityChecklistItem` + tests unit
3. Model `Submission` : fillable, casts, helpers + tests unit
4. Policy `SubmissionPolicy::updateConformity`
5. Controller `updateConformity` + route + tests feature
6. Card checklist dans `admin/submissions/show.blade.php` + meta csrf-token vérifiée
7. Script `toggleConformityItem` + hand-off Alpine
8. Smoke test manuel

---

## 9. Critères de succès

- [ ] L'éditeur voit la card checklist dans la colonne droite de la fiche soumission
- [ ] Il peut cocher/décocher n'importe quel item, la sauvegarde est instantanée
- [ ] Le compteur « X/9 » se met à jour sans recharger la page
- [ ] Quand la soumission passe en maquettage, la card devient read-only avec un état gelé
- [ ] Les reviewers et l'auteur ne peuvent pas modifier la checklist
- [ ] Aucune régression sur le reste du workflow éditorial
- [ ] Suite de tests ≥ 90 % de couverture sur le nouveau code

---

*Spec validée le 2026-04-18 — brainstorming avec David.*
