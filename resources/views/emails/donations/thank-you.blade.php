<x-mail::message>
# Merci pour votre genereux don !

Bonjour {{ $donorName }},

Nous avons bien recu votre don de **{{ number_format($amount, 2, ',', ' ') }} EUR** effectue le {{ $donationDate->format('d/m/Y') }}.

Au nom de toute l'equipe OREINA et des lepidopteres de France, nous vous remercions chaleureusement pour votre soutien.

## Votre recu fiscal

Votre recu fiscal (numero **{{ $receiptNumber }}**) est joint a cet email. Ce document vous permettra de beneficier d'une reduction d'impot de 66% du montant de votre don, dans la limite de 20% de votre revenu imposable.

## Votre don en action

Votre contribution nous permet de :
- Poursuivre nos programmes de recherche sur les lepidopteres
- Publier la revue scientifique OREINA
- Organiser des sorties de terrain et des formations
- Sensibiliser le public a la protection des papillons

<x-mail::button :url="config('app.url')">
Decouvrir nos actions
</x-mail::button>

Merci encore pour votre confiance et votre engagement a nos cotes.

Cordialement,<br>
L'equipe OREINA
</x-mail::message>
