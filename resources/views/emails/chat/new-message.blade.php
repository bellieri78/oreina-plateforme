<x-mail::message>
# Nouveau message

**{{ $sender->first_name ?? 'Un adhérent' }} {{ $sender->last_name }}** vous a envoyé un message privé sur OREINA.

Pour le lire et y répondre, connectez-vous à votre espace membre :

<x-mail::button :url="$url">
Voir le message
</x-mail::button>

Vous recevez cet email car un adhérent vous a écrit via l'annuaire. Vous pouvez bloquer un adhérent depuis la conversation.

Merci,<br>
L'équipe OREINA
</x-mail::message>
