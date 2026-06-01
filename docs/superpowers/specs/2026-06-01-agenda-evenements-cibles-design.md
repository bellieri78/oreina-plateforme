# Agenda — événements ciblés (extranet + groupes de travail)

> Spec de conception — 2026-06-01

## Contexte

L'agenda de l'espace membre (`resources/views/member/partials/_agenda.blade.php`)
affiche aujourd'hui **tous** les événements publiés (`Event::published()->upcoming()`),
sans aucun filtrage. Les événements ne portent ni public visé, ni rattachement à un
groupe, ni lien visio.

Les groupes de travail ont des **coordinateurs** (pivot `work_group_member`,
`role = 'coordinator'`) mais aucun événement.

Le CA et le Bureau **n'existent pas en base** : la page `/equipe` est codée en dur,
et `User.role` ne gère que des rôles éditoriaux (editor/reviewer/author). On distingue
donc deux axes indépendants :

- **Rôle adhérent** (multiple, géré dans l'extranet) : adhérent simple, CA, Bureau,
  validateur, coordinateur de GT…
- **Rôle outil/extranet** (éditorial) : inchangé, hors périmètre.

## Objectifs

1. Créer un événement depuis l'extranet avec une **visibilité** : public, adhérents,
   ou restreint à certaines fonctions adhérent (CA, Bureau, validateur…). Les
   événements non-publics ne s'affichent **que** dans l'espace membre.
2. Permettre à un **coordinateur** de groupe de planifier une réunion/événement
   (date + heure, **lieu OU visio**) visible par les **membres du groupe**, remontant
   dans leur agenda.
3. Ciblage par profil avec **logique cumulative** : un membre du Bureau voit aussi les
   événements « CA » et « adhérents ».

## Non-objectifs (YAGNI)

- Pas de récurrence d'événements.
- Pas d'inscription/RSVP aux réunions de groupe (les champs `registration_*`
  existants ne sont pas utilisés ici).
- Pas de refonte de la page `/equipe` (reste en dur pour l'instant).
- Pas de notifications/emails automatiques à ce stade (peut venir plus tard).

## Modèle de rôle adhérent

Stocké sur `members.adherent_roles` (JSON, cast `array`). Étiquettes **multiples**,
gérées en admin. Valeurs initiales :

| Clé          | Libellé                  | Axe          |
|--------------|--------------------------|--------------|
| `ca`         | Conseil d'administration | gouvernance  |
| `bureau`     | Bureau                   | gouvernance  |
| `validateur` | Validateur               | métier       |

- **Adhérent simple** = liste vide (baseline implicite : tout membre à jour).
- **Coordinateur de GT** : **dérivé** du pivot `work_group_member` (déjà en base),
  pas dupliqué dans `adherent_roles`.
- **Cascade gouvernance** : `bureau` ⊇ `ca`. Les rôles métier (`validateur`) sont
  **orthogonaux** (pas de cascade).

Constantes dans `App\Models\Member` :

```php
public const ADHERENT_ROLE_CA = 'ca';
public const ADHERENT_ROLE_BUREAU = 'bureau';
public const ADHERENT_ROLE_VALIDATEUR = 'validateur';

public const ADHERENT_ROLES = [
    self::ADHERENT_ROLE_CA => "Conseil d'administration",
    self::ADHERENT_ROLE_BUREAU => 'Bureau',
    self::ADHERENT_ROLE_VALIDATEUR => 'Validateur',
];

// Cascade : une clé "donne accès" aux clés listées.
protected const ADHERENT_ROLE_CASCADE = [
    self::ADHERENT_ROLE_BUREAU => [self::ADHERENT_ROLE_CA],
];
```

Helpers `Member` :

```php
public function effectiveAdherentRoles(): array
{
    $roles = collect($this->adherent_roles ?? []);
    foreach ($roles as $r) {
        foreach (self::ADHERENT_ROLE_CASCADE[$r] ?? [] as $implied) {
            $roles->push($implied);
        }
    }
    return $roles->unique()->values()->all();
}

public function hasAdherentRole(string $role): bool
{
    return in_array($role, $this->effectiveAdherentRoles(), true);
}
```

## Modèle de données — `events`

Migration ajoutant :

| Colonne          | Type            | Notes                                                        |
|------------------|-----------------|--------------------------------------------------------------|
| `visibility`     | string, défaut `public` | `public` \| `members` \| `restricted` \| `group`     |
| `audience_roles` | json, nullable  | fonctions ciblées si `restricted`, ex. `["ca","validateur"]` |
| `work_group_id`  | FK nullable     | renseigné si `visibility = group` ; `onDelete('cascade')`    |
| `meeting_url`    | string, nullable| lien visio (présentiel = champs `location_*` existants)      |

Index : ajouter `['visibility', 'start_date']`.

Constantes / casts dans `App\Models\Event` :

```php
public const VIS_PUBLIC = 'public';
public const VIS_MEMBERS = 'members';
public const VIS_RESTRICTED = 'restricted';
public const VIS_GROUP = 'group';

protected $casts = [/* … */ 'audience_roles' => 'array'];

public function workGroup(): BelongsTo
{
    return $this->belongsTo(WorkGroup::class);
}
```

> `members.adherent_roles` : voir section précédente (migration + cast `array`,
> ajout à `$fillable`).

## Logique de visibilité

### Côté espace membre (agenda)

Scope `Event::visibleToMember(Member $member)` — l'événement est visible si l'une des
conditions est vraie (et `status = published`) :

- `visibility = public`
- `visibility = members`
- `visibility = restricted` **et** `audience_roles ∩ effectiveAdherentRoles(member) ≠ ∅`
- `visibility = group` **et** `member` est **membre actif** de `work_group_id`

Implémentation (PostgreSQL, `audience_roles` en JSONB → opérateur `?|`) :

```php
public function scopeVisibleToMember($query, Member $member)
{
    $roles = $member->effectiveAdherentRoles();
    $groupIds = $member->workGroups()->wherePivot('status', 'active')
        ->pluck('work_groups.id')->all();

    return $query->where('status', 'published')->where(function ($q) use ($roles, $groupIds) {
        $q->whereIn('visibility', [self::VIS_PUBLIC, self::VIS_MEMBERS]);

        if ($roles) {
            $q->orWhere(fn ($r) => $r->where('visibility', self::VIS_RESTRICTED)
                ->whereRaw('audience_roles ?| array[' . /* bindings */ . ']', $roles));
        }
        if ($groupIds) {
            $q->orWhere(fn ($g) => $g->where('visibility', self::VIS_GROUP)
                ->whereIn('work_group_id', $groupIds));
        }
    });
}
```

> Détail d'implémentation : l'opérateur `?|` de PostgreSQL entre en conflit avec le
> binding de paramètres PDO. On construira la clause via `whereRaw` avec une liste
> de placeholders `?` côté valeur, ou un test `EXISTS`/`@>` par rôle. À cadrer dans
> le plan. Une version repli en mémoire (filtrer la collection) reste possible si le
> SQL JSON pose problème.

### Côté hub public

Scope `Event::scopePublicOnly()` = `where('visibility', self::VIS_PUBLIC)`.
`Hub\EventController::index` n'utilise que ce scope ; les événements
`members`/`restricted`/`group` ne fuient jamais sur le site public.

### Page détail d'un événement

`Hub\EventController::show` reste la page de détail réutilisée par l'agenda. Garde via
**`EventPolicy::view`** :

- `public` → tout le monde ;
- sinon → utilisateur authentifié + membre à jour, et :
  - `members` : OK ;
  - `restricted` : `hasAdherentRole` sur l'une des `audience_roles` ;
  - `group` : membre actif du groupe.
- Échec → 404 (ne pas révéler l'existence).

## Écran 1 — Formulaire d'événement admin

`resources/views/admin/events/{create,edit}.blade.php` + `Admin\EventController` :

- Nouveau bloc **« Visibilité »** : radio/select `public` | `members` | `restricted`.
- Si `restricted` : multi-cases des `Member::ADHERENT_ROLES` (Alpine pour afficher/masquer).
- Validation : `visibility in [public,members,restricted]` (le form admin ne crée pas
  de `group`) ; `audience_roles` requis et non vide si `restricted`, chaque valeur
  ∈ clés `ADHERENT_ROLES`.
- Liste admin (`index`) : **badge de visibilité** par ligne + filtre `visibility`.

## Écran 2 — Événement depuis la page d'un groupe

Sur `resources/views/member/work-groups/show.blade.php`, visible si
`WorkGroup::isCoordinator($currentMember)` :

- Bouton/section **« Planifier une réunion / un événement »** (formulaire ou modale).
- Champs : `title`, `start_date` (date + heure), `end_date` (optionnel), `event_type`
  (réunion par défaut), **mode** :
  - présentiel → `location_name` / `location_address` / `location_city` ;
  - visio → `meeting_url` (URL valide).
  - description (optionnelle).
- Contrôleur dédié : `Member\WorkGroupEventController` (`store`, `update`, `destroy`),
  garde par policy/`isCoordinator`. À la création : `visibility = group`,
  `work_group_id = $group->id`, `organizer_id = auth user`, `status = published`,
  `slug` auto.
- La page du groupe affiche **« Prochaines réunions »** (les `group` events à venir
  du groupe). Le coordinateur peut **éditer**, **annuler** (`status = 'cancelled'` —
  l'événement sort de l'agenda qui ne montre que `published`, mais reste tracé) ou
  **supprimer** (soft-delete).

## Écran 3 — Fiche membre (admin)

- Formulaire d'édition membre : groupe de cases **`adherent_roles`** (CA / Bureau /
  Validateur), validées contre les clés `ADHERENT_ROLES`.
- Affichage des rôles sur la fiche (show) sous forme de badges.

## Écran 4 — Agenda espace membre

- `DashboardController` : `$upcomingEvents = Event::visibleToMember($member)
  ->upcoming()->orderBy('start_date')->limit(...)->get()` (avec `with('workGroup')`).
  Repli `members`-only / public si pas de `$member`.
- `_agenda.blade.php` : pour chaque ligne, petit **repère** de source :
  - `group` → nom du groupe ;
  - `meeting_url` présent → « Visio » ;
  - `restricted` → libellé de la cible (CA / Bureau / Validateur) ;
  - `members` → « Adhérents » ; `public` → rien (ou « Public »).

## Tests (TDD)

Tests Feature/Unit à écrire **avant** le code correspondant :

- `effectiveAdherentRoles()` : cascade `bureau ⇒ ca`, `validateur` non cascadé.
- `Event::visibleToMember` :
  - membre simple voit `public`/`members`, pas `restricted`/`group` d'un autre groupe ;
  - membre `ca` voit événement ciblé `ca` ; membre `bureau` voit `ca` **et** `bureau` ;
  - membre `bureau` **ne voit pas** un événement `validateur` (orthogonal) ;
  - membre d'un groupe voit l'événement `group` de **son** groupe uniquement.
- Hub `index` : exclut tout sauf `public`.
- `EventPolicy::view` : 404 pour un non-ayant droit sur `restricted`/`group`.
- Création groupe : autorisée au coordinateur, refusée au membre simple ;
  l'événement créé a bien `visibility=group` + `work_group_id`.

## Lots de mise en œuvre (une seule spec)

- **Lot 1 — socle & ciblage admin**
  migrations (`events` + `members.adherent_roles`) ; constantes & helpers `Member` ;
  scopes `Event` + `EventPolicy` ; form admin (visibilité + rôles) + badge/filtre liste ;
  `adherent_roles` sur la fiche membre ; filtrage agenda ; hub public-only.
- **Lot 2 — événements de groupe**
  `WorkGroupEventController` + UI coordinateur (création/édition/annulation) ;
  section « Prochaines réunions » sur la page groupe ; repères de source dans l'agenda.

## Points d'attention techniques (mémoire projet)

- **PostgreSQL** : un env prod est en **9.6** — éviter `->change()` (utiliser
  `DB::statement` si modification de colonne) et **nommer** les index composites longs.
- **Tailwind v4** : `npm run build` après ajout de classes, committer `public/build`.
- **Pas de garde-fou destructif testé en live** ; pas de `migrate:fresh` sur les bases
  protégées.
- **Accents FR** : ne pas laisser un implémenteur (subagent) retirer les accents des
  vues — grep/restauration après chaque tâche.
