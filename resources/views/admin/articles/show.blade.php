@extends('layouts.admin')
@section('title', $article->title)
@section('breadcrumb')
    <a href="{{ route('admin.articles.index') }}">Articles</a>
    <span>/</span>
    <span>{{ Str::limit($article->title, 30) }}</span>
@endsection

@push('styles')
<style>
    .article-content { line-height: 1.7; color: #1C2B27; font-size: 1rem; }
    .article-content p { margin: 0 0 1em; }
    .article-content h2 { font-size: 1.5rem; font-weight: 700; margin: 1.5em 0 0.5em; color: #16302B; }
    .article-content h3 { font-size: 1.25rem; font-weight: 700; margin: 1.2em 0 0.4em; color: #16302B; }
    .article-content ul, .article-content ol { margin: 0 0 1em 1.5em; padding: 0; }
    .article-content li { margin-bottom: 0.25em; }
    .article-content blockquote { border-left: 3px solid #85B79D; padding: 0.25em 0 0.25em 1em; color: #68756F; margin: 1em 0; font-style: italic; }
    .article-content a { color: #356B8A; text-decoration: underline; }
    .article-content strong { font-weight: 700; }
    .article-content em { font-style: italic; }
    .article-content hr { border: none; border-top: 1px solid #e5e7eb; margin: 2em 0; }
</style>
@endpush

@section('content')
    {{-- Header actions --}}
    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-secondary">Modifier</a>
        @if($article->status === 'published')
            <a href="{{ route('hub.articles.show', $article) }}" target="_blank" class="btn btn-primary">Voir côté public ↗</a>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        {{-- SIDEBAR MÉTA --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">Informations</h3></div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    @switch($article->status)
                        @case('published') <span class="badge badge-success" style="font-size: 1rem; padding: 0.5rem 1rem;">Publié</span> @break
                        @case('validated') <span class="badge badge-info" style="font-size: 1rem; padding: 0.5rem 1rem;">Validé</span> @break
                        @case('submitted') <span class="badge badge-warning" style="font-size: 1rem; padding: 0.5rem 1rem;">Soumis</span> @break
                        @default <span class="badge badge-secondary" style="font-size: 1rem; padding: 0.5rem 1rem;">Brouillon</span>
                    @endswitch
                    @if($article->is_featured)
                        <span class="badge badge-info" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-left: 0.5rem;">Vedette</span>
                    @endif
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Vues</div>
                    <div style="font-weight: 500;">{{ number_format($article->views_count ?? 0, 0, ',', ' ') }}</div>
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Auteur</div>
                    <div>{{ $article->author?->name ?? 'Non défini' }}</div>
                </div>

                @if($article->category)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Catégorie</div>
                        <div>{{ $article->category }}</div>
                    </div>
                @endif

                @if($article->slug)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Slug</div>
                        <code style="background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.8125rem;">{{ $article->slug }}</code>
                    </div>
                @endif

                <div style="margin-bottom: 0.75rem;">
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Créé le</div>
                    <div>{{ $article->created_at->format('d/m/Y H:i') }}</div>
                </div>

                @if($article->published_at)
                    <div style="margin-bottom: 0.75rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Publié le</div>
                        <div>{{ $article->published_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif

                @if($article->validated_at)
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 1rem;">
                        <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 0.5rem;">Validation</div>
                        <div style="font-size: 0.875rem;">
                            Validé le {{ $article->validated_at->format('d/m/Y H:i') }}
                            @if($article->validator)
                                par <strong>{{ $article->validator->name }}</strong>
                            @endif
                        </div>
                        @if($article->validation_notes)
                            <blockquote style="margin: 0.75rem 0 0; padding: 0.5rem 0.75rem; border-left: 3px solid #85B79D; background: #f9fafb; font-style: italic; color: #4b5563; font-size: 0.875rem;">
                                {{ $article->validation_notes }}
                            </blockquote>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- BODY --}}
        <div class="card">
            <div class="card-body">
                @if($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}" alt="" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                @endif

                <h1 style="font-size: 1.875rem; font-weight: 700; color: #16302B; margin: 0 0 1rem;">{{ $article->title }}</h1>

                @if($article->summary)
                    <p style="font-size: 1.125rem; font-style: italic; color: #6b7280; margin-bottom: 1.5rem;">{{ $article->summary }}</p>
                @endif

                <div class="article-content">
                    {!! $article->content !!}
                </div>

                @if($article->document_path)
                    <div style="margin-top: 2rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
                        <i data-lucide="file-text" style="width: 24px; height: 24px; color: #356B8A;"></i>
                        <span style="flex: 1; font-weight: 500;">{{ $article->document_name ?? basename($article->document_path) }}</span>
                        <a href="{{ Storage::url($article->document_path) }}" target="_blank" class="btn btn-secondary" style="padding: 0.375rem 0.75rem;">Télécharger</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
