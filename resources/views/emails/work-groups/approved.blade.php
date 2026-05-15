<x-mail::message>
# Demande acceptée

Votre demande pour rejoindre le groupe de travail **{{ $workGroup->name }}** a été acceptée. Bienvenue !

<x-mail::button :url="$groupUrl">
Accéder au groupe
</x-mail::button>

Merci,<br>
L'équipe OREINA
</x-mail::message>
