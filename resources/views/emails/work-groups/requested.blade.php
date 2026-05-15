<x-mail::message>
# Nouvelle demande d'adhésion

**{{ $applicant->full_name ?? $applicant->first_name }}** souhaite rejoindre le groupe de travail **{{ $workGroup->name }}**.

<x-mail::button :url="$manageUrl">
Gérer les demandes
</x-mail::button>

Vous recevez ce message en tant que coordinateur de ce groupe.

Merci,<br>
L'équipe OREINA
</x-mail::message>
