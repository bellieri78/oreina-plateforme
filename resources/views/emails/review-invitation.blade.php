<x-mail::message>
# Invitation a evaluer un manuscrit

Bonjour {{ $review->reviewer->name ?? 'Cher collegue' }},

Vous avez ete invite(e) a evaluer un manuscrit soumis a la revue OREINA.

**Titre du manuscrit :** {{ $submission->title ?? 'Non specifie' }}

@if($submission->abstract)
**Resume :**
{{ Str::limit($submission->abstract, 300) }}
@endif

@if($dueDate)
**Date limite d'evaluation :** {{ $dueDate }}
@endif

@if($assignedBy)
**Assigne par :** {{ $assignedBy->name }}
@endif

Nous vous invitons a accepter ou decliner cette invitation dans les meilleurs delais.

<x-mail::button :url="$respondUrl">
Répondre à l'invitation
</x-mail::button>

Nous vous remercions pour votre contribution a la qualite scientifique de la revue OREINA.

Cordialement,<br>
L'equipe editoriale OREINA

<x-mail::subcopy>
Si vous n'etes pas en mesure d'evaluer ce manuscrit, merci de decliner l'invitation afin que nous puissions solliciter un autre reviewer.
</x-mail::subcopy>
</x-mail::message>
