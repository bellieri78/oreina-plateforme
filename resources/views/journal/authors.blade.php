@extends('layouts.journal')

@section('title', 'Instructions aux auteurs')
@section('meta_description', 'Guide complet pour la préparation des manuscrits soumis à la revue OREINA.')

@section('content')
    <div class="py-8 sm:py-12 px-4 sm:px-6 lg:px-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            {{-- Header --}}
            <div class="mb-12">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 rounded-2xl bg-oreina-turquoise/10">
                        <svg class="w-7 h-7 text-oreina-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-oreina-dark">Instructions aux auteurs</h1>
                        <p class="text-slate-600 mt-1">Guide pour la préparation des manuscrits</p>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="bg-white rounded-3xl border border-oreina-beige/50 overflow-hidden">
                {{-- Table of contents --}}
                <div class="p-6 sm:p-8 border-b border-oreina-beige/50 bg-slate-50">
                    <h2 class="font-bold text-oreina-dark mb-4">Sommaire</h2>
                    <nav class="space-y-2 text-sm">
                        <a href="#types" class="block text-slate-600 hover:text-oreina-turquoise transition">1. Types d'articles</a>
                        <a href="#structure" class="block text-slate-600 hover:text-oreina-turquoise transition">2. Structure du manuscrit</a>
                        <a href="#format" class="block text-slate-600 hover:text-oreina-turquoise transition">3. Format et mise en forme</a>
                        <a href="#figures" class="block text-slate-600 hover:text-oreina-turquoise transition">4. Figures et tableaux</a>
                        <a href="#references" class="block text-slate-600 hover:text-oreina-turquoise transition">5. Références bibliographiques</a>
                        <a href="#ethics" class="block text-slate-600 hover:text-oreina-turquoise transition">6. Éthique et droits d'auteur</a>
                    </nav>
                </div>

                <div class="p-6 sm:p-8 lg:p-12 prose prose-slate max-w-none">
                    {{-- Types d'articles --}}
                    <section id="types" class="mb-12">
                        <h2 class="text-xl font-bold text-oreina-dark mb-4">1. Types d'articles</h2>
                        <p>La revue OREINA publie différents types de contributions :</p>
                        <ul>
                            <li><strong>Articles de recherche</strong> : études originales avec méthodologie complète (max. 10 000 mots)</li>
                            <li><strong>Notes faunistiques</strong> : observations remarquables, nouvelles données de répartition (max. 3 000 mots)</li>
                            <li><strong>Synthèses taxonomiques</strong> : révisions, descriptions d'espèces nouvelles</li>
                            <li><strong>Articles de conservation</strong> : études sur la protection des espèces et habitats</li>
                        </ul>
                    </section>

                    {{-- Structure --}}
                    <section id="structure" class="mb-12">
                        <h2 class="text-xl font-bold text-oreina-dark mb-4">2. Structure du manuscrit</h2>
                        <p>Tout manuscrit doit comprendre :</p>

                        <h3 class="text-lg font-semibold text-oreina-dark mt-6 mb-3">Page de titre</h3>
                        <ul>
                            <li>Titre concis et informatif (max. 150 caractères)</li>
                            <li>Nom(s) et prénom(s) des auteurs</li>
                            <li>Affiliations et adresses</li>
                            <li>Adresse e-mail de l'auteur correspondant</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-oreina-dark mt-6 mb-3">Résumé</h3>
                        <ul>
                            <li>Résumé en français (max. 300 mots)</li>
                            <li>Résumé en anglais (Abstract)</li>
                            <li>5 à 8 mots-clés dans les deux langues</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-oreina-dark mt-6 mb-3">Corps de l'article</h3>
                        <p>Pour les articles de recherche, suivre la structure IMRAD :</p>
                        <ul>
                            <li><strong>Introduction</strong> : contexte, objectifs</li>
                            <li><strong>Matériel et méthodes</strong> : protocole détaillé</li>
                            <li><strong>Résultats</strong> : présentation objective des données</li>
                            <li><strong>Discussion</strong> : interprétation, comparaisons</li>
                            <li><strong>Conclusion</strong> : synthèse et perspectives</li>
                            <li><strong>Remerciements</strong> : contributions, financements</li>
                            <li><strong>Références</strong> : bibliographie citée</li>
                        </ul>
                    </section>

                    {{-- Format --}}
                    <section id="format" class="mb-12">
                        <h2 class="text-xl font-bold text-oreina-dark mb-4">3. Format et mise en forme</h2>
                        <ul>
                            <li>Police : Times New Roman 12 pt ou équivalent</li>
                            <li>Interligne : double</li>
                            <li>Marges : 2,5 cm sur tous les côtés</li>
                            <li>Pages numérotées en continu</li>
                            <li>Lignes numérotées pour faciliter la relecture</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-oreina-dark mt-6 mb-3">Nomenclature</h3>
                        <ul>
                            <li>Noms scientifiques en italique</li>
                            <li>Auteur et date à la première mention</li>
                            <li>Suivre le Code International de Nomenclature Zoologique</li>
                        </ul>
                    </section>

                    {{-- Figures --}}
                    <section id="figures" class="mb-12">
                        <h2 class="text-xl font-bold text-oreina-dark mb-4">4. Figures et tableaux</h2>

                        <h3 class="text-lg font-semibold text-oreina-dark mt-6 mb-3">Figures</h3>
                        <ul>
                            <li>Résolution minimale : 300 dpi pour les photographies</li>
                            <li>Formats acceptés : TIFF, JPEG, PNG</li>
                            <li>Fichiers séparés du manuscrit principal</li>
                            <li>Légendes groupées à la fin du manuscrit</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-oreina-dark mt-6 mb-3">Tableaux</h3>
                        <ul>
                            <li>Format Word ou Excel</li>
                            <li>Titre au-dessus du tableau</li>
                            <li>Notes explicatives en dessous</li>
                            <li>Éviter les traits verticaux</li>
                        </ul>
                    </section>

                    {{-- References --}}
                    <section id="references" class="mb-12">
                        <h2 class="text-xl font-bold text-oreina-dark mb-4">5. Références bibliographiques</h2>
                        <p>Utiliser le style auteur-date dans le texte :</p>
                        <ul>
                            <li>Un auteur : (Dupont, 2020)</li>
                            <li>Deux auteurs : (Dupont & Martin, 2020)</li>
                            <li>Plus de deux : (Dupont et al., 2020)</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-oreina-dark mt-6 mb-3">Format des références</h3>
                        <p><strong>Article de revue :</strong></p>
                        <p class="text-sm bg-slate-50 p-3 rounded-lg">
                            Dupont J. & Martin P. 2020. — Titre de l'article. <em>Nom de la revue</em> 12(3): 45-67.
                        </p>

                        <p class="mt-4"><strong>Livre :</strong></p>
                        <p class="text-sm bg-slate-50 p-3 rounded-lg">
                            Dupont J. 2020. — <em>Titre du livre</em>. Éditeur, Ville. 320 p.
                        </p>

                        <p class="mt-4"><strong>Chapitre de livre :</strong></p>
                        <p class="text-sm bg-slate-50 p-3 rounded-lg">
                            Dupont J. 2020. — Titre du chapitre, <em>in</em> Martin P. (éd.), <em>Titre du livre</em>. Éditeur, Ville: 45-78.
                        </p>
                    </section>

                    {{-- Ethics --}}
                    <section id="ethics">
                        <h2 class="text-xl font-bold text-oreina-dark mb-4">6. Éthique et droits d'auteur</h2>
                        <ul>
                            <li>Les manuscrits doivent être originaux et non publiés ailleurs</li>
                            <li>Les auteurs garantissent détenir les droits sur les figures soumises</li>
                            <li>Toute utilisation de données ou images d'autrui doit être autorisée</li>
                            <li>Les conflits d'intérêts potentiels doivent être déclarés</li>
                        </ul>

                        <div class="mt-6 p-4 bg-oreina-turquoise/10 rounded-xl border border-oreina-turquoise/20">
                            <p class="text-sm text-oreina-teal">
                                <strong>Licence :</strong> Les articles publiés dans OREINA sont diffusés sous licence
                                Creative Commons Attribution (CC BY 4.0), permettant une réutilisation libre avec attribution.
                            </p>
                        </div>
                    </section>
                </div>
            </div>

            {{-- Download template --}}
            <div class="mt-8 bg-white rounded-2xl border border-oreina-beige/50 p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-oreina-dark">Modèle de manuscrit</h3>
                    <p class="text-sm text-slate-600">Téléchargez notre modèle Word pour faciliter la mise en forme.</p>
                </div>
                <a href="#" class="btn-turquoise whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger le modèle
                </a>
            </div>

            {{-- CTA --}}
            <div class="mt-8 text-center">
                <a href="{{ route('journal.submit') }}" class="btn-turquoise inline-flex items-center gap-2">
                    Soumettre un article
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endsection
