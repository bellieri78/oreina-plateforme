@extends('layouts.member')

@section('title', 'Carte des membres')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-oreina-dark">Carte des membres</h1>
        <p class="text-sm text-gray-400 mt-0.5">Répartition géographique des adhérents par département</p>
    </div>

    <x-member.map-france />
</div>
@endsection
