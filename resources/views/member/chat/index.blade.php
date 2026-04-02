@extends('layouts.member')

@section('title', 'Discussion membres')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-oreina-dark">Discussion membres</h1>
        <p class="text-sm text-gray-400 mt-0.5">Échangez avec les autres adhérents</p>
    </div>

    <div class="member-card">
        @livewire('member.chat', ['memberId' => $member->id, 'expanded' => true])
    </div>
</div>
@endsection
