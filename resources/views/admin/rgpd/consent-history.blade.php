@extends('layouts.admin')

@section('title', 'RGPD - Historique des consentements')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Historique RGPD</h1>
            <p class="text-gray-600">{{ $member->full_name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.rgpd.export-member-data', $member) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter les donnees
            </a>
            <a href="{{ route('admin.members.show', $member) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Current Consents -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Consentements actuels</h3>

            <form action="{{ route('admin.rgpd.update-consents', $member) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">Newsletter</p>
                            <p class="text-xs text-gray-500">Recevoir les actualites</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="newsletter_subscribed" value="0">
                            <input type="checkbox" name="newsletter_subscribed" value="1"
                                   {{ $member->newsletter_subscribed ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-oreina-green/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-oreina-green"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">Communication</p>
                            <p class="text-xs text-gray-500">Recevoir des sollicitations</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="consent_communication" value="0">
                            <input type="checkbox" name="consent_communication" value="1"
                                   {{ $member->consent_communication ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-oreina-green/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-oreina-green"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">Droit a l'image</p>
                            <p class="text-xs text-gray-500">Utilisation des photos</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="consent_image" value="0">
                            <input type="checkbox" name="consent_image" value="1"
                                   {{ $member->consent_image ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-oreina-green/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-oreina-green"></div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <input type="text" name="notes" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Raison du changement...">
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-oreina-green text-white rounded-lg hover:bg-oreina-dark">
                        Mettre a jour
                    </button>
                </div>
            </form>

            @if(!$member->anonymise)
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <button type="button"
                            onclick="confirmAnonymize()"
                            class="w-full px-4 py-2 bg-red-50 text-red-700 border border-red-300 rounded-lg hover:bg-red-100">
                        Anonymiser ce contact
                    </button>
                </div>
            @else
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="p-3 bg-red-50 rounded-lg text-center">
                        <p class="text-sm font-medium text-red-700">Contact anonymise</p>
                        <p class="text-xs text-red-600">{{ $member->date_anonymisation?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations RGPD</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500">Derniere mise a jour</dt>
                    <dd class="font-medium">{{ $member->updated_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Derniere interaction</dt>
                    <dd class="font-medium">
                        {{ $member->last_interaction_at ? $member->last_interaction_at->format('d/m/Y') : 'Jamais' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Derniere revue RGPD</dt>
                    <dd class="font-medium">
                        {{ $member->rgpd_reviewed_at ? $member->rgpd_reviewed_at->format('d/m/Y') : 'Jamais' }}
                    </dd>
                </div>
                @if($member->rgpd_review_notes)
                    <div>
                        <dt class="text-gray-500">Notes de revue</dt>
                        <dd class="text-gray-700">{{ $member->rgpd_review_notes }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- History -->
    <div class="lg:col-span-2">
        <!-- Consent History -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Historique des consentements</h3>
            </div>

            @if($history->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    Aucun changement de consentement enregistre
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valeur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($history as $entry)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $entry->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $typeLabels = [
                                                'newsletter' => 'Newsletter',
                                                'communication' => 'Communication',
                                                'image' => 'Droit a l\'image',
                                            ];
                                        @endphp
                                        {{ $typeLabels[$entry->consent_type] ?? $entry->consent_type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($entry->value)
                                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Accepte</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Refuse</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @php
                                            $sourceLabels = [
                                                'manual' => 'Saisie manuelle',
                                                'import' => 'Import',
                                                'form' => 'Formulaire',
                                                'api' => 'API',
                                            ];
                                        @endphp
                                        {{ $sourceLabels[$entry->source] ?? $entry->source }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $entry->user?->name ?? 'Systeme' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Review History -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Historique des revues RGPD</h3>
            </div>

            @if($reviews->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    Aucune revue RGPD enregistree
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type d'alerte</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reviews as $review)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $review->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $alertLabels = [
                                                'no_interaction' => 'Sans interaction',
                                                'not_updated' => 'Non mis a jour',
                                                'expired_membership' => 'Adhesion expiree',
                                                'inactive_donor' => 'Donateur inactif',
                                                'manual' => 'Manuel',
                                            ];
                                        @endphp
                                        {{ $alertLabels[$review->alert_type] ?? $review->alert_type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $actionClasses = [
                                                'keep' => 'bg-green-100 text-green-800',
                                                'update' => 'bg-blue-100 text-blue-800',
                                                'contact' => 'bg-yellow-100 text-yellow-800',
                                                'anonymize' => 'bg-red-100 text-red-800',
                                            ];
                                            $actionLabels = [
                                                'keep' => 'Conserver',
                                                'update' => 'Mettre a jour',
                                                'contact' => 'Contacter',
                                                'anonymize' => 'Anonymiser',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded {{ $actionClasses[$review->action] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $actionLabels[$review->action] ?? $review->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $review->notes }}">
                                        {{ $review->notes ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $review->user?->name ?? 'Systeme' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Anonymize Confirmation Modal -->
@if(!$member->anonymise)
<div id="anonymizeModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <form action="{{ route('admin.rgpd.anonymize', $member) }}" method="POST">
                @csrf
                <div class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-full bg-red-100">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Anonymiser ce contact</h3>
                            <p class="text-sm text-gray-500">
                                Cette action va supprimer toutes les donnees personnelles de {{ $member->full_name }}.
                                Cette action est irreversible.
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Raison de l'anonymisation</label>
                        <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300" placeholder="Optionnel..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
                    <button type="button" onclick="closeAnonymizeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Anonymiser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmAnonymize() {
        document.getElementById('anonymizeModal').classList.remove('hidden');
    }

    function closeAnonymizeModal() {
        document.getElementById('anonymizeModal').classList.add('hidden');
    }

    document.getElementById('anonymizeModal').addEventListener('click', function(e) {
        if (e.target === this) closeAnonymizeModal();
    });
</script>
@endpush
@endif
@endsection
