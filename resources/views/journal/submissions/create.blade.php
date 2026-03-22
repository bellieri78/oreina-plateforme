@extends('layouts.journal')

@section('title', 'Nouvelle soumission')
@section('meta_description', 'Soumettez votre manuscrit à la revue OREINA.')

@section('content')
    <div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto">
            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('journal.submissions.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-oreina-turquoise transition mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="m15 18-6-6 6-6"/>
                    </svg>
                    Mes soumissions
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-oreina-dark">Soumettre un article</h1>
                <p class="text-slate-600 mt-2">
                    Remplissez le formulaire ci-dessous pour soumettre votre manuscrit.
                </p>
            </div>

            {{-- Form --}}
            <form action="{{ route('journal.submissions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- Article Info --}}
                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-oreina-dark mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 bg-oreina-turquoise text-white rounded-lg flex items-center justify-center text-sm font-bold">1</span>
                        Informations sur l'article
                    </h2>

                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-bold text-oreina-dark mb-2">
                                Titre de l'article <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                   class="w-full px-4 py-3 bg-slate-50 border-2 border-oreina-beige/30 rounded-xl focus:ring-2 focus:ring-oreina-turquoise focus:border-oreina-turquoise transition"
                                   placeholder="Titre complet de votre article">
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="abstract" class="block text-sm font-bold text-oreina-dark mb-2">
                                Résumé <span class="text-red-500">*</span>
                            </label>
                            <textarea id="abstract" name="abstract" rows="6" required
                                      class="w-full px-4 py-3 bg-slate-50 border-2 border-oreina-beige/30 rounded-xl focus:ring-2 focus:ring-oreina-turquoise focus:border-oreina-turquoise transition resize-none"
                                      placeholder="Résumé de votre article (100 à 3000 caractères)">{{ old('abstract') }}</textarea>
                            <p class="mt-1 text-xs text-slate-500">Entre 100 et 3000 caractères</p>
                            @error('abstract')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="keywords" class="block text-sm font-bold text-oreina-dark mb-2">
                                Mots-clés <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="keywords" name="keywords" value="{{ old('keywords') }}" required
                                   class="w-full px-4 py-3 bg-slate-50 border-2 border-oreina-beige/30 rounded-xl focus:ring-2 focus:ring-oreina-turquoise focus:border-oreina-turquoise transition"
                                   placeholder="Lépidoptères, France, taxonomie, conservation...">
                            <p class="mt-1 text-xs text-slate-500">Séparés par des virgules (5-8 mots-clés recommandés)</p>
                            @error('keywords')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Co-authors --}}
                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-oreina-dark mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 bg-oreina-turquoise text-white rounded-lg flex items-center justify-center text-sm font-bold">2</span>
                        Co-auteurs (optionnel)
                    </h2>

                    <div id="co-authors-container" class="space-y-4">
                        {{-- Co-authors will be added here dynamically --}}
                    </div>

                    <button type="button" id="add-coauthor" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-oreina-turquoise hover:bg-oreina-turquoise/10 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Ajouter un co-auteur
                    </button>
                </div>

                {{-- Manuscript Upload --}}
                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-oreina-dark mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 bg-oreina-turquoise text-white rounded-lg flex items-center justify-center text-sm font-bold">3</span>
                        Manuscrit
                    </h2>

                    <div>
                        <label for="manuscript_file" class="block text-sm font-bold text-oreina-dark mb-2">
                            Fichier PDF <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2">
                            <label for="manuscript_file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-oreina-beige/50 rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-slate-500">
                                        <span class="font-semibold">Cliquez pour télécharger</span> ou glissez-déposez
                                    </p>
                                    <p class="text-xs text-slate-500">PDF uniquement (max. 20 Mo)</p>
                                </div>
                                <input id="manuscript_file" name="manuscript_file" type="file" class="hidden" accept=".pdf" required>
                            </label>
                            <p id="file-name" class="mt-2 text-sm text-oreina-turquoise hidden"></p>
                        </div>
                        @error('manuscript_file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Terms --}}
                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-oreina-dark mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 bg-oreina-turquoise text-white rounded-lg flex items-center justify-center text-sm font-bold">4</span>
                        Conditions de soumission
                    </h2>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="accept_terms" name="accept_terms" required
                                   class="mt-1 w-5 h-5 text-oreina-turquoise border-2 border-oreina-beige/50 rounded focus:ring-oreina-turquoise">
                            <label for="accept_terms" class="text-sm text-slate-600">
                                Je certifie que ce manuscrit est un travail original qui n'a pas été publié ailleurs et n'est pas actuellement soumis à une autre revue. J'accepte les <a href="{{ route('journal.authors') }}" class="text-oreina-turquoise font-semibold hover:underline">conditions de publication</a> de la revue OREINA.
                            </label>
                        </div>
                        @error('accept_terms')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end gap-4">
                    <a href="{{ route('journal.submissions.index') }}" class="px-6 py-3 text-slate-600 font-semibold hover:bg-slate-100 rounded-xl transition">
                        Annuler
                    </a>
                    <button type="submit" class="btn-turquoise px-8 py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m22 2-7 20-4-9-9-4z"/>
                        </svg>
                        Soumettre le manuscrit
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let coAuthorIndex = 0;

        document.getElementById('add-coauthor').addEventListener('click', function() {
            const container = document.getElementById('co-authors-container');
            const div = document.createElement('div');
            div.className = 'grid sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl';
            div.innerHTML = `
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nom complet</label>
                    <input type="text" name="co_authors[${coAuthorIndex}][name]" required
                           class="w-full px-3 py-2 bg-white border border-oreina-beige/30 rounded-lg text-sm focus:ring-2 focus:ring-oreina-turquoise focus:border-oreina-turquoise">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                    <input type="email" name="co_authors[${coAuthorIndex}][email]"
                           class="w-full px-3 py-2 bg-white border border-oreina-beige/30 rounded-lg text-sm focus:ring-2 focus:ring-oreina-turquoise focus:border-oreina-turquoise">
                </div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Affiliation</label>
                        <input type="text" name="co_authors[${coAuthorIndex}][affiliation]"
                               class="w-full px-3 py-2 bg-white border border-oreina-beige/30 rounded-lg text-sm focus:ring-2 focus:ring-oreina-turquoise focus:border-oreina-turquoise">
                    </div>
                    <button type="button" onclick="this.closest('.grid').remove()" class="self-end p-2 text-red-500 hover:bg-red-50 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(div);
            coAuthorIndex++;
        });

        document.getElementById('manuscript_file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const fileNameEl = document.getElementById('file-name');
            if (fileName) {
                fileNameEl.textContent = 'Fichier sélectionné : ' + fileName;
                fileNameEl.classList.remove('hidden');
            } else {
                fileNameEl.classList.add('hidden');
            }
        });
    </script>
    @endpush
@endsection
