@extends('layouts.member')

@section('title', 'Discussion membres')
@section('page-title', 'Discussion membres')
@section('page-subtitle', 'Échangez avec les autres adhérents')

@section('content')
<div class="space-y-4">
    <div class="card panel">
        @if($member)
            @livewire('member.chat', ['memberId' => $member->id, 'expanded' => true])
        @else
            <p class="text-sm text-center py-6" style="color:var(--muted)">Complétez votre profil pour accéder au chat</p>
        @endif
    </div>
</div>
@endsection
