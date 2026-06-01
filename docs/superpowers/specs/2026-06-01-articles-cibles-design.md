# Articles ciblés par rôle adhérent

> Spec de conception — 2026-06-01

## Contexte

La plateforme sait déjà cibler les **événements** par profil (visibilité
public/adhérents/restreint + fonctions adhérent CA/Bureau/Validateur, cascade
cumulative). Voir `docs/superpowers/specs/2026-06-01-agenda-evenements-cibles-design.md`.

On veut la même capacité pour les **articles** (actualités du hub, table `articles`,
workflow `draft → submitted → validated → published`, affichés sur `/actualites`).

État actuel :
- `articles` : pas de notion de public visé. Affichés publiquement via
  `Hub\ArticleController` (index / category / show), `Hub\HomeController` (à la une),
  et l'API publique `Api\ArticleController`.
- Espace membre : le bloc « Actualités du réseau » du tableau de bord
  (`resources/views/member/partials/_actualites_demo.blade.php`) est **100 % démo**
  (tableau codé en dur). Aucune page/feed d'articles réels réservée aux membres.
- Fonctions adhérent déjà en base : `members.adherent_roles` (JSONB) + constantes
  `Member::ADHERENT_ROLES` (`ca`/`bureau`/`validateur`) + helpers
  `effectiveAdherentRoles()` (cascade bureau⇒ca) / `hasAdherentRole()`.

## Objectifs

1. Taguer un article avec une **visibilité** : public, adhérents, ou restreint à des
   fonctions adhérent (CA/Bureau/Validateur). Les articles non-publics ne s'affichent
   **que** dans l'espace membre.
2. Surfacer ces articles dans l'espace membre, en **flux unifié** (l'adhérent voit en
   un seul endroit tous les articles qui le concernent : publics **et** réservés) :
   - un **aperçu** sur le tableau de bord (le bloc « Actualités » devient réel) ;
   - une **page dédiée** « Actualités » (liste paginée) avec entrée de sidebar.
3. Ciblage par profil **cumulatif** (bureau ⊇ ca ; validateur orthogonal), identique
   aux événements.
4. Ne **jamais** exposer un article non-public sur les surfaces publiques (hub + API).

## Non-objectifs (YAGNI)

- Pas de niveau « groupe » pour les articles (réservé aux événements de GT).
- Pas de refonte du workflow éditorial des articles.
- Pas de gestion de l'envoi newsletter selon la visibilité (un article réservé ne
  devrait pas être diffusé publiquement par newsletter — **à signaler** comme limite,
  hors périmètre ici).
- Pas de page publique « Équipe » alimentée par les rôles (inchangé).

## Modèle de données — `articles`

Migration ajoutant (raw SQL, compat PostgreSQL 9.6, cf.
[[feedback_postgres_96_migrations]]) :

| Colonne          | Type            | Notes                                                        |
|------------------|-----------------|--------------------------------------------------------------|
| `visibility`     | varchar, défaut `public` | `public` \| `members` \| `restricted`               |
| `audience_roles` | JSONB, nullable | fonctions ciblées si `restricted`, ex. `["ca","validateur"]` |

Index : `articles_visibility_published_at_index` sur `(visibility, published_at)`
(nommé explicitement — tables aux noms courts ici, mais on garde la convention).

`App\Models\Article` :
- constantes `VIS_PUBLIC='public'`, `VIS_MEMBERS='members'`, `VIS_RESTRICTED='restricted'` ;
- `$fillable` += `visibility`, `audience_roles` ; `$casts` += `audience_roles => array`.

## Logique de visibilité

Mêmes sémantiques que les événements. Sur `App\Models\Article` :

```php
public function scopePublicOnly($query)
{
    return $query->where('visibility', self::VIS_PUBLIC);
}

public function scopeVisibleToMember($query, Member $member)
{
    $roles = $member->effectiveAdherentRoles();

    return $query->where(function ($q) use ($roles) {
        $q->whereIn('visibility', [self::VIS_PUBLIC, self::VIS_MEMBERS]);
        if (! empty($roles)) {
            $q->orWhere(function ($r) use ($roles) {
                $r->where('visibility', self::VIS_RESTRICTED)
                  ->where(function ($rr) use ($roles) {
                      foreach ($roles as $role) {
                          $rr->orWhereJsonContains('audience_roles', $role);
                      }
                  });
            });
        }
    });
}

public function isVisibleToMember(?Member $member): bool
{
    if ($this->visibility === self::VIS_PUBLIC) {
        return true;
    }
    if (! $member || ! $member->isCurrentMember()) {
        return false;
    }
    return match ($this->visibility) {
        self::VIS_MEMBERS => true,
        self::VIS_RESTRICTED => (bool) array_intersect(
            $this->audience_roles ?? [], $member->effectiveAdherentRoles()
        ),
        default => false,
    };
}
```

Le scope ne filtre pas `status` : les appelants chaînent `->published()` (scope
existant = `status='published'` ET `published_at <= now()`).

## Surfaces côté espace membre (flux unifié)

> **Gating cotisation** : le scope `visibleToMember` ne vérifie pas la cotisation
> (responsabilité de l'appelant, comme pour les événements). Sur les surfaces membre,
> on ne passe le membre au scope **que s'il est à jour** (`isCurrentMember()`) ; sinon
> on retombe sur `publicOnly()`. Un adhérent expiré ne voit donc que les articles
> publics, jamais les réservés.

### 1. Aperçu tableau de bord

- `Member\DashboardController` : si `$member` est à jour →
  `$memberArticles = Article::visibleToMember($member)->published()
  ->latest('published_at')->limit(4)->get()` ; sinon `Article::publicOnly()->published()
  ->latest('published_at')->limit(4)->get()`.
- Renommer `resources/views/member/partials/_actualites_demo.blade.php` →
  `_actualites.blade.php` ; il itère `$memberArticles` (titre, image, catégorie, date,
  lien vers le détail). Mettre à jour l'`@include` dans `member/dashboard.blade.php`.
- Si aucun article visible : état vide discret.

### 2. Page dédiée « Actualités »

- Route `member.articles.index` (sous le groupe espace membre authentifié) →
  `Member\ArticleController@index` : si le membre courant est à jour →
  `Article::visibleToMember($member)->published()`, sinon `Article::publicOnly()
  ->published()` ; puis `->latest('published_at')->paginate(12)`, filtre `?category=`
  réutilisant les catégories existantes.
- Vue `resources/views/member/articles/index.blade.php` (style espace membre). Chaque
  carte d'article non-public porte un **repère** (« Réservé adhérents », « CA »,
  « Bureau »… via `Member::ADHERENT_ROLES`).
- **Entrée sidebar** « Actualités » (icône Lucide) dans `layouts/member.blade.php`,
  accessible à tout compte connecté (le contenu se filtre tout seul ; un visiteur non
  adhérent ne verra que les articles publics).

### 3. Page détail

- On réutilise `hub.articles.show`. Garde dans `Hub\ArticleController@show` :
  résoudre le membre courant (ou null), puis
  `abort_unless($article->isPublished() && $article->isVisibleToMember($member), 404)`.
  Un article non-public n'est lisible que par un membre autorisé.

## Hub public + audit anti-fuite

Toutes les surfaces publiques ne renvoient que des articles `public` :

- `Hub\ArticleController@index` et `@category` → ajouter `->publicOnly()`.
- `Hub\ArticleController@show` → garde visibilité (ci-dessus). **Articles liés / à la
  une / même catégorie** affichés dans `show` → `->publicOnly()`.
- `Hub\HomeController` → toute requête d'articles (à la une / récents) → `->publicOnly()`.
- `Api\ArticleController` (index / show / éventuels listings) → `->publicOnly()` ; pour
  le détail, 404 si `visibility !== public`. (Vérifier d'abord la forme JSON et les
  filtres réels ; auditer chaque méthode comme pour l'API événements.)

> Leçon des événements : la revue finale avait trouvé une fuite dans les « événements
> similaires » et l'API. Ici on traite proactivement **index, category, show
> (+liés/à la une), home et API**.

## Admin

`resources/views/admin/articles/_form.blade.php` (+ `Admin\ArticleController`
`store`/`update`) :
- Bloc **« Visibilité »** : select public/adhérents/restreint + cases **fonctions
  ciblées** (`Member::ADHERENT_ROLES`), masquées sauf si « restreint » (toggle JS).
- Validation : `visibility` required in public/members/restricted ; `audience_roles`
  nullable array `required_if:visibility,restricted` ; chaque rôle in ca/bureau/validateur.
  Après validate : si visibilité ≠ restricted → `audience_roles = null`.
- (Réutiliser le composant déjà fait pour les événements — même markup/règles.)

## Tests (TDD)

- `Article::scopeVisibleToMember` / `publicOnly` / `isVisibleToMember` : cascade
  bureau⇒ca, validateur orthogonal, adhérent simple ne voit pas restricted, public/
  members visibles.
- Tableau de bord membre : voit un article `members`, pas un article `restricted` CA
  (si non CA).
- Page membre `member.articles.index` : liste filtrée ; un article restreint
  n'apparaît pas pour un non-ayant droit.
- Hub `index`/`category` : exclut non-public. Hub `show` : 404 pour un article
  `members` en invité ; 200 public. Articles liés : pas de fuite.
- API : `index` exclut non-public ; `show` 404 pour non-public.
- Admin : crée un article `restricted` avec rôles ; `restricted` sans rôle → erreur ;
  `members` efface les rôles.

## Lots de mise en œuvre (une seule spec)

- **Lot 1 — socle & sécurité**
  migration `articles` ; constantes + scopes/méthode sur `Article` ; hub index/category/
  show (+liés/à la une) + home + API en `publicOnly`/garde ; form admin visibilité+rôles.
- **Lot 2 — surfaces membre**
  feed tableau de bord (partial réel) ; page `member.articles.index` + entrée sidebar +
  repères de source ; tests bout-en-bout.

## Points d'attention (mémoire projet)

- **PostgreSQL 9.6** : migrations en `DB::statement` + `JSONB` (pas de `->change()`),
  index nommés.
- **Tailwind v4** : `npm run build` + committer `public/build` si nouvelles classes.
- **Accents FR** : ne pas les retirer des vues (durcir le prompt implémenteur + grep).
- **Pas de fuite** : auditer TOUTES les requêtes d'articles publiques (hub + API),
  y compris listes annexes (liés, à la une, home).
