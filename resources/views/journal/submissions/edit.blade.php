@extends('layouts.journal')

@section('title', 'Soumettre une révision')
@section('meta_description', 'Soumettez une version révisée de votre manuscrit.')

@section('content')
    <div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto">
            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('journal.submissions.show', $submission) }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-oreina-turquoise transition mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="m15 18-6-6 6-6"/>
                    </svg>
                    Retour aux détails
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-oreina-dark">Soumettre une révision</h1>
                <p class="text-slate-600 mt-2">
                    Soumettez une version révisée de votre manuscrit en tenant compte des commentaires reçus.
                </p>
            </div>

            {{-- Original submission info --}}
            <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6 mb-6">
                <h2 class="text-lg font-bold text-oreina-dark mb-3">{{ $submission->title }}</h2>
                <p class="text-sm text-slate-600 line-clamp-3">{{ Str::limit($submission->abstract, 300) }}</p>

                @if($submission->editor_notes)
                    <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                        <h3 class="font-semibold text-orange-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Commentaires de l'éditeur
                        </h3>
                        <div class="text-sm text-orange-700 leading-relaxed">
                            {!! nl2br(e($submission->editor_notes)) !!}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Revision form --}}
            <form action="{{ route('journal.submissions.update', $submission) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- New manuscript upload --}}
                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-oreina-dark mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 bg-oreina-turquoise text-white rounded-lg flex items-center justify-center text-sm font-bold">1</span>
                        Nouveau manuscrit
                    </h2>

                    <div>
                        <label for="manuscript_file" class="block text-sm font-bold text-oreina-dark mb-2">
                            Fichier PDF révisé <span class="text-red-500">*</span>
                        </label>
                        <p class="text-sm text-slate-500 mb-4">
                            Téléchargez votre manuscrit révisé. Ce fichier remplacera la version précédente.
                        </p>
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

                {{-- Revision notes --}}
                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-oreina-dark mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 bg-oreina-turquoise text-white rounded-lg flex items-center justify-center text-sm font-bold">2</span>
                        Notes de révision (optionnel)
                    </h2>

                    <div>
                        <label for="revision_notes" class="block text-sm font-bold text-oreina-dark mb-2">
                            Réponse aux commentaires
                        </label>
                        <p class="text-sm text-slate-500 mb-4">
                            Expliquez les modifications apportées en réponse aux commentaires de l'éditeur et/ou des évaluateurs.
                        </p>
                        <textarea id="revision_notes" name="revision_notes" rows="6"
                                  class="w-full px-4 py-3 bg-slate-50 border-2 border-oreina-beige/30 rounded-xl focus:ring-2 focus:ring-oreina-turquoise focus:border-oreina-turquoise transition resize-none"
                                  placeholder="Décrivez les modifications apportées...">{{ old('revision_notes') }}</textarea>
                        @error('revision_notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Important note --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Information importante</p>
                            <p>Une fois soumis, votre manuscrit révisé sera renvoyé à l'éditeur pour évaluation. Vous serez notifié de la nouvelle décision par email.</p>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end gap-4">
                    <a href="{{ route('journal.submissions.show', $submission) }}" class="px-6 py-3 text-slate-600 font-semibold hover:bg-slate-100 rounded-xl transition">
                        Annuler
                    </a>
                    <button type="submit" class="btn-turquoise px-8 py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="m22 2-7 20-4-9-9-4z"/>
                        </svg>
                        Soumettre la révision
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
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
