@extends('layouts.journal')

@section('title', 'Documentation éditoriale')
@section('meta_description', 'Documentation du workflow éditorial de la revue Chersotis — guide pour les membres du comité et les auteurs.')

@push('styles')
<style>
/* ============================================
   Chersotis workflow — timeline + cards
   (styles repris de la doc extranet)
   ============================================ */
.chersotis-wf {
    background: #fff;
    border: 1px solid rgba(219, 203, 199, 0.5);
    border-radius: 1rem;
    padding: 1.5rem;
    margin: 1.25rem 0;
}

.chersotis-wf-timeline {
    display: flex;
    align-items: flex-start;
    gap: 0;
}

.chersotis-wf-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 0 0 auto;
    min-width: 92px;
    text-align: center;
}

.chersotis-wf-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #14B8A6;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(20, 184, 166, 0.18);
    transition: transform 0.2s;
}
.chersotis-wf-step:hover .chersotis-wf-circle { transform: scale(1.05); }
.chersotis-wf-step.published .chersotis-wf-circle {
    background: #0f766e;
    box-shadow: 0 4px 10px rgba(15, 118, 110, 0.22);
}

.chersotis-wf-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: #16302B;
    margin-top: 0.5rem;
    line-height: 1.2;
}
.chersotis-wf-actor {
    font-size: 0.7rem;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 9999px;
    padding: 2px 8px;
    margin-top: 0.35rem;
    white-space: nowrap;
}
.chersotis-wf-line {
    flex: 1 1 auto;
    height: 2px;
    background: #14B8A6;
    margin-top: 23px;
    min-width: 12px;
    max-width: 60px;
    align-self: flex-start;
    opacity: 0.7;
    border-radius: 2px;
}

.chersotis-wf-branches {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px dashed rgba(219, 203, 199, 0.6);
}
.chersotis-wf-branch {
    border-radius: 0.75rem;
    padding: 1rem 1.25rem;
    border: 1px solid;
}
.chersotis-wf-branch.revision {
    background: #fff7ed;
    border-color: #fed7aa;
}
.chersotis-wf-branch.rejected {
    background: #fef2f2;
    border-color: #fecaca;
}
.chersotis-wf-branch-head {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    font-size: 0.9rem;
    margin-bottom: 0.35rem;
}
.chersotis-wf-branch.revision .chersotis-wf-branch-head { color: #c2410c; }
.chersotis-wf-branch.rejected .chersotis-wf-branch-head { color: #b91c1c; }
.chersotis-wf-branch p {
    color: #475569;
    font-size: 0.85rem;
    line-height: 1.55;
    margin: 0;
}

.chersotis-steps {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    margin: 1.5rem 0;
}
@media (min-width: 768px) {
    .chersotis-steps { grid-template-columns: repeat(2, 1fr); }
}
.chersotis-step {
    background: #fff;
    border: 1px solid rgba(219, 203, 199, 0.5);
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    transition: box-shadow 0.2s, border-color 0.2s;
}
.chersotis-step:hover {
    box-shadow: 0 6px 18px rgba(22, 48, 43, 0.06);
    border-color: rgba(219, 203, 199, 0.9);
}
.chersotis-step-head {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding-bottom: 0.9rem;
    margin-bottom: 0.9rem;
    border-bottom: 1px solid rgba(219, 203, 199, 0.4);
}
.chersotis-step-num {
    flex: 0 0 auto;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.95rem;
}
.chersotis-step.submitted    .chersotis-step-num { background: #dbeafe; color: #1d4ed8; }
.chersotis-step.desk-review  .chersotis-step-num { background: #fef9c3; color: #a16207; }
.chersotis-step.in-review    .chersotis-step-num { background: #e0e7ff; color: #4338ca; }
.chersotis-step.revision     .chersotis-step-num { background: #ffedd5; color: #c2410c; }
.chersotis-step.accepted     .chersotis-step-num { background: #dcfce7; color: #15803d; }
.chersotis-step.approval     .chersotis-step-num { background: #ede9fe; color: #6d28d9; }
.chersotis-step.published    .chersotis-step-num { background: rgba(20, 184, 166, 0.18); color: #0f766e; }
.chersotis-step-title {
    font-weight: 700;
    color: #16302B;
    margin: 0;
    font-size: 1rem;
    line-height: 1.25;
}
.chersotis-step-body p {
    font-size: 0.9rem;
    color: #334155;
    line-height: 1.6;
    margin-bottom: 0.5rem;
}
.chersotis-step-body p:last-child { margin-bottom: 0; }
.chersotis-step-body ul, .chersotis-step-body ol {
    font-size: 0.9rem;
    color: #334155;
    margin: 0.35rem 0 0.6rem 0;
    padding-left: 1.25rem;
}
.chersotis-step-body li { margin-bottom: 0.3rem; line-height: 1.55; }

/* Docs table */
.docs-table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
    font-size: 0.9rem;
    background: #fff;
    border-radius: 0.75rem;
    overflow: hidden;
    border: 1px solid rgba(219, 203, 199, 0.5);
}
.docs-table th,
.docs-table td {
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: 1px solid rgba(219, 203, 199, 0.4);
    vertical-align: top;
}
.docs-table th {
    background: #faf8f5;
    font-weight: 700;
    color: #16302B;
}
.docs-table tr:last-child td { border-bottom: 0; }

/* Badges */
.docs-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.docs-badge.success { background: #dcfce7; color: #15803d; }
.docs-badge.warning { background: #fef3c7; color: #a16207; }
.docs-badge.danger  { background: #fee2e2; color: #b91c1c; }
.docs-badge.info    { background: #dbeafe; color: #1d4ed8; }
.docs-badge.neutral { background: #f1f5f9; color: #475569; }

/* Info box */
.docs-info {
    background: var(--accent-surface);
    border-left: 4px solid var(--accent);
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    margin: 1rem 0;
    font-size: 0.9rem;
    color: #0f3631;
}
.docs-info strong { color: var(--accent); }

/* Section cards */
.docs-section {
    background: #fff;
    border: 1px solid rgba(219, 203, 199, 0.5);
    border-radius: 28px;
    padding: 2rem;
    margin-bottom: 1.5rem;
}
@media (min-width: 640px) { .docs-section { padding: 2.5rem; } }
@media (min-width: 1024px) { .docs-section { padding: 3rem; } }
.docs-section h2 {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0 0 1rem 0;
    color: #16302B;
}
.docs-section h3 {
    font-size: 1.15rem;
    font-weight: 700;
    margin: 1.75rem 0 0.75rem 0;
    color: #16302B;
}
.docs-section h4 {
    font-size: 1rem;
    font-weight: 700;
    margin: 1.25rem 0 0.5rem 0;
    color: #16302B;
}
.docs-section p,
.docs-section li {
    line-height: 1.65;
}
.docs-section > p:first-of-type {
    font-size: 1.02rem;
    color: #334155;
}

/* Mobile */
@media (max-width: 768px) {
    .chersotis-wf { padding: 1.25rem; }
    .chersotis-wf-timeline { flex-direction: column; align-items: center; gap: 0; }
    .chersotis-wf-step { min-width: unset; }
    .chersotis-wf-line {
        width: 2px;
        height: 28px;
        min-height: 28px;
        max-width: unset;
        margin-top: 0;
        align-self: center;
    }
    .chersotis-wf-branches { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div style="padding: 36px 0;">
    <div class="container" style="max-width: 1000px;">

        {{-- Bandeau d'accès temporaire --}}
        <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 0.75rem; padding: 1rem 1.25rem; margin-bottom: 2rem; display: flex; gap: 0.85rem; align-items: flex-start;">
            <i data-lucide="info" style="width:22px;height:22px;color:#c2410c;margin-top:2px;flex-shrink:0;"></i>
            <div>
                <p style="margin: 0; font-weight: 700; color: #c2410c;">Accès temporaire — Groupe de travail</p>
                <p style="margin: 0.3rem 0 0 0; font-size: 0.9rem; color: #7c2d12;">
                    Cette documentation est partagée publiquement le temps que les membres du comité éditorial rejoignent la plateforme. Une fois vos comptes créés, la documentation définitive sera accessible depuis votre espace personnel sur oreina.org.
                </p>
            </div>
        </div>

        {{-- Header --}}
        <div class="text-center mb-12">
            <div class="p-4 rounded-2xl inline-flex mb-6" style="background:var(--accent-surface)">
                <i data-lucide="book-open" style="width:40px;height:40px;color:var(--accent)"></i>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold mb-4">Documentation éditoriale</h1>
            <p class="text-slate-600 max-w-2xl mx-auto">
                Guide du workflow éditorial de la revue <strong>Chersotis</strong> : de la soumission d'un manuscrit à la publication en ligne.
            </p>
        </div>

        {{-- Sommaire --}}
        <nav class="bg-white rounded-2xl border border-oreina-beige/50 p-6 mb-8">
            <h2 class="text-lg font-bold mb-4" style="margin:0 0 1rem 0;">Sommaire</h2>
            <ul style="list-style:none;padding:0;margin:0;display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:0.5rem 1rem;">
                <li><a href="#workflow" style="color:var(--accent);">→ Workflow éditorial</a></li>
                <li><a href="#roles" style="color:var(--accent);">→ Rôles et responsabilités</a></li>
                <li><a href="#soumission" style="color:var(--accent);">→ Contenu d'une soumission</a></li>
                <li><a href="#relecture" style="color:var(--accent);">→ Circuit de relecture</a></li>
                <li><a href="#decisions" style="color:var(--accent);">→ Décisions éditoriales</a></li>
                <li><a href="#suivi-auteur" style="color:var(--accent);">→ Suivi par l'auteur</a></li>
                <li><a href="#capacites" style="color:var(--accent);">→ Capacités éditoriales</a></li>
                <li><a href="#publication" style="color:var(--accent);">→ Publication, DOI et citations</a></li>
                <li><a href="#numeros" style="color:var(--accent);">→ Numéros de la revue</a></li>
            </ul>
        </nav>

        {{-- ========================================= --}}
        {{-- 1. Workflow éditorial                    --}}
        {{-- ========================================= --}}
        <section id="workflow" class="docs-section">
            <h2>Workflow éditorial</h2>
            <p>
                La revue <strong>Chersotis</strong> est le journal scientifique de l'association OREINA, consacré aux Lépidoptères de France. Le workflow couvre l'ensemble du cycle de vie d'un manuscrit, depuis la soumission par l'auteur jusqu'à la publication en ligne avec attribution d'un DOI.
            </p>
            <p>
                Plusieurs rôles interviennent : l'<strong>auteur</strong>, l'<strong>éditeur</strong> (responsable d'une soumission, pilote le peer review et la décision éditoriale), les <strong>relecteurs</strong> (reviewers experts), le <strong>maquettiste</strong> (mise en page finale) et le <strong>rédacteur en chef</strong> (supervision du comité, attribution des articles aux éditeurs). Le rédacteur en chef peut cumuler la capacité d'éditeur, mais les deux rôles sont distincts : un article est piloté par un éditeur donné, qui n'est pas nécessairement le rédacteur en chef.
            </p>

            <div class="docs-info">
                <strong>À noter :</strong> l'adhésion à OREINA n'est pas requise pour soumettre un manuscrit à Chersotis. Toute personne disposant d'un compte sur oreina.org peut soumettre un article.
            </div>

            <h3>Schéma du workflow</h3>
            <p>Le parcours principal d'un manuscrit suit les sept étapes ci-dessous. Deux embranchements sont possibles : la demande de révision (retour à l'auteur) et le rejet (à deux moments du processus).</p>

            <div class="chersotis-wf">
                <div class="chersotis-wf-timeline">
                    <div class="chersotis-wf-step">
                        <div class="chersotis-wf-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" width="22" height="22">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <div class="chersotis-wf-label">Soumis</div>
                        <div class="chersotis-wf-actor">Auteur</div>
                    </div>
                    <div class="chersotis-wf-line"></div>
                    <div class="chersotis-wf-step">
                        <div class="chersotis-wf-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" width="22" height="22">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <div class="chersotis-wf-label">Éval. initiale</div>
                        <div class="chersotis-wf-actor">Éditeur</div>
                    </div>
                    <div class="chersotis-wf-line"></div>
                    <div class="chersotis-wf-step">
                        <div class="chersotis-wf-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" width="22" height="22">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="chersotis-wf-label">Relecture</div>
                        <div class="chersotis-wf-actor">Relecteurs</div>
                    </div>
                    <div class="chersotis-wf-line"></div>
                    <div class="chersotis-wf-step">
                        <div class="chersotis-wf-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" width="22" height="22">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="chersotis-wf-label">Décision</div>
                        <div class="chersotis-wf-actor">Éditeur</div>
                    </div>
                    <div class="chersotis-wf-line"></div>
                    <div class="chersotis-wf-step">
                        <div class="chersotis-wf-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" width="22" height="22">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="chersotis-wf-label">Maquettage</div>
                        <div class="chersotis-wf-actor">Maquettiste</div>
                    </div>
                    <div class="chersotis-wf-line"></div>
                    <div class="chersotis-wf-step">
                        <div class="chersotis-wf-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" width="22" height="22">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="chersotis-wf-label">Approbation</div>
                        <div class="chersotis-wf-actor">Auteur</div>
                    </div>
                    <div class="chersotis-wf-line"></div>
                    <div class="chersotis-wf-step published">
                        <div class="chersotis-wf-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" width="22" height="22">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div class="chersotis-wf-label">Publié</div>
                        <div class="chersotis-wf-actor">Éditeur</div>
                    </div>
                </div>

                <div class="chersotis-wf-branches">
                    <div class="chersotis-wf-branch revision">
                        <div class="chersotis-wf-branch-head">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                            </svg>
                            Révision demandée
                        </div>
                        <p>L'auteur corrige son manuscrit selon les commentaires des relecteurs, puis resoumet. Le manuscrit retourne en relecture ou est directement accepté.</p>
                    </div>
                    <div class="chersotis-wf-branch rejected">
                        <div class="chersotis-wf-branch-head">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            Rejet possible
                        </div>
                        <p>Le rejet peut intervenir à deux moments : lors de l'évaluation initiale (desk reject) ou après la relecture par les pairs.</p>
                    </div>
                </div>
            </div>

            <h3>Étapes détaillées</h3>
            <div class="chersotis-steps">

                <div class="chersotis-step submitted">
                    <div class="chersotis-step-head">
                        <div class="chersotis-step-num">1</div>
                        <div>
                            <h4 class="chersotis-step-title" style="margin:0;">Soumis</h4>
                        </div>
                    </div>
                    <div class="chersotis-step-body">
                        <p><strong>Qui agit :</strong> l'auteur (soumission directe) ou le rédacteur en chef / un éditeur (soumission faite pour le compte d'un auteur dépourvu de compte).</p>
                        <p><strong>Ce qui se passe :</strong> le manuscrit arrive dans la plateforme. Les éditeurs reçoivent une notification automatique. L'auteur reçoit un accusé de réception et peut suivre l'avancement depuis son espace personnel.</p>
                        <p><strong>Étape suivante :</strong> un éditeur prend en charge le manuscrit et démarre l'évaluation initiale.</p>
                    </div>
                </div>

                <div class="chersotis-step desk-review">
                    <div class="chersotis-step-head">
                        <div class="chersotis-step-num">2</div>
                        <div>
                            <h4 class="chersotis-step-title" style="margin:0;">Évaluation initiale</h4>
                        </div>
                    </div>
                    <div class="chersotis-step-body">
                        <p><strong>Qui agit :</strong> l'éditeur.</p>
                        <p><strong>Ce qui se passe :</strong> l'éditeur lit le manuscrit et vérifie trois critères : le sujet correspond-il à la ligne éditoriale de Chersotis ? Le manuscrit est-il complet (titre, résumé, bibliographie) ? Le formatage est-il correct ?</p>
                        <p><strong>Délai indicatif :</strong> 1 semaine.</p>
                        <p><strong>Actions possibles :</strong></p>
                        <ul>
                            <li><span class="docs-badge success">Envoyer en relecture</span> le manuscrit est recevable, l'éditeur l'envoie aux relecteurs.</li>
                            <li><span class="docs-badge danger">Rejeter</span> le manuscrit ne correspond pas aux critères de la revue (desk reject). L'auteur est notifié avec un motif.</li>
                        </ul>
                    </div>
                </div>

                <div class="chersotis-step in-review">
                    <div class="chersotis-step-head">
                        <div class="chersotis-step-num">3</div>
                        <div>
                            <h4 class="chersotis-step-title" style="margin:0;">En relecture</h4>
                        </div>
                    </div>
                    <div class="chersotis-step-body">
                        <p><strong>Qui agit :</strong> l'éditeur (assignation) et les relecteurs (évaluation).</p>
                        <p><strong>Ce qui se passe :</strong> l'éditeur assigne 1 à 3 relecteurs experts. Chaque relecteur reçoit un email d'invitation avec une date limite. Les relecteurs évaluent le manuscrit de manière indépendante.</p>
                        <p><strong>Délai indicatif :</strong> 4 semaines par relecteur.</p>
                        <p><strong>Actions de l'éditeur à la fin des relectures :</strong></p>
                        <ul>
                            <li><span class="docs-badge success">Accepter</span> le manuscrit est accepté pour publication.</li>
                            <li><span class="docs-badge warning">Demander révision</span> l'auteur doit corriger son manuscrit.</li>
                            <li><span class="docs-badge danger">Rejeter</span> le manuscrit n'est pas publiable.</li>
                        </ul>
                    </div>
                </div>

                <div class="chersotis-step revision">
                    <div class="chersotis-step-head">
                        <div class="chersotis-step-num">3b</div>
                        <div>
                            <h4 class="chersotis-step-title" style="margin:0;">Révision demandée</h4>
                        </div>
                    </div>
                    <div class="chersotis-step-body">
                        <p><strong>Qui agit :</strong> l'auteur.</p>
                        <p><strong>Ce qui se passe :</strong> l'auteur reçoit les commentaires des relecteurs destinés à l'auteur (les commentaires confidentiels pour l'éditeur ne sont pas transmis). L'auteur corrige son manuscrit.</p>
                        <p><strong>Délai indicatif :</strong> 4 semaines.</p>
                        <p><strong>Étape suivante :</strong> l'auteur soumet la version révisée. Selon l'ampleur des corrections, le manuscrit retourne en relecture ou est directement accepté par l'éditeur.</p>
                    </div>
                </div>

                <div class="chersotis-step accepted">
                    <div class="chersotis-step-head">
                        <div class="chersotis-step-num">4</div>
                        <div>
                            <h4 class="chersotis-step-title" style="margin:0;">Accepté</h4>
                        </div>
                    </div>
                    <div class="chersotis-step-body">
                        <p><strong>Qui agit :</strong> l'éditeur.</p>
                        <p><strong>Ce qui se passe :</strong> le manuscrit est accepté pour publication. L'auteur est notifié. Le maquettiste prépare la mise en forme finale (template Chersotis, badges Open Access, citation Harvard).</p>
                        <p><strong>Éléments préparés :</strong> PDF final mis en page, attribution d'un DOI (via Crossref ou en local), assignation à un numéro, pagination.</p>
                    </div>
                </div>

                <div class="chersotis-step approval">
                    <div class="chersotis-step-head">
                        <div class="chersotis-step-num">5</div>
                        <div>
                            <h4 class="chersotis-step-title" style="margin:0;">En attente d'approbation auteur</h4>
                        </div>
                    </div>
                    <div class="chersotis-step-body">
                        <p><strong>Qui agit :</strong> l'auteur principal.</p>
                        <p><strong>Ce qui se passe :</strong> une fois le maquettage terminé, l'équipe éditoriale envoie l'épreuve mise en page à l'auteur pour approbation finale avant publication. L'auteur reçoit un email avec un lien vers son espace.</p>
                        <p><strong>Actions disponibles :</strong></p>
                        <ul>
                            <li><strong>Approuver</strong> : l'article part en publication.</li>
                            <li><strong>Demander des corrections</strong> : l'article revient à la maquette avec les commentaires de l'auteur.</li>
                        </ul>
                    </div>
                </div>

                <div class="chersotis-step published">
                    <div class="chersotis-step-head">
                        <div class="chersotis-step-num">6</div>
                        <div>
                            <h4 class="chersotis-step-title" style="margin:0;">Publié</h4>
                        </div>
                    </div>
                    <div class="chersotis-step-body">
                        <p><strong>Qui agit :</strong> l'éditeur.</p>
                        <p><strong>Ce qui se passe :</strong> l'article est publié et accessible sur le site public de Chersotis. Le DOI est actif et redirige vers la fiche de l'article.</p>
                        <p><strong>Éléments publiés :</strong></p>
                        <ul>
                            <li>Fiche article sur le site (titre, auteurs, résumé, mots-clés).</li>
                            <li>PDF téléchargeable avec mise en page professionnelle.</li>
                            <li>DOI actif (format <em>10.XXXXX/chersotis.YYYY.NNNN</em>).</li>
                            <li>Métadonnées pour les moteurs de recherche académiques.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h3 id="roles">Rôles et responsabilités</h3>
            <table class="docs-table">
                <thead>
                    <tr>
                        <th>Rôle</th>
                        <th>Qui</th>
                        <th>Responsabilités</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Auteur</strong></td>
                        <td>Toute personne disposant d'un compte oreina.org (adhésion non requise)</td>
                        <td>Soumettre un manuscrit complet ; réviser si demandé ; approuver l'épreuve maquettée avant publication</td>
                    </tr>
                    <tr>
                        <td><strong>Éditeur</strong></td>
                        <td>Membre du comité avec la capacité <em>éditeur</em>. Plusieurs éditeurs peuvent cohabiter : chacun ne pilote que ses propres soumissions.</td>
                        <td>Prendre en charge une soumission ; évaluer la recevabilité ; assigner 1 à 3 relecteurs ; synthétiser les retours ; prendre la décision éditoriale ; préparer la publication</td>
                    </tr>
                    <tr>
                        <td><strong>Relecteur</strong></td>
                        <td>Expert du domaine avec la capacité <em>relecteur</em>, invité par l'éditeur</td>
                        <td>Accepter ou décliner l'invitation ; évaluer le manuscrit dans le délai ; soumettre une recommandation argumentée ; fournir commentaires pour l'auteur + confidentiels pour l'éditeur</td>
                    </tr>
                    <tr>
                        <td><strong>Maquettiste</strong></td>
                        <td>Membre avec la capacité <em>maquettiste</em>, assigné par l'éditeur après acceptation</td>
                        <td>Construire la maquette finale (éditeur de blocs, avec import Word/Markdown enrichi par IA) ; générer le PDF final</td>
                    </tr>
                    <tr>
                        <td><strong>Rédacteur en chef</strong></td>
                        <td>Capacité <em>rédacteur en chef</em>. Peut cumuler la capacité d'éditeur pour piloter lui-même certaines soumissions.</td>
                        <td>Superviser le comité ; attribuer les articles aux éditeurs ; gérer les capacités des membres ; arbitrer les overrides ; agir en dernier recours si un éditeur est indisponible</td>
                    </tr>
                </tbody>
            </table>

            <h3>Délais indicatifs</h3>
            <table class="docs-table">
                <thead>
                    <tr>
                        <th>Étape</th>
                        <th>Responsable</th>
                        <th>Délai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Évaluation initiale (desk review)</td><td>Éditeur</td><td>1 semaine</td></tr>
                    <tr><td>Relecture par les pairs</td><td>Relecteurs</td><td>4 semaines</td></tr>
                    <tr><td>Révision du manuscrit</td><td>Auteur</td><td>4 semaines</td></tr>
                    <tr><td>Maquettage + préparation PDF</td><td>Maquettiste</td><td>1 à 2 semaines</td></tr>
                    <tr><td>Approbation auteur (épreuves)</td><td>Auteur</td><td>1 à 2 semaines</td></tr>
                    <tr><td>Publication dans le numéro</td><td>Éditeur</td><td>Variable (selon le calendrier du numéro)</td></tr>
                </tbody>
            </table>
        </section>

        {{-- ========================================= --}}
        {{-- 2. Contenu de la soumission              --}}
        {{-- ========================================= --}}
        <section id="soumission" class="docs-section">
            <h2>Contenu d'une soumission</h2>
            <p>
                Pour soumettre un manuscrit à Chersotis, l'auteur doit fournir les éléments suivants via le formulaire en ligne :
            </p>
            <table class="docs-table">
                <thead>
                    <tr>
                        <th>Champ</th>
                        <th>Obligatoire</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><strong>Titre</strong></td><td>Oui</td><td>Titre complet du manuscrit</td></tr>
                    <tr><td><strong>Résumé</strong></td><td>Oui</td><td>Résumé de l'article (abstract)</td></tr>
                    <tr><td><strong>Mots-clés</strong></td><td>Oui</td><td>Mots-clés pour l'indexation</td></tr>
                    <tr><td><strong>Co-auteurs</strong></td><td>Non</td><td>Liste des co-auteurs éventuels</td></tr>
                    <tr><td><strong>Manuscrit Word</strong></td><td>Oui</td><td>Format <em>.doc</em> ou <em>.docx</em> uniquement (pas PDF). Images intégrées au bon emplacement dans le document. Max 30 Mo.</td></tr>
                    <tr><td><strong>Fichiers supplémentaires</strong></td><td>Non</td><td>Jusqu'à 10 fichiers, 50 Mo chacun. Formats <em>.xls</em>, <em>.xlsx</em>, <em>.pdf</em>, <em>.zip</em>. Pour tableaux complexes, inventaires faunistiques, données brutes, annexes.</td></tr>
                    <tr><td><strong>Conditions</strong></td><td>Oui</td><td>Acceptation des conditions de soumission</td></tr>
                </tbody>
            </table>
            <div class="docs-info">
                <strong>Format Word, pas PDF :</strong> décision du 7 avril 2026. Ce format facilite les allers-retours éditoriaux (annotations Word) et la relecture. Les images haute résolution (photos 300 DPI, graphiques 600 DPI PNG) ne sont demandées qu'après acceptation, en phase de maquettage.
            </div>
        </section>

        {{-- ========================================= --}}
        {{-- 3. Circuit de relecture                  --}}
        {{-- ========================================= --}}
        <section id="relecture" class="docs-section">
            <h2>Circuit de relecture</h2>
            <p>Le circuit de relecture est entièrement géré par la plateforme, de l'invitation à la soumission de l'évaluation.</p>

            <h3>Invitation d'un relecteur</h3>
            <p>L'éditeur sélectionne un relecteur parmi les utilisateurs avec la capacité correspondante. Un email d'invitation est envoyé automatiquement avec :</p>
            <ul>
                <li>Le titre et le résumé du manuscrit.</li>
                <li>Le nom de l'éditeur qui invite.</li>
                <li>Le délai de relecture attendu (4 semaines par défaut).</li>
                <li>Un lien pour accepter ou décliner (pas besoin de se connecter).</li>
            </ul>

            <h3>Acceptation ou déclin</h3>
            <p>Le relecteur clique le lien dans son email et arrive sur une page où il voit le résumé :</p>
            <ul>
                <li><strong>Accepter</strong> : la date limite est fixée à J+21, l'éditeur est notifié, et le relecteur peut ensuite accéder au formulaire d'évaluation (nécessite un login).</li>
                <li><strong>Décliner</strong> : l'éditeur est notifié et peut inviter un autre relecteur.</li>
            </ul>

            <h3>Formulaire d'évaluation</h3>
            <p>Le relecteur connecté remplit :</p>
            <ul>
                <li>5 scores (1 à 5) : originalité, méthodologie, clarté, importance, références.</li>
                <li>Commentaires pour l'auteur (obligatoire, transmis avec la décision).</li>
                <li>Commentaires confidentiels pour l'éditeur (optionnel, non transmis à l'auteur).</li>
                <li>Recommandation : accepter / révision mineure / révision majeure / rejeter.</li>
                <li>Fichier PDF d'évaluation (optionnel).</li>
            </ul>

            <h3>Relances automatiques</h3>
            <p>La plateforme envoie des relances automatiques quotidiennes dans deux cas :</p>
            <ul>
                <li>Invitation sans réponse depuis 7 jours : relance au relecteur.</li>
                <li>Relecture en retard (date limite dépassée, pas encore soumise) : relance.</li>
            </ul>
            <p>Un minimum de 5 jours est respecté entre deux relances pour le même relecteur.</p>

            <div class="docs-info">
                <strong>Politique non-anonyme :</strong> conformément à la décision du 7 avril 2026, les relecteurs ne sont pas anonymes. Leur identité est communiquée à l'auteur avec leur rapport. Il n'y a pas de mécanisme de masquage d'identité.
            </div>

            <h3>Statuts d'une relecture</h3>
            <ul>
                <li><span class="docs-badge info">Invité</span> le relecteur a reçu l'invitation, en attente de réponse.</li>
                <li><span class="docs-badge warning">Accepté</span> l'invitation a été acceptée, l'évaluation est en cours.</li>
                <li><span class="docs-badge danger">Décliné</span> le relecteur a refusé l'invitation.</li>
                <li><span class="docs-badge success">Terminé</span> l'évaluation a été soumise avec une recommandation.</li>
                <li><span class="docs-badge neutral">Expiré</span> le délai est dépassé sans réponse du relecteur.</li>
            </ul>
        </section>

        {{-- ========================================= --}}
        {{-- 4. Décisions éditoriales                 --}}
        {{-- ========================================= --}}
        <section id="decisions" class="docs-section">
            <h2>Décisions éditoriales</h2>
            <p>Les relecteurs soumettent une recommandation parmi les quatre suivantes. L'éditeur prend ensuite la décision finale en s'appuyant sur l'ensemble des évaluations reçues.</p>
            <table class="docs-table">
                <thead>
                    <tr>
                        <th>Décision</th>
                        <th>Signification</th>
                        <th>Conséquence</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="docs-badge success">Accepter en l'état</span></td>
                        <td>Le manuscrit est publiable tel quel</td>
                        <td>L'éditeur prépare la publication.</td>
                    </tr>
                    <tr>
                        <td><span class="docs-badge warning">Révisions mineures</span></td>
                        <td>Corrections légères (coquilles, clarifications, références)</td>
                        <td>L'auteur corrige et resoumet. L'éditeur peut accepter directement après révision.</td>
                    </tr>
                    <tr>
                        <td><span class="docs-badge warning">Révisions majeures</span></td>
                        <td>Modifications substantielles (méthodologie, analyse, structure)</td>
                        <td>L'auteur corrige et resoumet. Le manuscrit repasse généralement en relecture.</td>
                    </tr>
                    <tr>
                        <td><span class="docs-badge danger">Rejeter</span></td>
                        <td>Le manuscrit n'est pas publiable dans Chersotis</td>
                        <td>L'auteur est notifié avec les motifs.</td>
                    </tr>
                </tbody>
            </table>
            <p>Chaque relecteur fournit deux types de commentaires :</p>
            <ul>
                <li><strong>Commentaires pour l'auteur</strong> : transmis à l'auteur avec la décision.</li>
                <li><strong>Commentaires confidentiels pour l'éditeur</strong> : visibles uniquement par l'éditeur, non transmis à l'auteur.</li>
            </ul>

            <h3>Rejet avec recommandation Lepis</h3>
            <p>Quand un article est jugé mieux adapté au bulletin <strong>Lepis</strong> (publication interne OREINA, format plus court) qu'à Chersotis, l'éditeur peut le rediriger sans prononcer un rejet direct devant l'auteur. Trois étapes :</p>
            <ol>
                <li>L'éditeur rejette en cochant « Recommander pour Lepis ». Le statut passe en attente (invisible pour l'auteur). Administrateurs et rédacteurs en chef sont notifiés.</li>
                <li>Un administrateur ouvre la File Lepis et décide : transmettre à Lepis, ou rejeter définitivement.</li>
                <li><strong>Transmission à Lepis</strong> : l'auteur reçoit un mail (« Votre article a été transmis au bulletin Lepis, le rédacteur en chef prendra contact ») et les membres avec la capacité Lepis reçoivent le manuscrit pour prise de contact hors plateforme. <strong>Rejet définitif</strong> : flow de rejet standard avec motifs.</li>
            </ol>
        </section>

        {{-- ========================================= --}}
        {{-- 5. Suivi par l'auteur                    --}}
        {{-- ========================================= --}}
        <section id="suivi-auteur" class="docs-section">
            <h2>Suivi par l'auteur</h2>
            <p>L'auteur connecté accède à ses soumissions depuis son espace personnel. Chaque soumission affiche :</p>

            <h3>Timeline 7 étapes</h3>
            <p>Une barre de progression visuelle (les statuts internes sont regroupés pour l'auteur) : <strong>Soumis → En évaluation → Relecture → Décision → Maquettage → Approbation → Publié</strong>. Les étapes passées affichent une coche verte, l'étape active est surlignée. En cas de rejet, l'étape « Décision » affiche une croix.</p>

            <h3>Historique</h3>
            <p>Sous la timeline, un bloc « Historique » affiche la chronologie des changements d'état avec des <strong>libellés humains</strong> (ex. « Votre manuscrit a été envoyé en relecture »). Les informations internes (noms d'éditeurs, de relecteurs, notes de transition) ne sont <strong>pas visibles</strong> par l'auteur.</p>

            <h3>Indicateur « Action requise »</h3>
            <p>Quand une révision est demandée, un bandeau orange apparaît sur la page détail et un badge dans la liste. L'auteur est invité à soumettre sa révision via un bouton visible.</p>

            <div class="docs-info">
                <strong>Principe du « suivi de colis » :</strong> l'auteur voit où en est son article dans le processus, mais pas les détails internes (qui a évalué, qui a relu, notes confidentielles). La transparence est assurée par les libellés humains chronologiques, pas par l'exposition des acteurs.
            </div>
        </section>

        {{-- ========================================= --}}
        {{-- 6. Capacités éditoriales                 --}}
        {{-- ========================================= --}}
        <section id="capacites" class="docs-section">
            <h2>Capacités éditoriales</h2>
            <p>
                Le système de <strong>capacités éditoriales</strong> permet d'attribuer à un utilisateur un ou plusieurs rôles dans le workflow, indépendamment de son rôle global sur la plateforme. Un même utilisateur peut cumuler plusieurs capacités.
            </p>

            <h3>Les 5 capacités</h3>
            <ul>
                <li><strong>Rédacteur en chef</strong> : supervise l'ensemble du comité, assigne les éditeurs aux articles, modifie les capacités des membres.</li>
                <li><strong>Éditeur</strong> : prend en charge un article (auto-attribution depuis la file d'attente), désigne les relecteurs, synthétise les retours, valide la version finale, gère les allers-retours auteur.</li>
                <li><strong>Relecteur</strong> : accède au manuscrit assigné, soumet un rapport de relecture. Non anonyme.</li>
                <li><strong>Maquettiste</strong> : accède aux articles acceptés, crée la maquette, génère le PDF final.</li>
                <li><strong>Rédacteur en chef Lepis</strong> : reçoit par mail les articles que Chersotis transmet au bulletin Lepis. Peut consulter la fiche admin pour y lire le manuscrit, puis prend contact avec l'auteur <strong>hors plateforme</strong> pour négocier la publication dans Lepis.</li>
            </ul>

            <h3>Règle de séparation des rôles</h3>
            <p>Pour éviter les conflits d'intérêt, un utilisateur <strong>ne peut pas être à la fois éditeur et relecteur du même article</strong>. Toute tentative d'assignation en conflit est bloquée avec un message d'erreur.</p>
            <p>Un <strong>override explicite</strong> est possible pour les exceptions ponctuelles validées par le comité. Une modale de confirmation impose alors de saisir un <strong>motif</strong> (3 à 500 caractères, obligatoire) qui est enregistré dans la traçabilité.</p>

            <h3>Distinction capacité globale vs assignation par article</h3>
            <ul>
                <li>La <strong>capacité</strong> dit « cet utilisateur <em>peut être</em> éditeur » : c'est l'éligibilité globale.</li>
                <li>L'<strong>assignation</strong> dit « cet utilisateur <em>est</em> éditeur <em>de cet article précis</em> ».</li>
            </ul>
            <p>Un même utilisateur peut donc être éditeur de l'article 42 et relecteur de l'article 57, tant que la règle de séparation est respectée sur chaque article.</p>
        </section>

        {{-- ========================================= --}}
        {{-- 7. Publication, DOI et citations         --}}
        {{-- ========================================= --}}
        <section id="publication" class="docs-section">
            <h2>Publication, DOI et citations</h2>

            <h3>Attribution du DOI</h3>
            <p>Chaque article publié reçoit un DOI au format <em>10.XXXXX/chersotis.YYYY.NNNN</em> (numérotation séquentielle par année). Le DOI est attribué via le service Crossref. Il est déposé auprès de Crossref en production, ou généré localement tant que l'ISSN n'est pas obtenu.</p>
            <p>Le DOI est attribué <strong>avant</strong> la génération du PDF final pour qu'il figure dans le document.</p>

            <h3>Pagination continue (Tomes annuels)</h3>
            <p>Chersotis utilise une pagination continue par tome annuel : le premier article du tome commence à la page 1, le suivant à la page suivant la dernière page du précédent. Format d'affichage dans le pied de page du PDF : <em>Chersotis, Tome X, pp. Y–Z (année)</em>. Citation bibliographique : <em>Auteur (année). Titre. Chersotis, Tome X, Y–Z.</em></p>

            <h3>Exports de citations</h3>
            <p>Sur la page publique d'un article publié, 3 formats sont disponibles :</p>
            <ul>
                <li><strong>BibTeX</strong> : téléchargement <em>.bib</em>.</li>
                <li><strong>RIS</strong> : téléchargement <em>.ris</em> (compatible Zotero, Mendeley, EndNote).</li>
                <li><strong>Harvard</strong> : copie dans le presse-papier (format auteur-date).</li>
            </ul>
            <p>Les citations incluent automatiquement : auteurs, co-auteurs, titre, journal (Chersotis), tome, pages et DOI.</p>

            <h3>Format du PDF</h3>
            <p>La mise en page suit le template <em>Biology Letters</em> adapté à Chersotis : sidebar papillon + wordmark, badges Open Access + CC BY 4.0, citation Harvard en en-tête, sections et références standardisées. La police est sans-serif, les titres H1 en vert Chersotis, le corps de texte justifié.</p>
        </section>

        {{-- ========================================= --}}
        {{-- 8. Numéros de la revue                   --}}
        {{-- ========================================= --}}
        <section id="numeros" class="docs-section">
            <h2>Numéros de la revue</h2>
            <p>Les numéros publiés de la revue Chersotis sont organisés par tomes annuels.</p>
            <p>Chaque numéro possède :</p>
            <ul>
                <li>Un <strong>volume</strong> et un <strong>numéro</strong> (identifiants uniques).</li>
                <li>Une <strong>date de publication</strong> (mois et année).</li>
                <li>Une <strong>couverture</strong> (image).</li>
                <li>Un <strong>statut</strong> : en préparation, publié, ou archivé.</li>
            </ul>
            <p>Les numéros publiés sont accessibles sur la page <a href="{{ route('journal.issues.index') }}" style="color:var(--accent);text-decoration:underline;">Numéros</a> du site. Les articles d'un numéro sont paginés en continu.</p>
        </section>

        {{-- Footer --}}
        <div style="text-align:center;padding:2rem 0 1rem;color:var(--muted);font-size:0.85rem;">
            <p style="margin:0;">Documentation Chersotis — dernière mise à jour : avril 2026.</p>
            <p style="margin:0.5rem 0 0 0;">Pour toute question, contactez l'équipe de la revue via <a href="{{ route('journal.about') }}" style="color:var(--accent);text-decoration:underline;">la page À propos</a>.</p>
        </div>
    </div>
</div>
@endsection
