@extends('layouts.admin')
@section('title', 'Menus')
@section('breadcrumb')<span>Menus</span>@endsection

@section('content')
    <div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
            <i data-lucide="plus"></i> Nouvel item
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
    @endif

    {{-- Header section --}}
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header"><h3 class="card-title">Menu Header</h3></div>
        <div class="card-body" style="padding: 0;">
            @if($headerItems->isEmpty())
                <div style="padding: 1rem; color: #6b7280;">Aucun item dans le menu header.</div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Ordre</th>
                            <th>Libellé</th>
                            <th>URL</th>
                            <th>Statut</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($headerItems as $item)
                            @include('admin.menus._row', ['item' => $item, 'depth' => 0])
                            @foreach($item->children as $child)
                                @include('admin.menus._row', ['item' => $child, 'depth' => 1])
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Footer section --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Menu Footer</h3></div>
        <div class="card-body" style="padding: 0;">
            @if($footerItems->isEmpty())
                <div style="padding: 1rem; color: #6b7280;">Aucun item dans le menu footer.</div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Ordre</th>
                            <th>Libellé</th>
                            <th>URL</th>
                            <th>Statut</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($footerItems as $item)
                            @include('admin.menus._row', ['item' => $item, 'depth' => 0])
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
