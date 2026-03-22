@extends('layouts.journal')

@section('title', 'Soumettre un article')
@section('meta_description', 'Soumettez votre manuscrit à la revue OREINA. Instructions pour la soumission d\'articles scientifiques.')

@section('content')
    <div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            {{-- Header --}}
            <div class="text-center mb-12">
                <div class="p-4 rounded-2xl bg-oreina-turquoise/10 inline-flex mb-6">
                    <svg class="w-10 h-10 text-oreina-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/>
                    </svg>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-oreina-dark mb-4">Soumettre un article</h1>
                <p class="text-slate-600 max-w-2xl mx-auto">
                    La revue OREINA publie des articles originaux sur les Lépidoptères de France.
                    Tous les manuscrits sont soumis à une évaluation par les pairs.
                </p>
            </div>

            {{-- Process steps --}}
            <div class="bg-white rounded-3xl border border-oreina-beige/50 p-6 sm:p-8 lg:p-12 mb-8">
                <h2 class="text-xl font-bold text-oreina-dark mb-8">Processus de soumission</h2>

                <div class="space-y-6">
                    {{-- Step 1 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-oreina-turquoise text-white flex items-center justify-center font-bold">
                            1
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-oreina-dark mb-2">Préparation du manuscrit</h3>
                            <p class="text-slate-600 text-sm">
                                Rédigez votre manuscrit en suivant les <a href="{{ route('journal.authors') }}" class="text-oreina-turquoise hover:underline">instructions aux auteurs</a>.
                                Assurez-vous que votre article respecte le format et les exigences de la revue.
                            </p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-oreina-turquoise text-white flex items-center justify-center font-bold">
                            2
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-oreina-dark mb-2">Soumission en ligne</h3>
                            <p class="text-slate-600 text-sm">
                                Connectez-vous à votre espace membre et utilisez le formulaire de soumission.
                                Téléchargez votre manuscrit au format Word ou PDF.
                            </p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-oreina-turquoise text-white flex items-center justify-center font-bold">
                            3
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-oreina-dark mb-2">Évaluation par les pairs</h3>
                            <p class="text-slate-600 text-sm">
                                Votre manuscrit sera évalué par des experts du domaine. Ce processus peut prendre
                                plusieurs semaines. Vous recevrez un retour avec les commentaires des relecteurs.
                            </p>
                        </div>
                    </div>

                    {{-- Step 4 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-oreina-turquoise text-white flex items-center justify-center font-bold">
                            4
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-oreina-dark mb-2">Révision et publication</h3>
                            <p class="text-slate-600 text-sm">
                                Si votre article est accepté, vous effectuerez les révisions demandées.
                                Une fois finalisé, l'article sera publié en accès libre dans la revue.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Requirements --}}
            <div class="grid sm:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6">
                    <div class="w-12 h-12 rounded-xl bg-oreina-green/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-oreina-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Articles acceptés</h3>
                    <ul class="text-sm text-slate-600 space-y-2">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-oreina-green flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Articles de recherche originaux
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-oreina-green flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Notes faunistiques
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-oreina-green flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Synthèses taxonomiques
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-oreina-green flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Études de conservation
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6">
                    <div class="w-12 h-12 rounded-xl bg-oreina-turquoise/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-oreina-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Formats acceptés</h3>
                    <ul class="text-sm text-slate-600 space-y-2">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-oreina-turquoise flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Manuscrit : Word (.docx) ou PDF
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-oreina-turquoise flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Figures : JPEG, PNG, TIFF (300 dpi min.)
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-oreina-turquoise flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Tableaux : Excel ou intégrés au manuscrit
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-oreina-turquoise flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Données supplémentaires acceptées
                        </li>
                    </ul>
                </div>
            </div>

            {{-- CTA --}}
            <div class="bg-gradient-to-br from-oreina-teal to-oreina-teal-dark rounded-3xl p-8 text-center text-white">
                <h2 class="text-2xl font-bold mb-4">Prêt à soumettre ?</h2>
                <p class="text-white/80 mb-6 max-w-lg mx-auto">
                    Connectez-vous à votre espace membre pour soumettre votre manuscrit.
                    Pas encore membre ? Rejoignez l'association OREINA.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#" class="px-6 py-3 bg-white text-oreina-teal font-semibold rounded-xl hover:shadow-lg transition">
                        Se connecter
                    </a>
                    <a href="{{ route('hub.membership') }}" class="px-6 py-3 bg-white/10 text-white font-semibold rounded-xl hover:bg-white/20 transition border border-white/30">
                        Devenir membre
                    </a>
                </div>
            </div>

            {{-- Contact --}}
            <div class="mt-8 text-center">
                <p class="text-slate-600">
                    Des questions sur le processus de soumission ?
                    <a href="{{ route('hub.contact') }}" class="text-oreina-turquoise hover:underline font-medium">Contactez-nous</a>
                </p>
            </div>
        </div>
    </div>
@endsection
