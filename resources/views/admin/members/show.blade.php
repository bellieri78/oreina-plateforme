@extends('layouts.admin')

@section('title', $member->full_name)
@section('breadcrumb')
    <a href="{{ route('admin.members.index') }}">Contacts</a>
    <span>/</span>
    <span>{{ $member->full_name }}</span>
@endsection

@section('content')
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
                    @if($member->phone)
                        <div style="margin-bottom: 1rem;">
                            <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Telephone</div>
                            <div>{{ $member->phone }}</div>
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
            </div>
        </div>

        <!-- Tabs Content -->
        <div>
            <!-- Memberships -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Adhesions</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Periode</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($member->memberships()->orderByDesc('start_date')->get() as $membership)
                                <tr>
                                    <td>{{ $membership->membershipType->name ?? 'Standard' }}</td>
                                    <td>{{ $membership->start_date->format('d/m/Y') }} - {{ $membership->end_date->format('d/m/Y') }}</td>
                                    <td>{{ number_format($membership->amount_paid, 2, ',', ' ') }} EUR</td>
                                    <td>
                                        @if($membership->end_date >= now())
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-warning">Expiree</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #9ca3af;">Aucune adhesion</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Donations -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Dons</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Paiement</th>
                                <th>Recu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($member->donations as $donation)
                                <tr>
                                    <td>{{ $donation->donation_date->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge badge-success">{{ number_format($donation->amount, 0, ',', ' ') }} EUR</span>
                                    </td>
                                    <td>{{ $donation->payment_method ?? '-' }}</td>
                                    <td>
                                        @if($donation->tax_receipt_sent)
                                            <span class="badge badge-info">Envoye</span>
                                        @else
                                            <span class="badge badge-warning">A envoyer</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #9ca3af;">Aucun don</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Achats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Achats</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Produit</th>
                                <th>Montant</th>
                                <th>Source</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($member->purchases()->orderByDesc('purchase_date')->get() as $purchase)
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
                                    <td>
                                        <span class="badge badge-{{ $purchase->source === 'import' ? 'warning' : 'info' }}">
                                            {{ $purchase->getSourceLabel() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #9ca3af;">Aucun achat</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bulletins Lepis -->
            <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1.25rem; margin-top: 1.5rem;">
                <h3 style="margin-top: 0; font-weight: 600;">Bulletins Lepis recus</h3>

                @if($member->lepisBulletinRecipients->isEmpty())
                    <p style="color: #6b7280; margin: 0;">Aucun envoi de bulletin pour ce contact.</p>
                @else
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.5rem; text-align: left;">Bulletin</th>
                                <th style="padding: 0.5rem; text-align: left;">Format</th>
                                <th style="padding: 0.5rem; text-align: left;">Date d'envoi</th>
                                <th style="padding: 0.5rem; text-align: left;">Liste Brevo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($member->lepisBulletinRecipients as $r)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 0.5rem;">
                                        <a href="{{ route('admin.lepis.edit', $r->bulletin) }}" style="color: #2C5F2D; text-decoration: none;">
                                            {{ $r->bulletin?->title ?? '#' . $r->lepis_bulletin_id }}
                                        </a>
                                    </td>
                                    <td style="padding: 0.5rem;">
                                        {{ $r->format === 'digital' ? 'Numerique' : 'Papier' }}
                                    </td>
                                    <td style="padding: 0.5rem;">
                                        {{ $r->included_at?->locale('fr')->isoFormat('LL') }}
                                    </td>
                                    <td style="padding: 0.5rem; color: #6b7280; font-size: 0.875rem;">
                                        {{ $r->brevo_list_id ? '#' . $r->brevo_list_id : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
