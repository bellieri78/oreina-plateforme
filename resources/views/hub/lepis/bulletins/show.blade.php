@extends('layouts.hub')

@section('title', "Lepis n°{$bulletin->issue_number} — {$bulletin->title}")

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4">
    <a href="{{ route('hub.lepis.bulletins.index') }}" class="text-sm text-oreina-green hover:underline">← Tous les numéros</a>
    <h1 class="text-3xl font-bold text-oreina-dark mt-4">{{ $bulletin->title }}</h1>
    <p class="text-gray-500 mt-2">Lepis n°{{ $bulletin->issue_number }} — {{ $bulletin->quarter_label }} {{ $bulletin->year }}</p>
    {{-- Task 11 refines this page with summary, navigation, and CTA. --}}
</div>
@endsection
