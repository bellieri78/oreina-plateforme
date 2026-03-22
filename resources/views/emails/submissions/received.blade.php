<x-mail::message>
# Confirmation de soumission

Bonjour {{ $authorName }},

Nous accusons réception de votre soumission à la revue **OREINA**.

## Détails de votre soumission

**Titre :** {{ $title }}

**Date de soumission :** {{ $submittedAt->format('d/m/Y à H:i') }}

## Prochaines étapes

Votre manuscrit va être examiné par notre comité éditorial. Vous serez informé(e) de la décision par email.

Le processus d'évaluation comprend :
1. **Évaluation initiale** par le comité de rédaction
2. **Peer review** par des experts du domaine
3. **Décision éditoriale** (acceptation, révision ou rejet)

<x-mail::button :url="config('app.url') . '/revue/mes-soumissions'">
Suivre ma soumission
</x-mail::button>

Si vous avez des questions, n'hésitez pas à nous contacter.

Cordialement,<br>
Le comité de rédaction d'OREINA
</x-mail::message>
