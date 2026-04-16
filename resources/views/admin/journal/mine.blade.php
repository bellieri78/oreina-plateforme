@extends('layouts.admin')

@section('title', 'Mes articles — Chersotis')

@section('breadcrumb')<span>Revue</span><span>/</span><span>Mes articles</span>@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-oreina-dark mb-6">Mes articles en charge</h1>

    @if($submissions->isEmpty())
        <p class="text-gray-600">Aucun article pris en charge actuellement.</p>
    @else
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold">Titre</th>
                        <th class="px-4 py-2 text-left font-semibold">Auteur</th>
                        <th class="px-4 py-2 text-left font-semibold">Statut</th>
                        <th class="px-4 py-2 text-left font-semibold">Relectures</th>
                        <th class="px-4 py-2 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @foreach($submissions as $s)
                    <tr>
                        <td class="px-4 py-3"><strong>{{ $s->title }}</strong></td>
                        <td class="px-4 py-3">{{ $s->author?->name }}</td>
                        <td class="px-4 py-3">{{ $s->status instanceof \App\Enums\SubmissionStatus ? $s->status->label() : ($s->status ?? '') }}</td>
                        <td class="px-4 py-3">
                            {{ $s->reviews->where('status', \App\Models\Review::STATUS_COMPLETED)->count() }}
                            / {{ $s->reviews->count() }}
                        </td>
                        <td class="px-4 py-3">
                            @include('admin.journal._transition_buttons', ['submission' => $s])
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
