<x-mail::message>
# Votre article est prêt pour publication

Bonjour {{ $authorName }},

L'équipe éditoriale de **Chersotis** a finalisé la maquette de votre article :

**« {{ $title }} »**

Avant publication définitive, nous avons besoin de votre **approbation formelle** sur la version maquettée. Vous pouvez aussi signaler des corrections si nécessaire.

<x-mail::button :url="$showUrl">
Consulter la maquette et donner mon accord
</x-mail::button>

Sur la page de suivi, vous trouverez :
- Un lien vers le PDF maquetté
- Un bouton « Approuver pour publication »
- Un bouton « Signaler des corrections » (avec zone de commentaire)

Si vous demandez des corrections, l'équipe maquette les intègre puis vous renvoie une nouvelle version pour approbation (plusieurs allers-retours sont possibles).

Cordialement,<br>
Le comité de rédaction de Chersotis
</x-mail::message>
