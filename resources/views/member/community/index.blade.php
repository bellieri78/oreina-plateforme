@extends('layouts.member')

@section('title', 'Communauté')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-oreina-dark">Communauté OREINA</h1>
        <p class="text-sm text-gray-400 mt-0.5">Découvrez les membres et échangez</p>
    </div>

    {{-- Map --}}
    <x-member.map-france />

    {{-- Chat --}}
    <div class="member-card">
        <div class="member-card-header">
            <span class="dot" style="background: #14B8A6;"></span>
            Discussion membres
            <a href="{{ route('member.chat') }}" class="ml-auto text-xs text-oreina-green hover:underline font-medium">Voir tout →</a>
        </div>
        @if($member)
            @livewire('member.chat', ['memberId' => $member->id, 'expanded' => false])
        @else
            <p class="text-gray-400 text-sm text-center py-4">Complétez votre profil pour accéder au chat</p>
        @endif
    </div>
</div>
@endsection
