@extends('layouts.admin')
@section('title', 'Parametres')
@section('breadcrumb')<span>Administration</span><span>/</span><span>Parametres</span>@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <svg class="page-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Parametres
            </h1>
            <p class="page-subtitle">Configuration generale de la plateforme</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.settings.statistics') }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistiques
            </a>
            <form action="{{ route('admin.settings.clear-cache') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Vider le cache
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        <div class="settings-groups">
            @foreach($groups as $groupKey => $groupLabel)
                @if(isset($settings[$groupKey]))
                    <div class="card mb-4">
                        <div class="settings-group-header">
                            <h2 class="settings-group-title">{{ $groupLabel }}</h2>
                        </div>
                        <div class="settings-group-content">
                            @foreach($settings[$groupKey] as $setting)
                                <div class="setting-row">
                                    <div class="setting-label">
                                        <label for="{{ $setting['key'] }}">
                                            {{ $setting['description'] ?? $setting['key'] }}
                                        </label>
                                        <span class="setting-key">{{ $setting['key'] }}</span>
                                    </div>
                                    <div class="setting-input">
                                        @if($setting['type'] === 'boolean')
                                            <label class="toggle-switch">
                                                <input type="hidden" name="{{ $setting['key'] }}" value="0">
                                                <input type="checkbox"
                                                       name="{{ $setting['key'] }}"
                                                       value="1"
                                                       {{ $setting['value'] ? 'checked' : '' }}>
                                                <span class="toggle-slider"></span>
                                                <span class="toggle-label">{{ $setting['value'] ? 'Active' : 'Desactive' }}</span>
                                            </label>
                                        @elseif($setting['type'] === 'integer')
                                            <input type="number"
                                                   name="{{ $setting['key'] }}"
                                                   id="{{ $setting['key'] }}"
                                                   value="{{ $setting['value'] }}"
                                                   class="form-input" style="max-width: 120px;">
                                        @else
                                            <input type="text"
                                                   name="{{ $setting['key'] }}"
                                                   id="{{ $setting['key'] }}"
                                                   value="{{ $setting['value'] }}"
                                                   class="form-input">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les parametres
            </button>
        </div>
    </form>
@endsection
