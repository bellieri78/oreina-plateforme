<x-mail::message>
# Évaluation déposée

**{{ $reviewerName }}** a terminé son évaluation du manuscrit :

**« {{ $submissionTitle }} »**

**Recommandation :** {{ $recommendation }}

<x-mail::button :url="$showUrl">
Voir la soumission
</x-mail::button>

Cordialement,<br>
{{ config('journal.name') }}
</x-mail::message>
