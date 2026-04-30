@extends('layouts.admin')
@section('title', 'Bulletin Lepis n°' . $bulletin->issue_number)
@section('breadcrumb')
    <a href="{{ route('admin.lepis.index') }}">Lepis</a>
    <span>/</span>
    <span>n°{{ $bulletin->issue_number }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Lepis n°{{ $bulletin->issue_number }} — {{ $bulletin->quarter_label }} {{ $bulletin->year }}</h1>
            <p class="page-subtitle">{{ $bulletin->title }}</p>
        </div>
        <div class="page-header-actions">
            @if ($bulletin->isInMembersPhase() || $bulletin->isPublic())
                <a href="{{ route('hub.lepis.bulletins.show', $bulletin) }}" target="_blank" class="btn btn-outline">Voir sur le site</a>
            @endif
        </div>
    </div>

    @include('admin.lepis._carte_infos')
    @include('admin.lepis._carte_pdf')
    @include('admin.lepis._carte_cycle')
    @include('admin.lepis._carte_annonce')
    @if(in_array($bulletin->status, ['members', 'public']))
        @include('admin.lepis._carte_diffusion', ['bulletin' => $bulletin])
    @endif
@endsection
