# Formulaire d'édition contact admin — refonte multi-cartes + consolidation `phone`

**Date** : 2026-05-03
**Statut** : design validé, prêt pour planification
**Cible** : `/extranet/members/{id}/edit` (route `admin.members.edit`/`update`) et `/extranet/members/create`

## Contexte

Suite à la refonte de la fiche show (`/extranet/members/{id}`) qui ajoute une vue dense et hiérarchisée, le formulaire d'édition reste minimaliste : un seul long formulaire dans une carte de 800px qui n'édite que 9 champs sur la trentaine présents en base. Beaucoup d'attributs utiles (civilité, mobile, profession, intérêts, newsletter, consentements RGPD, type de contact) ne peuvent pas être édités depuis le backoffice et nécessitent un accès direct à la DB.

Cette refonte vise à :

1. **Aligner visuellement** le formulaire sur la fiche show (multi-cartes, mêmes classes `.card`).
2. **Compléter les champs éditables** pour couvrir tous les attributs métier raisonnables.
3. **Consolider la redondance `phone`** vs `telephone_fixe` + `mobile` en supprimant `phone`.

## Principes

- **Un seul `<form>`** englobant les 5 cartes, un seul submit.
- **Cartes déroulées par défaut** (pas d'accordéon) — l'admin veut tout voir d'un coup.
- **Sidebar récap minimaliste** côté `edit` (avatar + nom + numéro + date inscription + bouton retour). Pas en `create` (rien à résumer).
- **Partial `_form.blade.php` réutilisable** entre `edit` et `create`.
- **Aucune migration de schema** sauf la suppression de la colonne `members.phone`.
- **Cohérence visuelle** stricte avec la fiche show : mêmes classes, mêmes couleurs, mêmes patterns.

## Architecture du layout

### Page `edit.blade.php`

```
┌─────────────────────────────────────────────────────────────────┐
│  Breadcrumb : Contacts / Marie Durand / Modifier                │
├──────────────────┬──────────────────────────────────────────────┤
│  SIDEBAR RÉCAP   │  <form>                                      │
│  (1fr)           │   ┌─ Identité ───┐                           │
│                  │   │              │                           │
│  Avatar          │   ├─ Contact ────┤                           │
│  Nom complet     │   │              │                           │
│  N° M0042        │   ├─ Adresse ────┤                           │
│  Inscrit le      │   │              │                           │
│  [< Retour]      │   ├─ Préférences ┤                           │
│                  │   │              │                           │
│                  │   └─ Statut RGPD ┘                           │
│                  │                                              │
│                  │   [ Enregistrer ]  [ Annuler ]               │
│                  │  </form>                                     │
└──────────────────┴──────────────────────────────────────────────┘
```

Layout : `<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">`. Sidebar dans la 1re colonne, `<form>` complet dans la 2e.

### Page `create.blade.php`

Même chose, mais **sans sidebar** (la grille devient juste le `<form>` pleine largeur dans une seule colonne `max-width: 800px;`). Le partial `_form.blade.php` est inclus tel quel.

## Composants

### Partial `_form.blade.php` (5 cartes)

Toutes les cartes utilisent les classes existantes : `.card`, `.card-header`, `.card-title`, `.card-body`, `.form-group`, `.form-label`, `.form-input`. Erreurs affichées via `@error('xxx') ... @enderror` avec le pattern existant (`<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">`).

**Carte 1 — Identité**

| Champ | Type | Validation |
|---|---|---|
| `civilite` | `<select>` : (vide) / `M.` / `Mme` / `Autre` | `nullable\|in:M.,Mme,Autre` |
| `first_name *` | `<input type="text">` | `required\|string\|max:255` |
| `last_name *` | `<input type="text">` | `required\|string\|max:255` |
| `birth_date` | `<input type="date">` | `nullable\|date\|before:today` |
| `profession` | `<input type="text">` | `nullable\|string\|max:255` |

Layout : ligne 1 = grid `auto 1fr 1fr` (civilité étroit, prénom, nom). Ligne 2 = grid `1fr 1fr` (date + profession).

**Carte 2 — Contact**

| Champ | Type | Validation |
|---|---|---|
| `email *` | `<input type="email">` | `required\|email\|unique:members,email,{$member->id}` |
| `mobile` | `<input type="tel">` placeholder `06 XX XX XX XX` | `nullable\|string\|max:20` |
| `telephone_fixe` | `<input type="tel">` placeholder `01 XX XX XX XX` | `nullable\|string\|max:20` |

Layout : email pleine largeur, puis grid `1fr 1fr` (mobile + tél fixe).

**Carte 3 — Adresse**

| Champ | Type | Validation |
|---|---|---|
| `address` | `<input type="text">` | `nullable\|string\|max:255` |
| `postal_code` | `<input type="text">` | `nullable\|string\|max:10` |
| `city` | `<input type="text">` | `nullable\|string\|max:255` |
| `country` | `<input type="text">` (défaut `France`) | `nullable\|string\|max:255` |

Layout : adresse pleine largeur, puis grid `1fr 2fr 1fr` (cp + ville + pays — pattern existant inchangé).

**Carte 4 — Préférences**

| Champ | Type | Validation |
|---|---|---|
| `contact_type` | `<select>` : `individuel` / `organisation` | `required\|in:individuel,organisation` |
| `newsletter_subscribed` | `<input type="checkbox">` (libellé "Inscrit à la newsletter") | `boolean` (presence-based via `$request->has`) |
| `interests` | `<textarea rows="3">` | `nullable\|string\|max:2000` |

Layout : contact_type sur sa ligne, newsletter checkbox sur la suivante, intérêts en textarea pleine largeur.

**Carte 5 — Statut & RGPD**

| Champ | Type | Validation |
|---|---|---|
| `is_active` | `<input type="checkbox">` (existant — libellé "Contact actif") | `boolean` (presence-based) |
| `consent_communication` | `<input type="checkbox">` libellé "Autorise les communications associatives" | `boolean` (presence-based) |
| `consent_image` | `<input type="checkbox">` libellé "Autorise l'utilisation de son image" | `boolean` (presence-based) |

Les 3 checkboxes en colonne, pas de grid.

### Boutons

À la fin du formulaire (HORS des cartes), avec border-top de séparation :

```blade
<div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="{{ route('admin.members.show', $member) }}" class="btn btn-secondary">Annuler</a>
</div>
```

(En `create`, le lien Annuler pointe vers `route('admin.members.index')`.)

### Sidebar récap (page `edit` uniquement)

Carte minimaliste avec :

```
[Avatar initiales bg-blue]
Marie DURAND
N° M0042
Inscrite le 12/03/2024

[< Retour à la fiche]
```

Le bouton retour pointe vers `route('admin.members.show', $member)`. Cette sidebar n'est PAS un partial réutilisable — elle vit dans `edit.blade.php` directement.

## Modifications du controller `MemberController`

### `store()` (création)

Mêmes règles de validation que `update()` (cf. ci-dessous), avec `unique:members,email` (pas d'`,$member->id` car le membre n'existe pas encore).

### `update()`

Validation enrichie :

```php
$validated = $request->validate([
    'civilite' => ['nullable', 'in:M.,Mme,Autre'],
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

### `Member` model

Vérifier que tous les champs ajoutés sont dans `$fillable`. Champs concernés : `civilite`, `birth_date`, `profession`, `mobile`, `telephone_fixe`, `interests`, `contact_type`, `newsletter_subscribed`, `consent_communication`, `consent_image`. Si certains manquent, les ajouter.

`birth_date` doit être casté `date` pour bénéficier des helpers Carbon.

## Consolidation `phone`

### Inventaire de la donnée (vérifié)

- 922 members au total
- 283 ont `phone` renseigné
- 4 ont `phone` SEUL (sans `mobile` ni `telephone_fixe`)
- 279 ont `phone` dupliqué — TOUS égaux à `mobile`
- 0 ont `phone` différent de `mobile` ET de `telephone_fixe`

Donc la migration est sûre : aucun risque de perte de donnée.

### Migration `drop_phone_from_members`

```php
public function up(): void
{
    // Backfill : copier les rares phone-only vers mobile
    DB::statement("UPDATE members SET mobile = phone WHERE phone IS NOT NULL AND mobile IS NULL");

    // Drop la colonne (PG 9.6 compat)
    DB::statement("ALTER TABLE members DROP COLUMN phone");
}

public function down(): void
{
    DB::statement("ALTER TABLE members ADD COLUMN phone VARCHAR(255) NULL");
}
```

NB : le `down()` recrée la colonne mais ne re-renseigne pas les valeurs (perte irréversible côté rollback). C'est acceptable parce que `mobile` contient toute l'info utile.

### Impacts code (Member-related uniquement)

| Fichier | Action |
|---|---|
| `app/Models/Member.php` | Retirer `'phone'` du `$fillable`. Vérifier qu'aucun cast/scope/accessor n'y fait référence. |
| `resources/views/admin/members/show.blade.php` | Sidebar info ligne "Téléphone" : afficher `$member->mobile` ou `$member->telephone_fixe` selon présence. Si les 2 présents, afficher les 2 sur 2 lignes. |
| `resources/views/admin/members/_form.blade.php` | Réécrit dans cette feature, le champ `phone` disparaît. |
| `resources/views/member/profile.blade.php` | Si la page profil membre affiche/édite `phone`, remplacer par `mobile` + `telephone_fixe`. |
| `app/Http/Controllers/Member/ProfileController.php` | Idem si validation/update touche `phone`. |
| `app/Http/Controllers/Api/WebhookController.php::processMembership` | Le payload HelloAsso a un `payer.phone` que le code actuel pousse dans `Member::create([..., 'phone' => $payer['phone']])`. Remplacer par `'mobile' => $payer['phone'] ?? null`. |
| `app/Services/LegacyMembershipImportService.php` | Remplacer `phone` par `mobile` dans le mapping import. |
| `database/seeders/MemberSeeder.php` | Remplacer `phone` par `mobile` dans les fixtures. |
| `app/Http/Controllers/Admin/MapController.php` + view | Remplacer `phone` par `mobile` si applicable. |
| `app/Models/ExportTemplate.php` / `ImportTemplate.php` | Si `phone` est listé comme colonne mappable, remplacer par `mobile` + `telephone_fixe`. |

**Hors scope** (ne pas toucher) : `Structure->phone`, `User->phone`, `Donation->donor_phone` — ce sont des colonnes indépendantes sur d'autres tables.

## Tests

Un seul fichier feature : `tests/Feature/Admin/MemberEditFormTest.php`

- **`test_edit_form_renders_5_cards`** — la page rend les 5 titres de cartes : `Identité`, `Contact`, `Adresse`, `Préférences`, `Statut & RGPD`.
- **`test_edit_form_pre_fills_existing_values`** — un membre avec civilité Mme, profession "Botaniste", mobile "06 12...", `consent_image=true`, `interests='papillons alpins'` → tous ces champs apparaissent pré-remplis dans le HTML.
- **`test_update_persists_new_fields`** — POST avec tous les nouveaux champs → tous écrits en DB (assertion par `$member->fresh()`).
- **`test_update_validation_rejects_future_birth_date`** — POST avec birth_date dans le futur → erreur de session sur ce champ.
- **`test_update_validation_rejects_invalid_civilite`** — POST avec civilité "Mlle" → erreur.
- **`test_update_validation_rejects_invalid_contact_type`** — POST avec contact_type "famille" → erreur.
- **`test_update_uncheck_unchecks_booleans`** — un membre avec `newsletter_subscribed=true`, `consent_image=true` ; on POST sans cocher ces cases → DB passe à `false` pour les deux.
- **`test_phone_column_dropped_after_migration`** — vérification `Schema::hasColumn('members', 'phone')` est `false`.
- **`test_create_form_does_not_render_sidebar_recap`** — la page `/extranet/members/create` ne contient pas de bloc "Retour à la fiche" (pas de membre à résumer).

## Hors scope (différé)

Ces champs présents en DB ne sont pas inclus dans cette refonte (cf. discussion utilisateur 2026-05-03) :

- `photo_path` (upload d'image — feature à part entière)
- `latitude` / `longitude` (calculés via géocodage automatique sur sauvegarde)
- `referent_id`, `foyer_titulaire_id`, `organisation_id`, `fonction_dans_organisation` (selectors typeahead — UI plus complexe)
- `anonymise` / `date_anonymisation` (workflow RGPD dédié)
- `rgpd_reviewed_at` / `rgpd_review_notes` (page d'audit RGPD séparée)
- `member_number`, `joined_at`, `status`, `membership_expires_at` (gérés par le workflow d'adhésion)
- `created_by`, `updated_by`, `deleted_by`, `last_interaction_at` (auto-gérés)

## Compatibilité

- **PostgreSQL 9.6 prod** : la migration `drop_phone` utilise `DB::statement` brut — compatible. Aucun `->change()`.
- **Tests existants** : la suppression de `phone` du `$fillable` Member peut casser des tests qui créent des Member avec `'phone' => ...`. À vérifier et corriger en passant.
- **HelloAsso webhook** : le mapping `phone` → `mobile` dans `processMembership` est un changement de comportement (mineur — le payload HelloAsso a toujours mis le numéro dans `payer.phone` qu'on stockait dans `phone`, on le stocke maintenant dans `mobile`).

## Référence

- Design précédent (fiche show condensée) : `docs/superpowers/specs/2026-05-03-fiche-membre-condensee-design.md`
- Plan précédent : `docs/superpowers/plans/2026-05-03-fiche-membre-condensee.md`
- Mémoire projet : `feedback_postgres_96_migrations.md` (DB::statement obligatoire), `feedback_no_destructive_db_test.md` (garde-fou actif)
