<x-mail::message>
# Invitation de relecture acceptée

**{{ $reviewerName }}** a accepté de relire le manuscrit :

**« {{ $submissionTitle }} »**

@if($dueDate)
Date limite de relecture : **{{ $dueDate }}**
@endif

Vous serez notifié(e) lorsque l'évaluation sera déposée.

Cordialement,<br>
{{ config('journal.name') }}
</x-mail::message>
