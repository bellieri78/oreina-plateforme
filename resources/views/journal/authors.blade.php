@extends('layouts.journal')

@section('title', 'Instructions aux auteurs — Chersotis')
@section('meta_description', 'Guide complet pour la préparation des manuscrits soumis à la revue scientifique Chersotis, publication numérique sur les Lépidoptères de France.')

@section('content')
    <div style="padding: 36px 0;">
        <div class="container">
            {{-- Header --}}
            <div class="mb-12">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 rounded-2xl" style="background:var(--accent-surface)">
                        <i data-lucide="file-text" style="width:28px;height:28px;color:var(--accent)"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold">Instructions aux auteurs</h1>
                        <p class="text-slate-600 mt-1">Guide pour la préparation des manuscrits soumis à Chersotis</p>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="bg-white rounded-3xl border border-oreina-beige/50 overflow-hidden">
                {{-- Table of contents --}}
                <div class="p-6 sm:p-8 border-b border-oreina-beige/50 bg-slate-50">
                    <h2 class="font-bold mb-4">Sommaire</h2>
                    <nav class="space-y-2 text-sm">
                        <a href="#types" class="block text-slate-600 transition" style="hover:color:var(--accent)">1. Types d'articles</a>
                        <a href="#structure" class="block text-slate-600 transition">2. Structure du manuscrit</a>
                        <a href="#format" class="block text-slate-600 transition">3. Format et mise en forme</a>
                        <a href="#figures" class="block text-slate-600 transition">4. Figures et tableaux</a>
                        <a href="#references" class="block text-slate-600 transition">5. Références bibliographiques</a>
                        <a href="#ethics" class="block text-slate-600 transition">6. Éthique et droits d'auteur</a>
                    </nav>
                </div>

                <div class="p-6 sm:p-8 lg:p-12 prose prose-slate max-w-none">
                    {{-- Types d'articles --}}
                    <section id="types" class="mb-12">
                        <h2 class="text-xl font-bold mb-4">1. Types d'articles</h2>
                        <p>
                            Chersotis est une publication scientifique numérique gratuite, exigeante mais accessible,
                            valorisant les travaux inédits sur les Lépidoptères de France. La revue publie les types
                            de contributions suivants :
                        </p>
                        <ul>
                            <li>
                                <strong>Taxonomie</strong> : travaux inédits portant sur la classification,
                                nomenclature et systématique des Lépidoptères de France : descriptions d'espèces
                                nouvelles pour la science, révisions de groupes ou complexes d'espèces, analyses
                                phylogénétiques, synonymies et changements nomenclaturaux, taxonomie intégrative.
                            </li>
                            <li>
                                <strong>Faunistique</strong> : données sur la présence, répartition et statut
                                des espèces sur le territoire français : premières mentions nationales, régionales
                                ou départementales, extensions ou contractions d'aire de répartition documentées,
                                redécouvertes d'espèces considérées comme éteintes localement, bilans faunistiques
                                régionaux avec analyse biogéographique, espèces introduites.
                            </li>
                            <li>
                                <strong>Inventaires</strong> : compilations et analyses de données d'inventaires
                                avec portée scientifique : inventaires exhaustifs, synthèses pluriannuelles avec
                                analyse temporelle, comparaisons inter-sites ou inter-régionales, protocoles
                                d'inventaire.
                            </li>
                            <li>
                                <strong>Travaux de recherche</strong> : études originales répondant à des
                                questions scientifiques précises : écologie des populations et communautés,
                                biologie de la reproduction et développement, interactions plantes-insectes,
                                réponses au changement climatique, génétique des populations, biologie de la
                                conservation.
                            </li>
                            <li>
                                <strong>Écologie et biologie des espèces</strong> : études approfondies du cycle
                                de vie, comportement et relations écologiques : cycles biologiques détaillés avec
                                iconographie, études comportementales documentées, relations plantes-hôtes
                                spécialisées, adaptations à des milieux particuliers, stratégies de survie et
                                reproduction.
                            </li>
                            <li>
                                <strong>Acquisition de données</strong> : méthodes et outils pour l'obtention
                                de données sur les Lépidoptères : techniques d'observation ou de capture innovantes,
                                protocoles de suivi standardisés, méthodes d'identification (génétique, chimique...),
                                outils informatiques d'analyse, techniques de conservation des spécimens.
                            </li>
                        </ul>

                        <div class="mt-6 p-4 rounded-xl border bg-slate-50 border-slate-200 not-prose">
                            <p class="text-sm text-slate-600">
                                <i data-lucide="info" style="width:16px;height:16px;display:inline;vertical-align:text-bottom;color:var(--accent)"></i>
                                <strong>Critères de sélection :</strong> les manuscrits doivent présenter des données inédites
                                ou des synthèses approfondies, une méthodologie rigoureuse avec références bibliographiques,
                                une portée dépassant l'anecdotique ou le local, et contribuer à l'avancement des connaissances.
                            </p>
                        </div>
                    </section>

                    {{-- Structure --}}
                    <section id="structure" class="mb-12">
                        <h2 class="text-xl font-bold mb-4">2. Structure du manuscrit</h2>
                        <p>Tout manuscrit doit comprendre :</p>

                        <h3 class="text-lg font-semibold mt-6 mb-3">Page de titre</h3>
                        <ul>
                            <li>Titre concis et informatif (max. 150 caractères)</li>
                            <li>Nom(s) et prénom(s) des auteurs</li>
                            <li>Affiliations et adresses</li>
                            <li>Adresse e-mail de l'auteur correspondant</li>
                        </ul>

                        <h3 class="text-lg font-semibold mt-6 mb-3">Résumé</h3>
                        <ul>
                            <li>Résumé en français (max. 300 mots)</li>
                            <li>Résumé en anglais (Abstract)</li>
                            <li>5 à 8 mots-clés dans les deux langues</li>
                        </ul>

                        <h3 class="text-lg font-semibold mt-6 mb-3">Corps de l'article</h3>
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
                        <h2 class="text-xl font-bold mb-4">3. Format et mise en forme</h2>
                        <ul>
                            <li>Police : Times New Roman 12 pt ou équivalent</li>
                            <li>Interligne : double</li>
                            <li>Marges : 2,5 cm sur tous les côtés</li>
                            <li>Pages numérotées en continu</li>
                            <li>Lignes numérotées pour faciliter la relecture</li>
                        </ul>

                        <h3 class="text-lg font-semibold mt-6 mb-3">Nomenclature</h3>
                        <ul>
                            <li>Noms scientifiques en italique</li>
                            <li>Auteur et date à la première mention</li>
                            <li>Suivre le Code International de Nomenclature Zoologique</li>
                        </ul>
                    </section>

                    {{-- Figures --}}
                    <section id="figures" class="mb-12">
                        <h2 class="text-xl font-bold mb-4">4. Figures et tableaux</h2>

                        <h3 class="text-lg font-semibold mt-6 mb-3">Figures</h3>
                        <ul>
                            <li>Résolution minimale : 300 dpi pour les photographies</li>
                            <li>Formats acceptés : TIFF, JPEG, PNG</li>
                            <li>Fichiers séparés du manuscrit principal</li>
                            <li>Légendes groupées à la fin du manuscrit</li>
                        </ul>

                        <h3 class="text-lg font-semibold mt-6 mb-3">Tableaux</h3>
                        <ul>
                            <li>Format Word ou Excel</li>
                            <li>Titre au-dessus du tableau</li>
                            <li>Notes explicatives en dessous</li>
                            <li>Éviter les traits verticaux</li>
                        </ul>
                    </section>

                    {{-- References --}}
                    <section id="references" class="mb-12">
                        <h2 class="text-xl font-bold mb-4">5. Références bibliographiques</h2>
                        <p>Utiliser le style auteur-date dans le texte :</p>
                        <ul>
                            <li>Un auteur : (Dupont, 2020)</li>
                            <li>Deux auteurs : (Dupont & Martin, 2020)</li>
                            <li>Plus de deux : (Dupont et al., 2020)</li>
                        </ul>

                        <h3 class="text-lg font-semibold mt-6 mb-3">Format des références</h3>
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
                        <h2 class="text-xl font-bold mb-4">6. Éthique et droits d'auteur</h2>
                        <ul>
                            <li>Les manuscrits doivent être originaux et non publiés ailleurs</li>
                            <li>Les auteurs garantissent détenir les droits sur les figures soumises</li>
                            <li>Toute utilisation de données ou images d'autrui doit être autorisée</li>
                            <li>Les conflits d'intérêts potentiels doivent être déclarés</li>
                        </ul>

                        <div class="mt-6 p-4 rounded-xl border" style="background:var(--accent-surface);border-color:rgba(15,118,110,0.20)">
                            <p class="text-sm" style="color:var(--accent)">
                                <strong>Licence :</strong> Les articles publiés dans Chersotis sont diffusés sous licence
                                Creative Commons Attribution (CC BY 4.0), permettant une réutilisation libre avec attribution.
                            </p>
                        </div>
                    </section>
                </div>
            </div>

            {{-- Download template --}}
            <div class="mt-8 bg-white rounded-2xl border border-oreina-beige/50 p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold">Modèle de manuscrit</h3>
                    <p class="text-sm text-slate-600">Téléchargez notre modèle Word pour faciliter la mise en forme.</p>
                </div>
                <a href="#" class="btn btn-primary whitespace-nowrap">
                    <i data-lucide="download"></i>
                    Télécharger le modèle
                </a>
            </div>

            {{-- CTA --}}
            <div class="mt-8 p-6 rounded-2xl text-center" style="background:var(--accent-surface); border: 1px solid rgba(15,118,110,0.15);">
                <p class="text-sm mb-4" style="color:var(--accent)">
                    <i data-lucide="info" style="width:16px;height:16px;display:inline;vertical-align:text-bottom;"></i>
                    <strong>Pas besoin d'être adhérent pour soumettre un article.</strong> Il suffit de créer un compte gratuit sur la plateforme OREINA.
                </p>
                <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                    <a href="{{ route('journal.submit') }}" class="btn btn-primary">
                        <i data-lucide="file-plus"></i>
                        Soumettre un article
                    </a>
                    <a href="{{ route('hub.login') }}" class="btn btn-secondary">
                        <i data-lucide="user-round-plus"></i>
                        Créer un compte
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
