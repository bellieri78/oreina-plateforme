<x-mail::message>
# Confirmation de soumission

Bonjour {{ $authorName }},

Nous accusons réception de votre soumission à la revue **Chersotis** (Oreina).

## Détails de votre soumission

**Titre :** {{ $title }}

**Date de soumission :** {{ $submittedAt->format('d/m/Y à H:i') }}

## Processus éditorial

Votre manuscrit va être examiné. Voici les étapes à venir (délais indicatifs) :

1. **Évaluation initiale** par le comité de rédaction (1-2 semaines)
2. **Assignation d'un éditeur** qui suivra votre article
3. **Invitation des relecteurs** (1 semaine pour accepter)
4. **Peer review** (3 semaines pour rendre la relecture)
5. **Décision éditoriale** (acceptation, révision demandée, ou rejet)
6. **Maquettage** par l'équipe de production
7. **Approbation finale** par vous avant publication

À chaque étape importante, vous recevrez un email. Vous pouvez suivre l'avancement de votre soumission à tout moment sur votre tableau de bord.

<x-mail::button :url="config('app.url') . '/revue/mes-soumissions'">
Suivre ma soumission
</x-mail::button>

Pour toute question, vous pouvez nous écrire à {{ config('journal.contact_email') }}.

Cordialement,<br>
Le comité de rédaction de Chersotis
</x-mail::message>
