@extends('layouts.admin')

@section('title', 'File d\'attente éditoriale')

@section('breadcrumb')<span>Revue</span><span>/</span><span>File d'attente</span>@endsection

@section('content')
    <div class="page-header">
        <h1>File d'attente éditoriale</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom:1rem;">{{ session('error') }}</div>
    @endif

    @if($submissions->isEmpty())
        <div class="card">
            <div class="card-body" style="text-align:center;padding:2rem;color:#6b7280;">
                Aucun article en attente d'éditeur.
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Soumis le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($submissions as $s)
                        @php
                            $reviewerIds = $s->reviews->pluck('reviewer_id')->all();
                        @endphp
                        <tr>
                            <td title="{{ Str::limit($s->abstract, 300) }}">
                                <strong>{{ $s->title }}</strong>
                            </td>
                            <td>{{ $s->author?->name ?? '—' }}</td>
                            <td>{{ optional($s->submitted_at)->format('d/m/Y') }}</td>
                            <td>
                                <div style="display:flex;flex-direction:column;gap:6px;">
                                    @can('takeEditor', $s)
                                        <form method="POST" action="{{ route('admin.journal.queue.take', $s) }}" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                Prendre en charge
                                            </button>
                                        </form>
                                    @endcan

                                    @can('assignEditor', $s)
                                        <form method="POST" action="{{ route('admin.journal.queue.assign', $s) }}" style="margin:0;display:flex;align-items:center;gap:6px;">
                                            @csrf
                                            <select name="user_id" class="form-input" style="font-size:0.8rem;padding:4px 8px;min-width:150px;">
                                                <option value="">— Assigner à —</option>
                                                @foreach($eligibleEditors as $ed)
                                                    <option value="{{ $ed->id }}"
                                                        @if(in_array($ed->id, $reviewerIds)) disabled @endif>
                                                        {{ $ed->name }}
                                                        @if(in_array($ed->id, $reviewerIds)) (déjà relecteur) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label style="font-size:0.75rem;display:flex;align-items:center;gap:4px;white-space:nowrap;">
                                                <input type="checkbox" name="override" value="1"> forcer
                                            </label>
                                            <button type="submit" class="btn btn-dark btn-sm">Assigner</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
