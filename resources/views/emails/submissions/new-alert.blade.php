<x-mail::message>
# Nouvelle soumission reçue

Un nouveau manuscrit a été soumis à la revue {{ config('journal.name') }}.

**Titre :** {{ $submission->title }}

**Auteur :** {{ $authorName }}

@if($submission->abstract)
**Résumé :**
{{ Str::limit($submission->abstract, 300) }}
@endif

**Soumis le :** {{ $submission->submitted_at?->format('d/m/Y à H:i') }}

<x-mail::button :url="$queueUrl">
Voir la file d'attente
</x-mail::button>

Cordialement,<br>
{{ config('journal.name') }}
</x-mail::message>
