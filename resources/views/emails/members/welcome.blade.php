<x-mail::message>
# Bienvenue chez OREINA !

Bonjour {{ $memberName }},

Nous avons le plaisir de vous confirmer votre adhésion à **OREINA - Les Lépidoptères de France**.

## Votre numéro d'adhérent
**{{ $memberNumber }}**

Votre adhésion est valide jusqu'au **{{ $expiresAt->format('d/m/Y') }}**.

## Vos avantages

En tant que membre, vous bénéficiez de :
- L'accès à la revue scientifique OREINA
- La participation aux sorties de terrain
- Les actualités et événements de l'association
- L'accès aux bases de données (BDC, Artemisiae)

<x-mail::button :url="config('app.url')">
Accéder à mon espace
</x-mail::button>

## Créer votre compte

Si vous n'avez pas encore de compte sur notre plateforme, vous pouvez en créer un en utilisant l'adresse email associée à votre adhésion.

Merci de votre soutien pour la connaissance et la conservation des Lépidoptères de France !

Cordialement,<br>
L'équipe OREINA
</x-mail::message>
