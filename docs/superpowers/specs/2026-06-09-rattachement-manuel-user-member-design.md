# Design — Rattachement manuel d'un compte User à une fiche Member

**Date :** 2026-06-09
**Auteur :** David Demerges (avec Claude)
**Statut :** Validé, prêt pour plan d'implémentation

## Contexte & problème

Le lien entre un **contact** (`Member`) et un **utilisateur de l'extranet** (`User`) repose
sur la clé étrangère `members.user_id` (relation 1:1 optionnelle). Aujourd'hui, le **seul**
flux qui rattache automatiquement un nouveau `User` à une fiche `Member` existante est
l'inscription publique du Hub (`Hub\AuthController::register`), via un matching par **email
strictement identique** :

```php
$member = Member::where('email', $validated['email'])->whereNull('user_id')->first();
if ($member) { $member->update(['user_id' => $user->id]); }
```

Conséquence : dès que l'email du compte diffère de celui de la fiche, ou que le compte est
créé via l'API (`Api\AuthController::register`) ou le back-office
(`Admin\UserController::store`), **aucun rattachement** n'a lieu. On se retrouve avec des
comptes « orphelins » et des fiches `Member` (avec adhésions, dons, etc.) détachées.

Cette fonctionnalité ajoute un **filet de rattrapage manuel** : un admin peut, depuis le
back-office des utilisateurs, relier un compte à la bonne fiche.

## Périmètre

**Inclus :**
- Rattachement / détachement manuel `User` ↔ `Member` depuis le back-office.
- Point d'entrée sur la liste des utilisateurs (`/extranet/users`) + carte dédiée sur la
  page de détail d'un utilisateur.
- Suggestions automatiques de fiches candidates (match assoupli) + recherche libre.

**Hors scope (non traité ici) :**
- Le matching assoupli automatique à l'inscription (« piste 2 » alternative).
- L'alignement / la synchronisation des emails entre `User` et `Member`.
- La factorisation du rattachement-par-email de l'inscription Hub (« piste 1 ») — mais le
  service introduit ici est conçu pour être réutilisable à cette fin.

## Règle d'or

On ne propose au rattachement que des fiches dont **`user_id` est `null`** (pas de vol de
fiche). La pose du lien est **atomique** :

```php
$affected = Member::whereKey($member->id)
    ->whereNull('user_id')
    ->update(['user_id' => $user->id]);
// $affected === 0 → la fiche a été prise entre-temps → on rejette avec un message.
```

L'email du compte et l'email de la fiche **ne sont jamais modifiés** par le rattachement :
email de connexion et email de contact peuvent légitimement différer.

## Architecture

### 1. Service `App\Services\MemberUserLinkService`

Centralise la logique (réutilisable par d'autres flux plus tard).

| Méthode | Rôle |
|---------|------|
| `suggestionsFor(User $user): Collection` | Fiches `Member` sans compte, classées : **email identique** d'abord, puis **nom+prénom** correspondant à `User.name`. Retourne une petite liste (ex. 5). |
| `link(User $user, Member $member): bool` | Pose `user_id` de façon atomique (`whereNull('user_id')`). Retourne `false` si la fiche a déjà un compte. Écrit un `AuditLog`. |
| `unlink(Member $member): void` | Remet `user_id` à `null`. Écrit un `AuditLog`. |

**Logique de suggestion :** `User.name` est un champ unique (pas de `first_name`/`last_name`),
donc le match « nom » compare `User.name` à la concaténation `first_name + last_name` de la
fiche, insensible à la casse, avec tolérance de l'ordre inversé (nom/prénom). C'est
**volontairement permissif** : ce sont des suggestions, l'admin confirme toujours
manuellement.

Ordre de classement des suggestions :
1. `member.email` ILIKE `user.email` (correspondance email exacte ratée par l'auto-match).
2. Correspondance nom+prénom.

Toutes les requêtes filtrent sur `whereNull('user_id')` et excluent les fiches anonymisées
(`anonymise = false`).

### 2. Scopes Eloquent

- `User::scopeWithoutMember($query)` → `whereDoesntHave('member')`.
- `Member::scopeWithoutAccount($query)` → `whereNull('user_id')`.

### 3. Controller `App\Http\Controllers\Admin\AccountLinkController`

Controller dédié (ne surcharge pas `UserController`).

| Route | Méthode | Action |
|-------|---------|--------|
| `GET  users/{user}/member-search` | `search` | JSON : candidats `Member` sans compte filtrés par `q` (nom / email / n° adhérent). Limité (ex. 10). |
| `POST users/{user}/link-member`   | `store`  | Valide `member_id`, refuse si le `User` a déjà une fiche, lie atomiquement via le service, flash succès/erreur, redirige vers la page user. |
| `POST users/{user}/unlink-member` | `destroy`| Détache la fiche liée, flash succès. |

Routes déclarées dans `routes/admin.php`, sous le même middleware/garde que le reste de
`/extranet/users`.

### 4. UI

**Liste `/extranet/users` (`admin/users/index.blade.php`) — point d'entrée :**
- Nouvelle colonne **« Fiche contact »** : si liée, badge vert avec le nom (lien vers la
  fiche Member) ; sinon `—`.
- Nouveau filtre déroulant **« Fiche : toutes / liées / sans fiche »** (paramètre query
  `member_link`), géré dans `UserController::index` via les scopes.

**Page user `/extranet/users/{user}` (`admin/users/show.blade.php`) — nouvelle carte
sidebar « Fiche contact » :**
- **Si liée** : nom, n° adhérent, email de la fiche, lien vers la fiche Member, bouton
  **« Détacher »** (POST + `confirm()`).
- **Si non liée** :
  - Liste des **suggestions auto** (`suggestionsFor`) — chaque ligne : nom + email + n°
    adhérent + bouton **« Rattacher »** (POST).
  - **Champ de recherche libre** : Alpine `x-data` + `fetch` vers `member-search` (JSON),
    rend les résultats avec le même bouton « Rattacher ».

`UserController::show` charge `$user->member` et appelle le service pour les suggestions
(uniquement si non lié).

## Comportement email

Aucune modification de `user.email` ni `member.email` lors du rattachement/détachement.
Découplage assumé entre identité technique (login) et identité métier (contact).

## Garde-fous

- **User déjà rattaché** → le `link` est refusé tant que la fiche actuelle n'est pas
  détachée (message explicite). La carte n'affiche le bloc « rattacher » que si non lié.
- **Member déjà pris** → refus atomique au niveau SQL (`whereNull('user_id')`).
- **Fiche anonymisée** → exclue des suggestions et de la recherche.
- Accès réservé aux admins (middleware existant de `/extranet/users`).

## Tests (Feature)

1. Rattachement réussi → `members.user_id` renseigné + `AuditLog` créé.
2. Rattacher une fiche qui a déjà un compte → refusé (atomique).
3. Rattacher alors que le `User` a déjà une fiche → refusé.
4. Détachement → `user_id` remis à `null` + `AuditLog`.
5. `suggestionsFor` : inclut fiches sans compte à email/nom correspondant, exclut celles
   avec compte et les anonymisées, classe l'email exact en premier.
6. Filtre liste « sans fiche » / « liées » renvoie les bons comptes.

## Notes d'implémentation

- Penser à mettre à jour `/extranet/documentation`
  (`admin/documentation/index.blade.php`) après livraison.
- Rebuild Vite (`npm run build`) si de nouvelles classes Tailwind sont introduites, et
  commiter `public/build`.
- Vérifier qu'aucun accent n'est supprimé dans les vues après génération.
