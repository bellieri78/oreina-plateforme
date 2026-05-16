@extends('layouts.member')

@section('title', 'Messagerie')
@section('page-title', 'Messagerie')
@section('page-subtitle', 'Vos échanges privés avec les autres adhérents')

@section('content')
<div class="card panel" style="padding:0;overflow:hidden;">
    @if($member)
        @livewire('member.chat', ['initialConversationId' => $initialConversationId, 'initialTargetId' => $initialTargetId])
    @else
        <p style="padding:28px;text-align:center;color:var(--muted);">Complétez votre profil pour accéder à la messagerie.</p>
    @endif
</div>
@endsection
