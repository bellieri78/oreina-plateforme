@extends('layouts.member')

@section('title', 'Messagerie')
@section('page-title', 'Messagerie')
@section('page-subtitle', 'Vos echanges prives avec les autres adherents')

@section('content')
<div class="card panel" style="padding:0;overflow:hidden;">
    @if($member)
        @livewire('member.chat', ['initialConversationId' => $initialConversationId, 'initialTargetId' => $initialTargetId])
    @else
        <p style="padding:28px;text-align:center;color:var(--muted);">Completez votre profil pour acceder a la messagerie.</p>
    @endif
</div>
@endsection
