# Espace Membre — Documentation fonctionnelle

**Préfixe URL :** `/espace-membre`
**Middleware :** `auth`
**Layout :** `resources/views/layouts/member.blade.php` (3 colonnes)
**Branche :** `feature/espace-membre-couche1`

---

## Architecture

### Layout 3 colonnes

- **Sidebar gauche (240px)** : logo OREINA, avatar/initiales membre, département, badge adhérent actif, liste des GT rejoints, navigation complète, liens retour site + déconnexion
- **Zone centrale (flexible)** : header mobile (hamburger), flash messages, contenu de la page
- **Sidebar droite (280px)** : agenda événements à venir (groupés par mois), carte des membres (mini), chat adhérents (compact)

### Responsive

| Breakpoint | Comportement |
|-----------|-------------|
| Desktop (> 1024px) | 3 colonnes complètes |
| Tablette (768–1024px) | Sidebar gauche icônes seules (60px), sidebar droite sous le contenu |
| Mobile (< 768px) | Hamburger menu slide-in, sidebar droite empilée sous le contenu |

---

## Pages

### Tableau de bord (`/espace-membre`)
- **Controller :** `Member\DashboardController@index`
- Greeting personnalisé avec prénom + date
- Badge statut adhésion (actif / expiré)
- Cartes Groupes de Travail colorées (dynamiques, max 3)
- 4 stats : années adhésion, total dons, nombre de dons, observations (placeholder)
- Feed d'activité récente (Livewire `ActivityFeed`) : agrège dons, adhésions, revue, événements — 10 items max
- Derniers dons avec lien reçu fiscal
- Derniers numéros de la revue (si adhérent actif)

### Mon profil (`/espace-membre/profil`)
- **Controller :** `Member\ProfileController@index`
- Formulaire : civilité, nom, prénom, email, téléphone, adresse, photo
- Mise à jour avec synchronisation email User

### Mon adhésion (`/espace-membre/adhesion`)
- **Controller :** `Member\MembershipController@index`
- Statut adhésion courante + historique
- Téléchargement carte adhérent PDF (`/adhesion/carte`)
- Téléchargement attestation PDF (`/adhesion/attestation`)

### Mes contributions (`/espace-membre/contributions`)
- **Controller :** `Member\WorkGroupController@contributions`
- Stats : nombre de GT rejoints, total membres
- Liste des GT de l'adhérent avec rôle et chiffres

### Groupes de travail (`/espace-membre/groupes-de-travail`)
- **Controller :** `Member\WorkGroupController@index`
- Grille de cartes de tous les GT actifs
- Bordure colorée par GT, nombre de membres, badge "Membre" si inscrit
- Lien "En savoir plus" vers le site web du GT

### Communauté (`/espace-membre/communaute`)
- **Controller :** `Member\CommunityController@index`
- Carte des membres (composant SVG plein format)
- Chat adhérents (mode compact)

### Carte des membres (`/espace-membre/carte`)
- **Controller :** `Member\CommunityController@map`
- Carte SVG de France métropolitaine + Corse (96 départements)
- Points colorés par densité (dégradé vert : clair = peu, foncé = beaucoup)
- Tooltip au survol : nom département + nombre de membres
- Données agrégées par code postal (2 premiers chiffres), cache 24h
- Aucune donnée personnelle exposée

### Chat (`/espace-membre/chat`)
- **Controller :** `Member\ChatController@index`
- Chat en vue étendue (historique 50 messages)
- Livewire avec polling 5 secondes
- Envoi de messages (max 500 caractères)
- Affichage : initiales, prénom, temps relatif

### Lepis (`/espace-membre/lepis`)
- **Controller :** `Member\LepisController@index`
- Grille des bulletins Lepis publiés (paginés par 12)
- Téléchargement PDF (`/lepis/{bulletin}/telecharger`)
- Bouton "Suggérer un article"

### Suggestion Lepis (`/espace-membre/lepis/suggerer`)
- **Controller :** `Member\LepisController@suggest / storeSuggestion`
- Formulaire : titre, contenu (texte libre), pièce jointe optionnelle (max 10 Mo)
- Notification par redirection avec message de succès

### Mes documents (`/espace-membre/documents`)
- **Controller :** `Member\DocumentController@index`
- Reçus fiscaux Cerfa par don
- Reçus d'adhésion par membership

### La revue (`/espace-membre/revue`)
- **Controller :** `Member\JournalController@index`
- Numéros publiés (paginés par 12)
- Téléchargement PDF (adhérents actifs uniquement)

### Préférences (`/espace-membre/profil/preferences`)
- **Controller :** `Member\ProfileController@preferences`
- Consentements RGPD : newsletter, communication, droit à l'image

---

## Composants Livewire

| Composant | Fichier | Usage |
|-----------|---------|-------|
| `ActivityFeed` | `app/Livewire/Member/ActivityFeed.php` | Feed d'activité du dashboard (dons, adhésions, revue, événements) |
| `Chat` | `app/Livewire/Member/Chat.php` | Chat adhérents, mode compact (sidebar) ou étendu (page), polling 5s |

## Composants Blade

| Composant | Fichier | Usage |
|-----------|---------|-------|
| `<x-member.sidebar-right>` | `resources/views/components/member/sidebar-right.blade.php` | Sidebar droite : agenda, carte mini, chat mini |
| `<x-member.map-france>` | `resources/views/components/member/map-france.blade.php` | Carte SVG départementale, prop `compact` pour mode mini |

## View Composer

| Composer | Fichier | Données partagées |
|----------|---------|-------------------|
| `MemberLayoutComposer` | `app/View/Composers/MemberLayoutComposer.php` | `$upcomingEvents` — 10 prochains événements publiés |

---

## Modèles (créés pour l'espace membre)

### WorkGroup (`work_groups`)
| Champ | Type | Description |
|-------|------|-------------|
| name | string | Nom du GT |
| slug | string (unique) | URL-friendly |
| description | text | Description |
| color | string(7) | Couleur hex (#2C5F2D) |
| icon | string (nullable) | Icône |
| website_url | string (nullable) | Lien externe |
| is_active | boolean | Actif/inactif |

**Pivot :** `work_group_member` (work_group_id, member_id, role, joined_at)

### LepisBulletin (`lepis_bulletins`)
| Champ | Type | Description |
|-------|------|-------------|
| title | string | Titre du bulletin |
| issue_number | integer | Numéro |
| quarter | string(2) | Q1, Q2, Q3, Q4 |
| year | integer | Année |
| pdf_path | string | Chemin du PDF (storage public) |
| published_at | datetime | Date de publication |
| is_published | boolean | Publié/brouillon |

### LepisSuggestion (`lepis_suggestions`)
| Champ | Type | Description |
|-------|------|-------------|
| member_id | FK | Auteur |
| title | string | Titre/sujet |
| content | text | Contenu |
| attachment_path | string (nullable) | Pièce jointe |
| status | string | pending / noted |
| submitted_at | datetime | Date soumission |

### ChatMessage (`chat_messages`)
| Champ | Type | Description |
|-------|------|-------------|
| member_id | FK | Auteur |
| content | text | Message |

---

## Administration (Extranet)

### Groupes de Travail (`/extranet/work-groups`)
- CRUD complet (liste, création, édition, suppression)
- Gestion des membres : ajout/retrait, rôle (member/leader)
- Recherche, stats (total, actifs, total membres)

### Lepis Bulletins (`/extranet/lepis`)
- CRUD avec upload PDF
- Publication/dépublication (toggle)
- Stats (total, publiés)

### Suggestions Lepis (`/extranet/lepis-suggestions`)
- Liste avec filtre par statut
- Vue détaillée avec contenu + pièce jointe
- Marquage "noté"
- Suppression

---

## Fonctionnalités prévues (non implémentées)

D'après `docs/modules_membre.pdf`, il reste à développer :

- **Inscription aux événements/rencontres** depuis l'espace membre
- **Adhérer/re-adhérer** directement (lien HelloAsso)
- **Annuaire des membres par département** (avec accord de partage)
- **Kit de débutant / introduction aux nouveaux arrivants** (GT Validation)
- **Chiffres clés par GT** : temps bénévolat total et personnel, progression
- **Données Artemisiae** : observations, espèces, indicateurs géolocalisés, espèces prioritaires par département
- **Fiches espèces**
- **Leaderboard** contributions
- **Webinaires** : liste et accès
- **Revue enrichie** : historique articles publiés par l'adhérent, stats consultations/téléchargements
- **Galerie photos**

---

*Dernière mise à jour : 3 avril 2026*
