@extends('layouts.member')

@section('title', 'Communauté')
@section('page-title', 'Communauté')
@section('page-subtitle', 'Carte des membres et échanges')

@section('content')
<div class="space-y-6">
    {{-- Map --}}
    <x-member.map-france />

    {{-- Chat --}}
    <div class="card panel">
        <div class="panel-head">
            <span class="dot" style="background: var(--info);"></span>
            <div>
                <h2>Discussion membres</h2>
            </div>
            <a href="{{ route('member.chat') }}" class="ml-auto text-xs font-medium text-link hover:underline">Voir tout →</a>
        </div>
        @if($member)
            @livewire('member.chat', ['memberId' => $member->id, 'expanded' => false])
        @else
            <p class="text-sm text-center py-4" style="color:var(--muted)">Complétez votre profil pour accéder au chat</p>
        @endif
    </div>
</div>
@endsection
