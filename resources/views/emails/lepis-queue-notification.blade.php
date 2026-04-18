<x-mail::message>
# Nouvelle soumission en file Lepis

Une soumission vient d'être proposée pour redirection vers le bulletin **Lepis**. Elle est actuellement en attente de décision (transmission ou rejet définitif).

**Titre :** {{ $submission->title }}
**Auteur :** {{ $author->name }} ({{ $author->email }})
@if($actor)
**Proposé par :** {{ $actor->name }}
@endif

@if($notes)
**Motifs / commentaires :**
> {{ $notes }}
@endif

<x-mail::button :url="config('app.url').'/extranet/revue/file-lepis'">
Voir la file Lepis
</x-mail::button>

**Action attendue :** transmettre à Lepis (l'auteur recevra un message l'informant du transfert) ou rejeter définitivement (l'auteur recevra le mail de rejet avec motifs).

Cordialement,
Plateforme OREINA
</x-mail::message>
