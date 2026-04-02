@extends('layouts.member')

@section('title', 'Discussion membres')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-oreina-dark">Discussion membres</h1>
        <p class="text-sm text-gray-400 mt-0.5">Échangez avec les autres adhérents</p>
    </div>

    <div class="member-card">
        @if($member)
            @livewire('member.chat', ['memberId' => $member->id, 'expanded' => true])
        @else
            <p class="text-gray-400 text-sm text-center py-6">Complétez votre profil pour accéder au chat</p>
        @endif
    </div>
</div>
@endsection
