<x-mail::message>
# Nouvelle réponse

**{{ $author?->full_name ?? $author?->first_name ?? 'Un membre' }}** a répondu dans le fil **{{ $thread->title }}**.

> {{ \Illuminate\Support\Str::limit($post->content, 300) }}

<x-mail::button :url="$url">
Voir la discussion
</x-mail::button>

Vous recevez ce message car vous suivez ce fil de discussion.

Merci,<br>
L'équipe OREINA
</x-mail::message>
