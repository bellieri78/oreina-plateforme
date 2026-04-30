# Lepis — Format papier / numérique par adhérent + tracking d'envoi

**Date** : 2026-04-30
**Statut** : design validé, prêt pour planification
**Précédente itération** : `2026-04-21-bulletin-lepis-diffusion-design.md` (système 3 états `draft`/`members`/`public` + sync Brevo + policy de download — déjà livré).

## Contexte

Le bulletin trimestriel Lepis est aujourd'hui diffusé indistinctement à tous les adhérents à jour quand le rédac-chef passe un bulletin de `draft` à `members`. La liste Brevo créée par `SyncLepisBulletinToBrevoList` contient l'intégralité des adhérents actifs ; le rédac-chef envoie ensuite manuellement la campagne email depuis Brevo.

Ce design ajoute :

1. Une **préférence par adhésion** entre format papier et format numérique, capturée à l'adhésion (HelloAsso ou backoffice) et figée pour la durée de l'adhésion.
2. Un **filtrage de la liste Brevo** sur les seuls abonnés numériques.
3. La **génération d'un export postal** pour les abonnés papier (CSV destinataires).
4. Un **tracking d'envoi par contact** : table de snapshot qui mémorise, pour chaque bulletin, qui était dans la liste de diffusion (papier ou numérique). Visible côté fiche contact admin et côté fiche bulletin admin.
5. Une **règle d'inclusion** : un nouvel adhérent reçoit son premier bulletin par voie automatique (postal ou email) seulement si le bulletin passe en `members` après sa date d'adhésion. Les bulletins antérieurs ne sont jamais envoyés rétroactivement.
6. **L'espace membre reste inchangé** côté accès PDF : tout adhérent à jour voit tous les bulletins `members` et `public` via `/lepis/bulletins`. La distinction papier/numérique ne joue **que sur l'envoi automatique**, pas sur l'accès en ligne.

## Décisions de conception

| Décision | Choix retenu | Alternative écartée |
|---|---|---|
| Exclusivité du format | `paper` ou `digital`, exclusif | Cumul des deux (rejeté : ne reflète pas la réalité OREINA) |
| Mutabilité | Figé sur l'adhésion, redécidé au renouvellement | Modifiable en cours d'adhésion (rejeté : ouvre des cas limites côté snapshot) |
| Stockage | `memberships.lepis_format` | `members.lepis_format` (rejeté : perd l'historique inter-adhésions) |
| Visibilité des `draft` aux adhérents | Inchangée : `draft` réservé aux éditeurs | Élargir aux adhérents (rejeté : casse la sémantique WIP) |
| Définition "prochain numéro" | `bulletin.published_to_members_at > membership.start_date` | Calendrier fixe Q1=avril etc. (rejeté : découple de la réalité technique) |
| Tracking Brevo | Snapshot d'expédition seulement | Confirmation de livraison via webhooks (différé : extensible plus tard) |
| Export papier | Persisté en DB + export CSV à la demande | Géré hors-système (rejeté : laisse un trou côté fiche contact) |
| Workflow d'envoi numérique | Inchangé : campagne Brevo créée à la main par le rédac-chef | Envoi auto via API Brevo (rejeté : sortie de scope) |
| Valeur par défaut | Mix : `paper` pour le legacy, obligatoire pour les nouvelles | Default unique pour tous les cas |

## Modèle de données

### Migration `add_lepis_format_to_memberships`

Ajout d'une colonne sur `memberships` :

```sql
ALTER TABLE memberships
  ADD COLUMN lepis_format VARCHAR(10) NULL
  CHECK (lepis_format IN ('paper', 'digital'));
```

Backfill dans la même migration :

```sql
UPDATE memberships SET lepis_format = 'paper' WHERE lepis_format IS NULL;
```

NB : pas d'utilisation de `->change()` (incompatibilité PostgreSQL 9.6 sur prod).

### Migration `create_lepis_bulletin_recipients_table`

```sql
CREATE TABLE lepis_bulletin_recipients (
    id BIGSERIAL PRIMARY KEY,
    lepis_bulletin_id BIGINT NOT NULL REFERENCES lepis_bulletins(id) ON DELETE CASCADE,
    member_id BIGINT NOT NULL REFERENCES members(id) ON DELETE CASCADE,
    membership_id BIGINT NULL REFERENCES memberships(id) ON DELETE SET NULL,
    format VARCHAR(10) NOT NULL CHECK (format IN ('paper', 'digital')),
    email_at_snapshot VARCHAR(255) NULL,
    postal_address_at_snapshot JSONB NULL,
    brevo_list_id INT NULL,
    included_at TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE (lepis_bulletin_id, member_id)
);
CREATE INDEX idx_lepis_recipients_member ON lepis_bulletin_recipients (member_id);
CREATE INDEX idx_lepis_recipients_bulletin_format ON lepis_bulletin_recipients (lepis_bulletin_id, format);
```

Choix :

- **`format` figé au snapshot** : si l'adhérent renouvelle plus tard avec un format différent, l'envoi historique reste juste.
- **`email_at_snapshot` et `postal_address_at_snapshot`** figés également : un changement d'adresse ne réécrit pas l'historique.
- **`brevo_list_id`** : posé après création de la liste Brevo par le job, permet la réconciliation "qui était dans la liste 324".
- **`UNIQUE (lepis_bulletin_id, member_id)`** : garantit l'idempotence des appels au snapshotter.
- **`postal_address_at_snapshot` en JSONB** : `{address, postal_code, city, country}`. JSONB existe depuis PG 9.4, donc compatible.

### Modèles Eloquent

`App\Models\LepisBulletinRecipient` :

```php
class LepisBulletinRecipient extends Model
{
    public const FORMAT_PAPER = 'paper';
    public const FORMAT_DIGITAL = 'digital';

    protected $fillable = [
        'lepis_bulletin_id', 'member_id', 'membership_id', 'format',
        'email_at_snapshot', 'postal_address_at_snapshot',
        'brevo_list_id', 'included_at',
    ];

    protected $casts = [
        'postal_address_at_snapshot' => 'array',
        'included_at' => 'datetime',
    ];

    public function bulletin() { return $this->belongsTo(LepisBulletin::class, 'lepis_bulletin_id'); }
    public function member() { return $this->belongsTo(Member::class); }
    public function membership() { return $this->belongsTo(Membership::class); }
}
```

Sur `Membership` : ajout des constantes `LEPIS_FORMAT_PAPER` / `LEPIS_FORMAT_DIGITAL` et accessor `lepisFormat()` retournant la valeur (avec fallback `paper` si NULL).

Sur `Member` : relation `hasMany(LepisBulletinRecipient::class)` pour la fiche contact.

Sur `LepisBulletin` : relation `hasMany(LepisBulletinRecipient::class)` + accessors `paperRecipientsCount()`, `digitalRecipientsCount()`.

## Capture de la préférence

### Canal A — HelloAsso (formulaire en ligne)

Action manuelle préalable côté HelloAsso : ajouter un **custom field obligatoire** "Format Lepis" avec deux options "Papier" / "Numérique" sur le formulaire d'adhésion.

Côté webhook (`App\Http\Controllers\Api\WebhookController::processMembership`), à la création de la `Membership` :

- Parser `data.payments[].order.items[].customFields[]` (ou la position réelle dans le payload, à confirmer sur un payload réel via `Log::info('payload', $data)` au premier déploiement).
- Mapping : `'Papier' → Membership::LEPIS_FORMAT_PAPER`, `'Numérique' → Membership::LEPIS_FORMAT_DIGITAL`.
- Si le champ est absent ou vide : fallback `Membership::LEPIS_FORMAT_PAPER` + `Log::channel('webhooks')->warning('HelloAsso: lepis_format missing, defaulting to paper', [...])`.

### Canal B — Backoffice (extranet, fiche adhésion)

Sur le formulaire de création/édition d'une `Membership` :

- Champ select **obligatoire** : "Format Lepis : Papier / Numérique".
- Validation : `'lepis_format' => ['required', Rule::in(['paper', 'digital'])]`.
- Pour les adhésions existantes legacy déjà à `paper`, le champ est éditable — un admin peut corriger après coup.

### Canal C — Espace membre

L'adhérent ne peut pas modifier sa préférence en cours d'adhésion. Sur la page profil/adhésion (`/member/profile` ou équivalent), affichage **lecture seule** :

> Format de réception du bulletin Lepis : **Papier** *(ou Numérique)*
> Pour modifier ce choix, contactez le secrétariat à `secretariat@oreina.org`.

Au renouvellement, la préférence est redécidée (canaux A ou B).

## Snapshot et filtrage Brevo

### Service `LepisBulletinRecipientSnapshotter`

Nouveau : `App\Services\LepisBulletinRecipientSnapshotter`.

```php
public function snapshot(LepisBulletin $bulletin): SnapshotResult
{
    // T = $bulletin->published_to_members_at ?? now()
    // Query: members ayant memberships.start_date <= T,
    //        memberships.end_date >= T,
    //        memberships.status = 'active'
    // Pour chaque member, sélectionne la membership la plus récente.
    // Pour chaque (member, membership) :
    //   - format = membership.lepis_format ?? 'paper'
    //   - si format=digital : si email manquant → skip + warning
    //   - si format=paper : si address ou postal_code ou city manquant → skip + warning
    //   - upsert dans lepis_bulletin_recipients (UNIQUE bulletin_id, member_id)
    //     avec format, membership_id, email_at_snapshot, postal_address_at_snapshot, included_at
    // Retourne SnapshotResult { paperCount, digitalCount, skipped: [{member_id, reason}] }
}
```

Caractéristiques :

- **Idempotent** : ré-exécutable sans doublons grâce à l'UPSERT sur la contrainte UNIQUE. Une seconde exécution met à jour les champs (utile si un admin corrige un format après le passage `members`).
- **Pure** : ne crée pas de liste Brevo ni d'export CSV, juste la table de tracking.
- **Logué** : chaque skip part dans `Log::channel('webhooks')->warning` (ou un nouveau channel `lepis`).

### Modification du job `SyncLepisBulletinToBrevoList`

Logique actuelle (à changer) : récupère tous les `Member` actifs, les pousse dans la liste Brevo.

Nouvelle logique :

1. Appelle `LepisBulletinRecipientSnapshotter::snapshot($bulletin)` en première étape.
2. Récupère uniquement les recipients `format = digital` du bulletin depuis `lepis_bulletin_recipients` (et non plus `Member` directement) → la liste Brevo et la table de tracking sont strictement alignées.
3. Crée la liste Brevo (logique existante) avec ces recipients seuls.
4. Met à jour `brevo_list_id` sur les lignes `lepis_bulletin_recipients` correspondantes.
5. Pose `bulletin.brevo_synced_at` (existant) à la fin.

Comportement de retry et `brevo_sync_failed` inchangés.

### Déclenchement

Inchangé : passage `draft → members` via `LepisBulletinPublicationService` → dispatch du job. On ajoute juste l'appel au snapshotter en première étape du job.

### Cas du repassage `members → public`

Pas de nouveau snapshot. Pas de purge de la liste Brevo (gardée pour archive).

## UI backoffice

### Fiche bulletin admin (`/admin/lepis/{id}`)

Aujourd'hui : 4 cartes (infos, PDF, cycle, annonce). Ajout d'une **5e carte "Diffusion"** (`resources/views/admin/lepis/_carte_diffusion.blade.php`) :

- Visible uniquement si `status` ∈ {`members`, `public`}.
- Affiche : nombre de destinataires papier, nombre de destinataires numériques, total, date du dernier snapshot, `brevo_list_id` si présent.
- Bouton **"Exporter destinataires papier (CSV)"** → route `admin.lepis.recipients.export` avec query param `?format=paper` qui sort un CSV : `prenom,nom,email,adresse,code_postal,ville,pays,numero_adherent`.
- Bouton **"Recalculer le snapshot"** → POST sur une route `admin.lepis.recipients.snapshot` qui ré-exécute le snapshotter, avec confirmation JS. Utile si un admin a corrigé un `lepis_format` après le passage `members`.
- Si des skips sont remontés par le snapshotter, afficher une alerte avec le détail (ex. "3 adhérents écartés faute d'email").

### Fiche contact admin

Sur la fiche admin d'un `Member` (page existante `admin.contacts.show` ou équivalent — chemin exact à confirmer côté code), ajout d'une section **"Bulletins Lepis reçus"** :

- Listing chronologique inverse, source = `member.lepisBulletinRecipients` (ordonnée par `included_at desc`).
- Une ligne par bulletin : titre + format + date d'envoi + `brevo_list_id` si numérique.
- Lien cliquable vers la fiche admin du bulletin.

### Index des adhésions

Sur la liste des adhésions backoffice : ajout d'un **filtre dropdown** "Format Lepis : tous / papier / numérique". Sert aux stats rapides ("combien d'abonnés numériques cette saison ?").

## Espace membre

Pas de nouvelle page de listing "mes bulletins". L'adhérent voit déjà tous les bulletins via `/lepis/bulletins` (gated par `LepisBulletinPolicy::download()`, déjà en place et inchangée).

Sur la page profil/adhésion membre, simple affichage en lecture seule de la préférence courante (cf. canal C ci-dessus).

## Tests

### Unitaires

`Tests\Unit\Services\LepisBulletinRecipientSnapshotterTest`

- cas nominal : 1 membership active `paper` + 1 membership active `digital` → 2 recipients, format figé
- membership expirée à T : skip
- membership active mais `lepis_format` NULL : fallback `paper`
- email manquant + format `digital` : skip + warning
- adresse manquante + format `paper` : skip + warning
- adresse partielle (postal_code seul) + format `paper` : skip + warning
- idempotence : 2 appels successifs → pas de doublon, lignes mises à jour si format ou adresse changent
- 2 memberships pour le même member : prend la plus récente

### Feature — jobs

`Tests\Feature\Jobs\SyncLepisBulletinToBrevoListTest` (mise à jour)

- liste Brevo contient uniquement les recipients `digital` (pas tous les adhérents)
- `brevo_list_id` posé sur les lignes recipients après sync
- `brevo_sync_failed` posé si Brevo échoue 3 fois (comportement existant)

### Feature — webhooks

`Tests\Feature\Webhooks\HelloAssoMembershipTest` (mise à jour ou création)

- custom field `Format Lepis = Papier` → `membership.lepis_format = 'paper'`
- custom field `Format Lepis = Numérique` → `membership.lepis_format = 'digital'`
- custom field absent → `'paper'` + warning loggé

### Feature — admin

`Tests\Feature\Admin\LepisBulletinDiffusionTest`

- carte "Diffusion" rendue uniquement si `status` ≥ `members`
- export CSV ne contient que les `format = paper` du bulletin
- "Recalculer snapshot" idempotent

`Tests\Feature\Admin\MemberLepisHistoryTest`

- section "Bulletins reçus" sur fiche contact rend les recipients du membre dans l'ordre antichronologique

## Migration de production

Ordre :

1. **Migrations schema + data** : ajout colonne `memberships.lepis_format`, backfill `paper`, création table `lepis_bulletin_recipients`.
2. **Action manuelle côté HelloAsso** : ajout du custom field "Format Lepis" sur le formulaire en ligne (à faire par David).
3. **Smoke test** : passer une adhésion test via HelloAsso, vérifier que le webhook stocke bien le format.
4. **Backfill recommandé** : commande Artisan `php artisan lepis:backfill-recipients` qui exécute le snapshotter pour chaque bulletin existant ≥ `members`. Permet de reconstruire l'historique côté fiche contact pour les bulletins déjà passés. Sans ce backfill, les fiches contact n'afficheront rien jusqu'au prochain bulletin.
5. **Communication** au rédac-chef Lepis : "à partir du prochain numéro, la liste Brevo créée automatiquement ne contient que les abonnés numériques. Le nombre apparaît sur la fiche du bulletin. L'export papier est disponible via un bouton sur la même fiche."

## Hors scope (différé)

- **Confirmation de livraison Brevo** (events `delivered`, `bounce`, `unsubscribe` reliés au bulletin) : la table `lepis_bulletin_recipients` est extensible (ajout de colonnes `delivered_at`, `bounced_at`) mais le scope actuel s'arrête au snapshot d'expédition.
- **Engagement** (ouvertures, clics).
- **Envoi automatique de la campagne Brevo via API** : le rédac-chef continue de créer la campagne manuellement.
- **Modification de la préférence par l'adhérent depuis l'espace membre** : possible plus tard si demandé, mais pose la question du re-snapshot.
- **Purge automatique des listes Brevo après passage `public`** : gardées pour archive.

## Compatibilité PostgreSQL 9.6

L'instance prod tourne en PG 9.6. Toutes les migrations utilisent `DB::statement('ALTER TABLE ...')` brut pour les modifications de colonne. Pas de `->change()`. JSONB est OK.

## Références

- Précédent design : `docs/superpowers/specs/2026-04-21-bulletin-lepis-diffusion-design.md`
- Précédent plan : `docs/superpowers/plans/2026-04-21-bulletin-lepis-diffusion.md`
- Code livré : merge `6ec5922` sur `main` le 2026-04-21
