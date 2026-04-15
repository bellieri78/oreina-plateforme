@extends('layouts.admin')

@section('title', 'File d\'attente éditoriale')

@section('breadcrumb')<span>Revue</span><span>/</span><span>File d'attente</span>@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-oreina-dark mb-6">File d'attente éditoriale</h1>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($submissions->isEmpty())
        <p class="text-gray-600">Aucun article en attente d'éditeur.</p>
    @else
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold">Titre</th>
                        <th class="px-4 py-2 text-left font-semibold">Auteur</th>
                        <th class="px-4 py-2 text-left font-semibold">Soumis le</th>
                        <th class="px-4 py-2 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @foreach($submissions as $s)
                    @php
                        $reviewerIds = $s->reviews->pluck('reviewer_id')->all();
                    @endphp
                    <tr>
                        <td class="px-4 py-3" title="{{ Str::limit($s->abstract, 300) }}">
                            <strong>{{ $s->title }}</strong>
                        </td>
                        <td class="px-4 py-3">{{ $s->author?->name }}</td>
                        <td class="px-4 py-3">{{ optional($s->submitted_at)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 space-y-2">
                            @can('takeEditor', $s)
                                <form method="POST" action="{{ route('admin.journal.queue.take', $s) }}">
                                    @csrf
                                    <button class="bg-oreina-green text-white px-3 py-1 rounded text-xs hover:bg-oreina-dark">
                                        Prendre en charge
                                    </button>
                                </form>
                            @endcan

                            @can('assignEditor', $s)
                                <form method="POST" action="{{ route('admin.journal.queue.assign', $s) }}" class="flex items-center gap-2">
                                    @csrf
                                    <select name="user_id" class="border-gray-300 rounded text-xs">
                                        <option value="">— Assigner à —</option>
                                        @foreach($eligibleEditors as $ed)
                                            <option value="{{ $ed->id }}"
                                                @if(in_array($ed->id, $reviewerIds)) disabled @endif>
                                                {{ $ed->name }}
                                                @if(in_array($ed->id, $reviewerIds)) (déjà relecteur) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <label class="text-xs">
                                        <input type="checkbox" name="override" value="1"> forcer
                                    </label>
                                    <button class="bg-gray-700 text-white px-3 py-1 rounded text-xs">Assigner</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
