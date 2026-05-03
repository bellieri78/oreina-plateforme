@extends('layouts.admin')

@section('title', 'Modifier ' . $member->full_name)
@section('breadcrumb')
    <a href="{{ route('admin.members.index') }}">Contacts</a>
    <span>/</span>
    <a href="{{ route('admin.members.show', $member) }}">{{ $member->full_name }}</a>
    <span>/</span>
    <span>Modifier</span>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        {{-- SIDEBAR RÉCAP --}}
        <div class="card">
            <div class="card-body">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background-color: #356B8A; color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 600; margin: 0 auto;">
                        {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                    </div>
                    <h2 style="margin-top: 1rem; font-size: 1.25rem; font-weight: 600;">{{ $member->full_name }}</h2>
                </div>
                <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">N° adhérent</div>
                        <div>{{ $member->member_number }}</div>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Inscrit le</div>
                        <div>{{ $member->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
                <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 1rem;">
                    <a href="{{ route('admin.members.show', $member) }}" class="btn btn-secondary" style="width: 100%; text-align: center;">
                        ← Retour à la fiche
                    </a>
                </div>
            </div>
        </div>

        {{-- FORMULAIRE --}}
        <div>
            <form action="{{ route('admin.members.update', $member) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.members._form', ['member' => $member])

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.members.show', $member) }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
