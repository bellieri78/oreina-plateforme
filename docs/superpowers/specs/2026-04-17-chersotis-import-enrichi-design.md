# Chersotis — Import Word/ODT enrichi via Claude API

**Contexte** : L'import Word/ODT via Claude API fonctionne (conversion texte → Markdown → blocs). On enrichit maintenant la réponse Claude pour extraire automatiquement les métadonnées (références, affiliations, remerciements, titre) et détecter les taxons pour les lier à Artemisiae.

---

## Décisions

| # | Question | Réponse |
|---|----------|---------|
| Q1 | URL Artemisiae pour les taxons | `https://oreina.org/artemisiae/index.php?module=recherche&action=recherche&recherche={taxon}` |
| Q2 | Ancres citations inline → références | Uniquement sur la page publique (futur), pas dans l'éditeur de blocs |
| Q3 | Remerciements | Extraits et pré-remplis dans le champ sidebar |
| Q4 | Titre de l'article | Retiré du Markdown + retourné pour vérification/mise à jour |
| Q5 | Sections retirées du Markdown | Titre, affiliations auteurs, références bibliographiques, remerciements |

---

## Architecture

### Prompt Claude — réponse JSON structurée

Le prompt demande à Claude de retourner un JSON (pas du Markdown brut) :

```json
{
  "title": "Situation préoccupante de Chazara briseis (Linnaeus, 1774) dans l'Aude",
  "markdown": "## Résumé\n\nL'Hermite est un papillon...",
  "references": [
    "Brepson L. & Vesco A., 2023. Titre de l'article...",
    "Dupont J., 2022. Autre référence..."
  ],
  "authors_affiliations": [
    "Loïc BREPSON : Fédération Aude Claire, 32 rue des Augustins 11300 Limoux",
    "Angélina VESCO : Fédération Aude Claire, 32 rue des Augustins 11300 Limoux"
  ],
  "acknowledgements": "Les auteurs remercient le CEN Occitanie...",
  "taxons": [
    "Chazara briseis",
    "Hipparchia semele",
    "Melanargia galathea"
  ]
}
```

Le champ `markdown` ne contient **ni** le titre général, **ni** les affiliations auteurs, **ni** les références bibliographiques, **ni** les remerciements — ils sont extraits dans leurs champs respectifs.

### Prompt système révisé

```
Tu es un convertisseur de documents scientifiques entomologiques.
Analyse le texte suivant et retourne un JSON structuré avec ces champs :

- "title" : le titre principal de l'article (sans les auteurs)
- "markdown" : le corps de l'article converti en Markdown structuré (## pour les sous-titres, **gras**, *italique*, tableaux pipe, listes). NE PAS inclure : le titre principal, les affiliations des auteurs, les références bibliographiques, les remerciements.
- "references" : tableau JSON des références bibliographiques, une par entrée, dans l'ordre d'apparition
- "authors_affiliations" : tableau JSON des auteurs avec leurs affiliations (un auteur par entrée)
- "acknowledgements" : texte des remerciements (chaîne vide si absent)
- "taxons" : tableau JSON des noms d'espèces (noms latins binomiaux) trouvés dans le texte, sans doublons

Règles pour le Markdown :
- Les noms d'espèces doivent être en *italique*
- Conserver le formatage (gras, italique, exposant, indice)
- Utiliser des tableaux pipe pour les tableaux
- Ne pas modifier le contenu textuel, ne pas résumer
- Retourner uniquement le JSON, sans explication ni balise de code
```

### Traitement côté controller

1. **Parse JSON** de la réponse Claude
2. **Blocs** ← `markdown` passe dans `MarkdownToBlocksService`
3. **Enrichissement taxons** ← dans chaque bloc de type `paragraph`, rechercher les noms de taxons (depuis la liste `taxons`) dans le HTML et les transformer en liens Artemisiae :
   - `<em>Chazara briseis</em>` → `<a href="https://oreina.org/artemisiae/index.php?module=recherche&action=recherche&recherche=Chazara%20briseis" target="_blank"><em>Chazara briseis</em></a>`
4. **Réponse JSON** au navigateur enrichie avec les champs sidebar + titre détecté

### Réponse JSON au navigateur

```json
{
  "blocks": [...],
  "count": 42,
  "references": "Brepson L. & Vesco A., 2023. Titre...\nDupont J., 2022...",
  "authors_affiliations": "Loïc BREPSON : Fédération Aude Claire...\nAngélina VESCO : ...",
  "acknowledgements": "Les auteurs remercient...",
  "detected_title": "Situation préoccupante de Chazara briseis..."
}
```

Pour les fichiers `.md` (import direct), la réponse reste inchangée : `{ blocks, count }` uniquement.

### Côté JS (Alpine)

A la réception de la réponse enrichie :
- Les blocs remplacent l'éditeur (inchangé)
- Les textareas sidebar sont pré-remplies via leurs IDs :
  - `textarea[name="references"]` ← `data.references`
  - `textarea[name="author_affiliations"]` ← `data.authors_affiliations`
  - `textarea[name="acknowledgements"]` ← `data.acknowledgements`
- Si `detected_title` est présent et différent du titre actuel (`{{ $submission->title }}`), afficher un bandeau au-dessus de l'éditeur :
  > Titre détecté : *Situation préoccupante de...* — [Mettre à jour le titre]
- Le bouton "Mettre à jour" fait un fetch PATCH sur une route dédiée pour modifier `$submission->title`

---

## Gestion d'erreurs

| Situation | Comportement |
|---|---|
| Claude retourne du texte au lieu de JSON | Tenter de parser, si échec fallback : traiter la réponse comme du Markdown brut (comportement actuel) |
| Champs manquants dans le JSON | Utiliser des valeurs par défaut (tableau vide, chaîne vide) |
| Taxon non trouvé dans le HTML des blocs | Ignoré silencieusement (le lien n'est pas créé) |

---

## Fichiers impactés

### Modifiés

| Fichier | Changement |
|---|---|
| `app/Services/DocumentConversionService.php` | Nouveau prompt JSON, nouvelle méthode `toStructured()` qui retourne un array au lieu d'une string |
| `app/Http/Controllers/Admin/SubmissionController.php` | Enrichissement taxons dans les blocs, réponse JSON élargie, route PATCH titre |
| `resources/views/admin/submissions/_block-editor.blade.php` | JS : remplir sidebar + bandeau titre détecté |
| `resources/views/admin/submissions/layout.blade.php` | Ajouter IDs sur les textareas sidebar pour accès JS |
| `routes/admin.php` | Route PATCH pour mise à jour titre |
| `tests/Unit/Services/DocumentConversionServiceTest.php` | Adapter au format JSON structuré |
| `resources/views/admin/documentation/index.blade.php` | Doc mise à jour |
