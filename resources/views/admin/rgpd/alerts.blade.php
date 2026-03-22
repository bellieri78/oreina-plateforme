@extends('layouts.admin')

@section('title', 'RGPD - Alertes')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Alertes RGPD</h1>
            <p class="text-gray-600">Contacts necessitant une revue des donnees</p>
        </div>
        <a href="{{ route('admin.rgpd.index') }}"
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
@endif

<!-- Alert Type Tabs -->
<div class="mb-6">
    <nav class="flex space-x-4" aria-label="Tabs">
        @foreach($alertTypes as $type => $label)
            <a href="{{ route('admin.rgpd.alerts', ['type' => $type]) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg {{ $alertType === $type ? 'bg-oreina-green text-white' : 'text-gray-500 hover:text-gray-700 bg-white border border-gray-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>
</div>

<!-- Bulk Actions Form -->
<form id="bulkForm" action="{{ route('admin.rgpd.bulk-process') }}" method="POST">
    @csrf
    <input type="hidden" name="alert_type" value="{{ $alertType }}">

    <!-- Bulk Action Bar -->
    <div id="bulkActionBar" class="hidden mb-4 bg-oreina-light/20 border border-oreina-green rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-oreina-dark">
                    <span id="selectedCount">0</span> contact(s) selectionne(s)
                </span>
                <select name="action" class="rounded-lg border-gray-300 text-sm" required>
                    <option value="">-- Action --</option>
                    @foreach($actions as $actionKey => $actionLabel)
                        <option value="{{ $actionKey }}">{{ $actionLabel }}</option>
                    @endforeach
                </select>
                <input type="text" name="notes" placeholder="Notes (optionnel)" class="rounded-lg border-gray-300 text-sm w-64">
            </div>
            <button type="submit" class="px-4 py-2 bg-oreina-green text-white rounded-lg hover:bg-oreina-dark text-sm">
                Appliquer
            </button>
        </div>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($members->isEmpty())
            <div class="p-6 text-center text-gray-500">
                Aucun contact ne correspond a ce critere
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-oreina-green focus:ring-oreina-green">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Derniere MAJ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Derniere interaction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Infos</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($members as $member)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                                           class="memberCheckbox rounded border-gray-300 text-oreina-green focus:ring-oreina-green">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.members.show', $member) }}" class="text-oreina-green hover:underline font-medium">
                                        {{ $member->full_name }}
                                    </a>
                                    <p class="text-xs text-gray-500">{{ $member->contact_type }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $member->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $member->updated_at->format('d/m/Y') }}
                                    <p class="text-xs text-gray-400">{{ $member->updated_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($member->last_interaction_at)
                                        {{ $member->last_interaction_at->format('d/m/Y') }}
                                        <p class="text-xs text-gray-400">{{ $member->last_interaction_at->diffForHumans() }}</p>
                                    @else
                                        <span class="text-gray-400">Jamais</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($alertType === 'expired_membership' && $member->membership_expires_at)
                                        <span class="text-red-600">Expire: {{ $member->membership_expires_at->format('d/m/Y') }}</span>
                                    @elseif($alertType === 'inactive_donor')
                                        @php
                                            $lastDonation = $member->donations->sortByDesc('donation_date')->first();
                                        @endphp
                                        @if($lastDonation)
                                            <span class="text-purple-600">Dernier don: {{ $lastDonation->donation_date->format('d/m/Y') }}</span>
                                        @endif
                                    @else
                                        @if($member->rgpd_reviewed_at)
                                            <span class="text-green-600">Revu: {{ $member->rgpd_reviewed_at->format('d/m/Y') }}</span>
                                        @else
                                            <span class="text-gray-400">Jamais revu</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('admin.rgpd.consent-history', $member) }}"
                                           class="text-blue-600 hover:text-blue-800" title="Historique">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </a>
                                        <button type="button" onclick="openActionModal({{ $member->id }}, '{{ $member->full_name }}')"
                                                class="text-oreina-green hover:text-oreina-dark" title="Traiter">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $members->appends(['type' => $alertType])->links() }}
            </div>
        @endif
    </div>
</form>

<!-- Individual Action Modal -->
<div id="actionModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <form id="individualActionForm" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Traiter le contact</h3>
                    <p id="memberName" class="text-sm text-gray-500"></p>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <input type="hidden" name="alert_type" value="{{ $alertType }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                        <select name="action" class="w-full rounded-lg border-gray-300" required>
                            <option value="">-- Choisir --</option>
                            @foreach($actions as $actionKey => $actionLabel)
                                <option value="{{ $actionKey }}">{{ $actionLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300" placeholder="Notes optionnelles..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prochaine revue</label>
                        <input type="date" name="next_review_date" class="w-full rounded-lg border-gray-300" min="{{ now()->addDay()->format('Y-m-d') }}">
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeActionModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-oreina-green text-white rounded-lg hover:bg-oreina-dark">
                        Valider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.memberCheckbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActionBar();
    });

    // Individual checkboxes
    document.querySelectorAll('.memberCheckbox').forEach(cb => {
        cb.addEventListener('change', updateBulkActionBar);
    });

    function updateBulkActionBar() {
        const checked = document.querySelectorAll('.memberCheckbox:checked').length;
        const bar = document.getElementById('bulkActionBar');
        document.getElementById('selectedCount').textContent = checked;
        bar.classList.toggle('hidden', checked === 0);
    }

    // Modal functions
    function openActionModal(memberId, memberName) {
        document.getElementById('memberName').textContent = memberName;
        document.getElementById('individualActionForm').action = '{{ route("admin.rgpd.process", "") }}/' + memberId;
        document.getElementById('actionModal').classList.remove('hidden');
    }

    function closeActionModal() {
        document.getElementById('actionModal').classList.add('hidden');
    }

    // Close modal on outside click
    document.getElementById('actionModal').addEventListener('click', function(e) {
        if (e.target === this) closeActionModal();
    });
</script>
@endpush
@endsection
