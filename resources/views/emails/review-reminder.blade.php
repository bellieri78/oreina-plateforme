<x-mail::message>
# Rappel d'evaluation

Bonjour {{ $review->reviewer->name ?? 'Cher reviewer' }},

Nous vous rappelons qu'une evaluation vous a ete assignee pour le manuscrit suivant :

**Titre :** {{ $submission->title ?? 'Non specifie' }}

@if($dueDate)
**Date limite :** {{ $dueDate }}
@if($isOverdue)

<x-mail::panel>
**Attention :** Cette evaluation est en retard. Merci de la completer dans les plus brefs delais.
</x-mail::panel>
@endif
@endif

Nous comptons sur votre expertise pour evaluer ce manuscrit et nous vous remercions pour votre contribution a la revue OREINA.

<x-mail::button :url="config('app.url') . '/extranet/reviews/' . $review->id">
Voir l'evaluation
</x-mail::button>

Cordialement,<br>
L'equipe editoriale OREINA

<x-mail::subcopy>
Si vous ne pouvez pas completer cette evaluation, merci de nous en informer rapidement afin que nous puissions assigner un autre reviewer.
</x-mail::subcopy>
</x-mail::message>
