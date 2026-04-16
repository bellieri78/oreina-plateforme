@extends('layouts.journal')

@section('title', 'Évaluation — ' . Str::limit($review->submission->title, 40))

@section('content')
<div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-oreina-dark">Formulaire d'évaluation</h1>
            <p class="text-slate-600 mt-1">{{ $review->submission->title }}</p>
            @if($review->due_date)
                <p class="text-sm mt-1 {{ $review->due_date->isPast() ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                    Date limite : {{ $review->due_date->format('d/m/Y') }}
                    @if($review->due_date->isPast()) (dépassée) @endif
                </p>
            @endif
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                <ul class="list-disc pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('review.form.store', $review) }}" enctype="multipart/form-data">
            @csrf

            <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6 mb-6">
                <h2 class="text-lg font-bold text-oreina-dark mb-4">Évaluation (1 = faible, 5 = excellent)</h2>

                @foreach([
                    'score_originality' => 'Originalité',
                    'score_methodology' => 'Méthodologie',
                    'score_clarity' => 'Clarté de la rédaction',
                    'score_significance' => 'Importance / pertinence',
                    'score_references' => 'Qualité des références',
                ] as $field => $label)
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-oreina-dark mb-2">{{ $label }}</label>
                        <div class="flex gap-4">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="{{ $field }}" value="{{ $i }}"
                                           @checked(old($field) == $i) required
                                           class="text-oreina-turquoise focus:ring-oreina-turquoise">
                                    <span class="text-sm">{{ $i }}</span>
                                </label>
                            @endfor
                        </div>
                        @error($field)<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>

            <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6 mb-6">
                <h2 class="text-lg font-bold text-oreina-dark mb-4">Commentaires</h2>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-oreina-dark mb-1">
                        Commentaires pour l'auteur <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-slate-500 mb-2">Ces commentaires seront transmis à l'auteur avec la décision.</p>
                    <textarea name="comments_to_author" rows="6" required minlength="50"
                              class="w-full border-slate-300 rounded-lg focus:ring-oreina-turquoise focus:border-oreina-turquoise"
                              placeholder="Points forts, points faibles, suggestions d'amélioration...">{{ old('comments_to_author') }}</textarea>
                    @error('comments_to_author')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-oreina-dark mb-1">
                        Commentaires confidentiels pour l'éditeur
                    </label>
                    <p class="text-xs text-slate-500 mb-2">Ces commentaires ne seront pas transmis à l'auteur.</p>
                    <textarea name="comments_to_editor" rows="4"
                              class="w-full border-slate-300 rounded-lg focus:ring-oreina-turquoise focus:border-oreina-turquoise"
                              placeholder="Remarques confidentielles...">{{ old('comments_to_editor') }}</textarea>
                    @error('comments_to_editor')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6 mb-6">
                <h2 class="text-lg font-bold text-oreina-dark mb-4">Recommandation</h2>

                <div class="mb-4">
                    <select name="recommendation" required
                            class="w-full border-slate-300 rounded-lg focus:ring-oreina-turquoise focus:border-oreina-turquoise">
                        <option value="">— Sélectionner votre recommandation —</option>
                        @foreach(\App\Models\Review::getRecommendations() as $key => $label)
                            <option value="{{ $key }}" @selected(old('recommendation') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('recommendation')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-oreina-dark mb-1">
                        Fichier d'évaluation (PDF, optionnel)
                    </label>
                    <input type="file" name="review_file" accept=".pdf"
                           class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-oreina-turquoise/10 file:text-oreina-dark hover:file:bg-oreina-turquoise/20">
                    @error('review_file')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-oreina-turquoise text-white font-bold rounded-lg hover:bg-oreina-dark transition"
                    onclick="return confirm('Êtes-vous sûr(e) de vouloir soumettre votre évaluation ? Cette action est irréversible.')">
                Soumettre mon évaluation
            </button>
        </form>
    </div>
</div>
@endsection
