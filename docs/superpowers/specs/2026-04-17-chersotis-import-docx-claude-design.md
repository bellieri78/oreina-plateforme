# Chersotis — Import Word/ODT via Claude API (refonte sous-projet I)

**Contexte** : Le `DocumentToBlocksService` basé sur phpword produit des blocs de qualité insuffisante (titres mal détectés, indentation perdue). Un convertisseur en ligne docx→md suivi de l'import .md existant donne un bien meilleur résultat. On remplace le parsing phpword direct par un appel à Claude Haiku pour convertir le texte brut en Markdown structuré, puis on passe par `MarkdownToBlocksService`.

---

## Décisions

| # | Question | Réponse |
|---|----------|---------|
| Q1 | Stockage clé API | `.env` classique Laravel (`ANTHROPIC_API_KEY`) |
| Q2 | Fallback si API indisponible | Message d'erreur explicite, pas de fallback silencieux |
| Q3 | Import .md direct | Conservé tel quel, sans dépendance API |

---

## Architecture

```
.md/.txt/.markdown → MarkdownToBlocksService → blocs  (inchangé)
.docx/.odt → DocumentConversionService → Markdown → MarkdownToBlocksService → blocs
```

### DocumentConversionService

Responsabilité : recevoir un chemin de fichier `.docx` ou `.odt`, en extraire le texte brut via phpword, envoyer ce texte à Claude Haiku pour conversion en Markdown structuré, retourner le Markdown.

Méthode publique : `toMarkdown(string $filePath): string`

**Extraction de texte** : phpword charge le document, on parcourt tous les éléments (sections → paragraphes/tables/listes) et on extrait le texte brut avec des marqueurs minimaux (retours à la ligne, séparateurs de cellules pour les tableaux). L'objectif est de donner à Claude un texte lisible avec suffisamment d'indices de structure.

**Appel API** : HTTP POST direct via `Http::post()` de Laravel (pas de SDK). Endpoint : `https://api.anthropic.com/v1/messages`.

**Prompt système** :
```
Tu es un convertisseur de documents scientifiques. Convertis le texte suivant en Markdown structuré.
Règles :
- Utilise # / ## / ### pour les titres (déduis le niveau du contexte et de la mise en forme)
- Conserve **gras** et *italique*
- Les noms d'espèces (noms latins binomiaux) doivent être en *italique*
- Utilise des tableaux pipe Markdown pour les tableaux
- Utilise des listes à puces ou numérotées selon le contexte
- Utilise > pour les citations
- Ne modifie pas le contenu textuel, ne résume pas, ne commente pas
- Retourne uniquement le Markdown, sans explication ni balise de code
```

**Modèle** : `claude-haiku-4-5-20251001`
**Max tokens** : 8192
**Timeout** : 60 secondes (les articles peuvent être longs)
**Coût estimé** : ~0.01-0.03€ par document

### Gestion d'erreurs

| Situation | Comportement |
|---|---|
| `ANTHROPIC_API_KEY` absente ou vide | Erreur 422 JSON : "La conversion de documents Word nécessite la configuration de l'API. Importez un fichier .md à la place." |
| API timeout ou erreur réseau | Erreur 422 JSON : "La conversion a échoué (service indisponible). Réessayez ou importez un fichier .md." |
| API retourne une erreur (401, 429, 500) | Erreur 422 JSON avec message descriptif |
| Document vide ou illisible | Erreur 422 JSON : "Le document ne contient pas de texte exploitable." |

L'import `.md` direct reste toujours disponible, sans aucune dépendance API.

### Config

```php
// config/services.php — ajouter dans le tableau existant
'anthropic' => [
    'api_key' => env('ANTHROPIC_API_KEY'),
],
```

```env
# .env
ANTHROPIC_API_KEY=sk-ant-...
```

### Controller

La méthode `importMarkdown` dans `SubmissionController` dispatch selon l'extension :
- `.md` / `.txt` / `.markdown` → `MarkdownToBlocksService::parse(content)` (inchangé)
- `.docx` / `.odt` → `DocumentConversionService::toMarkdown(path)` → `MarkdownToBlocksService::parse(markdown)`

### UI

Inchangée par rapport à l'état actuel : bouton "Importer un document", accepte `.md,.txt,.markdown,.docx,.odt`.

---

## Tests

### DocumentConversionServiceTest

| Test | Vérifie |
|---|---|
| `test_extract_text_from_docx` | L'extraction de texte brut via phpword retourne le contenu du document |
| `test_extract_text_from_odt` | Idem pour ODT |
| `test_extract_text_includes_table_content` | Les cellules de tableau sont extraites |
| `test_throws_when_api_key_missing` | Exception si `ANTHROPIC_API_KEY` est vide |
| `test_throws_when_api_fails` | Exception si l'API retourne une erreur (mock HTTP) |
| `test_to_markdown_returns_string` | Appel complet avec mock HTTP retourne du Markdown |

Les tests qui appellent l'API utilisent `Http::fake()` de Laravel pour mocker les réponses.

---

## Fichiers impactés

### Créés
- `app/Services/DocumentConversionService.php`
- `tests/Unit/Services/DocumentConversionServiceTest.php`

### Modifiés
- `config/services.php` (ajout section `anthropic`)
- `app/Http/Controllers/Admin/SubmissionController.php` (dispatch vers `DocumentConversionService`)
- `resources/views/admin/documentation/index.blade.php` (doc mise à jour)

### Supprimés
- `app/Services/DocumentToBlocksService.php`
- `tests/Unit/Services/DocumentToBlocksServiceTest.php`

### Conservés
- `phpoffice/phpword` (utilisé pour l'extraction de texte)
- `MarkdownToBlocksService` (inchangé)
