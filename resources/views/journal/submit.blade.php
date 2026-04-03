@extends('layouts.journal')

@section('title', 'Soumettre un article')
@section('meta_description', 'Soumettez votre manuscrit à la revue OREINA. Instructions pour la soumission d\'articles scientifiques.')

@section('content')
    <div style="padding: 36px 0;">
        <div class="container">
            {{-- Header --}}
            <div class="text-center mb-12">
                <div class="p-4 rounded-2xl inline-flex mb-6" style="background:var(--accent-surface)">
                    <i data-lucide="upload" style="width:40px;height:40px;color:var(--accent)"></i>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold mb-4">Soumettre un article</h1>
                <p class="text-slate-600 max-w-2xl mx-auto">
                    La revue OREINA publie des articles originaux sur les Lépidoptères de France.
                    Tous les manuscrits sont soumis à une évaluation par les pairs.
                </p>
            </div>

            {{-- Process steps --}}
            <div class="bg-white rounded-3xl border border-oreina-beige/50 p-6 sm:p-8 lg:p-12 mb-8">
                <h2 class="text-xl font-bold mb-8">Processus de soumission</h2>

                <div class="space-y-6">
                    {{-- Step 1 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full text-white flex items-center justify-center font-bold" style="background:var(--accent)">
                            1
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold mb-2">Préparation du manuscrit</h3>
                            <p class="text-slate-600 text-sm">
                                Rédigez votre manuscrit en suivant les <a href="{{ route('journal.authors') }}" class="font-medium hover:underline" style="color:var(--accent)">instructions aux auteurs</a>.
                                Assurez-vous que votre article respecte le format et les exigences de la revue.
                            </p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full text-white flex items-center justify-center font-bold" style="background:var(--accent)">
                            2
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold mb-2">Soumission en ligne</h3>
                            <p class="text-slate-600 text-sm">
                                Connectez-vous à votre espace membre et utilisez le formulaire de soumission.
                                Téléchargez votre manuscrit au format Word ou PDF.
                            </p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full text-white flex items-center justify-center font-bold" style="background:var(--accent)">
                            3
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold mb-2">Évaluation par les pairs</h3>
                            <p class="text-slate-600 text-sm">
                                Votre manuscrit sera évalué par des experts du domaine. Ce processus peut prendre
                                plusieurs semaines. Vous recevrez un retour avec les commentaires des relecteurs.
                            </p>
                        </div>
                    </div>

                    {{-- Step 4 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full text-white flex items-center justify-center font-bold" style="background:var(--accent)">
                            4
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold mb-2">Révision et publication</h3>
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
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background:rgba(44,95,45,0.12)">
                        <i data-lucide="check-circle" style="width:24px;height:24px;color:#2C5F2D"></i>
                    </div>
                    <h3 class="font-bold mb-2">Articles acceptés</h3>
                    <ul class="text-sm text-slate-600 space-y-2">
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;color:#2C5F2D"></i>
                            Articles de recherche originaux
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;color:#2C5F2D"></i>
                            Notes faunistiques
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;color:#2C5F2D"></i>
                            Synthèses taxonomiques
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;color:#2C5F2D"></i>
                            Études de conservation
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-2xl border border-oreina-beige/50 p-6">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background:var(--accent-surface)">
                        <i data-lucide="file-text" style="width:24px;height:24px;color:var(--accent)"></i>
                    </div>
                    <h3 class="font-bold mb-2">Formats acceptés</h3>
                    <ul class="text-sm text-slate-600 space-y-2">
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;color:var(--accent)"></i>
                            Manuscrit : Word (.docx) ou PDF
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;color:var(--accent)"></i>
                            Figures : JPEG, PNG, TIFF (300 dpi min.)
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;color:var(--accent)"></i>
                            Tableaux : Excel ou intégrés au manuscrit
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;color:var(--accent)"></i>
                            Données supplémentaires acceptées
                        </li>
                    </ul>
                </div>
            </div>

            {{-- CTA --}}
            <div class="rounded-3xl p-8 text-center text-white" style="background:linear-gradient(135deg,var(--accent),#0d5c55)">
                <h2 class="text-2xl font-bold mb-4">Prêt à soumettre ?</h2>
                <p class="mb-6 max-w-lg mx-auto" style="color:rgba(255,255,255,0.80)">
                    Connectez-vous à votre espace membre pour soumettre votre manuscrit.
                    Pas encore membre ? Rejoignez l'association OREINA.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#" class="px-6 py-3 font-semibold rounded-xl hover:shadow-lg transition" style="background:white;color:var(--accent)">
                        Se connecter
                    </a>
                    <a href="{{ route('hub.membership') }}" class="btn btn-ghost-light">
                        Devenir membre
                    </a>
                </div>
            </div>

            {{-- Contact --}}
            <div class="mt-8 text-center">
                <p class="text-slate-600">
                    Des questions sur le processus de soumission ?
                    <a href="{{ route('hub.contact') }}" class="font-medium hover:underline" style="color:var(--accent)">Contactez-nous</a>
                </p>
            </div>
        </div>
    </div>
@endsection
