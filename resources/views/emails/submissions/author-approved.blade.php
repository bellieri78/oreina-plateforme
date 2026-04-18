<x-mail::message>
# Approbation auteur reçue

**{{ $authorName }}** a donné son accord pour la publication de :

**« {{ $title }} »**

L'article peut maintenant être publié.

<x-mail::button :url="$adminUrl">
Ouvrir dans le backoffice
</x-mail::button>

Cordialement,<br>
Chersotis — notifications éditoriales
</x-mail::message>
