<x-mail::message>
# Nouvel article transmis au bulletin Lepis

Après examen par le comité éditorial Chersotis, l'article suivant vous est transmis pour évaluation en vue d'une publication dans le bulletin **Lepis**.

**Titre :** {{ $submission->title }}

**Auteur :** {{ $author->name }}
**Email auteur :** [{{ $author->email }}](mailto:{{ $author->email }})

@if($chersotisNotes)
**Motifs éditoriaux (Chersotis) :**
> {{ $chersotisNotes }}
@endif

<x-mail::button :url="config('app.url').'/extranet/submissions/'.$submission->id">
Consulter la fiche et le manuscrit
</x-mail::button>

## Action attendue

Il vous revient de **contacter directement l'auteur** (voir adresse ci-dessus) pour :
- Lui proposer une publication dans Lepis sous une forme adaptée au format du bulletin ;
- Ou lui indiquer que l'article ne convient finalement pas à Lepis non plus.

Ce transfert se fait hors plateforme. L'auteur a déjà été informé par un message lui indiquant que son article a été redirigé vers Lepis et qu'il sera contacté prochainement.

Cordialement,
L'équipe éditoriale **Chersotis**
{{ config('app.url') }}
</x-mail::message>
