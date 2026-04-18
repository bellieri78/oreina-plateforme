<x-mail::message>
# Bonjour {{ $author->name }},

Après examen attentif, votre manuscrit

> *{{ $submission->title }}*

a été jugé mieux adapté au bulletin **Lepis**, publication interne d'OREINA dédiée aux notes courtes, observations de terrain et vulgarisation.

Le rédacteur en chef de Lepis prendra directement contact avec vous dans les prochains jours pour vous proposer la suite : publication dans Lepis sous une forme adaptée, ou éventuels ajustements avant publication.

<x-mail::button :url="config('app.url').'/revue/mes-soumissions'">
Voir mes soumissions
</x-mail::button>

Merci de votre contribution à OREINA.

Cordialement,
L'équipe éditoriale **Chersotis**
{{ config('app.url') }}
</x-mail::message>
