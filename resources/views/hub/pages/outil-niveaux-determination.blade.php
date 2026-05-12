@extends('layouts.hub')

@section('title', 'Niveaux de détermination - Référentiel d\'Artemisiae')
@section('meta_description', 'Le référentiel des niveaux de détermination d\'Artemisiae : grille à 7 niveaux + catégories mines/fourreaux pour qualifier la difficulté d\'identification de chaque taxon. Fichier en accès libre, versionné, citable.')

@section('content')
    {{-- Header --}}
    <section class="pt-16 pb-12 bg-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('hub.projets.ident') }}" class="text-sm text-slate-500 hover:text-oreina-coral inline-flex items-center gap-2 transition">
                    <i data-lucide="arrow-left" style="width:14px;height:14px"></i>
                    Retour au projet IDENT
                </a>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="flex-1">
                    <div class="eyebrow gold mb-4 inline-flex">
                        <i class="icon icon-gold" data-lucide="list-tree"></i>
                        Outil IDENT
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-bold text-oreina-dark">Niveaux de détermination</h1>
                    <p class="text-lg sm:text-xl text-slate-600 mt-4 max-w-2xl">
                        Le référentiel qui attribue, taxon par taxon et stade par stade, un niveau de difficulté d'identification — et les exigences documentaires associées sur <em>Artemisiae</em>
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm bg-white p-5 rounded-2xl border border-slate-200 lg:min-w-[420px]">
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Format</p>
                        <p class="font-bold text-oreina-dark">CSV — référentiel + attribution</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Cible</p>
                        <p class="font-bold text-oreina-dark">Observateurs, validateurs, plateformes</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Accès</p>
                        <p class="font-bold text-oreina-dark">Libre, versionné, citable</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wide">Rattachement</p>
                        <p class="font-bold text-oreina-dark">Projet IDENT</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Chapô --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-5 gap-12 items-center">
                <div class="lg:col-span-2">
                    <div class="rounded-3xl shadow-lg flex items-center justify-center bg-gradient-to-br from-oreina-yellow/15 to-oreina-coral/10 relative overflow-hidden" style="min-height: 340px;">
                        <i data-lucide="list-tree" style="width:140px;height:140px;color:#8b6c05;opacity:0.85"></i>
                        <i data-lucide="layers" style="width:36px;height:36px;color:var(--blue);opacity:0.55;position:absolute;top:24px;right:32px"></i>
                        <i data-lucide="file-down" style="width:36px;height:36px;color:var(--coral);opacity:0.55;position:absolute;bottom:32px;left:28px"></i>
                        <i data-lucide="check-circle-2" style="width:32px;height:32px;color:#2f694e;opacity:0.55;position:absolute;bottom:36px;right:36px"></i>
                    </div>
                </div>
                <div class="lg:col-span-3 text-slate-600 space-y-6">
                    <p class="text-xl leading-relaxed">
                        Tous les Lépidoptères n'offrent pas la même prise à l'identification. Certains se reconnaissent d'un coup d'œil ; d'autres réclament l'examen attentif d'une bonne photographie ; d'autres encore ne livrent leur identité qu'au prix d'une préparation génitalique, d'un élevage, d'un enregistrement sonore ou d'un séquençage.
                    </p>
                    <p class="leading-relaxed">
                        Le référentiel des <strong>niveaux de détermination</strong> formalise cette inégalité : il attribue à chaque taxon, et pour chaque stade biologique, un degré de difficulté qui détermine la nature de la preuve à fournir et le niveau de validité atteignable par la donnée. C'est l'outil structurant qu'utilise <em>Artemisiae</em> pour qualifier ses 6 000+ taxons.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Articulation avec la typologie T1-T5 PatriNat --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="git-fork"></i>
                    Articulation
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Un outil opérationnel adossé à la typologie nationale T1-T5</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    La grille présentée ici n'est pas une typologie concurrente de la <strong>typologie T1-T5 développée par PatriNat (MNHN)</strong>, qui structure le projet IDENT à l'échelle nationale. Elle en est <strong>la déclinaison opérationnelle pour la saisie sur <em>Artemisiae</em></strong> : là où T1-T5 décrit la difficulté intrinsèque d'identification d'un taxon (à vue, à vue + manipulation, dissection, séquençage), la grille Artemisiae traduit cette difficulté en <em>exigences documentaires concrètes</em> (preuve à fournir, validation automatique ou manuelle, validité atteignable) et y ajoute des dimensions spécifiques aux Lépidoptères&nbsp;: identification sonore, identification moléculaire, complexes systématiques, mines et fourreaux.
                </p>
                <p>
                    Concrètement, un observateur n'a pas besoin de connaître la typologie PatriNat pour saisir une donnée — il rencontre la grille Artemisiae, qui lui indique directement&nbsp;: «&nbsp;pour cette espèce, à ce stade, une photo couvrant tel critère est attendue&nbsp;». La cohérence est assurée à la conception du référentiel par le COTECH IDENT.
                </p>
            </div>
        </div>
    </section>

    {{-- Trois fonctions --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="target"></i>
                    À quoi sert le référentiel
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Trois fonctions, un seul fichier</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="card p-6 bg-white border-t-4 border-oreina-green">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="user-check"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Aider l'observateur</h3>
                    <p class="text-sm text-slate-600">Comprendre, dès la saisie, le niveau d'exigence attendu&nbsp;: quelle preuve fournir, quel type de critère regarder, à quel stade.</p>
                </div>
                <div class="card p-6 bg-white border-t-4 border-oreina-blue">
                    <div class="pub-card-icon blue mb-4">
                        <i class="icon icon-blue" data-lucide="badge-check"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Outiller le validateur</h3>
                    <p class="text-sm text-slate-600">Standardiser les exigences documentaires (photo, série, dissection, son, barcode) et automatiser ce qui peut l'être pour les taxons aisés.</p>
                </div>
                <div class="card p-6 bg-white border-t-4 border-oreina-coral">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="gauge"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Hiérarchiser la validité</h3>
                    <p class="text-sm text-slate-600">Attribuer aux données un niveau de validité («&nbsp;probable&nbsp;», «&nbsp;très probable&nbsp;») cohérent avec la qualité de la preuve apportée.</p>
                </div>
            </div>

            <div class="mt-8 p-6 bg-white rounded-2xl border-l-4 border-oreina-yellow">
                <p class="text-slate-700 leading-relaxed text-sm">
                    <strong class="text-oreina-dark">L'objectif n'est pas de décourager la saisie des taxons difficiles</strong>, mais d'<strong>afficher honnêtement le coût épistémique</strong> de chaque identification et de garantir la traçabilité scientifique du jeu de données. Une donnée «&nbsp;probable&nbsp;» n'est pas une mauvaise donnée&nbsp;: c'est une donnée dont on dit ce qu'elle peut faire.
                </p>
            </div>
        </div>
    </section>

    {{-- Les 7 niveaux --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="layers-3"></i>
                    La grille
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Sept niveaux d'identification</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Pour les niveaux 1 et 2, la validation peut être automatique. À partir du niveau 3, elle devient nécessairement humaine et exige une preuve documentaire dont la nature est définie a priori.</p>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold text-oreina-dark">Niveau</th>
                            <th class="px-4 py-3 text-left font-bold text-oreina-dark">Libellé</th>
                            <th class="px-4 py-3 text-left font-bold text-oreina-dark">Validation</th>
                            <th class="px-4 py-3 text-left font-bold text-oreina-dark">Exigence documentaire</th>
                            <th class="px-4 py-3 text-left font-bold text-oreina-dark">Validité atteignable</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="px-4 py-4"><span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-oreina-green/15 text-oreina-green font-bold">1</span></td>
                            <td class="px-4 py-4 text-slate-700">Taxon à identification aisée</td>
                            <td class="px-4 py-4"><span class="text-xs font-bold text-oreina-green uppercase">Automatique</span></td>
                            <td class="px-4 py-4 text-slate-600">Aucune preuve à l'appui</td>
                            <td class="px-4 py-4 text-slate-600">« probable »</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-4"><span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-oreina-turquoise/15 text-oreina-turquoise font-bold">2</span></td>
                            <td class="px-4 py-4 text-slate-700">Taxon confondable</td>
                            <td class="px-4 py-4"><span class="text-xs font-bold text-oreina-turquoise uppercase">Automatique</span></td>
                            <td class="px-4 py-4 text-slate-600">Photo visualisant les critères</td>
                            <td class="px-4 py-4 text-slate-600">« probable » à « très probable »</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-4"><span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-oreina-yellow/25 font-bold" style="color:#8b6c05">3</span></td>
                            <td class="px-4 py-4 text-slate-700">Taxon rare</td>
                            <td class="px-4 py-4"><span class="text-xs font-bold uppercase" style="color:#8b6c05">Manuelle</span></td>
                            <td class="px-4 py-4 text-slate-600">Photos couvrant tous les éléments diagnostiques, voire spécimen</td>
                            <td class="px-4 py-4 text-slate-600">« très probable »</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-4"><span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-oreina-coral/15 text-oreina-coral font-bold">4</span></td>
                            <td class="px-4 py-4 text-slate-700">Taxon à détermination difficile</td>
                            <td class="px-4 py-4"><span class="text-xs font-bold text-oreina-coral uppercase">Manuelle</span></td>
                            <td class="px-4 py-4 text-slate-600">Bino + loupe ; dissection souvent nécessaire ; élevage si chenille</td>
                            <td class="px-4 py-4 text-slate-600">« très probable »</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-4"><span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-oreina-blue/15 text-oreina-blue font-bold">5</span></td>
                            <td class="px-4 py-4 text-slate-700">Identification sonore</td>
                            <td class="px-4 py-4"><span class="text-xs font-bold text-oreina-blue uppercase">Manuelle / Auto</span></td>
                            <td class="px-4 py-4 text-slate-600">Enregistrement sonore</td>
                            <td class="px-4 py-4 text-slate-600">« très probable »</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-4"><span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-oreina-blue/15 text-oreina-blue font-bold">6</span></td>
                            <td class="px-4 py-4 text-slate-700">Identification moléculaire</td>
                            <td class="px-4 py-4"><span class="text-xs font-bold text-oreina-blue uppercase">Manuelle</span></td>
                            <td class="px-4 py-4 text-slate-600">Séquence barcode COI</td>
                            <td class="px-4 py-4 text-slate-600">« très probable »</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-4"><span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-slate-200 text-slate-700 font-bold">7</span></td>
                            <td class="px-4 py-4 text-slate-700">Complexes systématiques (« bordéliques »)</td>
                            <td class="px-4 py-4"><span class="text-xs font-bold text-slate-500 uppercase">Approche écologique</span></td>
                            <td class="px-4 py-4 text-slate-600">Proposition du complexe à la saisie (ex.&nbsp;<em>Phengaris</em>)</td>
                            <td class="px-4 py-4 text-slate-600">—</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-8 p-6 bg-slate-50 rounded-2xl border border-slate-200">
                <p class="text-sm text-slate-600 leading-relaxed">
                    <strong class="text-oreina-dark">Un niveau distinct par stade biologique.</strong> Un même taxon peut être attribué à un niveau&nbsp;2 au stade adulte et à un niveau&nbsp;4 au stade chenille, ou inversement. Le référentiel organise donc l'attribution en cinq colonnes&nbsp;: <strong>Adulte, Chenille, Œuf, Chrysalide, Indéterminé</strong>, complétées par une colonne <strong>mine/fourreau</strong>.
                </p>
            </div>
        </div>
    </section>

    {{-- Référentiel additionnel mines / fourreaux --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow sage mb-4 inline-flex">
                    <i class="icon icon-sage" data-lucide="leaf"></i>
                    Référentiel additionnel
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Mines et fourreaux : une grille parallèle</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Les stades larvaires mineurs et fourreaux — typiques de nombreuses familles de Microlépidoptères (Coleophoridae, Gracillariidae, Nepticulidae, Elachistidae…) — appellent une approche spécifique. Quatre catégories ont été définies.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="card p-6 bg-white border-t-4 border-oreina-green">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl font-bold text-oreina-green">A</span>
                        <span class="text-xs font-bold text-oreina-green uppercase">Acceptée</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Sans photo</h3>
                    <p class="text-xs text-slate-600 mb-3">Donnée acceptée sans nécessité de poster une photo (taxon déjà documenté pour l'observateur).</p>
                    <p class="text-xs text-slate-400">Équivalent niveau 2</p>
                </div>
                <div class="card p-6 bg-white border-t-4 border-oreina-turquoise">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl font-bold text-oreina-turquoise">B</span>
                        <span class="text-xs font-bold text-oreina-turquoise uppercase">Bonne photo</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Mine + plante</h3>
                    <p class="text-xs text-slate-600 mb-3">Photo(s) de la mine ou du fourreau + indication de la plante nourricière.</p>
                    <p class="text-xs text-slate-400">Équivalent niveau 2</p>
                </div>
                <div class="card p-6 bg-white border-t-4 border-oreina-yellow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl font-bold" style="color:#8b6c05">B+</span>
                        <span class="text-xs font-bold uppercase" style="color:#8b6c05">Photo enrichie</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Mine + larve</h3>
                    <p class="text-xs text-slate-600 mb-3">Photo(s) de la mine (œuf inclus) ET de la larve/chrysalide + plante nourricière.</p>
                    <p class="text-xs text-slate-400">Équivalent niveau 4</p>
                </div>
                <div class="card p-6 bg-white border-t-4 border-oreina-coral">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl font-bold text-oreina-coral">E</span>
                        <span class="text-xs font-bold text-oreina-coral uppercase">Élevage</span>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Élevage requis</h3>
                    <p class="text-xs text-slate-600 mb-3">Photo(s) + plante nourricière + élevage nécessaire&nbsp;; dissection éventuelle.</p>
                    <p class="text-xs text-slate-400">Équivalent niveau 4</p>
                </div>
            </div>

            <div class="mt-8 p-6 bg-white rounded-2xl border-l-4 border-oreina-coral">
                <p class="text-sm text-slate-700 leading-relaxed mb-3">
                    <strong class="text-oreina-dark">Deux règles transversales encadrent ces catégories&nbsp;:</strong>
                </p>
                <ul class="list-disc pl-8 space-y-2 marker:text-oreina-coral text-sm text-slate-600">
                    <li><span class="pl-2 inline-block">la <strong>mention de la plante-hôte est obligatoire</strong> pour toute donnée de mine — blocage à la saisie en cas d'omission&nbsp;;</span></li>
                    <li><span class="pl-2 inline-block">pour chaque observateur, la <strong>première donnée d'une espèce</strong> doit être accompagnée d'une photo, quel que soit le niveau.</span></li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Méthodologie d'attribution --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="settings-2"></i>
                    Méthodologie
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Comment chaque taxon a-t-il été classé ?</h2>
            </div>

            <div class="text-slate-600 leading-relaxed space-y-6">
                <p>
                    L'attribution est conduite taxon par taxon, en mobilisant quatre sources&nbsp;:
                </p>
                <ul class="list-disc pl-8 space-y-3 marker:text-oreina-blue">
                    <li><span class="pl-2 inline-block">la <strong>littérature de référence</strong> (guides nationaux, monographies, faunes européennes)&nbsp;;</span></li>
                    <li><span class="pl-2 inline-block">la <strong>consultation des plateformes naturalistes européennes</strong> équivalentes, dont en particulier la base <em>Micro-moth Verification Guidance</em> de Butterfly Conservation (Royaume-Uni), qui a servi de référence méthodologique pour la catégorisation des mines et fourreaux&nbsp;;</span></li>
                    <li><span class="pl-2 inline-block">l'expertise des <strong>coordinateurs des quatre observatoires</strong> d'Artemisiae (Rhopalocères, Zygènes, Hétérocères, Microlépidoptères)&nbsp;;</span></li>
                    <li><span class="pl-2 inline-block">pour chaque <strong>stade biologique pertinent</strong>, un niveau distinct (un adulte peut être niveau 2 et sa chenille niveau 4, ou inversement).</span></li>
                </ul>
                <p>
                    Techniquement, le référentiel s'appuie sur deux fichiers&nbsp;: <code class="text-xs bg-slate-100 px-2 py-1 rounded">niveaux_determination.csv</code> (le référentiel lui-même) et <code class="text-xs bg-slate-100 px-2 py-1 rounded">resultats_determination.csv</code> (l'attribution taxon par taxon). Les deux sont intégrés au schéma PostgreSQL d'<em>Artemisiae</em> avec contraintes d'intégrité référentielle, et chaque attribution peut être documentée par un commentaire libre.
                </p>
            </div>
        </div>
    </section>

    {{-- Bilan chiffré --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow coral mb-4 inline-flex">
                    <i class="icon icon-coral" data-lucide="bar-chart-3"></i>
                    Bilan
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Où en est-on aujourd'hui&nbsp;?</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">État du référentiel à la version actuelle. La grille n'est pas figée&nbsp;: elle s'enrichit et se révise au fil des décisions du COTECH IDENT.</p>
            </div>

            {{-- Chiffre global --}}
            <div class="card p-8 bg-white text-center mb-10">
                <p class="text-5xl sm:text-6xl font-bold text-oreina-coral mb-3">6&nbsp;141</p>
                <p class="text-lg text-slate-700">taxons ont reçu une attribution de niveau</p>
                <p class="text-sm text-slate-500 mt-2">répartis entre les quatre observatoires d'<em>Artemisiae</em></p>
            </div>

            {{-- Répartition par observatoire --}}
            <div class="mb-10">
                <h3 class="text-lg font-bold text-oreina-dark mb-4">Répartition par observatoire</h3>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="card p-5 bg-white">
                        <p class="text-3xl font-bold text-oreina-coral mb-1">3&nbsp;240</p>
                        <p class="text-xs uppercase tracking-wide text-slate-400 mb-2">52,8&nbsp;%</p>
                        <p class="text-sm text-slate-700 font-bold">Microlépidoptères</p>
                    </div>
                    <div class="card p-5 bg-white">
                        <p class="text-3xl font-bold text-oreina-blue mb-1">2&nbsp;441</p>
                        <p class="text-xs uppercase tracking-wide text-slate-400 mb-2">39,7&nbsp;%</p>
                        <p class="text-sm text-slate-700 font-bold">Hétérocères</p>
                    </div>
                    <div class="card p-5 bg-white">
                        <p class="text-3xl font-bold text-oreina-green mb-1">357</p>
                        <p class="text-xs uppercase tracking-wide text-slate-400 mb-2">5,8&nbsp;%</p>
                        <p class="text-sm text-slate-700 font-bold">Rhopalocères</p>
                    </div>
                    <div class="card p-5 bg-white">
                        <p class="text-3xl font-bold" style="color:#8b6c05">90</p>
                        <p class="text-xs uppercase tracking-wide text-slate-400 mb-2">1,5&nbsp;%</p>
                        <p class="text-sm text-slate-700 font-bold">Zygènes</p>
                    </div>
                </div>
            </div>

            {{-- Distribution stade adulte --}}
            <div class="mb-10">
                <h3 class="text-lg font-bold text-oreina-dark mb-4">Distribution des niveaux pour le stade adulte</h3>
                <p class="text-sm text-slate-500 mb-5">Sur les 6&nbsp;059 taxons documentés au stade adulte.</p>

                <div class="card p-6 bg-white">
                    <div class="space-y-4">
                        {{-- N1 --}}
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-bold text-slate-700">Niveau 1 — identification aisée</span>
                                <span class="text-slate-500">447&nbsp;taxons · 7,4&nbsp;%</span>
                            </div>
                            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-oreina-green rounded-full" style="width:7.4%"></div>
                            </div>
                        </div>
                        {{-- N2 --}}
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-bold text-slate-700">Niveau 2 — confondable</span>
                                <span class="text-slate-500">2&nbsp;391&nbsp;taxons · 39,5&nbsp;%</span>
                            </div>
                            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-oreina-turquoise rounded-full" style="width:39.5%"></div>
                            </div>
                        </div>
                        {{-- N3 --}}
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-bold text-slate-700">Niveau 3 — rare</span>
                                <span class="text-slate-500">680&nbsp;taxons · 11,2&nbsp;%</span>
                            </div>
                            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-oreina-yellow rounded-full" style="width:11.2%"></div>
                            </div>
                        </div>
                        {{-- N4 --}}
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-bold text-slate-700">Niveau 4 — détermination difficile</span>
                                <span class="text-slate-500">2&nbsp;520&nbsp;taxons · 41,6&nbsp;%</span>
                            </div>
                            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-oreina-coral rounded-full" style="width:41.6%"></div>
                            </div>
                        </div>
                        {{-- N5 --}}
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-bold text-slate-700">Niveau 5 — sonore</span>
                                <span class="text-slate-500">6&nbsp;taxons · 0,1&nbsp;%</span>
                            </div>
                            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-oreina-blue rounded-full" style="width:0.1%; min-width:2px"></div>
                            </div>
                        </div>
                        {{-- N6 --}}
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-bold text-slate-700">Niveau 6 — moléculaire</span>
                                <span class="text-slate-500">14&nbsp;taxons · 0,2&nbsp;%</span>
                            </div>
                            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-oreina-blue rounded-full" style="width:0.2%; min-width:2px"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-slate-600 mt-5 italic">
                    <strong>Lecture&nbsp;:</strong> environ 47&nbsp;% des taxons français se rangent dans les niveaux 1 et 2 (validation automatique potentielle), tandis que <strong>plus de 53&nbsp;% relèvent des niveaux 3 ou supérieurs</strong>, soit une validation humaine obligatoire.
                </p>
            </div>

            {{-- Contraste par observatoire --}}
            <div class="mb-10">
                <h3 class="text-lg font-bold text-oreina-dark mb-4">Une difficulté très contrastée selon les observatoires</h3>
                <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold text-oreina-dark">Observatoire</th>
                                <th class="px-4 py-3 text-right font-bold text-oreina-dark">Niveaux 1+2 (aisé/confondable)</th>
                                <th class="px-4 py-3 text-right font-bold text-oreina-dark">Niveau 4 (difficile)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="px-4 py-4 text-slate-700 font-bold">Rhopalocères</td>
                                <td class="px-4 py-4 text-right text-oreina-green font-bold">80,2&nbsp;%</td>
                                <td class="px-4 py-4 text-right text-slate-600">15,4&nbsp;%</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-4 text-slate-700 font-bold">Hétérocères</td>
                                <td class="px-4 py-4 text-right text-oreina-green font-bold">64,3&nbsp;%</td>
                                <td class="px-4 py-4 text-right text-slate-600">16,3&nbsp;%</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-4 text-slate-700 font-bold">Zygènes</td>
                                <td class="px-4 py-4 text-right text-slate-600">34,4&nbsp;%</td>
                                <td class="px-4 py-4 text-right text-oreina-coral font-bold">64,4&nbsp;%</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-4 text-slate-700 font-bold">Microlépidoptères</td>
                                <td class="px-4 py-4 text-right text-slate-600">30,0&nbsp;%</td>
                                <td class="px-4 py-4 text-right text-oreina-coral font-bold">63,4&nbsp;%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-sm text-slate-600 mt-4 italic">
                    Ce contraste documente quantitativement une réalité bien connue&nbsp;: les Rhopalocères se prêtent à une identification de terrain dans quatre cas sur cinq, tandis que les Zygènes et plus encore les Microlépidoptères exigent une validation experte dans près de deux tiers des cas.
                </p>
            </div>

            {{-- Mines / fourreaux + stades pré-imaginaux --}}
            <div class="grid lg:grid-cols-2 gap-6">
                <div class="card p-6 bg-white">
                    <h3 class="text-lg font-bold text-oreina-dark mb-3">Le pan mines et fourreaux</h3>
                    <p class="text-sm text-slate-600 mb-4">
                        <strong>996 taxons</strong> disposent d'une attribution dans la grille mines/fourreaux, presque exclusivement en Microlépidoptères. La distribution montre une dominance de la catégorie la plus exigeante&nbsp;:
                    </p>
                    <ul class="text-sm text-slate-600 space-y-1.5">
                        <li>• <strong class="text-oreina-coral">E (élevage)</strong>&nbsp;: 651 taxons (65,4&nbsp;%)</li>
                        <li>• <strong style="color:#8b6c05">B+ (mine + larve)</strong>&nbsp;: 215 taxons (21,6&nbsp;%)</li>
                        <li>• <strong class="text-oreina-turquoise">B (mine simple)</strong>&nbsp;: 108 taxons (10,8&nbsp;%)</li>
                        <li>• <strong class="text-oreina-green">A (acceptée)</strong>&nbsp;: 11 taxons (1,1&nbsp;%)</li>
                        <li class="text-slate-400">• Mixtes (B/E, B+/E)&nbsp;: 10 taxons (1,0&nbsp;%)</li>
                    </ul>
                    <p class="text-xs text-slate-500 mt-4 italic">Familles les mieux couvertes&nbsp;: Coleophoridae (296), Gracillariidae (177), Nepticulidae (168), Elachistidae (115).</p>
                </div>

                <div class="card p-6 bg-white">
                    <h3 class="text-lg font-bold text-oreina-dark mb-3">Les stades pré-imaginaux</h3>
                    <p class="text-sm text-slate-600 mb-4">
                        La documentation des stades précoces est encore parcellaire — c'est un chantier en cours. L'écrasante majorité des taxons documentés à ces stades relève du niveau 4.
                    </p>
                    <ul class="text-sm text-slate-600 space-y-2">
                        <li><strong>Chenille</strong>&nbsp;: 1 126 taxons renseignés <span class="text-slate-400">(789 au niveau 4, soit 70&nbsp;%)</span></li>
                        <li><strong>Œuf</strong>&nbsp;: 857 taxons renseignés <span class="text-slate-400">(799 au niveau 4)</span></li>
                        <li><strong>Chrysalide</strong>&nbsp;: 738 taxons renseignés <span class="text-slate-400">(682 au niveau 4)</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Téléchargement et versionnage --}}
    <section class="py-20 bg-gradient-to-br from-oreina-yellow/10 via-white to-oreina-coral/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="eyebrow gold mb-4 inline-flex">
                    <i class="icon icon-gold" data-lucide="file-down"></i>
                    Accès au fichier
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-oreina-dark max-w-3xl mx-auto leading-tight">
                    Le référentiel en libre accès, versionné, citable
                </h2>
                <p class="text-slate-600 mt-6 max-w-3xl mx-auto leading-relaxed">
                    Le référentiel est un objet vivant. Il est révisé au fil des décisions du COTECH IDENT, des compléments documentaires apportés par le réseau et des évolutions taxonomiques (TAXREF). Chaque version est <strong>archivée, datée et identifiable</strong> pour permettre la traçabilité dans les publications et rapports d'étude qui s'y réfèrent.
                </p>
            </div>

            {{-- Encart téléchargement principal --}}
            <div class="card p-8 bg-white shadow-lg max-w-4xl mx-auto mb-10">
                <div class="grid md:grid-cols-3 gap-6 items-center">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-xs font-bold uppercase tracking-wide text-oreina-coral bg-oreina-coral/10 px-3 py-1 rounded-full">Version courante</span>
                            <span class="text-sm text-slate-500">v[X.Y] · [Mois Année]</span>
                        </div>
                        <h3 class="text-xl font-bold text-oreina-dark mb-2">Niveaux de détermination — Lépidoptères de France</h3>
                        <p class="text-sm text-slate-600 mb-4">
                            Référentiel complet (7 niveaux + catégories mines/fourreaux) et attribution taxon par taxon pour les quatre observatoires d'<em>Artemisiae</em>.
                        </p>
                        <p class="text-xs text-slate-500">
                            Format CSV (UTF-8) · Encodage taxonomique TAXREF · Documentation jointe au format PDF
                        </p>
                    </div>
                    <div class="flex flex-col gap-3">
                        <a href="/documents/niveaux-determination/niveaux_determination_courant.csv" class="btn btn-primary">
                            <i class="icon icon-sage" data-lucide="download"></i>
                            Référentiel (CSV)
                        </a>
                        <a href="/documents/niveaux-determination/resultats_determination_courant.csv" class="btn btn-ghost-dark">
                            <i class="icon icon-coral" data-lucide="download"></i>
                            Attribution (CSV)
                        </a>
                        <a href="/documents/niveaux-determination/documentation_courant.pdf" class="btn btn-ghost-dark text-sm">
                            <i class="icon icon-blue" data-lucide="file-text"></i>
                            Documentation (PDF)
                        </a>
                    </div>
                </div>
            </div>

            {{-- Trois colonnes : qu'est-ce qu'on télécharge, comment c'est versionné, comment le citer --}}
            <div class="grid md:grid-cols-3 gap-5">
                <div class="card p-6 bg-white">
                    <div class="pub-card-icon blue mb-4">
                        <i class="icon icon-blue" data-lucide="package"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Ce que contient le téléchargement</h3>
                    <ul class="text-sm text-slate-600 space-y-1.5">
                        <li>• Le <strong>référentiel</strong> (libellés des 7 niveaux et 4 catégories mines/fourreaux)</li>
                        <li>• L'<strong>attribution taxon par taxon</strong>, par stade biologique</li>
                        <li>• Les <strong>commentaires</strong> documentant les choix d'attribution</li>
                        <li>• La <strong>documentation méthodologique</strong> (PDF joint)</li>
                    </ul>
                </div>
                <div class="card p-6 bg-white">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="git-branch"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Politique de versionnage</h3>
                    <p class="text-sm text-slate-600 mb-3">
                        Chaque version est numérotée (vMAJEURE.MINEURE) et accompagnée d'un <strong>journal de changements</strong>&nbsp;:
                    </p>
                    <ul class="text-sm text-slate-600 space-y-1.5">
                        <li>• <strong>Mineure</strong>&nbsp;: compléments d'attribution, corrections ponctuelles</li>
                        <li>• <strong>Majeure</strong>&nbsp;: révision structurelle (libellés, catégories, mise en cohérence TAXREF)</li>
                    </ul>
                    <p class="text-sm text-slate-600 mt-3">Toutes les versions antérieures restent accessibles dans l'archive (cf.&nbsp;ci-dessous).</p>
                </div>
                <div class="card p-6 bg-white">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="quote"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2">Comment citer</h3>
                    <p class="text-sm text-slate-600 mb-3">
                        Pour toute réutilisation dans une publication, un rapport d'étude ou un protocole de validation&nbsp;:
                    </p>
                    <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs text-slate-700 leading-relaxed font-mono">
                        oreina ([Année]). Niveaux de détermination des Lépidoptères de France — Artemisiae, v[X.Y]. [URL]
                    </div>
                    <p class="text-xs text-slate-500 mt-3">Licence&nbsp;: à préciser (Creative Commons recommandée).</p>
                </div>
            </div>

            {{-- Archive des versions --}}
            <div class="mt-10 card p-6 bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-oreina-dark">Archive des versions</h3>
                    <span class="text-xs text-slate-500">Toutes les versions précédentes restent téléchargeables</span>
                </div>
                <div class="divide-y divide-slate-100">
                    {{-- Modèle de ligne à dupliquer dynamiquement --}}
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-slate-700">v[X.Y]</p>
                            <p class="text-xs text-slate-500">[Date] · [résumé des évolutions]</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="#" class="text-xs text-oreina-coral hover:underline inline-flex items-center gap-1"><i data-lucide="download" style="width:12px;height:12px"></i>CSV</a>
                            <a href="#" class="text-xs text-oreina-blue hover:underline inline-flex items-center gap-1"><i data-lucide="file-text" style="width:12px;height:12px"></i>Doc</a>
                        </div>
                    </div>
                    {{-- À dupliquer pour chaque version archivée --}}
                </div>
            </div>
        </div>
    </section>

    {{-- Perspectives --}}
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="eyebrow blue mb-4 inline-flex">
                    <i class="icon icon-blue" data-lucide="compass"></i>
                    Perspectives
                </div>
                <h2 class="text-3xl font-bold text-oreina-dark">Les chantiers ouverts</h2>
                <p class="text-slate-500 mt-3 max-w-3xl">Le référentiel n'est pas figé. Plusieurs chantiers structurent les versions à venir.</p>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="card p-6 bg-slate-50">
                    <div class="pub-card-icon coral mb-3">
                        <i class="icon icon-coral" data-lucide="bug"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Compléter les stades pré-imaginaux</h3>
                    <p class="text-xs text-slate-600">Pour les Hétérocères et Microlépidoptères, dont la documentation chenille/œuf/chrysalide reste parcellaire.</p>
                </div>
                <div class="card p-6 bg-slate-50">
                    <div class="pub-card-icon blue mb-3">
                        <i class="icon icon-blue" data-lucide="audio-waveform"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Affiner sonore et moléculaire</h3>
                    <p class="text-xs text-slate-600">Les niveaux 5 et 6 sont encore marginaux (20 taxons) mais appelés à croître au fil des nouveaux protocoles.</p>
                </div>
                <div class="card p-6 bg-slate-50">
                    <div class="pub-card-icon gold mb-3">
                        <i class="icon icon-gold" data-lucide="git-merge"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Formaliser les complexes (niveau 7)</h3>
                    <p class="text-xs text-slate-600">Liste des complexes systématiques et traitement à la saisie (ex.&nbsp;<em>Phengaris</em>, <em>Pyrgus</em>, agrégats critiques EU-PoMS).</p>
                </div>
                <div class="card p-6 bg-slate-50">
                    <div class="pub-card-icon sage mb-3">
                        <i class="icon icon-sage" data-lucide="eye"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 text-sm">Exposer la grille à la saisie</h3>
                    <p class="text-xs text-slate-600">Afficher le niveau d'exigence attendu directement dans l'interface d'<em>Artemisiae</em>, dès le choix du taxon.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="cta-panel">
                <div class="eyebrow"><i class="icon icon-white" data-lucide="message-circle"></i>Contribuer</div>
                <h2>Faire évoluer le référentiel</h2>
                <p>Vous êtes spécialiste d'un groupe et vous repérez une attribution discutable, un complexe à intégrer, un stade à documenter&nbsp;? Le COTECH IDENT examine les propositions du réseau au fil de l'eau. Chaque retour, chaque cas-limite documenté nourrit la prochaine version.</p>
                <div class="content-actions">
                    <a href="{{ route('hub.contact') }}" class="btn btn-primary">
                        <i class="icon icon-sage" data-lucide="mail"></i>
                        Signaler un cas, proposer une révision
                    </a>
                    <a href="{{ route('hub.projets.ident') }}" class="btn btn-ghost-light">
                        <i class="icon icon-white" data-lucide="arrow-left"></i>
                        Retour au projet IDENT
                    </a>
                </div>
            </article>
        </div>
    </section>

    {{-- Pour aller plus loin --}}
    <section class="py-16 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-oreina-dark">Articulation avec les autres outils oreina</h2>
                <p class="text-slate-500 mt-3 max-w-3xl mx-auto">Le référentiel des niveaux de détermination est l'un des outils du projet IDENT. Il dialogue avec les autres briques de la connaissance et de la qualification des données.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('hub.projets.ident') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon gold mb-4">
                        <i class="icon icon-gold" data-lucide="search"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-coral transition">Projet IDENT</h3>
                    <p class="text-xs text-slate-500">Le cadre méthodologique&nbsp;: typologie T1-T5 PatriNat, agrégats, sympatrie, cartes ABDSM.</p>
                </a>
                <a href="{{ route('hub.outils.labo-lepidos') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon coral mb-4">
                        <i class="icon icon-coral" data-lucide="flask-conical"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-coral transition">Labo Lépidos</h3>
                    <p class="text-xs text-slate-500">Les webinaires dédiés aux complexes d'espèces — la pédagogie qui complète le référentiel.</p>
                </a>
                <a href="{{ route('hub.projets.qualif') }}" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon sage mb-4">
                        <i class="icon icon-sage" data-lucide="badge-check"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-green transition">Projet QUALIF</h3>
                    <p class="text-xs text-slate-500">La qualification des données d'observation, dont la grille est un ingrédient central.</p>
                </a>
                <a href="https://oreina.org/artemisiae/" target="_blank" rel="noopener" class="card p-6 hover:shadow-lg transition group">
                    <div class="pub-card-icon blue mb-4">
                        <i class="icon icon-blue" data-lucide="external-link"></i>
                    </div>
                    <h3 class="font-bold text-oreina-dark mb-2 group-hover:text-oreina-blue transition">Artemisiae</h3>
                    <p class="text-xs text-slate-500">Le portail de saisie où la grille est mobilisée au quotidien par observateurs et validateurs.</p>
                </a>
            </div>
        </div>
    </section>
@endsection
