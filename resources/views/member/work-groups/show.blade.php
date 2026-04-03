@extends('layouts.member')

@section('title', $workGroup->name)

@section('page-title')
    <div class="crumbs">
        <a href="{{ route('member.contributions') }}">Mes contributions</a>
        <span>&middot;</span>
        <a href="{{ route('member.work-groups') }}">Groupes de travail</a>
        <span>&middot;</span>
        <span style="color: var(--text);">{{ $workGroup->name }}</span>
    </div>
@endsection

@section('topbar-actions')
    <button class="btn btn-secondary"><i data-lucide="folder-plus" class="icon icon-blue"></i>Deposer un document</button>
    <button class="btn btn-primary"><i data-lucide="message-square-plus" class="icon icon-sage"></i>Nouveau sujet</button>
@endsection

@push('styles')
<style>
    /* === GT SHOW — Design System V4 === */
    .group-hero {
        padding: 28px;
        background: linear-gradient(180deg, rgba(53,107,138,0.06), rgba(255,255,255,1));
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
    }

    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border-radius: 999px;
        background: rgba(53,107,138,0.10);
        color: var(--blue);
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 14px;
    }

    .group-hero h1 {
        margin: 0;
        font-size: clamp(32px, 4vw, 46px);
        line-height: 0.98;
        letter-spacing: -0.05em;
    }

    .group-hero p {
        margin: 14px 0 0;
        max-width: 860px;
        color: var(--muted);
        font-size: 16px;
        line-height: 1.7;
    }

    .hero-meta {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        border: 1px solid rgba(22,48,43,0.06);
        background: var(--surface-soft);
        color: var(--muted);
    }

    .hero-actions {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .gt-layout {
        display: grid;
        grid-template-columns: 1.2fr 0.82fr;
        gap: 20px;
        align-items: start;
    }

    .stack { display: grid; gap: 18px; }

    .discussion-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow);
        padding: 22px;
    }

    .discussion-list,
    .resource-list,
    .gt-member-list,
    .timeline-list,
    .chat-list {
        display: grid;
        gap: 12px;
    }

    .discussion-item {
        padding: 18px;
        border-radius: 18px;
        background: var(--surface-soft);
        border: 1px solid rgba(22,48,43,0.06);
    }

    .discussion-item h3 {
        margin: 0;
        font-size: 18px;
        line-height: 1.25;
        letter-spacing: -0.02em;
    }

    .discussion-item p {
        margin: 8px 0 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .discussion-meta {
        margin-top: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .badge {
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        border: 1px solid rgba(22,48,43,0.06);
        background: white;
        color: var(--muted);
    }
    .badge.blue { background: rgba(53,107,138,0.10); color: var(--blue); }
    .badge.sage { background: rgba(133,183,157,0.16); color: #2f694e; }
    .badge.gold { background: rgba(237,196,66,0.18); color: #8b6c05; }
    .badge.coral { background: rgba(239,122,92,0.12); color: var(--coral); }

    .small-meta {
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .side-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow);
        padding: 22px;
    }
    .side-card.blue { background: var(--surface-blue); }
    .side-card.sage { background: var(--surface-sage); }

    .resource-item,
    .gt-member-item,
    .timeline-item,
    .chat-item {
        padding: 14px;
        border-radius: 16px;
        background: rgba(255,255,255,0.72);
        border: 1px solid rgba(22,48,43,0.06);
    }

    .resource-item strong,
    .gt-member-item strong,
    .timeline-item strong,
    .chat-item strong {
        display: block;
        font-size: 14px;
        line-height: 1.4;
        letter-spacing: -0.01em;
    }

    .resource-item p,
    .gt-member-item p,
    .timeline-item p,
    .chat-item p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.55;
    }

    .gt-member-row,
    .timeline-row,
    .chat-row {
        display: grid;
        grid-template-columns: 42px 1fr auto;
        gap: 12px;
        align-items: start;
    }

    .gt-avatar,
    .bullet {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        flex: 0 0 42px;
        background: rgba(133,183,157,0.16);
        color: var(--forest);
        font-weight: 800;
    }

    .bullet.blue { background: rgba(53,107,138,0.10); color: var(--blue); }
    .bullet.gold { background: rgba(237,196,66,0.18); color: #8b6c05; }

    .tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 16px;
    }

    .tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid rgba(22,48,43,0.08);
        background: var(--surface);
        color: var(--muted);
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
    }
    .tab.active {
        background: rgba(53,107,138,0.10);
        color: var(--blue);
        border-color: rgba(53,107,138,0.14);
    }

    @media (max-width: 1180px) {
        .gt-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .gt-member-row,
        .timeline-row,
        .chat-row {
            grid-template-columns: 42px 1fr;
        }
        .gt-member-row .small-meta,
        .timeline-row .small-meta,
        .chat-row .small-meta {
            grid-column: 2;
            justify-self: start;
        }
    }
</style>
@endpush

@section('content')
{{-- Hero --}}
<section class="group-hero">
    <div class="eyebrow">
        <i data-lucide="flask-conical" class="icon icon-blue"></i>
        Groupe de travail
    </div>
    <h1>{{ $workGroup->name }}</h1>

    @if($workGroup->description)
        <p>{{ $workGroup->description }}</p>
    @endif

    <div class="hero-meta">
        <span class="pill"><i data-lucide="users" class="icon icon-blue"></i>{{ $workGroup->members_count }} membre{{ $workGroup->members_count > 1 ? 's' : '' }}</span>
        <span class="pill"><i data-lucide="message-circle" class="icon icon-gold"></i>Discussions a venir</span>
        <span class="pill"><i data-lucide="folder-open" class="icon icon-sage"></i>Ressources a venir</span>
    </div>

    <div class="hero-actions">
        @if($isMember)
            <button class="btn btn-primary"><i data-lucide="message-square-plus" class="icon icon-sage"></i>Ouvrir une discussion</button>
            <button class="btn btn-secondary"><i data-lucide="messages-square" class="icon icon-blue"></i>Acceder au chat</button>
        @else
            <button class="btn btn-primary"><i data-lucide="user-plus" class="icon icon-sage"></i>Rejoindre le groupe</button>
        @endif
    </div>
</section>

{{-- Layout: main + sidebar --}}
<section class="gt-layout">
    {{-- Left column: Discussions + Chat --}}
    <div class="stack">
        {{-- Discussions --}}
        <article class="discussion-card">
            <div class="panel-head">
                <div>
                    <h2>Discussions</h2>
                    <p>Le fil principal conserve les sujets structurants. Chaque echange reste reperable, documente et reutilisable.</p>
                </div>
                <a href="#" class="text-link"><i data-lucide="arrow-right" class="icon icon-blue"></i>Voir tous les sujets</a>
            </div>

            <div class="tabs">
                <span class="tab active"><i data-lucide="pin" class="icon icon-blue"></i>Epingles</span>
                <span class="tab"><i data-lucide="clock-3" class="icon icon-blue"></i>Recents</span>
                <span class="tab"><i data-lucide="help-circle" class="icon icon-blue"></i>A trancher</span>
            </div>

            <div class="discussion-list">
                <article class="discussion-item">
                    <h3>Criteres de separation dans le complexe <em>Melitaea</em></h3>
                    <p>Echanges sur les caracteres diagnostiques mobilisables, les limites d'interpretation et la necessite eventuelle d'une note de synthese interne.</p>
                    <div class="discussion-meta">
                        <div class="badges">
                            <span class="badge blue">Identification</span>
                            <span class="badge gold">A trancher</span>
                            <span class="badge">7 reponses</span>
                        </div>
                        <span class="small-meta">Derniere activite &middot; aujourd'hui</span>
                    </div>
                </article>

                <article class="discussion-item">
                    <h3>Mise a jour du referentiel et consequences sur les donnees historiques</h3>
                    <p>Discussion autour d'un changement nomenclatural et des impacts sur l'alignement avec les bases existantes, les exports et la communication aupres des contributeurs.</p>
                    <div class="discussion-meta">
                        <div class="badges">
                            <span class="badge blue">Referentiel</span>
                            <span class="badge sage">Suivi</span>
                            <span class="badge">4 reponses</span>
                        </div>
                        <span class="small-meta">Hier</span>
                    </div>
                </article>

                <article class="discussion-item">
                    <h3>Sujet epingle &middot; Procedure de relecture des cas complexes</h3>
                    <p>Rappel des etapes, des attendus de justification et des ressources a consulter avant arbitrage collectif.</p>
                    <div class="discussion-meta">
                        <div class="badges">
                            <span class="badge coral">Epingle</span>
                            <span class="badge sage">Methode</span>
                        </div>
                        <span class="small-meta">Mis a jour cette semaine</span>
                    </div>
                </article>
            </div>
        </article>

        {{-- Chat --}}
        <article class="discussion-card">
            <div class="panel-head">
                <div>
                    <h2>Chat collaboratif</h2>
                    <p>Un espace de conversation rapide, complementaire aux discussions structurees, pour fluidifier les echanges entre membres.</p>
                </div>
                <a href="#" class="text-link"><i data-lucide="arrow-right" class="icon icon-blue"></i>Ouvrir le chat complet</a>
            </div>

            <div class="chat-list">
                <article class="chat-item">
                    <div class="chat-row">
                        <div class="gt-avatar">D</div>
                        <div>
                            <strong>David D.</strong>
                            <p>J'ai ajoute quelques elements bibliographiques sur le complexe evoque ce matin.</p>
                        </div>
                        <div class="small-meta">10:42</div>
                    </div>
                </article>

                <article class="chat-item">
                    <div class="chat-row">
                        <div class="gt-avatar">M</div>
                        <div>
                            <strong>Marie L.</strong>
                            <p>Je regarde les photos associees et je reviens avec une proposition de formulation.</p>
                        </div>
                        <div class="small-meta">10:48</div>
                    </div>
                </article>

                <article class="chat-item">
                    <div class="chat-row">
                        <div class="gt-avatar">P</div>
                        <div>
                            <strong>Paul R.</strong>
                            <p>On pourrait centraliser ca dans une note courte pour les validateurs.</p>
                        </div>
                        <div class="small-meta">11:03</div>
                    </div>
                </article>
            </div>
        </article>
    </div>

    {{-- Right column: Resources + Members + Timeline --}}
    <div class="stack">
        {{-- Resources --}}
        <article class="side-card blue">
            <div class="panel-head">
                <div>
                    <h2>Ressources du groupe</h2>
                    <p>Documents, notes, references, guides et liens utiles.</p>
                </div>
            </div>
            <div class="resource-list">
                <article class="resource-item">
                    <strong>Note interne &middot; criteres de determination</strong>
                    <p>Version de travail partagee avec le groupe pour harmoniser les pratiques.</p>
                </article>
                <article class="resource-item">
                    <strong>Bibliographie de reference</strong>
                    <p>Selection de publications utiles aux arbitrages taxonomiques du groupe.</p>
                </article>
                <article class="resource-item">
                    <strong>Lien Artemisiae &middot; requete partagee</strong>
                    <p>Acces rapide a un sous-ensemble de donnees discute en reunion.</p>
                </article>
            </div>
        </article>

        {{-- Members (real data) --}}
        <article class="side-card sage">
            <div class="panel-head">
                <div>
                    <h2>Membres</h2>
                    <p>Les principaux contributeurs et referents de l'espace.</p>
                </div>
            </div>
            <div class="gt-member-list">
                @forelse($workGroup->members as $m)
                <article class="gt-member-item">
                    <div class="gt-member-row">
                        <div class="gt-avatar">{{ strtoupper(substr($m->first_name, 0, 1)) }}</div>
                        <div>
                            <strong>{{ $m->first_name }} {{ strtoupper(substr($m->last_name, 0, 1)) }}.</strong>
                            @if($m->pivot->role === 'leader')
                                <p>Referent du groupe</p>
                            @else
                                <p>Membre</p>
                            @endif
                        </div>
                        <div class="small-meta">
                            @if($m->pivot->role === 'leader')
                                Referent
                            @else
                                Actif
                            @endif
                        </div>
                    </div>
                </article>
                @empty
                <article class="gt-member-item">
                    <div class="gt-member-row">
                        <div class="gt-avatar">?</div>
                        <div>
                            <strong>Aucun membre</strong>
                            <p>Ce groupe n'a pas encore de membres.</p>
                        </div>
                    </div>
                </article>
                @endforelse
            </div>
        </article>

        {{-- Timeline --}}
        <article class="side-card">
            <div class="panel-head">
                <div>
                    <h2>Echeances</h2>
                    <p>Les prochains jalons, reunions et livrables du groupe.</p>
                </div>
            </div>
            <div class="timeline-list">
                <article class="timeline-item">
                    <div class="timeline-row">
                        <div class="bullet gold"><i data-lucide="calendar-days" class="icon icon-gold"></i></div>
                        <div>
                            <strong>Reunion du groupe</strong>
                            <p>Arbitrage sur les sujets ouverts et validation des prochaines etapes.</p>
                        </div>
                        <div class="small-meta">A venir</div>
                    </div>
                </article>
                <article class="timeline-item">
                    <div class="timeline-row">
                        <div class="bullet blue"><i data-lucide="file-check" class="icon icon-blue"></i></div>
                        <div>
                            <strong>Note methodologique a finaliser</strong>
                            <p>Version courte pour harmoniser les criteres de relecture au sein du reseau.</p>
                        </div>
                        <div class="small-meta">Cette semaine</div>
                    </div>
                </article>
            </div>
        </article>
    </div>
</section>
@endsection
