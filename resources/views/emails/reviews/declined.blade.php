<x-mail::message>
# Invitation de relecture déclinée

**{{ $reviewerName }}** a décliné l'invitation de relecture pour le manuscrit :

**« {{ $submissionTitle }} »**

Vous pouvez inviter un autre relecteur depuis la fiche de la soumission.

Cordialement,<br>
{{ config('journal.name') }}
</x-mail::message>
