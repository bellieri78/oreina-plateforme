# Crédits photo — Pages projets

Photos utilisées dans les chapôs des pages projets du hub (`/projets/*`).

| Page | Espèce | Fichier | Lieu / Date | Photographe |
|---|---|---|---|---|
| TAXREF | *Chersotis oreina* (Dufay, 1984) | `taxref/chersotis-oreina.{webp,jpg}` | Hautes-Alpes | R. Balestra |
| SEQREF | *Saturnia pavonia* (Linnaeus, 1758) | `seqref/saturnia-pavonia.{webp,jpg}` | Pyrénées-Atlantiques, V.2024 | D. Demergès |
| IDENT  | *Pyrgus malvoides* (Elwes & Edwards, 1897) | `ident/pyrgus-malvoides.{webp,jpg}` | Ariège, gr. T5 — armures génitales nécessaires | D. Demergès |
| QUALIF | *Catocala fraxini* (Linnaeus, 1758) | `qualif/catocala-fraxini.{webp,jpg}` | Massif central, VIII.2024 | D. Demergès |

## Spécifications techniques

- **Format préféré** : WebP qualité 78-82 (≈ 150-250 ko)
- **Format de secours** : JPEG qualité 82 (≈ 250-400 ko)
- **Dimensions** : 1200 × 1500 px (rapport 4/5, format portrait)
- **Cadrage** : sujet centré, marges respirantes (la légende en overlay couvre les 20 % inférieurs)

## Workflow

1. Sélectionner un cliché dans le stock perso
2. Recadrer en 4/5 portrait (1200 × 1500 px)
3. Exporter en JPEG qualité 82
4. Convertir en WebP : `cwebp -q 80 fichier.jpg -o fichier.webp`
5. Déposer dans `public/images/projets/{taxref|seqref|ident|qualif}/`
6. Mettre à jour ce fichier avec les nouveaux crédits si l'espèce change
