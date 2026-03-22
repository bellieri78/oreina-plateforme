<x-mail::message>
# Décision éditoriale

Bonjour {{ $authorName }},

Nous vous informons de la décision concernant votre soumission à la revue **OREINA**.

## Votre article

**Titre :** {{ $title }}

## Décision

@if($decision === 'accept')
<x-mail::panel>
**Félicitations ! Votre article a été accepté pour publication.**
</x-mail::panel>

Votre manuscrit sera publié dans un prochain numéro de la revue. Vous serez contacté(e) pour les dernières étapes de mise en page.

@elseif($decision === 'minor_revision')
<x-mail::panel>
**Révision mineure demandée**
</x-mail::panel>

Votre article a été évalué favorablement mais nécessite quelques modifications mineures avant d'être accepté. Veuillez prendre en compte les commentaires ci-dessous et soumettre une version révisée.

@elseif($decision === 'major_revision')
<x-mail::panel>
**Révision majeure demandée**
</x-mail::panel>

Les évaluateurs ont identifié des points importants à retravailler. Nous vous invitons à prendre en compte leurs commentaires et à soumettre une version substantiellement révisée de votre manuscrit.

@else
<x-mail::panel>
**Nous sommes au regret de vous informer que votre article n'a pas été retenu pour publication.**
</x-mail::panel>

Nous vous encourageons à prendre en compte les commentaires des évaluateurs et à soumettre de futures contributions.

@endif

@if($editorNotes)
## Commentaires de l'éditeur

{{ $editorNotes }}
@endif

@if(in_array($decision, ['minor_revision', 'major_revision']))
<x-mail::button :url="config('app.url') . '/revue/mes-soumissions'">
Soumettre ma révision
</x-mail::button>
@endif

Si vous avez des questions concernant cette décision, n'hésitez pas à nous contacter.

Cordialement,<br>
Le comité de rédaction d'OREINA
</x-mail::message>
