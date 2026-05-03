# Admin Articles & Events — refonte page show + corrections form events

**Date** : 2026-05-03
**Statut** : design validé, prêt pour planification
**Cibles** : `/extranet/articles/{id}` et `/extranet/events/{id}` (pages admin de visualisation), formulaire édition événement.

## Contexte

Les pages show admin actuelles (`resources/views/admin/articles/show.blade.php` et `resources/views/admin/events/show.blade.php`) ont 4 bugs concrets identifiés à l'audit :

1. **Image non affichée** : la colonne `featured_image` est en base et bien remplie, mais aucun `<img>` n'est rendu sur les pages show admin (alors que le hub public le fait correctement).
2. **HTML brut affiché** : la sortie Quill (HTML) est rendue par `{!! nl2br(e($content)) !!}` — le `e()` échappe les tags, le `{!!` les rend tels quels, donc l'admin voit `&lt;p&gt;` au lieu de paragraphes formatés.
3. **Form events sans input image** : la colonne `featured_image` est fillable et stockable mais le formulaire n'a aucun input pour la téléverser.
4. **Form events sans WYSIWYG** : `content` est édité via `<textarea>` brut, alors que le form articles a déjà Quill v2 intégré.

Cette refonte :
- Refait les 2 pages show admin en layout 2 colonnes (sidebar méta + body riche).
- Aligne le form events sur le pattern d'articles (Quill + input image).
- **Ne touche pas aux forms articles** (déjà conformes), ni aux pages publiques (déjà correctes), ni aux index pages, ni aux modèles, ni à la DB.

## Principes

- **Cohérence visuelle** avec la fiche membre récemment livrée : grille `1fr 2fr`, classes `.card`, badges Lucide, sidebar méta avec sous-blocs masqués si vides.
- **Pas de duplication** : on suit les patterns existants du hub public (`Storage::url`, `{!! $content !!}`) et du form articles (Quill v2 via CDN).
- **Bugs corrigés au passage** sans introduire d'autres changements.
- **Aucune migration** : toutes les colonnes nécessaires existent déjà.

## Architecture des pages show

### Show admin Article

Layout grille `1fr 2fr` :

```
┌──────────────────────────────────────────────────────────────────┐
│  Breadcrumb : Articles / "Titre"          [Modifier] [👁]         │
├──────────────────┬───────────────────────────────────────────────┤
│  SIDEBAR (1fr)   │  BODY (2fr)                                   │
│                  │                                               │
│  Vues : N        │  [Image cover full-width]                     │
│  Statut [badge]  │                                               │
│  Mis en avant ✓  │  Titre                                        │
│  Dates           │  Résumé                                       │
│  Auteur          │  Contenu HTML rendu                           │
│  Catégorie       │  Document attaché [DL] (si présent)           │
│  Validation      │                                               │
│  (si validé)     │                                               │
└──────────────────┴───────────────────────────────────────────────┘
```

**Boutons header (au-dessus de la grille)** :
- `Modifier` → `route('admin.articles.edit', $article)` (toujours visible)
- `👁 Voir côté public` → `route('hub.articles.show', $article)` (visible si `status === 'published'`)

**Sidebar — sous-blocs**, chacun masqué si vide :
- **Vues** : `views_count` formaté avec espace (ex. `1 234`)
- **Statut** : badge couleur selon valeur (`draft` gris / `submitted` orange / `validated` blue / `published` green). Lib `.badge .badge-{color}` existante.
- **Mis en avant** : checkmark si `is_featured`
- **Dates** : `Publié le ...` (depuis `published_at`), `Modifié le ...` (depuis `updated_at`)
- **Auteur** : `$article->author->name` avec lien vers `admin.users.show` si dispo
- **Catégorie** : valeur libre du champ `category`
- **Validation** : visible si `validated_by` non-null. Affiche le nom du valideur + `validation_notes` en blockquote si renseignées.

**Body — éléments** :
- **Image cover** : `<img src="{{ Storage::url($article->featured_image) }}">` avec `width: 100%; max-height: 400px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 1.5rem;`. Bloc entièrement masqué si `featured_image === null`.
- **Titre** : `<h1>` avec font-size large, couleur `--oreina-dark`.
- **Résumé** : `<p>` italique gris si `summary` non-vide.
- **Contenu HTML** : `<div class="article-content">{!! $article->content !!}</div>`. La classe `.article-content` style typographique (paragraphes, h2, h3, listes, blockquote, liens) — soit ajouté dans `admin.css`, soit stylé inline avec un bloc `<style>` scopé.
- **Document attaché** : si `document_path`, bloc avec icône Lucide `file-text`, nom du document (ou `document_name` ou basename), bouton "Télécharger" → `Storage::url($article->document_path)`.

### Show admin Event

Même layout `1fr 2fr` :

```
┌──────────────────────────────────────────────────────────────────┐
│  Breadcrumb : Événements / "Titre"        [Modifier] [👁]         │
├──────────────────┬───────────────────────────────────────────────┤
│  SIDEBAR (1fr)   │  BODY (2fr)                                   │
│                  │                                               │
│  Type            │  [Image cover full-width]                     │
│  Statut [badge]  │                                               │
│  À venir/passé   │  Titre                                        │
│  Date début      │  📅 12 avril 2026 · 14h00 → 17h30             │
│  Date fin        │  📍 Lieu cliquable (Google Maps)              │
│  Lieu            │                                               │
│  Organisateur    │  Description (intro)                          │
│  Tarif           │  ─────────────                                │
│  Publié le       │  Contenu HTML rendu                           │
│                  │  ─────────────                                │
│                  │  Inscription (si pertinent)                   │
└──────────────────┴───────────────────────────────────────────────┘
```

**Boutons header** : `Modifier` + `👁 Voir côté public` (si publié).

**Sidebar — sous-blocs** :
- **Type** : `event_type` (Conférence / Atelier / etc.)
- **Statut** : badge `draft` / `published` (couleurs gris/green)
- **Temporalité** : badge supplémentaire calculé via `Event::isUpcoming()` / `isPast()` (méthodes existantes) — 🟢 À venir / 🟠 En cours / ⚫ Passé
- **Date début** / **Date fin** : `start_date` et `end_date` formatées français (`d/m/Y` puis `H:i` sur ligne séparée)
- **Lieu** : `location_name`, puis ville sur ligne séparée si différente
- **Organisateur** : `$event->organizer->name`
- **Tarif** : `price > 0` → `15 €` ; sinon → `Gratuit`
- **Publié le** : `published_at` formaté

**Body** :
- **Image cover** : pareil qu'articles, masquée si null
- **Titre** : `<h1>`
- **Date+lieu en bandeau** : ligne avec icône Lucide `calendar` + date formatée intelligemment :
  - Même jour : `12 avril 2026 · 14h00 → 17h30`
  - Multi-jours : `12 avril 2026 → 14 avril 2026`
  - Pas de `end_date` : `12 avril 2026 · 14h00`
- **Lieu cliquable** : icône `map-pin` + `location_name`, `location_address`, `location_city`. Lien vers Google Maps construit depuis l'adresse texte (URL encodée). Si pas d'adresse, juste texte sans lien.
- **Description** : `<p>` italique en intro
- **Séparateur** `<hr>`
- **Contenu HTML** : `{!! $event->content !!}` rendu correctement, mêmes styles `.article-content`
- **Séparateur**
- **Bloc Inscription** : visible si `registration_required` OU `registration_url` OU `max_participants`. Affiche :
  - `Maximum N participants` si `max_participants`
  - Bouton externe `↗ S'inscrire` lien vers `registration_url` si présent

## Modifications du formulaire Event

`resources/views/admin/events/_form.blade.php` :

### Ajout 1 — Champ image upload

À placer dans la colonne droite du form, avant le bloc "Inscription" actuel :

```blade
<div class="form-group">
    <label class="form-label" for="featured_image">Image de couverture</label>
    @if(isset($event) && $event->featured_image)
        <div style="margin-bottom: 0.75rem;">
            <img src="{{ Storage::url($event->featured_image) }}" alt="" style="max-width: 100%; max-height: 200px; border-radius: 0.5rem;">
        </div>
    @endif
    <input type="file" name="featured_image" id="featured_image" accept="image/*" class="form-input">
    <small style="color: #6b7280; font-size: 0.8125rem;">JPG, PNG ou WebP. 5 Mo maximum.</small>
    @error('featured_image')<p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
</div>
```

### Ajout 2 — Quill remplace textarea

Le `<textarea name="content">` actuel devient un Quill v2 identique au pattern d'articles. Reprend le bloc JS du form articles (CDN `https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css` et `.../quill.js`, init `theme: 'snow'`, hidden input synchronisé à chaque `text-change`).

### Pré-requis form `enctype`

`resources/views/admin/events/edit.blade.php` et `create.blade.php` : vérifier que le `<form>` parent a bien `enctype="multipart/form-data"`. Si non présent, l'ajouter (sans cela l'upload d'image silently ne marche pas — bug classique).

## Modifications du EventController

`app/Http/Controllers/Admin/EventController.php` — `store()` et `update()` :

### Validation

Ajouter à la liste de règles :

```php
'featured_image' => 'nullable|image|max:5120',  // 5 MB
```

### Logique de save

Avant le `Event::create()` ou `$event->update()`, ajouter :

```php
if ($request->hasFile('featured_image')) {
    if (isset($event) && $event->featured_image) {
        Storage::disk('public')->delete($event->featured_image);
    }
    $validated['featured_image'] = $request->file('featured_image')
        ->store('events/images', 'public');
}
```

(Pattern identique à `ArticleController::store/update`.)

## Tests

3 fichiers feature, 11 tests au total.

### `tests/Feature/Admin/ArticleShowAdminTest.php` (4 tests)

- `test_show_renders_featured_image_when_present` — article avec `featured_image = 'articles/images/x.jpg'` → URL `/storage/articles/images/x.jpg` apparaît dans HTML
- `test_show_does_not_render_image_block_when_absent` — article avec `featured_image = null` → aucun `<img` dans le bloc body
- `test_show_renders_content_html_correctly` — article avec contenu `<p>Bonjour <strong>monde</strong></p>` → `<strong>` apparaît tel quel rendu, **pas** `&lt;strong&gt;`
- `test_show_displays_validation_block_when_validated` — article `validated_by` set + `validation_notes = "OK"` → bloc validation visible avec nom valideur et notes

### `tests/Feature/Admin/EventShowAdminTest.php` (4 tests)

- `test_show_renders_featured_image_when_present` — idem articles
- `test_show_renders_content_html_correctly` — idem articles
- `test_show_displays_upcoming_badge_for_future_event` — `start_date = now()->addWeek()` → "À venir" visible
- `test_show_displays_past_badge_for_past_event` — `end_date = now()->subWeek()` → "Passé" visible

### `tests/Feature/Admin/EventFormImageTest.php` (3 tests)

- `test_create_event_with_uploaded_image_persists_path` — POST `events.store` avec `UploadedFile::fake()->image('cover.jpg')` → DB a `featured_image` qui commence par `events/images/`
- `test_update_event_replaces_old_image` — event existant avec image, PUT avec nouvelle image → ancienne supprimée du storage (vérifier via `Storage::fake('public')->assertMissing(...)`), nouvelle stockée
- `test_validation_rejects_non_image_file` — POST avec `UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')` → session has errors sur `featured_image`

## Hors scope (différé)

- **Refonte des forms en 5 cartes thématiques** (option C écartée à la phase brainstorming)
- **Bouton "Supprimer l'image"** sans la remplacer (on peut juste écraser par une nouvelle pour l'instant)
- **Galerie multi-images** ou pièces jointes multiples
- **Prévisualisation HTML** dans le form pendant l'édition
- **Stats avancées** (vues sur 7 jours, événements participants, etc.)
- **Crop/resize automatique** des images uploadées
- **Workflow de validation côté show** (boutons "Soumettre" / "Valider" / "Rejeter") — restent sur les pages dédiées existantes

## Compatibilité

- **PostgreSQL 9.6 prod** : sans objet, aucune migration.
- **Cache view** : `php artisan view:clear` après déploiement.
- **Tests existants** : aucun test ne devrait casser. Les pages show admin ne sont pas testées aujourd'hui.
- **Cohérence storage** : les fichiers uploadés vont dans `storage/app/public/events/images/` ; le symlink `public/storage` est déjà en place.

## Référence

- Audit du 2026-05-03 (cf. brainstorming session)
- Pattern de référence côté forms : `resources/views/admin/articles/_form.blade.php` (Quill v2 + image upload)
- Pattern de référence côté show public : `resources/views/hub/articles/show.blade.php` (image + `{!! $content !!}`)
- Mémoires projet : `feedback_postgres_96_migrations.md` (sans objet ici), `feedback_no_filament.md` (Blade custom — déjà respecté)
