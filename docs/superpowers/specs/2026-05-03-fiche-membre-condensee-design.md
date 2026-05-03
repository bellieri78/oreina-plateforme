# Fiche contact admin — refonte condensée + engagement OREINA

**Date** : 2026-05-03
**Statut** : design validé, prêt pour planification
**Cible** : `/extranet/members/{id}` (route `admin.members.show`)

## Contexte

La fiche contact actuelle est une grille 2 colonnes (sidebar info + body avec 4 cartes empilées : Adhésions, Dons, Achats, Bulletins Lepis). Chaque carte rend la liste complète sans pagination, ce qui produit une page très longue dès qu'un adhérent a un peu d'historique. La fiche n'expose pas non plus l'engagement non-financier de l'adhérent : ses groupes de travail, ses publications dans Chersotis (la revue scientifique), ses suggestions Lepis.

Cette refonte vise à rendre la fiche **plus dense, plus hiérarchisée et plus complète** sans sacrifier l'accès à l'historique.

## Principes

- **Synthèse d'abord** : une bande KPI en tête donne les chiffres-clés en un coup d'œil.
- **Détail à la demande** : les listes longues sont raccourcies à 5 items, avec "Voir tout (N)" qui déplie inline (pas de page séparée).
- **Affichage conditionnel** : un sous-bloc sans donnée est masqué, pas affiché en gris. Évite le bruit pour les nouveaux adhérents.
- **Cohérence visuelle** : on réutilise les classes existantes (`.card`, `.dashboard-stat-card`, `.badge`, `.table`) et les icônes Lucide (`<i data-lucide="...">`) déjà en place dans le dashboard et l'index des contacts. Pas de nouveau système d'icônes.

## Architecture du layout

```
┌──────────────────────────────────────────────────────────────────┐
│  Breadcrumb : Contacts / Marie Durand                            │
├──────────────────────────────────────────────────────────────────┤
│  KPI BAR (dashboard-stat-grid)                                   │
│  [📋 5 Adhésions] [💶 320 € Dons] [🛒 3 Achats]                 │
│  [📰 2 Bulletins] [👥 1 Groupe] [✍️ 4 Publis] [💡 2 Suggestions]│
├──────────────────┬───────────────────────────────────────────────┤
│  SIDEBAR INFO    │  BODY                                         │
│  (1fr)           │  (2fr)                                        │
│                  │                                               │
│  Identité +      │  Carte Adhésions (raccourcie)                 │
│  contact         │  Carte Dons (raccourcie)                      │
│  + Format Lepis  │  Carte Achats (raccourcie)                    │
│  + Groupes       │  Carte Bulletins Lepis (raccourcie)           │
│  + Engagement    │  Carte Engagement OREINA (nouveau)            │
│                  │                                               │
└──────────────────┴───────────────────────────────────────────────┘
```

La grille reste `1fr 2fr` comme aujourd'hui. La KPI bar est positionnée **avant** la grille, pleine largeur.

## Composants

### KPI bar (`dashboard-stat-grid`)

Réutilise les classes du dashboard admin (`.dashboard-stat-card`, `.dashboard-stat-icon`, `.dashboard-stat-value`, `.dashboard-stat-label`, variantes `dashboard-stat-{blue,green,purple,orange}`).

7 KPIs possibles, **chacun masqué si valeur = 0** :

| KPI | Icône Lucide | Variant | Valeur |
|---|---|---|---|
| Adhésions | `id-card` | purple | `count(memberships)` |
| Dons cumulés | `circle-dollar-sign` | green | `sum(donations.amount)` formaté en € (`1.2k €` au-delà de 999) |
| Achats | `shopping-cart` | orange | `count(purchases)` |
| Bulletins reçus | `mail` | blue | `count(lepis_bulletin_recipients)` |
| Groupes | `users-round` | purple | `count(workGroups)` |
| Publications Chersotis | `book-open` | blue | `count(submissions)` (toutes, pas seulement publiées) |
| Suggestions Lepis | `lightbulb` | orange | `count(lepis_suggestions)` |

Chaque KPI est un lien `<a href="#section-id">` qui ancre vers la carte correspondante du body. Les cartes ont des id `id="adhesions"`, `id="dons"`, `id="achats"`, `id="bulletins"`, `id="engagement"`. Les 3 KPIs `Groupes` / `Publications` / `Suggestions` ancrent toutes vers `#engagement`.

Sur écran étroit, `flex-wrap: wrap` (déjà dans `.dashboard-stat-grid`).

### Sidebar info enrichie

Le bloc actuel reste tel quel jusqu'au "Inscrit le". On ajoute en dessous **3 sous-blocs**, séparés par les mêmes `border-top: 1px solid #e5e7eb; padding-top: 1rem` que le bloc actuel. Chaque sous-bloc est masqué si vide.

**Sous-bloc 1 — Format Lepis** (visible si `currentMembership()` non-null) :

```blade
<div style="margin-bottom: 1rem;">
    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Format Lepis</div>
    <div>{{ $lepisFormat === 'digital' ? 'Numérique' : 'Papier' }}</div>
</div>
```

**Sous-bloc 2 — Groupes** (visible si `workGroups->isNotEmpty()`) :

```
GROUPES (2)
· Coléoptères — coordinatrice
· Macroheterocera
```

Liste à 5 items max avec `"+ N autres"` au-delà. Le rôle est affiché en italique et en gris si différent de `member`.

**Sous-bloc 3 — Engagement** (visible si au moins une activité non vide : submissions OU suggestions) :

```
ENGAGEMENT
Auteur Chersotis — 4 publi · 2 brouillons
Contributeur Lepis — 2 suggestions
```

Chaque ligne est masquée si son count est 0. Si aucune ligne, le sous-bloc complet est masqué. Pas d'icône (le sous-titre `ENGAGEMENT` suffit). Les chiffres sont calculés depuis les eager-loads.

### Cartes raccourcies (Adhésions / Dons / Achats / Bulletins Lepis)

Pour chaque carte qui rend une table :

- Si `count <= 5` : tout afficher comme aujourd'hui, pas de "Voir tout".
- Si `count > 5` : afficher les 5 premiers (déjà ordonnés antichronologiquement par les controllers existants), puis un footer cliquable.

**Implémentation HTML/CSS pure via `<details>`** (pas de JS, accessibilité native) :

```blade
@php $items = $member->memberships()->orderByDesc('start_date')->get(); @endphp
<div class="card" id="adhesions" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">Adhésions ({{ $items->count() }})</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($items->count() <= 5)
            {{-- table complète, comportement actuel --}}
        @else
            <table class="table">{{-- 5 premières lignes --}}</table>
            <details style="border-top: 1px solid #e5e7eb;">
                <summary style="padding: 0.75rem 1rem; cursor: pointer; color: #356B8A; font-size: 0.875rem;">
                    Voir tout ({{ $items->count() }})
                </summary>
                <table class="table">{{-- les ($items->count() - 5) restantes --}}</table>
            </details>
        @endif
    </div>
</div>
```

Le `<details>` natif HTML donne le toggle gratuit, focusable au clavier, et CSS-stylable. État non-persisté entre rechargements (acceptable pour cette page).

Bulletin Lepis : la section actuelle (que j'ai ajoutée pendant la feature précédente) est convertie au pattern `.card` standard pour la cohérence. Elle perd ses `style=""` inline au profit des classes existantes.

### Carte "Engagement OREINA" (nouveau)

5e carte dans le body, après "Bulletins Lepis". Id `id="engagement"`. Affiche jusqu'à 3 sous-sections :

**Groupes de travail** (visible si `workGroups->isNotEmpty()`) :

```
GROUPES DE TRAVAIL (2)
· Coléoptères — coordinatrice
  Membre depuis le 12/05/2024
· Macroheterocera
  Membre depuis le 03/01/2023
```

Pas de truncation (rare d'avoir + de 5 groupes). Date depuis `pivot.joined_at`, rôle depuis `pivot.role`.

**Publications Chersotis** (visible si `submissions->isNotEmpty()`) :

```
CHERSOTIS (4 soumissions)
· Article sur les Pieridae alpines        [Publié] [2025]
· Note de terrain — Saxifraga             [En revue] [2024]
· ... (2 plus, voir tout)
```

Chaque ligne : titre lien vers `admin.submissions.show`, badge de statut (`Publié` vert, `En revue` blue, `Accepté` blue, `Brouillon` gris, etc., `Rejeté` red), année à droite. Toutes les soumissions listées (pas seulement publiées) — la sous-section apparaît dès qu'au moins une existe. Truncation à 5 max avec `<details>` "Voir tout" comme les autres cartes.

**Suggestions Lepis** (visible si `lepis_suggestions->isNotEmpty()`) :

```
LEPIS — SUGGESTIONS (2)
· "Idée pour un dossier biodiversité"     [Notée]
  Soumise le 03/03/2026
· "Coquilles dans le n°3 2025"            [En attente]
  Soumise le 12/01/2026
```

Chaque ligne : titre entre guillemets, badge `En attente` (warning) ou `Notée` (success), date sous le titre. Truncation à 5 max comme ci-dessus. Le clic sur le titre mène à la fiche admin de la suggestion (`admin.lepis-suggestions.show`).

Si **aucune** des 3 sous-sections n'a de contenu, la carte entière est masquée (pas de "Aucun engagement").

## Données à charger

Dans `MemberController::show`, étendre l'eager-load :

```php
$member->load([
    'memberships.membershipType',  // existant
    'donations',                   // existant
    'purchases.product',           // existant (à confirmer)
    'consents',                    // existant
    'lepisBulletinRecipients.bulletin',  // existant (livré dans la feature précédente)
    'workGroups',                  // NOUVEAU — pivot inclus automatiquement (withPivot)
    'lepisSuggestions',            // NOUVEAU — voir ci-dessous
    'user.submissions',            // NOUVEAU — passe par User car Submission.author_id pointe sur users
]);
```

Le modèle `Member` n'a pas de relation directe `lepisSuggestions()`. **Action requise** : ajouter `public function lepisSuggestions(): HasMany { return $this->hasMany(LepisSuggestion::class); }` sur `app/Models/Member.php` (table `lepis_suggestions.member_id`).

Le modèle `Member` n'a pas de relation directe `submissions()` non plus, mais on peut l'ajouter en `HasManyThrough` pour simplifier le code blade :

```php
public function submissions(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
{
    return $this->hasManyThrough(
        \App\Models\Submission::class,
        \App\Models\User::class,
        'id',           // FK on users
        'author_id',    // FK on submissions
        'user_id',      // local key on members
        'id'            // local key on users
    );
}
```

Cela permet `$member->submissions` directement dans le blade sans détour par `$member->user->submissions`. À ajouter aussi sur `Member`.

KPIs computés à la volée à partir des collections eager-loaded : `$member->donations->sum('amount')`, `$member->workGroups->count()`, etc. Pas de query supplémentaire.

## Modifications de fichiers

### Modifiés
- `app/Models/Member.php` : ajouter `lepisSuggestions()` HasMany et `submissions()` HasManyThrough.
- `app/Http/Controllers/Admin/MemberController.php` : étendre `$member->load([...])` dans `show()`.
- `resources/views/admin/members/show.blade.php` : refonte complète selon ce design (KPI bar, sidebar enrichie, cartes raccourcies, carte engagement).

### Pas créés / pas touchés
- Pas de nouveau modèle.
- Pas de migration.
- Pas de nouvelle route.
- Pas de nouveau partial blade (tout reste dans `show.blade.php` pour ne pas fragmenter une page qui reste lisible).

## Tests

Un seul fichier feature, `tests/Feature/Admin/MemberShowFicheTest.php` :

- **`test_kpi_bar_hides_zero_counters`** : un membre fraîchement créé sans aucun historique → la page rend mais la KPI bar ne contient que "1 Adhésion" (pas de KPI à 0 affiché).
- **`test_kpi_bar_shows_donation_sum_in_euros`** : un membre avec 3 dons de 100/200/30 → KPI affiche "330 €" (pas "3").
- **`test_membership_card_truncates_at_5`** : un membre avec 7 adhésions → la table visible a 5 lignes + un `<details>` avec les 2 restantes.
- **`test_engagement_card_hidden_when_no_activity`** : un membre sans groupe, sans submission, sans suggestion → la carte "Engagement OREINA" n'apparaît pas dans le HTML.
- **`test_engagement_card_shows_groups_with_role`** : un membre dans 1 groupe avec `pivot.role = 'coordinator'` → la carte rend "Coléoptères — coordinatrice".
- **`test_engagement_card_shows_all_submissions_with_status_badges`** : un membre avec 1 publi + 1 brouillon → 2 lignes, badges respectifs.
- **`test_sidebar_engagement_block_hidden_when_no_activity`** : si pas de submission ni suggestion → le sous-bloc "Engagement" de la sidebar n'apparaît pas.
- **`test_lepis_format_in_sidebar_when_active_membership`** : adhérent à jour avec format papier → "Format Lepis : Papier" visible dans la sidebar.

Pas de test de scroll-anchor (les ids sont juste des ancres, pas de comportement à vérifier).

## Hors scope (différé)

- **Persistance de l'état "Voir tout" entre rechargements** : `<details>` ne mémorise pas, c'est OK pour cette page.
- **Filtrage / tri interactif** dans les tables raccourcies : non, on garde l'ordre antichronologique imposé par le controller.
- **Export PDF de la fiche** : non.
- **Édition inline** des champs (statut, format Lepis, etc.) : reste sur la page Edit dédiée, pas de modif ici.
- **Refonte de la page index `/extranet/members`** : non, scope strictement `show`.

## Compatibilité

- **PostgreSQL 9.6 prod** : aucune migration, donc aucun risque.
- **Cache view** : `php artisan view:clear` après déploiement (cache des templates compilés).
- **Tests existants** : pas de migration ni de modif de modèle invasive — les ajouts de relations Eloquent sont additifs et n'affectent pas les autres tests.

## Référence

- Fiche actuelle : `resources/views/admin/members/show.blade.php` (228 lignes)
- Pattern KPI réutilisé : `resources/views/admin/dashboard.blade.php`
- Mémoire projet : `feedback_no_filament.md` (backoffice custom Blade), `feedback_postgres_96_migrations.md` (PG 9.6 compat — sans objet ici)
