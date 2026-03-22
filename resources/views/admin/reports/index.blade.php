@extends('layouts.admin')

@section('title', 'Rapports')

@section('breadcrumb')
    <span>Rapports</span>
@endsection

@section('content')
    <div style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Generation de rapports PDF</h2>
        <p style="color: #6b7280;">Selectionnez une annee et generez les rapports souhaites au format PDF.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
        {{-- Rapport annuel global --}}
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #2C5F2D 0%, #16302B 100%); color: white;">
                <h3 class="card-title" style="color: white;">Rapport annuel</h3>
            </div>
            <div class="card-body">
                <p style="color: #6b7280; margin-bottom: 1rem;">Rapport complet incluant adhesions, dons et benevolat.</p>
                <form action="{{ route('admin.reports.annual') }}" method="GET" style="display: flex; gap: 0.5rem;">
                    <select name="year" class="form-input" style="flex: 1;">
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; margin-right: 4px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        PDF
                    </button>
                </form>
            </div>
        </div>

        {{-- Rapport adhesions --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rapport des adhesions</h3>
            </div>
            <div class="card-body">
                <p style="color: #6b7280; margin-bottom: 1rem;">Liste detaillee des adhesions avec statistiques par type et mode de paiement.</p>
                <form action="{{ route('admin.reports.memberships') }}" method="GET" style="display: flex; gap: 0.5rem;">
                    <select name="year" class="form-input" style="flex: 1;">
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; margin-right: 4px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        PDF
                    </button>
                </form>
            </div>
        </div>

        {{-- Rapport dons --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rapport des dons</h3>
            </div>
            <div class="card-body">
                <p style="color: #6b7280; margin-bottom: 1rem;">Historique des dons avec evolution mensuelle et statistiques globales.</p>
                <form action="{{ route('admin.reports.donations') }}" method="GET" style="display: flex; gap: 0.5rem;">
                    <select name="year" class="form-input" style="flex: 1;">
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; margin-right: 4px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        PDF
                    </button>
                </form>
            </div>
        </div>

        {{-- Rapport benevolat --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rapport du benevolat</h3>
            </div>
            <div class="card-body">
                <p style="color: #6b7280; margin-bottom: 1rem;">Activites benevoles, heures par type et classement des benevoles.</p>
                <form action="{{ route('admin.reports.volunteer') }}" method="GET" style="display: flex; gap: 0.5rem;">
                    <select name="year" class="form-input" style="flex: 1;">
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; margin-right: 4px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Additional info --}}
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h3 class="card-title">Attestations individuelles</h3>
        </div>
        <div class="card-body">
            <p style="color: #6b7280; margin-bottom: 1rem;">
                Pour generer une attestation de benevolat pour un membre specifique, rendez-vous sur sa fiche dans le module Benevolat :
            </p>
            <ol style="color: #6b7280; margin-left: 1.5rem;">
                <li>Accedez a <a href="{{ route('admin.volunteer.index') }}">Benevolat > Tableau de bord</a></li>
                <li>Cliquez sur le nom d'un benevole dans le classement</li>
                <li>Sur la page du rapport benevole, un bouton permet de telecharger l'attestation</li>
            </ol>
        </div>
    </div>
@endsection
