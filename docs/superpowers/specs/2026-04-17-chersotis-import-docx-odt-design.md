# Chersotis — Import Word/ODT dans l'éditeur de blocs (extension sous-projet I)

**Contexte** : Le sous-projet I a livré l'import Markdown → blocs de maquette. Les auteurs soumettent en Word (.docx) après allers-retours éditoriaux. Actuellement, il faut convertir manuellement en Markdown (via Pandoc) avant import. Ce sous-projet ajoute l'import direct de fichiers `.docx` et `.odt` dans l'éditeur de blocs, sans dépendance système externe.

**Dépendance** : `phpoffice/phpword` (PHP natif, lit `.docx` et `.odt`).

---

## Questions posées & décisions

| # | Question | Réponse |
|---|----------|---------|
| Q1 | Images intégrées dans les Word ? | Non — envoyées séparément en haute résolution après acceptation |
| Q2 | Les auteurs utilisent les styles Word (Titre 1, etc.) ? | Non — formatage manuel (gras + taille de police) |
| Q3 | Notes de bas de page / commentaires Word ? | Non — document nettoyé avant import |

---

## Architecture

### Nouveau service : `DocumentToBlocksService`

Responsabilité : parser un fichier `.docx` ou `.odt` via `phpoffice/phpword` et retourner un `array` de blocs compatibles avec l'éditeur de maquette.

Le service existant `MarkdownToBlocksService` reste inchangé.

### Détection des titres (heuristique)

Les auteurs n'utilisent pas les styles Word. Heuristique pour détecter les titres :

1. Paragraphe **court** (< 100 caractères de texte brut)
2. **Tout le texte est en gras**
3. **Taille de police supérieure** à la taille du corps du document

Niveau de titre déduit de la taille de police :
- `>= 16pt` → H1
- `>= 14pt` → H2
- `< 14pt` (mais > corps) → H3

Si un paragraphe est court et tout en gras mais sans changement de taille, il est traité comme H3 (titre de section courant dans les articles scientifiques).

**Fallback** : si des styles Word sont présents (`Heading1`, `Heading2`, etc.), ils sont prioritaires sur l'heuristique.

### Mapping des éléments

| Élément Word/ODT | Bloc généré | Détails |
|---|---|---|
| Paragraphe court, tout gras, grande police | `heading` | H1/H2/H3 selon taille |
| Paragraphe avec style `Heading1`/`Heading2`/`Heading3` | `heading` | Prioritaire sur heuristique |
| Paragraphe normal | `paragraph` | Gras → `<strong>`, italique → `<em>`, exposant → `<sup>`, indice → `<sub>` |
| Tableau | `table` | 1re ligne → `headers`, reste → `rows` |
| Liste à puces | `list` (unordered) | Via `NumberingStyle` du paragraphe |
| Liste numérotée | `list` (ordered) | Via `NumberingStyle` du paragraphe |
| Image intégrée | Ignorée | Les images sont envoyées séparément |
| Saut de page / section | Ignoré | — |
| Note de bas de page | Ignorée | Document nettoyé avant import |

### Format de sortie des blocs

Identique à `MarkdownToBlocksService` :

```php
// heading
['id' => 'block-doc-1', 'type' => 'heading', 'level' => '2', 'content' => 'Méthode']

// paragraph
['id' => 'block-doc-2', 'type' => 'paragraph', 'content' => 'Texte avec <strong>gras</strong> et <em>italique</em>.']

// table
['id' => 'block-doc-3', 'type' => 'table', 'headers' => ['Espèce', 'Localité'], 'rows' => [['P. apollo', 'Pyrénées']], 'caption' => '']

// list
['id' => 'block-doc-4', 'type' => 'list', 'listType' => 'unordered', 'items' => ['Point 1', 'Point 2']]
```

---

## Intégration dans le flux existant

### Route

Inchangée : `POST /extranet/submissions/{id}/import-markdown` (le nom de route reste pour rétrocompatibilité, mais la méthode est renommée conceptuellement).

### Controller

La méthode `importMarkdown` dans `SubmissionController` est élargie :

1. Validation : accepte `.md`, `.txt`, `.markdown`, `.docx`, `.odt`
2. Dispatch selon l'extension :
   - `.md` / `.txt` / `.markdown` → `MarkdownToBlocksService::parse()`
   - `.docx` / `.odt` → `DocumentToBlocksService::parse()`
3. Réponse JSON identique : `{ blocks: [...], count: N }`

### UI

- Le bouton **"Importer Markdown"** devient **"Importer un document"**
- L'input file accepte `.md,.txt,.markdown,.docx,.odt`
- Le message d'erreur pour format non supporté est mis à jour
- Le message de succès reste identique

### Documentation

Mise à jour de la page documentation extranet pour refléter les nouveaux formats acceptés.

---

## Tests

### Tests unitaires : `DocumentToBlocksServiceTest`

| Test | Vérifie |
|---|---|
| `test_heading_by_style` | Paragraphe avec style Heading1 → bloc heading H1 |
| `test_heading_by_heuristic` | Paragraphe court + tout gras + grande police → heading |
| `test_paragraph_with_inline_formatting` | Gras, italique, exposant → HTML inline |
| `test_table` | Tableau Word → bloc table (headers + rows) |
| `test_list_unordered` | Liste à puces → bloc list unordered |
| `test_list_ordered` | Liste numérotée → bloc list ordered |
| `test_images_ignored` | Images intégrées ne génèrent pas de bloc |
| `test_empty_paragraphs_skipped` | Paragraphes vides ignorés |
| `test_each_block_has_id` | Tous les blocs ont un `id` |

Les tests utilisent des fichiers `.docx` de fixture créés programmatiquement avec `PhpWord` dans le `setUp()`.

### Test feature : import via controller

Vérifier que l'upload d'un `.docx` retourne bien du JSON avec les blocs.

---

## Limites connues

- Les titres formatés sans gras ni changement de taille seront traités comme des paragraphes → correction manuelle dans l'éditeur après import
- Les blockquotes n'existent pas en Word → pas de bloc `quote` généré
- Les listes imbriquées sont aplaties (un seul niveau)
- Les images intégrées sont ignorées (par design — envoyées séparément)
- Les fichiers `.doc` (ancien format binaire) ne sont pas supportés — uniquement `.docx` (Office Open XML) et `.odt`

---

## Fichiers impactés

### Créés
- `app/Services/DocumentToBlocksService.php`
- `tests/Unit/Services/DocumentToBlocksServiceTest.php`

### Modifiés
- `composer.json` (ajout `phpoffice/phpword`)
- `app/Http/Controllers/Admin/SubmissionController.php` (dispatch par extension)
- `resources/views/admin/submissions/_block-editor.blade.php` (label bouton + accept)
- `resources/views/admin/documentation/index.blade.php` (doc mise à jour)
