<x-mail::message>
# Corrections demandées par l'auteur

**{{ $authorName }}** a consulté la maquette de :

**« {{ $title }} »**

et a signalé les corrections suivantes avant de pouvoir approuver la publication :

<x-mail::panel>
{{ $comment }}
</x-mail::panel>

L'article est repassé en statut **en maquettage**. Merci d'intégrer les corrections puis de renvoyer la version mise à jour pour approbation.

<x-mail::button :url="$adminUrl">
Ouvrir la maquette
</x-mail::button>

Cordialement,<br>
Chersotis — notifications éditoriales
</x-mail::message>
