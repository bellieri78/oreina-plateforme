@extends('layouts.admin')
@section('title', 'FAQ')
@section('breadcrumb')<span>FAQ</span>@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
    @endif

    <p style="color: #6b7280; margin-bottom: 1.5rem;">
        Les sections sont figées. Pour ajouter une question dans une section, clique sur « + Ajouter ». Pour réordonner, utilise les flèches.
    </p>

    @foreach($sections as $section)
        @php
            $questions = $questionsBySection[$section['slug']] ?? collect();
        @endphp
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">
                    {{ $section['label'] }}
                    <span style="color: #6b7280; font-weight: 400; font-size: 0.875rem; margin-left: 0.5rem;">· {{ $questions->count() }} question{{ $questions->count() > 1 ? 's' : '' }}</span>
                </h3>
                <a href="{{ route('admin.faq.create', ['section' => $section['slug']]) }}" class="btn btn-primary" style="padding: 0.375rem 0.75rem;">
                    <i data-lucide="plus"></i> Ajouter une question
                </a>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($questions->isEmpty())
                    <div style="padding: 1rem; color: #6b7280;">Aucune question dans cette section.</div>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 90px;">Ordre</th>
                                <th>Question</th>
                                <th style="width: 110px;">Statut</th>
                                <th style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questions as $faq)
                                @include('admin.faq._row', ['faq' => $faq])
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endforeach
@endsection
