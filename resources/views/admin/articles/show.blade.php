@extends('layouts.admin')
@section('title', $article->title)
@section('breadcrumb')
    <a href="{{ route('admin.articles.index') }}">Articles</a>
    <span>/</span>
    <span>{{ Str::limit($article->title, 30) }}</span>
@endsection

@section('content')
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $article->title }}</h3>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-secondary">Modifier</a>
                </div>
            </div>
            <div class="card-body">
                @if($article->summary)
                    <div style="background-color: #f9fafb; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; font-style: italic; color: #6b7280;">
                        {{ $article->summary }}
                    </div>
                @endif

                <div style="line-height: 1.7; color: #374151;">
                    {!! nl2br(e($article->content)) !!}
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informations</h3>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 1rem;">
                        @switch($article->status)
                            @case('published')
                                <span class="badge badge-success" style="font-size: 1rem; padding: 0.5rem 1rem;">Publie</span>
                                @break
                            @case('validated')
                                <span class="badge badge-info" style="font-size: 1rem; padding: 0.5rem 1rem;">Valide</span>
                                @break
                            @case('submitted')
                                <span class="badge badge-warning" style="font-size: 1rem; padding: 0.5rem 1rem;">Soumis</span>
                                @break
                            @default
                                <span class="badge badge-secondary" style="font-size: 1rem; padding: 0.5rem 1rem;">Brouillon</span>
                        @endswitch
                        @if($article->is_featured)
                            <span class="badge badge-info" style="font-size: 1rem; padding: 0.5rem 1rem; margin-left: 0.5rem;">Vedette</span>
                        @endif
                    </div>

                    <div style="margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">Auteur :</span>
                        <span style="font-weight: 500;">{{ $article->author?->name ?? 'Non defini' }}</span>
                    </div>

                    @if($article->category)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Categorie :</span>
                            <span style="font-weight: 500;">{{ $article->category }}</span>
                        </div>
                    @endif

                    @if($article->slug)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Slug :</span>
                            <code style="background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.875rem;">{{ $article->slug }}</code>
                        </div>
                    @endif

                    <div style="margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">Cree le :</span>
                        <span>{{ $article->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    @if($article->published_at)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Publie le :</span>
                            <span>{{ $article->published_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif

                    @if($article->validated_at)
                        <div style="margin-bottom: 0.75rem;">
                            <span style="color: #6b7280;">Valide le :</span>
                            <span>{{ $article->validated_at->format('d/m/Y H:i') }}</span>
                            @if($article->validator)
                                <span style="color: #6b7280;">par</span>
                                <span>{{ $article->validator->name }}</span>
                            @endif
                        </div>
                    @endif

                    <div style="margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">Vues :</span>
                        <span style="font-weight: 500;">{{ number_format($article->views_count ?? 0) }}</span>
                    </div>

                    @if($article->validation_notes)
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <span style="color: #6b7280; display: block; margin-bottom: 0.5rem;">Notes de validation :</span>
                            <p style="color: #374151; font-size: 0.875rem;">{{ $article->validation_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
