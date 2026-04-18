@extends('layouts.admin')
@section('title', 'Nouvelle soumission')
@section('breadcrumb')
    <a href="{{ route('admin.submissions.index') }}">Soumissions</a>
    <span>/</span>
    <span>Nouvelle</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nouvelle soumission</h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem;">
                    <p style="font-weight: 600; color: #991b1b; margin: 0 0 0.5rem 0;">Merci de corriger les erreurs suivantes :</p>
                    <ul style="margin: 0; padding-left: 1.25rem; color: #991b1b; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.submissions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.submissions._form')
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Creer la soumission</button>
                    <a href="{{ route('admin.submissions.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
