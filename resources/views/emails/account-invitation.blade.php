<x-mail::message>
# Bonjour {{ $author->name }},

**{{ $invitedBy->name }}** a déposé sur la revue **Chersotis** une soumission en votre nom :

> *{{ $submission->title }}*

Afin de pouvoir suivre le processus éditorial et échanger avec l'équipe, un compte a été créé pour vous sur la plateforme. Il vous suffit de définir votre mot de passe pour l'activer.

<x-mail::button :url="$claimUrl" color="success">
Activer mon compte
</x-mail::button>

Ce lien est valide jusqu'au **{{ $expirationDate }}**. Passé cette date, contactez [chersotis-revue@oreina.org](mailto:chersotis-revue@oreina.org) pour obtenir un nouveau lien.

---

## Processus éditorial de Chersotis

Votre soumission va suivre ces étapes :

1. **Accusé de réception** — l'équipe a bien reçu votre manuscrit
2. **Évaluation initiale** — un éditeur vérifie l'adéquation au périmètre de la revue
3. **Relecture par les pairs** — deux relecteurs spécialisés examinent le manuscrit
4. **Décision** — l'éditeur synthétise les retours et vous transmet la décision
5. **Révisions éventuelles** — vous pouvez être invité à amender le manuscrit
6. **Maquettage** — la revue prépare la mise en page
7. **Approbation finale** — vous validez la version maquettée avant publication

Vous pourrez suivre l'avancement depuis votre espace auteur.

---

Cordialement,
L'équipe éditoriale **Chersotis**
{{ config('app.url') }}
</x-mail::message>
