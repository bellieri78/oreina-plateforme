@component('mail::message')
# Votre carte d'adherent OREINA

Bonjour {{ $member->first_name ?? $member->full_name }},

Veuillez trouver ci-joint votre carte d'adherent OREINA.

Cette carte atteste de votre adhesion a l'association OREINA - Les Lepidopteres de France.

**Informations de votre adhesion :**
- Numero de membre : {{ $member->member_number ?? 'Non attribue' }}
- Nom : {{ $member->full_name }}

Conservez cette carte precieusement. Elle vous permettra de justifier votre qualite de membre aupres de nos partenaires.

Merci pour votre soutien !

Cordialement,

L'equipe OREINA
@endcomponent
