@extends('layouts.admin')

@section('title', $member->full_name)
@section('breadcrumb')
    <a href="{{ route('admin.members.index') }}">Contacts</a>
    <span>/</span>
    <span>{{ $member->full_name }}</span>
@endsection

@section('content')
    @php
        $adhesionsCount = $member->memberships->count();
        $donsTotal = $member->donations->sum('amount');
        $achatsCount = $member->purchases->count();
        $bulletinsCount = $member->lepisBulletinRecipients->count();
        $groupesCount = $member->workGroups->count();
        $publicationsCount = $member->user?->submissions->count() ?? 0;
        $suggestionsCount = $member->lepisSuggestions->count();
        $currentMembership = $member->memberships->where('end_date', '>=', now())->sortByDesc('end_date')->first();
        $lepisFormat = $currentMembership?->lepis_format ?: ($currentMembership ? 'paper' : null);

        $formatDonsTotal = function ($v) {
            if ($v >= 1000) return number_format($v / 1000, 1, ',', ' ') . 'k €';
            return number_format($v, 0, ',', ' ') . ' €';
        };
    @endphp

    {{-- KPI BAR : visible seulement si au moins un compteur > 0 --}}
    @if($adhesionsCount + $donsTotal + $achatsCount + $bulletinsCount + $groupesCount + $publicationsCount + $suggestionsCount > 0)
        <div class="dashboard-stats" style="margin-bottom: 1.5rem;">
            @if($adhesionsCount > 0)
                <a href="#adhesions" class="dashboard-stat-card dashboard-stat-purple" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="id-card" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $adhesionsCount }}</span>
                        <span class="dashboard-stat-label">Adhésions</span>
                    </div>
                </a>
            @endif
            @if($donsTotal > 0)
                <a href="#dons" class="dashboard-stat-card dashboard-stat-green" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="circle-dollar-sign" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $formatDonsTotal($donsTotal) }}</span>
                        <span class="dashboard-stat-label">Dons cumulés</span>
                    </div>
                </a>
            @endif
            @if($achatsCount > 0)
                <a href="#achats" class="dashboard-stat-card dashboard-stat-orange" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="shopping-cart" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $achatsCount }}</span>
                        <span class="dashboard-stat-label">Achats</span>
                    </div>
                </a>
            @endif
            @if($bulletinsCount > 0)
                <a href="#bulletins" class="dashboard-stat-card dashboard-stat-blue" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="mail" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $bulletinsCount }}</span>
                        <span class="dashboard-stat-label">Bulletins reçus</span>
                    </div>
                </a>
            @endif
            @if($groupesCount > 0)
                <a href="#engagement" class="dashboard-stat-card dashboard-stat-purple" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="users-round" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $groupesCount }}</span>
                        <span class="dashboard-stat-label">Groupes</span>
                    </div>
                </a>
            @endif
            @if($publicationsCount > 0)
                <a href="#engagement" class="dashboard-stat-card dashboard-stat-blue" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="book-open" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $publicationsCount }}</span>
                        <span class="dashboard-stat-label">Publications Chersotis</span>
                    </div>
                </a>
            @endif
            @if($suggestionsCount > 0)
                <a href="#engagement" class="dashboard-stat-card dashboard-stat-orange" style="text-decoration: none;">
                    <div class="dashboard-stat-icon"><i data-lucide="lightbulb" style="width:28px;height:28px;"></i></div>
                    <div class="dashboard-stat-content">
                        <span class="dashboard-stat-value">{{ $suggestionsCount }}</span>
                        <span class="dashboard-stat-label">Suggestions Lepis</span>
                    </div>
                </a>
            @endif
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        <!-- Info Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informations</h3>
                <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-secondary" style="padding: 0.35rem 0.75rem;">
                    Modifier
                </a>
            </div>
            <div class="card-body">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background-color: #356B8A; color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 600; margin: 0 auto;">
                        {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                    </div>
                    <h2 style="margin-top: 1rem; font-size: 1.25rem; font-weight: 600;">{{ $member->full_name }}</h2>
                    @if($member->is_active)
                        <span class="badge badge-success">Actif</span>
                    @else
                        <span class="badge badge-danger">Inactif</span>
                    @endif
                </div>

                <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Email</div>
                        <div>{{ $member->email }}</div>
                    </div>
                    @if($member->mobile)
                        <div style="margin-bottom: 1rem;">
                            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Mobile</div>
                            <div>{{ $member->mobile }}</div>
                        </div>
                    @endif
                    @if($member->telephone_fixe)
                        <div style="margin-bottom: 1rem;">
                            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Téléphone fixe</div>
                            <div>{{ $member->telephone_fixe }}</div>
                        </div>
                    @endif
                    @if($member->address || $member->city)
                        <div style="margin-bottom: 1rem;">
                            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Adresse</div>
                            <div>
                                @if($member->address){{ $member->address }}<br>@endif
                                {{ $member->postal_code }} {{ $member->city }}
                                @if($member->country && $member->country !== 'France')<br>{{ $member->country }}@endif
                            </div>
                        </div>
                    @endif
                    <div>
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Inscrit le</div>
                        <div>{{ $member->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>

                {{-- NOUVEAU : Format Lepis --}}
                @if($lepisFormat)
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 1rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Format Lepis</div>
                        <div>{{ $lepisFormat === 'digital' ? 'Numérique' : 'Papier' }}</div>
                    </div>
                @endif

                {{-- NOUVEAU : Groupes --}}
                @if($member->workGroups->isNotEmpty())
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 1rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Groupes ({{ $member->workGroups->count() }})</div>
                        @foreach($member->workGroups->take(5) as $group)
                            <div style="margin-bottom: 0.25rem; font-size: 0.875rem;">
                                · {{ $group->name }}
                                @if(($group->pivot->role ?? 'member') !== 'member')
                                    <span style="color: #6b7280; font-style: italic;">— {{ $group->pivot->role }}</span>
                                @endif
                            </div>
                        @endforeach
                        @if($member->workGroups->count() > 5)
                            <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem;">+ {{ $member->workGroups->count() - 5 }} autres</div>
                        @endif
                    </div>
                @endif

                {{-- NOUVEAU : Engagement (auteur Chersotis + contributeur Lepis) --}}
                @php
                    $publishedSubmissionsCount = $member->user?->submissions->where('status', \App\Enums\SubmissionStatus::Published)->count() ?? 0;
                    $draftSubmissionsCount = ($publicationsCount > 0) ? $publicationsCount - $publishedSubmissionsCount : 0;
                @endphp
                @if($publicationsCount > 0 || $suggestionsCount > 0)
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 1rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Engagement</div>
                        @if($publicationsCount > 0)
                            <div style="margin-bottom: 0.5rem; font-size: 0.875rem;">
                                <strong>Auteur Chersotis</strong>
                                <div style="color: #6b7280; font-size: 0.8125rem;">
                                    {{ $publishedSubmissionsCount }} publi{{ $publishedSubmissionsCount > 1 ? 's' : '' }}
                                    @if($draftSubmissionsCount > 0)
                                        · {{ $draftSubmissionsCount }} en cours
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if($suggestionsCount > 0)
                            <div style="margin-bottom: 0.5rem; font-size: 0.875rem;">
                                <strong>Contributeur Lepis</strong>
                                <div style="color: #6b7280; font-size: 0.8125rem;">
                                    {{ $suggestionsCount }} suggestion{{ $suggestionsCount > 1 ? 's' : '' }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Tabs Content -->
        <div>
            {{-- Adhésions --}}
            @php $adhesions = $member->memberships->sortByDesc('start_date')->values(); @endphp
            <div class="card" id="adhesions" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Adhésions ({{ $adhesions->count() }})</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <table class="table">
                        <thead><tr><th>Type</th><th>Période</th><th>Montant</th><th>Statut</th></tr></thead>
                        <tbody>
                            @forelse($adhesions->take(5) as $membership)
                                <tr>
                                    <td>{{ $membership->membershipType->name ?? 'Standard' }}</td>
                                    <td>{{ $membership->start_date->format('d/m/Y') }} - {{ $membership->end_date->format('d/m/Y') }}</td>
                                    <td>{{ number_format($membership->amount_paid, 2, ',', ' ') }} EUR</td>
                                    <td>
                                        @if($membership->end_date >= now())
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-warning">Expirée</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align: center; color: #9ca3af;">Aucune adhésion</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($adhesions->count() > 5)
                        <details style="border-top: 1px solid #e5e7eb;">
                            <summary style="padding: 0.75rem 1rem; cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $adhesions->count() }})</summary>
                            <table class="table">
                                <tbody>
                                    @foreach($adhesions->slice(5) as $membership)
                                        <tr>
                                            <td>{{ $membership->membershipType->name ?? 'Standard' }}</td>
                                            <td>{{ $membership->start_date->format('d/m/Y') }} - {{ $membership->end_date->format('d/m/Y') }}</td>
                                            <td>{{ number_format($membership->amount_paid, 2, ',', ' ') }} EUR</td>
                                            <td>
                                                @if($membership->end_date >= now())
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-warning">Expirée</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </details>
                    @endif
                </div>
            </div>

            {{-- Dons --}}
            @php $dons = $member->donations->sortByDesc('donation_date')->values(); @endphp
            <div class="card" id="dons" style="margin-bottom: 1.5rem;">
                <div class="card-header"><h3 class="card-title">Dons ({{ $dons->count() }})</h3></div>
                <div class="card-body" style="padding: 0;">
                    <table class="table">
                        <thead><tr><th>Date</th><th>Montant</th><th>Paiement</th><th>Reçu</th></tr></thead>
                        <tbody>
                            @forelse($dons->take(5) as $donation)
                                <tr>
                                    <td>{{ $donation->donation_date->format('d/m/Y') }}</td>
                                    <td><span class="badge badge-success">{{ number_format($donation->amount, 0, ',', ' ') }} EUR</span></td>
                                    <td>{{ $donation->payment_method ?? '-' }}</td>
                                    <td>
                                        @if($donation->tax_receipt_sent)
                                            <span class="badge badge-info">Envoyé</span>
                                        @else
                                            <span class="badge badge-warning">À envoyer</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align: center; color: #9ca3af;">Aucun don</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($dons->count() > 5)
                        <details style="border-top: 1px solid #e5e7eb;">
                            <summary style="padding: 0.75rem 1rem; cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $dons->count() }})</summary>
                            <table class="table">
                                <tbody>
                                    @foreach($dons->slice(5) as $donation)
                                        <tr>
                                            <td>{{ $donation->donation_date->format('d/m/Y') }}</td>
                                            <td><span class="badge badge-success">{{ number_format($donation->amount, 0, ',', ' ') }} EUR</span></td>
                                            <td>{{ $donation->payment_method ?? '-' }}</td>
                                            <td>
                                                @if($donation->tax_receipt_sent)
                                                    <span class="badge badge-info">Envoyé</span>
                                                @else
                                                    <span class="badge badge-warning">À envoyer</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </details>
                    @endif
                </div>
            </div>

            {{-- Achats --}}
            @php $achats = $member->purchases->sortByDesc('purchase_date')->values(); @endphp
            <div class="card" id="achats" style="margin-bottom: 1.5rem;">
                <div class="card-header"><h3 class="card-title">Achats ({{ $achats->count() }})</h3></div>
                <div class="card-body" style="padding: 0;">
                    <table class="table">
                        <thead><tr><th>Date</th><th>Produit</th><th>Montant</th><th>Source</th></tr></thead>
                        <tbody>
                            @forelse($achats->take(5) as $purchase)
                                <tr>
                                    <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($purchase->product)
                                            <a href="{{ route('admin.products.show', $purchase->product) }}">{{ $purchase->product->name }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ number_format($purchase->total_amount, 2, ',', ' ') }} EUR</td>
                                    <td><span class="badge badge-{{ $purchase->source === 'import' ? 'warning' : 'info' }}">{{ $purchase->getSourceLabel() }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align: center; color: #9ca3af;">Aucun achat</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($achats->count() > 5)
                        <details style="border-top: 1px solid #e5e7eb;">
                            <summary style="padding: 0.75rem 1rem; cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $achats->count() }})</summary>
                            <table class="table">
                                <tbody>
                                    @foreach($achats->slice(5) as $purchase)
                                        <tr>
                                            <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                                            <td>
                                                @if($purchase->product)
                                                    <a href="{{ route('admin.products.show', $purchase->product) }}">{{ $purchase->product->name }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ number_format($purchase->total_amount, 2, ',', ' ') }} EUR</td>
                                            <td><span class="badge badge-{{ $purchase->source === 'import' ? 'warning' : 'info' }}">{{ $purchase->getSourceLabel() }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </details>
                    @endif
                </div>
            </div>

            {{-- Bulletins Lepis (converti au pattern .card) --}}
            @php $recipients = $member->lepisBulletinRecipients; @endphp
            <div class="card" id="bulletins" style="margin-bottom: 1.5rem;">
                <div class="card-header"><h3 class="card-title">Bulletins Lepis reçus ({{ $recipients->count() }})</h3></div>
                <div class="card-body" style="padding: 0;">
                    @if($recipients->isEmpty())
                        <div style="padding: 1rem; color: #6b7280;">Aucun envoi de bulletin pour ce contact.</div>
                    @else
                        <table class="table">
                            <thead><tr><th>Bulletin</th><th>Format</th><th>Date d'envoi</th><th>Liste Brevo</th></tr></thead>
                            <tbody>
                                @foreach($recipients->take(5) as $r)
                                    <tr>
                                        <td><a href="{{ route('admin.lepis.edit', $r->bulletin) }}" style="color: #2C5F2D;">{{ $r->bulletin?->title ?? '#' . $r->lepis_bulletin_id }}</a></td>
                                        <td>{{ $r->format === 'digital' ? 'Numérique' : 'Papier' }}</td>
                                        <td>{{ $r->included_at?->locale('fr')->isoFormat('LL') }}</td>
                                        <td style="color: #6b7280; font-size: 0.875rem;">{{ $r->brevo_list_id ? '#' . $r->brevo_list_id : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($recipients->count() > 5)
                            <details style="border-top: 1px solid #e5e7eb;">
                                <summary style="padding: 0.75rem 1rem; cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $recipients->count() }})</summary>
                                <table class="table">
                                    <tbody>
                                        @foreach($recipients->slice(5) as $r)
                                            <tr>
                                                <td><a href="{{ route('admin.lepis.edit', $r->bulletin) }}" style="color: #2C5F2D;">{{ $r->bulletin?->title ?? '#' . $r->lepis_bulletin_id }}</a></td>
                                                <td>{{ $r->format === 'digital' ? 'Numérique' : 'Papier' }}</td>
                                                <td>{{ $r->included_at?->locale('fr')->isoFormat('LL') }}</td>
                                                <td style="color: #6b7280; font-size: 0.875rem;">{{ $r->brevo_list_id ? '#' . $r->brevo_list_id : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </details>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Engagement OREINA --}}
            @php
                $submissions = $member->user?->submissions ?? collect();
                $suggestions = $member->lepisSuggestions;
                $groups = $member->workGroups;
            @endphp
            @if($groups->isNotEmpty() || $submissions->isNotEmpty() || $suggestions->isNotEmpty())
                <div class="card" id="engagement" style="margin-bottom: 1.5rem;">
                    <div class="card-header"><h3 class="card-title">Engagement OREINA</h3></div>
                    <div class="card-body">

                        @if($groups->isNotEmpty())
                            <div style="margin-bottom: 1.25rem;">
                                <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">
                                    Groupes de travail ({{ $groups->count() }})
                                </div>
                                @foreach($groups as $group)
                                    <div style="margin-bottom: 0.5rem;">
                                        · <strong>{{ $group->name }}</strong>
                                        @if(($group->pivot->role ?? 'member') !== 'member')
                                            — {{ $group->pivot->role }}
                                        @endif
                                        @if($group->pivot->joined_at)
                                            <div style="color: #6b7280; font-size: 0.8125rem; margin-left: 0.875rem;">
                                                Membre depuis le {{ \Carbon\Carbon::parse($group->pivot->joined_at)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($submissions->isNotEmpty())
                            <div style="margin-bottom: 1.25rem;">
                                <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">
                                    Chersotis ({{ $submissions->count() }} soumission{{ $submissions->count() > 1 ? 's' : '' }})
                                </div>
                                @foreach($submissions->take(5) as $sub)
                                    <div style="margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="flex: 1;">
                                            · <a href="{{ route('admin.submissions.show', $sub) }}" style="color: #2C5F2D;">{{ $sub->title }}</a>
                                            <span class="badge badge-info" style="margin-left: 0.5rem;">{{ $sub->status?->label() ?? $sub->status }}</span>
                                        </div>
                                        <div style="color: #6b7280; font-size: 0.8125rem;">
                                            {{ $sub->published_at?->format('Y') ?? $sub->created_at->format('Y') }}
                                        </div>
                                    </div>
                                @endforeach
                                @if($submissions->count() > 5)
                                    <details style="margin-top: 0.5rem;">
                                        <summary style="cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $submissions->count() }})</summary>
                                        @foreach($submissions->slice(5) as $sub)
                                            <div style="margin: 0.5rem 0; display: flex; justify-content: space-between; align-items: center;">
                                                <div style="flex: 1;">
                                                    · <a href="{{ route('admin.submissions.show', $sub) }}" style="color: #2C5F2D;">{{ $sub->title }}</a>
                                                    <span class="badge badge-info" style="margin-left: 0.5rem;">{{ $sub->status?->label() ?? $sub->status }}</span>
                                                </div>
                                                <div style="color: #6b7280; font-size: 0.8125rem;">
                                                    {{ $sub->published_at?->format('Y') ?? $sub->created_at->format('Y') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </details>
                                @endif
                            </div>
                        @endif

                        @if($suggestions->isNotEmpty())
                            <div>
                                <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">
                                    Lepis — Suggestions ({{ $suggestions->count() }})
                                </div>
                                @foreach($suggestions->take(5) as $sug)
                                    <div style="margin-bottom: 0.75rem;">
                                        · <strong>« {{ $sug->title }} »</strong>
                                        <span class="badge badge-{{ $sug->status === 'noted' ? 'success' : 'warning' }}" style="margin-left: 0.5rem;">
                                            {{ $sug->status === 'noted' ? 'Notée' : 'En attente' }}
                                        </span>
                                        @if($sug->submitted_at)
                                            <div style="color: #6b7280; font-size: 0.8125rem; margin-left: 0.875rem;">
                                                Soumise le {{ $sug->submitted_at->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                @if($suggestions->count() > 5)
                                    <details>
                                        <summary style="cursor: pointer; color: #356B8A; font-size: 0.875rem;">Voir tout ({{ $suggestions->count() }})</summary>
                                        @foreach($suggestions->slice(5) as $sug)
                                            <div style="margin: 0.5rem 0;">
                                                · <strong>« {{ $sug->title }} »</strong>
                                                <span class="badge badge-{{ $sug->status === 'noted' ? 'success' : 'warning' }}" style="margin-left: 0.5rem;">
                                                    {{ $sug->status === 'noted' ? 'Notée' : 'En attente' }}
                                                </span>
                                                @if($sug->submitted_at)
                                                    <div style="color: #6b7280; font-size: 0.8125rem; margin-left: 0.875rem;">
                                                        Soumise le {{ $sug->submitted_at->format('d/m/Y') }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </details>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
