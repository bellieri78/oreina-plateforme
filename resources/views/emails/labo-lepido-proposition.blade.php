<x-mail::message>
# Nouvelle proposition Labo Lépido

**De :** {{ $data['nom'] }} ({{ $data['email'] }})
**Type :** @if($data['type_proposition'] === 'animer') Propose d'animer un Labo Lépido @else Suggère un sujet à traiter @endif

---

**Complexe ou agrégat concerné**

{{ $data['sujet'] }}

**Motivation et contexte**

{{ $data['motivation'] }}

@if(!empty($data['ressources']))
**Ressources disponibles**

{{ $data['ressources'] }}
@endif

---

<x-mail::button :url="'mailto:' . $data['email']">
Répondre directement
</x-mail::button>

<small>Message envoyé depuis le formulaire <a href="{{ route('hub.outils.labo-lepidos') }}#proposer">Labo Lépidos</a> du site oreina.org. Vous pouvez répondre directement à ce mail (Reply-To : {{ $data['email'] }}).</small>
</x-mail::message>
