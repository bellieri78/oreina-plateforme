@extends('layouts.admin')

@section('title', 'Documentation')

@section('breadcrumb')
    <span>Documentation</span>
@endsection

@section('content')
<div class="documentation-page">
    <div class="doc-header">
        <h1>Documentation Extranet OREINA</h1>
        <p>Guide d'utilisation de la plateforme de gestion</p>
    </div>

    <div class="doc-grid">
        {{-- Sidebar navigation --}}
        <nav class="doc-nav">
            <div class="doc-nav-section">
                <div class="doc-nav-title">Prise en main</div>
                <a href="#introduction" class="doc-nav-link active">Introduction</a>
                <a href="#connexion" class="doc-nav-link">Connexion</a>
                <a href="#roles-acces" class="doc-nav-link">Rôles et accès</a>
                <a href="#inscription" class="doc-nav-link">Inscription et rattachement</a>
                <a href="#tableau-de-bord" class="doc-nav-link">Tableau de bord</a>
            </div>

            <div class="doc-nav-section">
                <div class="doc-nav-title">Fonctionnalites</div>
                <a href="#filtres" class="doc-nav-link">Filtres avances</a>
                <a href="#export" class="doc-nav-link">Export CSV</a>
                <a href="#import" class="doc-nav-link">Import CSV</a>
                <a href="#actions-groupees" class="doc-nav-link">Actions groupees</a>
            </div>

            <div class="doc-nav-section">
                <div class="doc-nav-title">Gestion des contacts</div>
                <a href="#contacts" class="doc-nav-link">Contacts</a>
                <a href="#structures" class="doc-nav-link">Structures</a>
                <a href="#carte" class="doc-nav-link">Carte interactive</a>
                <a href="#adhesions" class="doc-nav-link">Adhesions</a>
                <a href="#cartes-adherent" class="doc-nav-link">Cartes d'adherent</a>
                <a href="#dons" class="doc-nav-link">Dons</a>
                <a href="#produits" class="doc-nav-link">Produits</a>
                <a href="#achats" class="doc-nav-link">Achats</a>
                <a href="#benevolat" class="doc-nav-link">Benevolat</a>
            </div>

            <div class="doc-nav-section">
                <div class="doc-nav-title">Contenu</div>
                <a href="#articles" class="doc-nav-link">Articles</a>
                <a href="#evenements" class="doc-nav-link">Evenements</a>
                <a href="#groupes-travail" class="doc-nav-link">Groupes de travail</a>
                <a href="#lepis" class="doc-nav-link">Lepis (bulletins)</a>
            </div>

            <div class="doc-nav-section">
                <div class="doc-nav-title">Revue Chersotis</div>
                <a href="#numeros" class="doc-nav-link">Numeros</a>
                <a href="#soumissions" class="doc-nav-link">Workflow editorial</a>
                <a href="#capacites-editoriales" class="doc-nav-link">Capacites editoriales</a>
                <a href="#file-attente" class="doc-nav-link">File d'attente editoriale</a>
                <a href="#mes-articles" class="doc-nav-link">Mes articles (editeur)</a>
                <a href="#reviews" class="doc-nav-link">Reviews</a>
            </div>

            <div class="doc-nav-section">
                <div class="doc-nav-title">Administration</div>
                <a href="#utilisateurs" class="doc-nav-link">Utilisateurs</a>
                <a href="#permissions" class="doc-nav-link">Permissions</a>
                <a href="#rapports" class="doc-nav-link">Rapports PDF</a>
                <a href="#brevo" class="doc-nav-link">Brevo (Emails)</a>
                <a href="#import-export" class="doc-nav-link">Import / Export</a>
                <a href="#emails" class="doc-nav-link">Emails transactionnels</a>
                <a href="#parametres" class="doc-nav-link">Parametres</a>
                <a href="#statistiques" class="doc-nav-link">Statistiques</a>
                <a href="#rgpd" class="doc-nav-link">RGPD</a>
            </div>
        </nav>

        {{-- Main content --}}
        <div class="doc-content">
            {{-- Introduction --}}
            <section id="introduction" class="doc-section">
                <h2>Introduction</h2>
                <p>Bienvenue dans l'extranet OREINA. Cette plateforme centralise la gestion de l'association :</p>
                <ul>
                    <li><strong>Gestion des adherents</strong> : suivi des membres, adhesions et dons</li>
                    <li><strong>Publication de contenu</strong> : articles d'actualite et evenements</li>
                    <li><strong>Revue scientifique</strong> : gestion des numeros, soumissions et peer reviews</li>
                </ul>

                <div class="doc-info">
                    <strong>Version actuelle :</strong> 1.0<br>
                    <strong>Derniere mise a jour :</strong> Mars 2026
                </div>
            </section>

            {{-- Connexion --}}
            <section id="connexion" class="doc-section">
                <h2>Connexion</h2>
                <p>Pour acceder a l'extranet, rendez-vous sur <code>/extranet/login</code> et entrez vos identifiants.</p>

                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Entrez votre email</strong>
                            <p>Utilisez l'adresse email associee a votre compte OREINA.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Entrez votre mot de passe</strong>
                            <p>Si vous avez oublie votre mot de passe, contactez un administrateur.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Cliquez sur "Se connecter"</strong>
                            <p>Vous serez redirige vers le tableau de bord.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Rôles et accès --}}
            <section id="roles-acces" class="doc-section">
                <h2>Rôles et accès</h2>
                <p>La plateforme OREINA utilise une table unique <code>users</code> avec un système de rôles pour gérer les accès aux différents espaces.</p>

                <h3>Les deux points d'entrée</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Espace</th>
                                <th>URL de connexion</th>
                                <th>Redirection après login</th>
                                <th>Public cible</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Espace membre</strong></td>
                                <td><code>/connexion</code></td>
                                <td><code>/espace-membre</code></td>
                                <td>Tous les utilisateurs (adhérents, éditeurs, admins)</td>
                            </tr>
                            <tr>
                                <td><strong>Extranet (admin)</strong></td>
                                <td><code>/extranet/login</code></td>
                                <td><code>/extranet</code></td>
                                <td>Éditeurs et administrateurs uniquement</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Les rôles disponibles</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Rôle</th>
                                <th>Espace membre</th>
                                <th>Extranet</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>user</code></td>
                                <td>✅ Oui</td>
                                <td>❌ Non (403)</td>
                                <td>Adhérent standard — accès à son espace personnel, chat, GT, Lepis</td>
                            </tr>
                            <tr>
                                <td><code>author</code></td>
                                <td>✅ Oui</td>
                                <td>❌ Non (403)</td>
                                <td>Auteur — peut soumettre des articles pour la revue</td>
                            </tr>
                            <tr>
                                <td><code>reviewer</code></td>
                                <td>✅ Oui</td>
                                <td>❌ Non (403)</td>
                                <td>Reviewer — peut évaluer des soumissions</td>
                            </tr>
                            <tr>
                                <td><code>editor</code></td>
                                <td>✅ Oui</td>
                                <td>✅ Oui</td>
                                <td>Éditeur — accès à l'administration du contenu et des membres</td>
                            </tr>
                            <tr>
                                <td><code>admin</code></td>
                                <td>✅ Oui</td>
                                <td>✅ Oui</td>
                                <td>Administrateur — accès complet à toutes les fonctionnalités</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="doc-info">
                    <strong>Middleware :</strong> Les routes <code>/extranet</code> sont protégées par le middleware <code>admin</code> qui vérifie que l'utilisateur a le rôle <code>editor</code> ou <code>admin</code>. Un adhérent qui tente d'accéder à l'extranet recevra une erreur 403.<br>
                    <strong>Redirection guests :</strong> Un utilisateur non connecté qui tente d'accéder à <code>/espace-membre</code> est redirigé vers <code>/connexion</code>.
                </div>

                <h3>Capacités éditoriales Chersotis</h3>
                <p>En complément du rôle global, un système de <strong>capacités éditoriales</strong> (chief_editor, editor, reviewer, layout_editor, lepis_editor) permet à un même utilisateur d'occuper plusieurs rôles dans la revue Chersotis. Voir la section dédiée <a href="#capacites-editoriales" style="color:#356B8A;">Capacités éditoriales</a> pour le détail des 5 rôles, la règle de séparation, et la gestion.</p>
            </section>

            {{-- Inscription et rattachement --}}
            <section id="inscription" class="doc-section">
                <h2>Inscription et rattachement</h2>
                <p>La plateforme permet à n'importe qui de créer un compte gratuitement via <code>/inscription</code>. L'adhésion à l'association n'est pas requise pour créer un compte.</p>

                <h3>Processus d'inscription</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Création du compte</strong>
                            <p>L'utilisateur renseigne son nom, son email et un mot de passe. Un captcha Cloudflare Turnstile protège le formulaire contre les bots (activable via <code>TURNSTILE_ENABLED=true</code> en prod). Un enregistrement est créé dans la table <code>users</code> avec le rôle <code>user</code>.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Email de vérification envoyé</strong>
                            <p>Un email contenant un lien signé à durée limitée (60 min, route <code>verification.verify</code>) est envoyé à l'adresse fournie. L'utilisateur est connecté immédiatement (soft-gate) mais voit une bannière orange tant qu'il n'a pas cliqué le lien. Les actions sensibles — notamment la soumission d'articles via <code>/revue/mes-soumissions/nouvelle</code> — sont bloquées par le middleware <code>verified</code> tant que l'email n'est pas confirmé.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Rattachement automatique par email</strong>
                            <p>Au moment de la création du compte, le système cherche dans la table <code>members</code> un enregistrement avec le même email et sans <code>user_id</code>. Si un adhérent est trouvé, le compte est automatiquement rattaché à sa fiche membre.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">4</span>
                        <div class="step-content">
                            <strong>Redirection vers l'espace membre</strong>
                            <p>L'utilisateur est connecté automatiquement et redirigé vers <code>/espace-membre</code>. Un message lui indique si son compte a été rattaché à une fiche adhérent existante, et qu'un email de vérification a été envoyé.</p>
                        </div>
                    </div>
                </div>

                <h3>Compte sans adhésion vs adhérent</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fonctionnalité</th>
                                <th>Compte gratuit (sans adhésion)</th>
                                <th>Adhérent actif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Soumettre un article à Chersotis</td>
                                <td>✅ Oui</td>
                                <td>✅ Oui</td>
                            </tr>
                            <tr>
                                <td>Accès au tableau de bord</td>
                                <td>✅ Basique</td>
                                <td>✅ Complet</td>
                            </tr>
                            <tr>
                                <td>Télécharger la revue Chersotis (PDF)</td>
                                <td>❌ Non</td>
                                <td>✅ Oui</td>
                            </tr>
                            <tr>
                                <td>Accès au bulletin Lepis</td>
                                <td>❌ Non</td>
                                <td>✅ Oui</td>
                            </tr>
                            <tr>
                                <td>Chat adhérents</td>
                                <td>❌ Non</td>
                                <td>✅ Oui</td>
                            </tr>
                            <tr>
                                <td>Groupes de travail</td>
                                <td>❌ Non</td>
                                <td>✅ Oui</td>
                            </tr>
                            <tr>
                                <td>Carte des membres</td>
                                <td>❌ Non</td>
                                <td>✅ Oui</td>
                            </tr>
                            <tr>
                                <td>Documents (Cerfa, attestations)</td>
                                <td>❌ Non</td>
                                <td>✅ Oui</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="doc-info">
                    <strong>Comptes existants (pré-2026-04) :</strong> Tous les comptes créés avant la mise en place de la vérification email ont été automatiquement marqués comme vérifiés (migration de "grandfather"). Seuls les <em>nouveaux</em> inscrits doivent passer la vérification.
                </div>

                <div class="doc-info">
                    <strong>Rattachement manuel :</strong> Si l'email du compte ne correspond pas à celui de la fiche adhérent (email différent, faute de frappe...), un administrateur peut rattacher manuellement le compte en modifiant le champ <code>user_id</code> de la fiche membre dans l'extranet, ou en mettant à jour l'email de l'adhérent pour qu'il corresponde.<br><br>
                    <strong>Important :</strong> Le rattachement automatique ne fonctionne que si l'adhérent a été créé dans l'extranet <strong>avant</strong> la création du compte. Si le compte est créé en premier, il faudra rattacher manuellement après création de la fiche adhérent.
                </div>
            </section>

            {{-- Tableau de bord --}}
            <section id="tableau-de-bord" class="doc-section">
                <h2>Tableau de bord</h2>
                <p>Le tableau de bord presente une vue d'ensemble des statistiques cles :</p>
                <ul>
                    <li>Nombre total d'adherents</li>
                    <li>Adhesions actives et en attente</li>
                    <li>Total des dons recus</li>
                    <li>Articles publies et en attente</li>
                    <li>Evenements a venir</li>
                    <li>Soumissions en cours de review</li>
                </ul>
            </section>

            {{-- Filtres avances --}}
            <section id="filtres" class="doc-section">
                <h2>Filtres avances</h2>
                <p>Toutes les listes (contacts, adhesions, dons) disposent de filtres avances pour affiner vos recherches.</p>

                <h3>Filtres disponibles par module</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Module</th>
                                <th>Filtres</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Contacts</strong></td>
                                <td>Statut, Ville, Pays, Date d'inscription, Adhesion active</td>
                            </tr>
                            <tr>
                                <td><strong>Adhesions</strong></td>
                                <td>Statut (active/expiree), Type, Mode de paiement, Annee, Periode</td>
                            </tr>
                            <tr>
                                <td><strong>Dons</strong></td>
                                <td>Annee, Recu fiscal, Mode de paiement, Campagne, Montant, Periode</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Utilisation</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Ouvrir les filtres</strong>
                            <p>Cliquez sur "Filtres" pour deployer le panneau de filtrage.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Selectionnez vos criteres</strong>
                            <p>Remplissez un ou plusieurs champs selon vos besoins.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Appliquer</strong>
                            <p>Cliquez sur "Filtrer" pour voir les resultats. "Reinitialiser" efface tous les filtres.</p>
                        </div>
                    </div>
                </div>

                <div class="doc-info">
                    <strong>Astuce :</strong> Les filtres sont conserves dans l'URL. Vous pouvez partager un lien filtre avec un collegue ou le mettre en favori.
                </div>
            </section>

            {{-- Export CSV --}}
            <section id="export" class="doc-section">
                <h2>Export CSV</h2>
                <p>Exportez vos donnees au format CSV compatible avec Excel, LibreOffice, et autres tableurs.</p>

                <h3>Types d'export</h3>
                <ul>
                    <li><strong>Export complet</strong> : Exporte toutes les donnees visibles (avec filtres appliques)</li>
                    <li><strong>Export selection</strong> : Exporte uniquement les elements coches</li>
                </ul>

                <h3>Procedure</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Filtrer (optionnel)</strong>
                            <p>Appliquez des filtres pour limiter les donnees a exporter.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Selectionner (optionnel)</strong>
                            <p>Cochez les lignes specifiques a exporter, ou laissez vide pour tout exporter.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Cliquer sur "Exporter"</strong>
                            <p>Le fichier CSV se telecharge automatiquement.</p>
                        </div>
                    </div>
                </div>

                <div class="doc-info">
                    <strong>Format :</strong> UTF-8 avec BOM, separateur point-virgule (;) pour compatibilite Excel francais.
                </div>
            </section>

            {{-- Import CSV --}}
            <section id="import" class="doc-section">
                <h2>Import CSV</h2>
                <p>Importez des contacts en masse a partir d'un fichier CSV.</p>

                <h3>Format attendu</h3>
                <p>Le fichier doit contenir une ligne d'en-tete avec les noms de colonnes. Les colonnes reconnues automatiquement sont :</p>

                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Champ</th>
                                <th>Colonnes acceptees</th>
                                <th>Obligatoire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nom</td>
                                <td><code>nom</code>, <code>last_name</code>, <code>lastname</code></td>
                                <td><span class="badge badge-danger">Oui</span></td>
                            </tr>
                            <tr>
                                <td>Prenom</td>
                                <td><code>prenom</code>, <code>first_name</code>, <code>firstname</code></td>
                                <td><span class="badge badge-danger">Oui</span></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><code>email</code>, <code>mail</code>, <code>e-mail</code></td>
                                <td><span class="badge badge-secondary">Non</span></td>
                            </tr>
                            <tr>
                                <td>Telephone</td>
                                <td><code>telephone</code>, <code>phone</code>, <code>tel</code></td>
                                <td><span class="badge badge-secondary">Non</span></td>
                            </tr>
                            <tr>
                                <td>Adresse</td>
                                <td><code>adresse</code>, <code>address</code></td>
                                <td><span class="badge badge-secondary">Non</span></td>
                            </tr>
                            <tr>
                                <td>Code postal</td>
                                <td><code>code_postal</code>, <code>postal_code</code>, <code>cp</code></td>
                                <td><span class="badge badge-secondary">Non</span></td>
                            </tr>
                            <tr>
                                <td>Ville</td>
                                <td><code>ville</code>, <code>city</code></td>
                                <td><span class="badge badge-secondary">Non</span></td>
                            </tr>
                            <tr>
                                <td>Pays</td>
                                <td><code>pays</code>, <code>country</code></td>
                                <td><span class="badge badge-secondary">Non</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Procedure d'import</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Preparer le fichier</strong>
                            <p>Creez un fichier CSV avec la premiere ligne contenant les en-tetes.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Acceder a l'import</strong>
                            <p>Cliquez sur "Importer" dans la liste des contacts.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Selectionner et importer</strong>
                            <p>Choisissez votre fichier et cliquez sur "Importer".</p>
                        </div>
                    </div>
                </div>

                <div class="doc-warning">
                    <strong>Attention :</strong> Les lignes sans nom ou prenom seront ignorees. Verifiez le rapport d'import pour les erreurs eventuelles.
                </div>
            </section>

            {{-- Actions groupees --}}
            <section id="actions-groupees" class="doc-section">
                <h2>Actions groupees</h2>
                <p>Effectuez des operations sur plusieurs elements en une seule action.</p>

                <h3>Actions disponibles</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Module</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Contacts</strong></td>
                                <td>Exporter selection, Activer, Desactiver, Supprimer</td>
                            </tr>
                            <tr>
                                <td><strong>Adhesions</strong></td>
                                <td>Exporter selection, Supprimer</td>
                            </tr>
                            <tr>
                                <td><strong>Dons</strong></td>
                                <td>Exporter selection, Marquer recu envoye, Marquer recu non envoye, Supprimer</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Utilisation</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Selectionner les elements</strong>
                            <p>Cochez les cases a gauche des lignes souhaitees. Utilisez "Tout selectionner" pour la page entiere.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Choisir l'action</strong>
                            <p>La barre d'actions apparait des qu'une selection est faite. Cliquez sur l'action souhaitee.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Confirmer</strong>
                            <p>Pour les actions destructives (suppression), une confirmation est demandee.</p>
                        </div>
                    </div>
                </div>

                <div class="doc-warning">
                    <strong>Attention :</strong> La suppression est irreversible. Les contacts avec des adhesions ou dons associes ne peuvent pas etre supprimes.
                </div>
            </section>

            {{-- Contacts --}}
            <section id="contacts" class="doc-section">
                <h2>Gestion des Contacts</h2>
                <p>La section Contacts permet de gerer toutes les personnes en relation avec l'association : adherents, anciens adherents, acheteurs de produits, donateurs, etc.</p>

                <h3>Contacts vs Adherents</h3>
                <div class="doc-info">
                    <strong>Important :</strong> Un <strong>contact</strong> est toute personne enregistree dans la base. Un <strong>adherent</strong> est un contact ayant une adhesion active. Un contact peut donc etre :
                    <ul style="margin-top: 0.5rem;">
                        <li>Un adherent actif (adhesion en cours)</li>
                        <li>Un ancien adherent (adhesion expiree)</li>
                        <li>Un simple acheteur de produits (sans adhesion)</li>
                        <li>Un donateur</li>
                    </ul>
                </div>

                <h3>Types de contacts</h3>
                <ul>
                    <li><strong>Individuel</strong> : personne physique</li>
                    <li><strong>Association</strong> : structure associative (detectee automatiquement a l'import)</li>
                </ul>

                <h3>Liste des contacts</h3>
                <p>Affiche tous les contacts avec leurs informations principales. Utilisez les filtres pour affiner la recherche :</p>
                <ul>
                    <li><strong>Recherche</strong> : par nom, prenom ou email</li>
                    <li><strong>Type</strong> : individuel ou association</li>
                    <li><strong>Statut</strong> : actif ou inactif</li>
                    <li><strong>Adhesion</strong> : active, expiree ou sans adhesion</li>
                </ul>

                <h3>Ajouter un contact</h3>
                <p>Cliquez sur "Nouveau contact" et remplissez le formulaire :</p>
                <ul>
                    <li><strong>Civilite</strong> : M., Mme, etc.</li>
                    <li><strong>Nom / Prenom</strong> : nom obligatoire</li>
                    <li><strong>Email</strong> : adresse de contact principale (optionnel)</li>
                    <li><strong>Adresse</strong> : pour l'envoi des publications</li>
                    <li><strong>Options</strong> : newsletter, annuaire, etc.</li>
                </ul>

                <h3>Fiche contact</h3>
                <p>Chaque fiche contact affiche :</p>
                <ul>
                    <li><strong>Informations personnelles</strong> : coordonnees, adresse</li>
                    <li><strong>Adhesions</strong> : historique complet avec type et montant</li>
                    <li><strong>Achats</strong> : produits achetes (magazines, hors-series, etc.)</li>
                    <li><strong>Dons</strong> : historique des dons</li>
                </ul>

                <div class="doc-warning">
                    <strong>Attention :</strong> Un contact ne peut pas etre supprime s'il a des adhesions, achats ou dons associes.
                </div>
            </section>

            {{-- Structures --}}
            <section id="structures" class="doc-section">
                <h2>Structures</h2>
                <p>Les structures permettent d'organiser les membres en groupes hierarchiques (antennes regionales, groupes locaux, etc.).</p>

                <h3>Types de structures</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Exemple</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge badge-danger">National</span></td>
                                <td>Structure nationale de l'association</td>
                                <td>OREINA National</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-warning">Regional</span></td>
                                <td>Antenne regionale</td>
                                <td>Groupe Auvergne-Rhone-Alpes</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-info">Departemental</span></td>
                                <td>Groupe departemental</td>
                                <td>Antenne Rhone (69)</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-secondary">Local</span></td>
                                <td>Groupe local</td>
                                <td>Section Lyon-Villeurbanne</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Hierarchie</h3>
                <p>Les structures sont organisees en arborescence :</p>
                <ul>
                    <li>Une structure peut avoir une <strong>structure parente</strong></li>
                    <li>Une structure peut contenir plusieurs <strong>sous-structures</strong></li>
                    <li>La vue arbre permet de visualiser la hierarchie complete</li>
                </ul>

                <h3>Gestion des membres</h3>
                <p>Chaque structure peut avoir des membres avec differents roles :</p>
                <ul>
                    <li><strong>Responsable</strong> : responsable de la structure</li>
                    <li><strong>Correspondant</strong> : contact principal</li>
                    <li><strong>Tresorier</strong> : gestion financiere locale</li>
                    <li><strong>Secretaire</strong> : gestion administrative</li>
                    <li><strong>Membre</strong> : membre simple</li>
                </ul>

                <h3>Creer une structure</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Acceder aux structures</strong>
                            <p>Cliquez sur "Structures" dans le menu Contacts.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Nouvelle structure</strong>
                            <p>Cliquez sur "Nouvelle structure" et remplissez les informations.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Definir la hierarchie</strong>
                            <p>Selectionnez une structure parente si necessaire.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">4</span>
                        <div class="step-content">
                            <strong>Ajouter des membres</strong>
                            <p>Utilisez "Gerer les membres" pour associer des contacts a la structure.</p>
                        </div>
                    </div>
                </div>

                <div class="doc-info">
                    <strong>Astuce :</strong> Le code d'une structure doit etre unique. Utilisez une convention comme REG-ARA pour "Regional Auvergne-Rhone-Alpes" ou DEP-69 pour "Departemental Rhone".
                </div>
            </section>

            {{-- Carte interactive --}}
            <section id="carte" class="doc-section">
                <h2>Carte Interactive</h2>
                <p>La carte permet de visualiser geographiquement vos contacts sur le territoire francais.</p>

                <h3>Vue d'ensemble</h3>
                <p>La carte affiche les statistiques en haut :</p>
                <ul>
                    <li><strong>Total contacts</strong> : nombre total d'adherents</li>
                    <li><strong>Geolocalises</strong> : contacts avec coordonnees GPS</li>
                    <li><strong>Non geolocalises</strong> : contacts sans coordonnees (adresse incomplete ou non geocodee)</li>
                </ul>

                <h3>Affichage des marqueurs</h3>
                <ul>
                    <li><strong>Clusters</strong> : les marqueurs proches sont regroupes automatiquement. Cliquez sur un cluster pour zoomer.</li>
                    <li><strong>Couleurs</strong> : chaque type de contact a une couleur differente (adherent, donateur, prospect, etc.)</li>
                    <li><strong>Fiche rapide</strong> : cliquez sur un marqueur pour voir les informations du contact</li>
                </ul>

                <h3>Filtres</h3>
                <p>Cliquez sur "Filtres" pour affiner l'affichage :</p>
                <ul>
                    <li><strong>Type de contact</strong> : cochez/decochez les types a afficher</li>
                    <li><strong>Departements</strong> : filtrer par code departement (2 premiers chiffres du code postal)</li>
                    <li><strong>Statut</strong> : afficher uniquement les contacts actifs ou inactifs</li>
                </ul>

                <div class="doc-info">
                    <strong>Astuce :</strong> L'option "Autres" dans les departements affiche les contacts sans code postal ou hors zone principale.
                </div>

                <h3>Recherche par rayon</h3>
                <p>Pour trouver les contacts dans un perimetre donne :</p>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Activer la recherche</strong>
                            <p>Cliquez sur "Recherche par rayon". Le curseur devient une croix.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Definir le centre</strong>
                            <p>Saisissez une adresse et cliquez "Localiser", ou cliquez directement sur la carte.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Ajuster le rayon</strong>
                            <p>Utilisez le curseur pour definir la distance (1 a 100 km).</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">4</span>
                        <div class="step-content">
                            <strong>Rechercher</strong>
                            <p>Cliquez sur "Rechercher". Le nombre de contacts trouves s'affiche.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">5</span>
                        <div class="step-content">
                            <strong>Exporter (optionnel)</strong>
                            <p>Cliquez sur "Exporter CSV" pour telecharger la liste avec les distances.</p>
                        </div>
                    </div>
                </div>

                <h3>Geocodage</h3>
                <p>Les contacts sont geolocalises automatiquement a partir de leur adresse.</p>
                <ul>
                    <li><strong>Geocodage automatique</strong> : cliquez sur "Geocoder les contacts" pour traiter les adresses non geolocalisees</li>
                    <li><strong>Service utilise</strong> : OpenStreetMap Nominatim (gratuit, limite a 1 requete/seconde)</li>
                    <li><strong>Prerequis</strong> : l'adresse doit etre complete (ville au minimum)</li>
                </ul>

                <div class="doc-warning">
                    <strong>Attention :</strong> Le geocodage peut prendre du temps si vous avez beaucoup de contacts. Le processus peut etre arrete a tout moment.
                </div>
            </section>

            {{-- Adhesions --}}
            <section id="adhesions" class="doc-section">
                <h2>Gestion des Adhesions</h2>
                <p>Suivez les adhesions annuelles des contacts. Le systeme distingue deux periodes : avant 2026 (systeme historique) et a partir de 2026 (nouveau systeme).</p>

                <h3>Systeme historique (2008-2025)</h3>
                <p>Avant 2026, le montant paye par un adherent comprenait :</p>
                <ul>
                    <li><strong>Adhesion</strong> : 5€ fixes</li>
                    <li><strong>Achat magazine</strong> : le reste du montant (ex: 35€ = 5€ adhesion + 30€ magazine)</li>
                </ul>
                <div class="doc-info">
                    Les donnees historiques ont ete importees depuis les fichiers CSV de l'association. Chaque adhesion historique est liee a un achat de magazine correspondant.
                </div>

                <h3>Nouveau systeme (2026+)</h3>
                <p>A partir de 2026, l'adhesion inclut l'acces gratuit au magazine en ligne. Les types disponibles sont :</p>
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Prix</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Individuelle France</strong></td>
                            <td>20€</td>
                            <td>Adhesion standard pour les particuliers en France</td>
                        </tr>
                        <tr>
                            <td><strong>Famille</strong></td>
                            <td>25€</td>
                            <td>Adhesion pour un foyer familial</td>
                        </tr>
                        <tr>
                            <td><strong>Bienfaiteur</strong></td>
                            <td>50€</td>
                            <td>Adhesion avec contribution majoree</td>
                        </tr>
                        <tr>
                            <td><strong>Personne morale</strong></td>
                            <td>50€</td>
                            <td>Pour les associations et organisations</td>
                        </tr>
                        <tr>
                            <td><strong>Hors France</strong></td>
                            <td>20€</td>
                            <td>Adhesion pour les residents hors France metropolitaine</td>
                        </tr>
                        <tr>
                            <td><strong>Petit budget</strong></td>
                            <td>12€</td>
                            <td>Tarif reduit sur justificatif</td>
                        </tr>
                    </tbody>
                </table>

                <h3>Statuts</h3>
                <ul>
                    <li><span class="badge badge-warning">En attente</span> : paiement non confirme</li>
                    <li><span class="badge badge-success">Active</span> : adhesion en cours de validite</li>
                    <li><span class="badge badge-secondary">Expiree</span> : adhesion arrivee a echeance</li>
                </ul>

                <h3>Fiche contact</h3>
                <p>Sur la fiche de chaque contact, vous trouverez :</p>
                <ul>
                    <li>L'historique complet des adhesions (avec montant et type)</li>
                    <li>L'historique des achats (magazines, hors-series, etc.)</li>
                    <li>L'historique des dons</li>
                </ul>
            </section>

            {{-- Cartes d'adherent --}}
            <section id="cartes-adherent" class="doc-section">
                <h2>Cartes d'adherent</h2>
                <p>Generez des cartes d'adherent personnalisees au format PDF pour vos membres.</p>

                <h3>Format des cartes</h3>
                <ul>
                    <li><strong>Taille</strong> : format carte de credit (85,6 x 54 mm)</li>
                    <li><strong>Contenu</strong> : nom, numero d'adherent, type d'adhesion, dates de validite</li>
                    <li><strong>Securite</strong> : code de verification unique</li>
                </ul>

                <h3>Generation individuelle</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Acceder a la liste</strong>
                            <p>Allez dans Adhesions > Cartes d'adherent.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Apercu ou telechargement</strong>
                            <p>Cliquez sur l'icone oeil pour un apercu, ou l'icone telechargement pour obtenir le PDF.</p>
                        </div>
                    </div>
                </div>

                <h3>Generation par lot</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Selectionner les adherents</strong>
                            <p>Cochez les adherents souhaites dans la liste.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Telecharger les cartes</strong>
                            <p>Cliquez sur "Telecharger les cartes" pour generer un PDF A4 avec plusieurs cartes par page.</p>
                        </div>
                    </div>
                </div>

                <div class="doc-info">
                    <strong>Note :</strong> Seuls les adherents avec une adhesion active peuvent avoir une carte. Les adhesions expirees ne sont pas incluses.
                </div>
            </section>

            {{-- Dons --}}
            <section id="dons" class="doc-section">
                <h2>Gestion des Dons</h2>
                <p>Enregistrez et suivez les dons recus par l'association.</p>

                <h3>Enregistrer un don</h3>
                <ul>
                    <li>Selectionnez le donateur (adherent existant ou nouveau)</li>
                    <li>Indiquez le montant et la date</li>
                    <li>Choisissez le mode de paiement</li>
                    <li>Indiquez si un recu fiscal doit etre emis</li>
                </ul>

                <h3>Recus fiscaux</h3>
                <p>Les recus fiscaux (Cerfa) peuvent etre generes automatiquement pour les dons eligibles. Cliquez sur l'icone PDF dans la liste des dons.</p>
            </section>

            {{-- Produits --}}
            <section id="produits" class="doc-section">
                <h2>Gestion des Produits</h2>
                <p>Gerez le catalogue des produits proposes par l'association : magazines, hors-series, inscriptions aux rencontres, etc.</p>

                <h3>Types de produits</h3>
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Exemple</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge badge-info">Magazine</span></td>
                            <td>Numero annuel de la revue OREINA</td>
                            <td>Magazine OREINA 2024</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-secondary">Hors-serie</span></td>
                            <td>Publications speciales hors abonnement</td>
                            <td>Hors-serie Papillons de nuit</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-success">Rencontre</span></td>
                            <td>Inscription aux rencontres annuelles</td>
                            <td>Rencontre OREINA 2026 - Pyrenees</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-default">Autre</span></td>
                            <td>Autres produits (goodies, posters, etc.)</td>
                            <td>Poster Lepidopteres de France</td>
                        </tr>
                    </tbody>
                </table>

                <h3>Creer un produit</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Acceder au catalogue</strong>
                            <p>Allez dans Finances > Produits et cliquez sur "Nouveau produit".</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Remplir les informations</strong>
                            <p>Nom, type, prix, annee (pour les magazines), description.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Activer le produit</strong>
                            <p>Cochez "Actif" pour que le produit soit disponible a la vente.</p>
                        </div>
                    </div>
                </div>

                <div class="doc-info">
                    <strong>Note :</strong> Les produits "Magazine" des annees 2008 a 2025 ont ete crees automatiquement lors de l'import des donnees historiques.
                </div>
            </section>

            {{-- Achats --}}
            <section id="achats" class="doc-section">
                <h2>Gestion des Achats</h2>
                <p>Suivez les achats de produits effectues par les contacts de l'association.</p>

                <h3>Sources des achats</h3>
                <ul>
                    <li><span class="badge badge-warning">Import</span> : achats issus de l'import des donnees historiques</li>
                    <li><span class="badge badge-info">Manuel</span> : achats saisis manuellement dans l'extranet</li>
                    <li><span class="badge badge-success">HelloAsso</span> : achats provenant de la plateforme HelloAsso</li>
                </ul>

                <h3>Enregistrer un achat</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Acceder aux achats</strong>
                            <p>Allez dans Finances > Achats et cliquez sur "Nouvel achat".</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Selectionner le contact</strong>
                            <p>Choisissez le contact dans la liste deroulante.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Choisir le produit</strong>
                            <p>Selectionnez le produit : le prix unitaire sera pre-rempli automatiquement.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">4</span>
                        <div class="step-content">
                            <strong>Completer les details</strong>
                            <p>Quantite, date d'achat, moyen de paiement, notes eventuelles.</p>
                        </div>
                    </div>
                </div>

                <h3>Achats historiques</h3>
                <p>Les achats de magazines importes depuis les donnees historiques (2008-2025) sont lies aux adhesions correspondantes. Sur la fiche d'un achat importe, vous verrez le lien vers l'adhesion d'origine.</p>

                <h3>Export</h3>
                <p>Exportez la liste des achats au format CSV en cliquant sur "Exporter". Vous pouvez filtrer par periode, produit ou contact avant l'export.</p>
            </section>

            {{-- Benevolat --}}
            <section id="benevolat" class="doc-section">
                <h2>Benevolat</h2>
                <p>Le module Benevolat permet de suivre les activites benevoles de l'association et de valoriser l'engagement des membres.</p>

                <h3>Tableau de bord</h3>
                <p>Le tableau de bord presente une vue d'ensemble du benevolat :</p>
                <ul>
                    <li><strong>Statistiques annuelles</strong> : nombre d'activites, heures totales, benevoles actifs</li>
                    <li><strong>Activites a venir</strong> : prochaines activites planifiees avec nombre d'inscrits</li>
                    <li><strong>Activites recentes</strong> : dernieres activites terminees avec presence</li>
                    <li><strong>Top benevoles</strong> : classement des benevoles les plus actifs de l'annee</li>
                </ul>

                <h3>Types d'activites</h3>
                <p>Les activites sont classees par type pour faciliter le suivi :</p>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Sortie terrain</strong></td>
                                <td>Prospection, inventaire, observation sur le terrain</td>
                            </tr>
                            <tr>
                                <td><strong>Animation</strong></td>
                                <td>Animations nature, ateliers pedagogiques</td>
                            </tr>
                            <tr>
                                <td><strong>Stand</strong></td>
                                <td>Presence sur un stand lors d'evenements</td>
                            </tr>
                            <tr>
                                <td><strong>Reunion</strong></td>
                                <td>Reunions internes, comites, AG</td>
                            </tr>
                            <tr>
                                <td><strong>Formation</strong></td>
                                <td>Sessions de formation ou d'initiation</td>
                            </tr>
                            <tr>
                                <td><strong>Bureau</strong></td>
                                <td>Travail administratif, saisie de donnees</td>
                            </tr>
                            <tr>
                                <td><strong>Communication</strong></td>
                                <td>Redaction, photos, reseaux sociaux</td>
                            </tr>
                            <tr>
                                <td><strong>Autre</strong></td>
                                <td>Autres types d'activites benevoles</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Creer une activite</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Informations de base</strong>
                            <p>Titre, type d'activite, date et horaires (debut/fin).</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Localisation</strong>
                            <p>Lieu de rendez-vous et ville (optionnels).</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Organisation</strong>
                            <p>Selectionnez un organisateur et eventuellement une structure.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">4</span>
                        <div class="step-content">
                            <strong>Limite de places (optionnel)</strong>
                            <p>Definissez un nombre maximum de participants si necessaire.</p>
                        </div>
                    </div>
                </div>

                <h3>Gestion des participants</h3>
                <p>Une fois l'activite creee, gerez les inscriptions depuis la fiche activite :</p>
                <ul>
                    <li><strong>Ajouter un participant</strong> : selectionnez un membre et son statut initial</li>
                    <li><strong>Modifier le statut</strong> : Inscrit → Confirme → Present ou Absent</li>
                    <li><strong>Saisir les heures</strong> : enregistrez le temps effectue par chaque benevole</li>
                    <li><strong>Marquer tous presents</strong> : action groupee pour valider la presence de tous</li>
                </ul>

                <h3>Statuts de participation</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge badge-info">Inscrit</span></td>
                                <td>Inscription enregistree, en attente de confirmation</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-primary">Confirme</span></td>
                                <td>Participation confirmee par le benevole</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-success">Present</span></td>
                                <td>Le benevole a participe a l'activite</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-danger">Absent</span></td>
                                <td>Le benevole n'a pas pu participer</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-secondary">Annule</span></td>
                                <td>Inscription annulee par le benevole</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Statuts d'activite</h3>
                <div class="doc-workflow">
                    <div class="workflow-item">
                        <span class="workflow-status info">Planifiee</span>
                        <span class="workflow-arrow">→</span>
                    </div>
                    <div class="workflow-item">
                        <span class="workflow-status warning">En cours</span>
                        <span class="workflow-arrow">→</span>
                    </div>
                    <div class="workflow-item">
                        <span class="workflow-status success">Terminee</span>
                    </div>
                </div>
                <p>Une activite peut aussi etre marquee comme <span class="badge badge-secondary">Annulee</span> si elle n'a pas eu lieu.</p>

                <h3>Rapport par benevole</h3>
                <p>Consultez le rapport detaille d'un benevole pour voir :</p>
                <ul>
                    <li>Total d'activites et d'heures sur l'annee selectionnee</li>
                    <li>Repartition par type d'activite</li>
                    <li>Evolution mensuelle de l'engagement</li>
                    <li>Historique complet des participations</li>
                    <li>Statistiques globales (toutes annees confondues)</li>
                </ul>

                <h3>Export</h3>
                <p>Exportez les activites au format CSV pour :</p>
                <ul>
                    <li>Generer des attestations de benevolat</li>
                    <li>Analyser les heures par type ou par structure</li>
                    <li>Preparer des bilans pour les assemblees generales</li>
                </ul>

                <div class="doc-info">
                    <strong>Astuce :</strong> Les heures de benevolat sont automatiquement calculees a partir des horaires de l'activite si vous ne les saisissez pas manuellement pour chaque participant.
                </div>

                <div class="doc-warning">
                    <strong>Important :</strong> N'oubliez pas de marquer les participants comme "Present" apres chaque activite pour que les statistiques soient fiables.
                </div>
            </section>

            {{-- Articles --}}
            <section id="articles" class="doc-section">
                <h2>Articles</h2>
                <p>Publiez des actualites sur le site Hub de l'association.</p>

                <h3>Workflow de publication</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Brouillon</strong>
                            <p>L'article est en cours de redaction.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>En attente</strong>
                            <p>L'article est soumis pour validation.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Publie</strong>
                            <p>L'article est visible sur le site public.</p>
                        </div>
                    </div>
                </div>

                <h3>Categories</h3>
                <p>Chaque article doit etre associe a une categorie : Actualite, Science, Association, etc.</p>
            </section>

            {{-- Evenements --}}
            <section id="evenements" class="doc-section">
                <h2>Evenements</h2>
                <p>Gerez les evenements de l'association (sorties, conferences, assemblees).</p>

                <h3>Informations requises</h3>
                <ul>
                    <li><strong>Titre</strong> : nom de l'evenement</li>
                    <li><strong>Dates</strong> : debut et fin</li>
                    <li><strong>Lieu</strong> : adresse ou indication</li>
                    <li><strong>Description</strong> : details de l'evenement</li>
                    <li><strong>Type</strong> : sortie terrain, conference, AG, etc.</li>
                </ul>
            </section>

            {{-- Groupes de travail --}}
            <section id="groupes-travail" class="doc-section">
                <h2>Groupes de travail</h2>
                <p>Gérez les espaces collaboratifs du réseau OREINA. Les GT sont visibles par les adhérents dans leur espace membre.</p>

                <h3>Créer un groupe</h3>
                <p>Accédez à <strong>Groupes de travail > Nouveau groupe</strong> et renseignez :</p>
                <ul>
                    <li><strong>Nom</strong> : nom du GT (ex: Taxonomie, SeqRef)</li>
                    <li><strong>Description</strong> : objectifs et périmètre du groupe</li>
                    <li><strong>Couleur</strong> : couleur d'affichage sur le dashboard membre</li>
                    <li><strong>URL site web</strong> : lien vers une page de présentation (optionnel)</li>
                    <li><strong>Actif</strong> : cocher pour rendre visible dans l'espace membre</li>
                </ul>

                <h3>Gérer les membres d'un GT</h3>
                <p>Depuis la page d'édition d'un GT, vous pouvez ajouter ou retirer des membres. Chaque membre peut avoir le rôle <code>member</code> ou <code>leader</code>.</p>
            </section>

            {{-- Lepis --}}
            <section id="lepis" class="doc-section">
                <h2>Lepis (bulletins trimestriels)</h2>
                <p>Le bulletin Lepis est un PDF trimestriel mis à disposition des adhérents dans leur espace membre.</p>

                <h3>Publier un bulletin</h3>
                <p>Accédez à <strong>Lepis > Nouveau bulletin</strong> et renseignez :</p>
                <ul>
                    <li><strong>Titre</strong> : titre du bulletin</li>
                    <li><strong>Numéro</strong> et <strong>trimestre</strong> (Q1 à Q4) + <strong>année</strong></li>
                    <li><strong>Fichier PDF</strong> : le bulletin finalisé à uploader</li>
                    <li><strong>Publié</strong> : cocher pour rendre accessible aux adhérents</li>
                </ul>

                <h3>Suggestions d'articles</h3>
                <p>Les adhérents peuvent soumettre des suggestions d'articles via leur espace membre. Consultez-les dans <strong>Suggestions Lepis</strong> pour les marquer comme "noté" ou les supprimer.</p>
            </section>

            {{-- Numeros --}}
            <section id="numeros" class="doc-section">
                <h2>Numeros de la Revue</h2>
                <p>Gerez les numeros publies de la revue scientifique OREINA.</p>

                <h3>Creer un numero</h3>
                <ul>
                    <li><strong>Volume / Numero</strong> : identifiants du numero</li>
                    <li><strong>Date de publication</strong> : mois/annee</li>
                    <li><strong>Couverture</strong> : image de couverture</li>
                    <li><strong>Statut</strong> : en preparation, publie, archive</li>
                </ul>
            </section>

            {{-- Soumissions --}}
            <section id="soumissions" class="doc-section">
                <h2>Workflow editorial -- Chersotis</h2>
                <p>Ce document decrit le processus editorial complet de la revue <strong>Chersotis</strong>, journal scientifique de l'association OREINA consacre aux Lepidopteres de France. Le workflow couvre l'ensemble du cycle de vie d'un manuscrit, depuis la soumission par l'auteur jusqu'a la publication en ligne avec attribution d'un DOI. Trois roles interviennent dans ce processus : l'<strong>auteur</strong>, le <strong>redacteur en chef</strong> (editeur) et les <strong>reviewers</strong> (relecteurs experts). Chaque manuscrit transite par une serie de statuts qui garantissent la rigueur scientifique et la tracabilite editoriale.</p>

                <div class="doc-info">
                    <strong>Important :</strong> L'adhesion a OREINA n'est pas requise pour soumettre un manuscrit a Chersotis. Toute personne disposant d'un compte sur oreina.org peut soumettre un article.
                </div>

                {{-- ========================================== --}}
                {{-- 1. Schema du workflow                      --}}
                {{-- ========================================== --}}

                <h3>Schema du workflow</h3>
                <p>Le parcours principal d'un manuscrit suit les étapes ci-dessous. Deux embranchements sont possibles : la demande de révision (retour à l'auteur) et le rejet (à deux moments du processus). Depuis le P0 du 16 avril 2026, le statut <em>brouillon</em> a été supprimé — la soumission est publiée directement au statut <strong>Soumis</strong>.</p>

                {{-- Visual workflow diagram --}}
                <div class="workflow-diagram">
                    <div class="workflow-main-path">
                        {{-- Submitted --}}
                        <div class="workflow-stage">
                            <div class="workflow-stage-icon submitted">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                </svg>
                            </div>
                            <div class="workflow-stage-label">Soumis</div>
                            <div class="workflow-stage-actor">Auteur ou rédacteur</div>
                        </div>

                        <div class="workflow-connector">
                            <div class="workflow-connector-line"></div>
                            <div class="workflow-connector-arrow"></div>
                        </div>

                        {{-- Desk Review --}}
                        <div class="workflow-stage">
                            <div class="workflow-stage-icon desk-review">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </div>
                            <div class="workflow-stage-label">Eval. initiale</div>
                            <div class="workflow-stage-actor">Editeur</div>
                        </div>

                        <div class="workflow-connector">
                            <div class="workflow-connector-line"></div>
                            <div class="workflow-connector-arrow"></div>
                        </div>

                        {{-- In Review --}}
                        <div class="workflow-stage">
                            <div class="workflow-stage-icon in-review">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                </svg>
                            </div>
                            <div class="workflow-stage-label">En review</div>
                            <div class="workflow-stage-actor">Reviewers</div>
                        </div>

                        <div class="workflow-connector">
                            <div class="workflow-connector-line"></div>
                            <div class="workflow-connector-arrow"></div>
                        </div>

                        {{-- Accepted --}}
                        <div class="workflow-stage">
                            <div class="workflow-stage-icon accepted">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div class="workflow-stage-label">Accepte</div>
                            <div class="workflow-stage-actor">Editeur</div>
                        </div>

                        <div class="workflow-connector">
                            <div class="workflow-connector-line"></div>
                            <div class="workflow-connector-arrow"></div>
                        </div>

                        {{-- Published --}}
                        <div class="workflow-stage">
                            <div class="workflow-stage-icon published">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <div class="workflow-stage-label">Publie</div>
                            <div class="workflow-stage-actor">Editeur</div>
                        </div>
                    </div>

                    {{-- Secondary paths --}}
                    <div class="workflow-branches">
                        <div class="workflow-branch revision">
                            <div class="branch-label">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                Revision demandee
                            </div>
                            <div class="branch-description">L'auteur corrige son manuscrit selon les commentaires des reviewers, puis resoumet. Le manuscrit retourne en review ou est directement accepte.</div>
                        </div>
                        <div class="workflow-branch rejected">
                            <div class="branch-label">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Rejet possible
                            </div>
                            <div class="branch-description">Le rejet peut intervenir a deux moments : lors de l'evaluation initiale (desk reject) ou apres la relecture par les pairs</div>
                        </div>
                    </div>
                </div>

                {{-- ========================================== --}}
                {{-- 2. Etapes detaillees                       --}}
                {{-- ========================================== --}}

                <h3>Etapes detaillees</h3>
                <div class="workflow-details">

                    {{-- 1. Soumis --}}
                    <div class="workflow-detail-card">
                        <div class="detail-header submitted">
                            <span class="detail-number">1</span>
                            <span class="detail-title">Soumis (submitted)</span>
                        </div>
                        <div class="detail-content">
                            <p><strong>Qui agit :</strong> Auteur (soumission directe) ou rédacteur en chef / éditeur (soumission backoffice pour un auteur sans compte — voir section dédiée ci-dessous)</p>
                            <p><strong>Ce qui se passe :</strong> Le manuscrit arrive dans l'extranet. L'éditeur et le rédacteur en chef reçoivent une notification (<code>NewSubmissionAlert</code>). L'auteur reçoit un accusé de réception (<code>SubmissionReceived</code>, ou <code>AccountInvitation</code> s'il a été saisi via le backoffice) et peut suivre l'avancement depuis son espace <code>/revue/mes-soumissions</code>.</p>
                            <p><strong>Action pour passer a l'etape suivante :</strong> L'editeur clique sur "Evaluer" pour demarrer l'evaluation initiale.</p>
                        </div>
                    </div>

                    {{-- 2. Evaluation initiale --}}
                    <div class="workflow-detail-card">
                        <div class="detail-header desk-review">
                            <span class="detail-number">2</span>
                            <span class="detail-title">Evaluation initiale (under_initial_review)</span>
                        </div>
                        <div class="detail-content">
                            <p><strong>Qui agit :</strong> Editeur</p>
                            <p><strong>Ce qui se passe :</strong> L'editeur lit le manuscrit et verifie trois criteres : le sujet correspond-il a la ligne editoriale de Chersotis ? Le manuscrit est-il complet (titre, resume, bibliographie) ? Le formatage est-il correct ?</p>
                            <p><strong>Delai indicatif :</strong> 1 semaine</p>
                            <p><strong>Actions possibles :</strong></p>
                            <ul>
                                <li><span class="badge badge-success">Envoyer en review</span> -- Le manuscrit est recevable, l'editeur l'envoie aux relecteurs</li>
                                <li><span class="badge badge-danger">Rejeter</span> -- Le manuscrit ne correspond pas aux criteres de la revue (desk reject). L'auteur est notifie avec un motif.</li>
                            </ul>
                        </div>
                    </div>

                    {{-- 4. En review --}}
                    <div class="workflow-detail-card">
                        <div class="detail-header in-review">
                            <span class="detail-number">3</span>
                            <span class="detail-title">En relecture (under_peer_review)</span>
                        </div>
                        <div class="detail-content">
                            <p><strong>Qui agit :</strong> Editeur (assignation) + Reviewers (evaluation)</p>
                            <p><strong>Ce qui se passe :</strong> L'editeur assigne 1 a 3 relecteurs experts depuis l'extranet (<code>/extranet/reviews/create</code>). Chaque reviewer recoit un email d'invitation avec une date limite. Les reviewers evaluent le manuscrit de maniere independante.</p>
                            <p><strong>Delai indicatif :</strong> 4 semaines pour chaque reviewer</p>
                            <p><strong>Processus cote reviewer :</strong></p>
                            <ol>
                                <li>Reception de l'email d'invitation</li>
                                <li>Acceptation ou declinaison de la relecture</li>
                                <li>Evaluation du manuscrit (30 jours par defaut)</li>
                                <li>Soumission d'une recommandation et de commentaires (voir section "Decisions" ci-dessous)</li>
                            </ol>
                            <p><strong>Actions de l'editeur une fois les relectures terminees :</strong></p>
                            <ul>
                                <li><span class="badge badge-success">Accepter</span> -- Le manuscrit est accepte pour publication</li>
                                <li><span class="badge badge-warning">Demander revision</span> -- L'auteur doit corriger son manuscrit</li>
                                <li><span class="badge badge-danger">Rejeter</span> -- Le manuscrit n'est pas publiable</li>
                            </ul>
                        </div>
                    </div>

                    {{-- 4b. Revision --}}
                    <div class="workflow-detail-card">
                        <div class="detail-header revision">
                            <span class="detail-number">3b</span>
                            <span class="detail-title">Revision demandee (revision_requested / revision_after_review)</span>
                        </div>
                        <div class="detail-content">
                            <p><strong>Qui agit :</strong> Auteur</p>
                            <p><strong>Ce qui se passe :</strong> L'auteur recoit les commentaires des relecteurs (commentaires destines a l'auteur uniquement -- les commentaires confidentiels pour l'editeur ne sont pas transmis). L'auteur corrige son manuscrit en tenant compte des remarques.</p>
                            <p><strong>Delai indicatif :</strong> 4 semaines</p>
                            <p><strong>Action pour passer a l'etape suivante :</strong> L'auteur soumet la version revisee. Selon l'ampleur des corrections demandees (mineures ou majeures), le manuscrit retourne en relecture ou est directement accepte par l'editeur.</p>
                        </div>
                    </div>

                    {{-- 5. Accepte --}}
                    <div class="workflow-detail-card">
                        <div class="detail-header accepted">
                            <span class="detail-number">4</span>
                            <span class="detail-title">Accepte (accepted)</span>
                        </div>
                        <div class="detail-content">
                            <p><strong>Qui agit :</strong> Editeur</p>
                            <p><strong>Ce qui se passe :</strong> Le manuscrit est accepte pour publication dans Chersotis. L'auteur est notifie. L'editeur prepare la mise en forme finale.</p>
                            <p><strong>Actions disponibles :</strong></p>
                            <ul>
                                <li><strong>Generer le PDF</strong> -- Creer le document final avec la mise en page de Chersotis (en-tete, logo, citation)</li>
                                <li><strong>Assigner un DOI</strong> -- Attribuer un identifiant unique via Crossref ou en local</li>
                                <li><strong>Assigner a un numero</strong> -- Placer l'article dans un numero de Chersotis</li>
                                <li><strong>Definir les pages</strong> -- Indiquer la pagination dans le numero</li>
                            </ul>
                        </div>
                    </div>

                    {{-- 6. Publie --}}
                    <div class="workflow-detail-card">
                        <div class="detail-header published">
                            <span class="detail-number">5</span>
                            <span class="detail-title">Publie (published)</span>
                        </div>
                        <div class="detail-content">
                            <p><strong>Qui agit :</strong> Editeur</p>
                            <p><strong>Ce qui se passe :</strong> L'article est publie et accessible sur le site public de Chersotis a l'adresse <code>/revue/articles/</code>. Le DOI est actif et redirige vers la fiche de l'article.</p>
                            <p><strong>Elements publies :</strong></p>
                            <ul>
                                <li>Fiche article sur le site (titre, auteurs, resume, mots-cles)</li>
                                <li>PDF telechargeable avec mise en page professionnelle</li>
                                <li>DOI actif (ex : 10.24349/xxxx-123)</li>
                                <li>Metadonnees pour les moteurs de recherche academiques</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- ========================================== --}}
                {{-- 3. Roles et responsabilites                --}}
                {{-- ========================================== --}}

                <h3>Roles et responsabilites</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Qui</th>
                                <th>Responsabilites</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Auteur</strong></td>
                                <td>Toute personne disposant d'un compte oreina.org (adhesion non requise)</td>
                                <td>Soumettre un manuscrit complet ; reviser le manuscrit si demande ; accepter les conditions de soumission</td>
                            </tr>
                            <tr>
                                <td><strong>Editeur</strong></td>
                                <td>Redacteur en chef de Chersotis</td>
                                <td>Evaluer la recevabilite des manuscrits (desk review) ; assigner les reviewers ; prendre la decision editoriale finale (accepter, demander revision, rejeter) ; preparer la publication (DOI, PDF, numero)</td>
                            </tr>
                            <tr>
                                <td><strong>Reviewer</strong></td>
                                <td>Expert du domaine, invite par l'editeur</td>
                                <td>Accepter ou decliner l'invitation ; evaluer le manuscrit dans le delai imparti ; soumettre une recommandation argumentee ; fournir des commentaires pour l'auteur et des commentaires confidentiels pour l'editeur</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- ========================================== --}}
                {{-- 4. Delais indicatifs                       --}}
                {{-- ========================================== --}}

                <h3>Delais indicatifs</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Etape</th>
                                <th>Responsable</th>
                                <th>Delai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Evaluation initiale (desk review)</td>
                                <td>Editeur</td>
                                <td>1 semaine</td>
                            </tr>
                            <tr>
                                <td>Relecture par les pairs (peer review)</td>
                                <td>Reviewers</td>
                                <td>4 semaines</td>
                            </tr>
                            <tr>
                                <td>Revision du manuscrit</td>
                                <td>Auteur</td>
                                <td>4 semaines</td>
                            </tr>
                            <tr>
                                <td>Preparation de la publication</td>
                                <td>Editeur</td>
                                <td>Variable (selon le calendrier du numero)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- ========================================== --}}
                {{-- 5. Contenu de la soumission                --}}
                {{-- ========================================== --}}

                <h3>Contenu de la soumission</h3>
                <p>Pour soumettre un manuscrit a Chersotis, l'auteur doit fournir les elements suivants via le formulaire en ligne (<code>/revue/mes-soumissions/nouvelle</code>) :</p>

                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Champ</th>
                                <th>Obligatoire</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Titre</strong></td>
                                <td>Oui</td>
                                <td>Titre complet du manuscrit</td>
                            </tr>
                            <tr>
                                <td><strong>Resume</strong></td>
                                <td>Oui</td>
                                <td>Resume de l'article (abstract)</td>
                            </tr>
                            <tr>
                                <td><strong>Mots-cles</strong></td>
                                <td>Oui</td>
                                <td>Mots-cles pour l'indexation</td>
                            </tr>
                            <tr>
                                <td><strong>Co-auteurs</strong></td>
                                <td>Non</td>
                                <td>Liste des co-auteurs eventuels</td>
                            </tr>
                            <tr>
                                <td><strong>Manuscrit Word</strong></td>
                                <td>Oui</td>
                                <td>Format <code>.doc</code> ou <code>.docx</code> uniquement (pas PDF). Images intégrées au bon emplacement dans le document. Max 30 Mo. Validé par MIME <code>finfo</code> + inspection du ZIP OOXML.</td>
                            </tr>
                            <tr>
                                <td><strong>Fichiers supplémentaires</strong></td>
                                <td>Non</td>
                                <td>Jusqu'à 10 fichiers, 50 Mo chacun. Formats : <code>.xls</code>, <code>.xlsx</code>, <code>.pdf</code>, <code>.zip</code>. Pour tableaux complexes, inventaires faunistiques, données brutes, annexes.</td>
                            </tr>
                            <tr>
                                <td><strong>Captcha</strong></td>
                                <td>Oui (en prod)</td>
                                <td>Cloudflare Turnstile, transparent en mode "Managed"</td>
                            </tr>
                            <tr>
                                <td><strong>Conditions</strong></td>
                                <td>Oui</td>
                                <td>Acceptation des conditions de soumission</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="doc-info">
                    <strong>Décision de la réunion Chersotis du 7 avril 2026 :</strong> le format de soumission est Word, pas PDF. Cette décision facilite les allers-retours éditoriaux (annotations Word) et la relecture. Les images haute résolution (photos 300 DPI, graphiques 600 DPI PNG) ne sont demandées qu'après acceptation, en phase de maquettage.<br>
                    <strong>Stockage sécurisé :</strong> les fichiers uploadés sont stockés dans <code>storage/app/submissions/{id}/{type}/</code>, hors du répertoire <code>public/</code> (pas d'exécution PHP possible, pas d'URL directe). Le téléchargement passe par la route authentifiée <code>journal.submissions.file.download</code> avec vérification de la policy <code>SubmissionPolicy::viewFile</code>.
                </div>

                <h3>Fiche soumission enrichie (backoffice)</h3>
                <p>La page de détail d'une soumission dans l'extranet (<code>/extranet/submissions/{id}</code>) a été enrichie avec 4 blocs éditoriaux :</p>

                <ul>
                    <li><strong>Boutons de transition</strong> — remplacent l'ancien formulaire de changement de statut. Les boutons affichés sont calculés dynamiquement par la policy <code>SubmissionPolicy::transitionTo</code> et passent par la machine à états (pas de changement de statut direct).</li>
                    <li><strong>Équipe éditoriale</strong> — affiche l'éditeur assigné en permanence, et le maquettiste <em>uniquement à partir du stade accepté</em> (pas de maquettiste en amont, le champ serait prématuré). Le rédacteur en chef peut changer l'éditeur ou assigner un maquettiste depuis des formulaires inline.</li>
                    <li><strong>Carte Fichiers</strong> — placée en haut de la colonne droite et mise en valeur (accent turquoise) tant que la soumission est en phase d'évaluation/relecture/révision. Les liens de téléchargement du manuscrit et du PDF final sont accessibles à tout stade.</li>
                    <li><strong>Invitation de relecteurs</strong> — formulaire inline pour inviter un relecteur parmi les utilisateurs avec la capacité <code>reviewer</code>. La séparation des rôles est vérifiée (un relecteur ne peut pas être l'éditeur du même article sauf override). La liste des relecteurs actuels avec leur statut (invité, accepté, terminé, décliné) est affichée en dessous.</li>
                    <li><strong>Journal des actions</strong> — chronologie complète et non-anonymisée de toutes les transitions : changements de statut, assignations éditeur, invitations relecteurs, avec noms des acteurs/cibles et notes. Visible par les éditeurs et administrateurs uniquement.</li>
                </ul>

                <div class="doc-info">
                    <strong>Différence avec le suivi auteur :</strong> le journal des actions admin montre <em>tout</em> (acteurs, assignations internes, notes). Le journal côté auteur (<code>/revue/mes-soumissions/{id}</code>) est anonymisé et ne montre que les changements de statut avec des libellés humains — voir la section "Suivi par l'auteur" ci-dessous.
                </div>

                <h3>Soumission backoffice (pour un auteur sans compte)</h3>
                <p>Lorsqu'un article arrive hors de la plateforme (email, Drive, transition depuis le magazine Oreina), un <strong>rédacteur en chef</strong> ou un <strong>éditeur</strong> peut le saisir directement depuis <code>/extranet/submissions/create</code>. Ce bouton "Nouvelle soumission" n'est visible que pour ces deux rôles (Gate <code>create-submission-for-author</code>).</p>

                <h4>Deux modes dans le formulaire</h4>
                <ul>
                    <li><strong>Auteur existant</strong> — dropdown classique listant tous les utilisateurs. Utilisé quand l'auteur a déjà un compte oreina.org.</li>
                    <li><strong>Nouvel auteur</strong> — saisie de <code>nom</code> + <code>email</code> uniquement. Le système crée un <em>compte fantôme</em> (User avec <code>password = null</code>, <code>invited_at = now</code>) et déclenche une <strong>invitation par mail</strong> avec un lien signé (14 jours) pour que l'auteur définisse son mot de passe et active son compte. Ce compte devient un User normal dès activation.</li>
                </ul>

                <div class="doc-warning">
                    <strong>Séparation claire des responsabilités :</strong>
                    <ul style="margin-top:0.5rem;margin-bottom:0;">
                        <li>Le champ <code>submitted_by_user_id</code> trace qui a saisi la soumission (différent de <code>author_id</code>). Vaut <code>null</code> quand l'auteur se soumet lui-même.</li>
                        <li>Tant que l'auteur n'a pas activé son compte (<code>claimed_at IS NULL</code>), il apparaît dans le dropdown "Auteur existant" avec le suffixe <em>(compte non activé)</em>, et il n'a aucune capacité éditoriale (donc invisible dans les dropdowns éditeur/relecteur).</li>
                        <li>Le lien d'invitation est une URL signée HMAC Laravel — pas de table de tokens à gérer. Expiration configurable via <code>config('journal.invitation_expiration_days')</code> (14 jours par défaut).</li>
                    </ul>
                </div>

                <h4>Mails envoyés à la création</h4>
                <ul>
                    <li><strong>Auteur existant</strong> → <code>SubmissionReceived</code> (accusé de réception) + <code>NewSubmissionAlert</code> (à tous les rédacteurs en chef et éditeurs)</li>
                    <li><strong>Nouvel auteur</strong> → <code>AccountInvitation</code> (active le compte + sert d'accusé de réception : contient le titre + les 7 étapes du process) + <code>NewSubmissionAlert</code></li>
                </ul>

                <h4>Cas d'usage typique (articles en transition)</h4>
                <p>Les articles en attente déposés sur le Drive Oreina seront saisis via ce flow : le rédacteur en chef de la revue renseigne le titre, les mots-clés, le résumé (100 caractères min) et uploade le manuscrit Word/PDF (30 Mo max). L'éditeur reçoit sa notification, l'auteur reçoit son mail d'invitation, et tout le workflow éditorial standard prend le relais à partir de là.</p>

                <h3>Rejet avec recommandation Lepis</h3>
                <p>Quand un article est jugé mieux adapté au bulletin <strong>Lepis</strong> (publication interne OREINA, format plus court) qu'à Chersotis, l'éditeur peut le rediriger sans prononcer un rejet direct devant l'auteur. Trois étapes :</p>
                <ol>
                    <li><strong>Éditeur rejette + coche « Recommander pour Lepis »</strong> dans la modale de rejet. Le statut passe à <code>rejected_pending_lepis</code> (invisible pour l'auteur, qui continue à voir son dernier statut public). Les administrateurs et rédacteurs en chef reçoivent un mail <code>LepisQueueNotification</code>.</li>
                    <li><strong>Un admin ouvre la File Lepis</strong> (<code>/extranet/revue/file-lepis</code>, menu dédié en sidebar avec badge numérique du nombre en attente) et décide : « Transmettre à Lepis » ou « Rejeter définitivement ».</li>
                    <li><strong>Transmettre à Lepis</strong> → statut terminal <code>redirected_to_lepis</code> + mail à l'auteur (« Votre article a été transmis au bulletin Lepis, le rédacteur en chef prendra contact ») + mail <code>LepisArticleReceived</code> aux users avec la capacité <code>lepis_editor</code> (contiennent titre, email auteur, motifs, lien vers fiche). <strong>Rejeter définitivement</strong> → flow de rejet standard, l'auteur reçoit <code>SubmissionDecision</code> avec motifs.</li>
                </ol>
                <div class="doc-info">
                    <strong>Capacité lepis_editor :</strong> à accorder via <code>/extranet/users/{id}/edit</code> (section « Capacités éditoriales Chersotis », formulaire indépendant avec bouton dédié « Enregistrer les capacités »).
                </div>

                <h3>Gabarit PDF — paramètres de maquette</h3>
                <p>Le rendu PDF LaTeX est paramétré dans <code>config/latex.php</code> (valeurs surchargeables via <code>.env</code>). Les décisions de la réunion Chersotis du 16 avril 2026 (section 10) sont appliquées par défaut :</p>
                <ul>
                    <li><strong>Police</strong> : sans-serif (helvetica / Latin Modern Sans via <code>LATEX_FONT_MAIN</code>)</li>
                    <li><strong>Titres H1 (sections) et titre article</strong> : vert Chersotis <code>#2C5F2D</code> (charte OREINA, préféré au bleu/teal)</li>
                    <li><strong>Titres H2</strong> : noir, non-italique</li>
                    <li><strong>Corps de texte</strong> : aligné à gauche (non justifié) via <code>\RaggedRight</code> — meilleure lisibilité, accessibilité dyslexie</li>
                    <li><strong>Marges main content</strong> (pages 2+) : <code>left=60mm, right=20mm</code> → largeur utile ~130 mm (vs 160 mm précédemment, spec §10)</li>
                    <li><strong>Bibliographie</strong> : taille réduite (<code>\small</code>, 1 cran en dessous du corps)</li>
                    <li><strong>Figures / Tableaux</strong> : numérotation automatique via compteurs natifs LaTeX (<code>\figure</code>, <code>\table</code>) — cf. section numérotation ci-dessous</li>
                </ul>
                <div class="doc-info">
                    <strong>Personnalisation :</strong> pour modifier une couleur ou une marge sans toucher au code, utilisez les variables d'environnement (<code>LATEX_COLOR_TITLE_GREEN</code>, <code>LATEX_BODY_MARGIN_LEFT</code>, <code>LATEX_BODY_ALIGNMENT</code> — <code>ragged</code> ou <code>justified</code>, etc.).
                </div>

                <h3>Numérotation automatique des figures et tableaux</h3>
                <p>Dans l'éditeur de blocs (<code>/extranet/submissions/{id}/layout</code>), chaque bloc image affiche un badge <strong>« Figure N »</strong> (teal) et chaque bloc table un badge <strong>« Tableau N »</strong> (indigo). La numérotation est <strong>recalculée automatiquement</strong> à chaque déplacement / ajout / suppression de bloc — l'éditeur ou le maquettiste ne renseigne que la légende descriptive (plus de « Fig. 1 - … » saisi à la main).</p>
                <p>Sur la page publique de l'article (<code>/revue/articles/{slug}</code>), les figures et tableaux sont affichés avec leur numéro en préfixe : <code>Figure 1. &lt;légende&gt;</code>, <code>Tableau 1. &lt;légende&gt;</code>. Les compteurs sont indépendants (une table entre deux images n'incrémente pas le numéro des figures).</p>
                <p>Côté PDF LaTeX, la numérotation utilise déjà le compteur natif <code>\figure</code> / <code>\table</code> — aucune action requise côté template. Les sous-figures (<em>Figure 1a, 1b</em>) ne sont pas encore implémentées : hors scope MVP, viendra avec un champ <code>sub_figures</code> dans une phase ultérieure.</p>

                <h3>Checklist conformité éditeur avant maquettage</h3>
                <p>Sur la fiche d'une soumission (<code>/extranet/submissions/{id}</code>), la colonne de droite affiche une card <strong>« Checklist conformité »</strong> (9 items, accent orange). L'éditeur coche au fil de sa relecture les points de conformité formelle qu'il a vérifiés :</p>
                <ul>
                    <li>Format bibliographique (Harvard, cohérence citations/biblio)</li>
                    <li>Affiliations complètes pour tous les auteurs</li>
                    <li>Coordonnées de correspondance (email auteur référent)</li>
                    <li>Figures numérotées et légendées</li>
                    <li>Remerciements présents (financements, permissions)</li>
                    <li>Résumé FR + EN + mots-clés</li>
                    <li>Droits images / copyright vérifiés</li>
                    <li>Conflits d'intérêt déclarés</li>
                    <li>Données supplémentaires identifiées</li>
                </ul>
                <p>Chaque clic est <strong>sauvegardé instantanément</strong> (pas de bouton « Enregistrer » à chercher) — un badge « ✓ enregistré » apparaît brièvement pour le confirmer. Le compteur en haut à droite de la card affiche l'état (ex. <code>7/9</code>).</p>
                <div class="doc-info">
                    <strong>Non bloquante :</strong> la checklist n'empêche pas de cliquer « Passer en maquettage » même incomplète — c'est un aide-mémoire, pas un garde-fou. Une fois l'article au statut <code>in_production</code> (ou au-delà), la card devient <strong>en lecture seule</strong> : témoin figé de ce qui a été vérifié avant mise en page.<br>
                    <strong>Permissions :</strong> seuls l'éditeur assigné, le rédacteur en chef et les admins peuvent cocher. Reviewers et auteurs ne voient pas cette card.
                </div>

                <h3>Pagination continue (Tomes annuels)</h3>
                <p>Chersotis utilise une pagination continue par tome annuel : le premier article du tome commence à la page 1, le suivant commence à la page suivant la dernière page du précédent.</p>

                <h4>Assigner la pagination</h4>
                <p>Sur la fiche soumission admin (<code>/extranet/submissions/{id}</code>), dans le bloc "Publication", saisir le nombre de pages du PDF et cliquer "Calculer". Le système calcule automatiquement <code>start_page</code> et <code>end_page</code> en fonction des articles déjà paginés dans le même numéro (tome).</p>

                <h4>Prérequis</h4>
                <ul>
                    <li>La soumission doit être rattachée à un numéro (<code>journal_issue_id</code>)</li>
                    <li>Le numéro doit avoir une année (<code>year</code>, remplie automatiquement depuis <code>publication_date</code>)</li>
                    <li>Le nombre de pages du PDF final doit être connu (après maquettage)</li>
                </ul>

                <h4>Format d'affichage</h4>
                <p>Le pied de page du PDF généré affiche : <code>Chersotis, Tome X, pp. Y–Z (année)</code>. La citation bibliographique est au format : <code>Auteur (année). Titre. Chersotis, Tome X, Y–Z.</code></p>

                <div class="doc-info">
                    <strong>Recalcul :</strong> si le nombre de pages change (nouveau PDF maquetté), on peut recalculer. Seul l'article concerné est mis à jour — les articles suivants ne sont pas re-paginés automatiquement (évite les surprises en cascade). Un recalcul global doit être fait manuellement si nécessaire.
                </div>

                <h3>Maquettage et transition automatique</h3>
                <p>Lorsqu'un article est au statut <strong>Accepté</strong> (<code>accepted</code>), cliquer sur le bouton <strong>"Maquetter"</strong> ouvre l'éditeur de blocs et fait passer automatiquement le statut à <strong>"En maquettage"</strong> (<code>in_production</code>). Cette transition est loguée dans la timeline éditoriale.</p>
                <p>Le bouton "Maquetter" et le bloc "Maquette de l'article" sont visibles pour les statuts <code>accepted</code>, <code>in_production</code> et <code>published</code>.</p>

                <h3>Import de documents</h3>
                <p>Dans la page de maquettage d'un article (<code>/extranet/submissions/{id}/layout</code>), un bouton <strong>"Importer un document"</strong> (violet) permet d'uploader un fichier qui est automatiquement converti en blocs de maquette enrichis.</p>

                <h4>Formats acceptés</h4>
                <ul>
                    <li><strong>Word</strong> (<code>.docx</code>) — converti en Markdown côté navigateur (instantané via mammoth.js), puis enrichi par l'IA</li>
                    <li><strong>Markdown</strong> (<code>.md</code>, <code>.txt</code>, <code>.markdown</code>) — enrichi directement par l'IA</li>
                </ul>
                <p>Taille maximale : 5 Mo. Tous les formats passent par le même enrichissement IA.</p>

                <h4>Enrichissement intelligent (Claude Haiku)</h4>
                <p>L'IA analyse le contenu du document et extrait automatiquement :</p>
                <ul>
                    <li><strong>Corps de l'article</strong> — titres hiérarchisés, formatage, tableaux, listes. Le titre, les affiliations, le résumé, les références et les remerciements sont <strong>retirés du corps</strong> et placés dans les champs dédiés.</li>
                    <li><strong>Résumé (français)</strong> — extrait et pré-rempli dans le champ dédié au-dessus de l'éditeur</li>
                    <li><strong>Summary (anglais)</strong> — extrait si présent, sinon <strong>traduit automatiquement</strong> depuis le résumé français</li>
                    <li><strong>Auteurs (affichage)</strong> — noms extraits des affiliations, pré-remplis dans le champ sidebar. Ce champ est utilisé pour l'affichage sur toutes les pages publiques (listing, article, recherche, numéro), indépendamment du compte utilisateur lié à la soumission.</li>
                    <li><strong>Références bibliographiques</strong> — extraites et reformatées en <strong>style Harvard</strong> (noms de revues en italique). Pré-remplies dans le champ sidebar.</li>
                    <li><strong>Affiliations auteurs</strong> — extraites et pré-remplies dans le champ sidebar</li>
                    <li><strong>Remerciements</strong> — extraits et pré-remplis dans le champ sidebar</li>
                    <li><strong>Noms de taxons</strong> — détectés et enrichis avec un <strong>lien vers Artemisiae</strong> (soulignement vert, cliquable vers la fiche espèce)</li>
                    <li><strong>Citations inline</strong> — les renvois bibliographiques dans le texte comme <code>(Dupont, 2023)</code> sont enrichis avec un <strong>tooltip</strong> affichant la référence complète au survol</li>
                    <li><strong>Titre détecté</strong> — si différent du titre de la soumission, un bandeau propose de le mettre à jour</li>
                </ul>

                <h4>Page publique de l'article</h4>
                <p>Sur la page publique, les enrichissements suivants sont visibles :</p>
                <ul>
                    <li><strong>Résumé + Summary</strong> — affichés dans l'encadré dédié (le summary en italique). Remplacent le résumé initial de soumission.</li>
                    <li><strong>Taxons</strong> — les noms d'espèces en italique sont cliquables (lien vert vers Artemisiae)</li>
                    <li><strong>Citations</strong> — au survol d'un renvoi comme (Dupont, 2023), un tooltip dark affiche la référence complète</li>
                    <li><strong>Références</strong> — les noms de revues en italique sont correctement rendus (Markdown <code>*revue*</code> → <em>revue</em>)</li>
                    <li><strong>Métadonnées</strong> — DOI, date, type, logo Open Access et badge CC BY 4.0 dans une grille compacte</li>
                </ul>

                <div class="doc-info">
                    <strong>Attention :</strong> l'import remplace tous les blocs existants et les champs sidebar (avec confirmation).<br>
                    <strong>Prérequis :</strong> la clé API Anthropic doit être configurée (<code>ANTHROPIC_PLATFORM_KEY</code> dans <code>.env</code>). L'enrichissement prend 30 à 120 secondes selon la taille du document.
                </div>

                <h4>Sauvegarde et aperçu PDF</h4>
                <p>L'import Markdown et les modifications de blocs ne sont pas sauvegardés automatiquement en base. Le bouton <strong>"Aperçu PDF"</strong> est désactivé tant que des modifications non sauvegardées existent (le compteur de blocs se met à jour en temps réel). Il faut cliquer <strong>"Sauvegarder"</strong> avant de pouvoir prévisualiser le PDF.</p>

                <h3>Suivi par l'auteur ("suivi de colis")</h3>
                <p>L'auteur connecté accède à ses soumissions via <code>/revue/mes-soumissions</code>. Chaque soumission affiche :</p>

                <h4>Timeline 6 étapes</h4>
                <p>Une barre de progression visuelle simplifiée en 6 étapes (les 10 statuts internes sont regroupés pour l'auteur) :</p>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Étape affichée</th>
                                <th>Statuts internes correspondants</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><strong>Soumis</strong></td><td><code>submitted</code></td></tr>
                            <tr><td><strong>En évaluation</strong></td><td><code>under_initial_review</code>, <code>revision_requested</code></td></tr>
                            <tr><td><strong>Relecture</strong></td><td><code>under_peer_review</code>, <code>revision_after_review</code></td></tr>
                            <tr><td><strong>Décision</strong></td><td><code>accepted</code>, <code>rejected</code></td></tr>
                            <tr><td><strong>Maquettage</strong></td><td><code>in_production</code></td></tr>
                            <tr><td><strong>Publié</strong></td><td><code>published</code></td></tr>
                        </tbody>
                    </table>
                </div>
                <p>Les étapes passées affichent un check vert, l'étape active est surlignée en turquoise. En cas de rejet, l'étape "Décision" affiche une croix rouge.</p>

                <h4>Journal d'activité</h4>
                <p>Sous la timeline, un bloc "Historique" affiche la chronologie des changements d'état avec des <strong>libellés humains</strong> (ex. "Votre manuscrit a été envoyé en relecture"). Les informations internes (noms d'éditeurs, de relecteurs, notes de transition) ne sont <strong>pas visibles</strong> par l'auteur — principe du "suivi de colis".</p>

                <h4>Indicateur "Action requise"</h4>
                <p>Quand le statut est <code>revision_requested</code> ou <code>revision_after_review</code>, un bandeau orange "Action requise" apparaît sur la page détail et un badge pulsant dans la liste. L'auteur est invité à soumettre sa révision via un bouton visible dans les deux cas.</p>

                <div class="doc-info">
                    <strong>Principe "suivi de colis" :</strong> l'auteur voit OÙ en est son article dans le processus, mais pas les détails internes (qui a évalué, qui a relu, notes confidentielles). La transparence est assurée par les libellés humains chronologiques, pas par l'exposition des acteurs.
                </div>

                <h3>Circuit de relecture</h3>
                <p>Le circuit de relecture est entièrement géré par la plateforme, de l'invitation à la soumission de l'évaluation.</p>

                <h4>Invitation d'un relecteur</h4>
                <p>Depuis la fiche soumission (<code>/extranet/submissions/{id}</code>), l'éditeur sélectionne un relecteur dans le bloc "Inviter un relecteur". Un email d'invitation est envoyé automatiquement au relecteur avec :</p>
                <ul>
                    <li>Le titre et le résumé du manuscrit</li>
                    <li>Le nom de l'éditeur qui invite</li>
                    <li>Le délai de relecture attendu (3 semaines par défaut)</li>
                    <li>Un <strong>lien signé</strong> pour accepter ou décliner (pas besoin de se connecter)</li>
                </ul>

                <h4>Acceptation / Déclin</h4>
                <p>Le relecteur clique le lien dans son email et arrive sur une page publique (URL signée, pas de login requis) où il voit le résumé et peut :</p>
                <ul>
                    <li><strong>Accepter</strong> → la date limite est fixée à J+21, l'éditeur est notifié par email, et le relecteur peut accéder au formulaire d'évaluation (nécessite un login).</li>
                    <li><strong>Décliner</strong> → l'éditeur est notifié par email et peut inviter un autre relecteur.</li>
                </ul>

                <h4>Formulaire d'évaluation</h4>
                <p>Le relecteur connecté accède à <code>/revue/relecture/{review}/evaluer</code> et remplit :</p>
                <ul>
                    <li>5 scores (1 à 5) : originalité, méthodologie, clarté, importance, références</li>
                    <li>Commentaires pour l'auteur (obligatoire, transmis avec la décision)</li>
                    <li>Commentaires confidentiels pour l'éditeur (optionnel, non transmis à l'auteur)</li>
                    <li>Recommandation : accepter / révision mineure / révision majeure / rejeter</li>
                    <li>Fichier PDF d'évaluation (optionnel)</li>
                </ul>
                <p>À la soumission, la review passe en statut <code>completed</code> et l'éditeur est notifié par email.</p>

                <h4>Relances automatiques</h4>
                <p>Une commande artisan <code>reviews:send-reminders</code> est schedulée quotidiennement à 08h00. Elle envoie des relances dans deux cas :</p>
                <ul>
                    <li><strong>Invitation sans réponse depuis 7 jours</strong> → email de relance au relecteur</li>
                    <li><strong>Relecture en retard</strong> (date limite dépassée, statut <code>accepted</code> sans <code>completed_at</code>) → email de relance</li>
                </ul>
                <p>Anti-spam : un minimum de 5 jours entre deux relances pour le même relecteur (champ <code>last_reminder_at</code>).</p>

                <div class="doc-info">
                    <strong>Politique non-anonyme :</strong> conformément à la décision du 7 avril 2026, les relecteurs ne sont pas anonymes. Leur identité est communiquée à l'auteur avec leur rapport.
                </div>

                {{-- ========================================== --}}
                {{-- 6. Decisions editoriales                   --}}
                {{-- ========================================== --}}

                <h3>Decisions editoriales</h3>
                <p>Les reviewers soumettent une recommandation parmi les quatre suivantes. L'editeur prend ensuite la decision finale en s'appuyant sur l'ensemble des evaluations recues.</p>

                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Decision</th>
                                <th>Signification</th>
                                <th>Consequence</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge badge-success">Accepter en l'etat</span></td>
                                <td>Le manuscrit est publiable tel quel</td>
                                <td>Statut → <strong>accepted</strong>. L'editeur prepare la publication.</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-warning">Revisions mineures</span></td>
                                <td>Corrections legeres requises (coquilles, clarifications, references)</td>
                                <td>Statut → <strong>revision</strong>. L'auteur corrige et resoumet. L'editeur peut accepter directement apres revision.</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-warning">Revisions majeures</span></td>
                                <td>Modifications substantielles necessaires (methodologie, analyse, structure)</td>
                                <td>Statut → <strong>revision</strong>. L'auteur corrige et resoumet. Le manuscrit repasse generalement en relecture.</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-danger">Rejeter</span></td>
                                <td>Le manuscrit n'est pas publiable dans Chersotis</td>
                                <td>Statut → <strong>rejected</strong>. L'auteur est notifie avec les motifs.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p>Chaque reviewer fournit deux types de commentaires :</p>
                <ul>
                    <li><strong>Commentaires pour l'auteur</strong> -- transmis a l'auteur avec la decision</li>
                    <li><strong>Commentaires confidentiels pour l'editeur</strong> -- visibles uniquement par l'editeur, non transmis a l'auteur</li>
                </ul>

                {{-- ========================================== --}}
                {{-- 7. Publication                             --}}
                {{-- ========================================== --}}

                <h3>Publication d'un article</h3>
                <p>Lorsqu'un manuscrit est <strong>accepte</strong>, une section "Publication" apparait sur sa fiche dans l'extranet. Voici les etapes pour finaliser la publication :</p>

                <div class="doc-steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <strong>Generer le PDF final</strong>
                            <p>Cliquez sur "Generer le PDF" pour creer le document avec la mise en page officielle de Chersotis (en-tete, logo, citation formatee).</p>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <strong>Assigner le DOI</strong>
                            <p>Deux options : "DOI local" (genere un identifiant sans enregistrement externe) ou "Enregistrer sur Crossref" (enregistrement officiel aupres de l'agence internationale).</p>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <strong>Assigner a un numero et publier</strong>
                            <p>Selectionnez le numero de Chersotis, indiquez les pages, puis cliquez sur "Publier l'article". L'article devient accessible sur le site public a l'adresse <code>/revue/articles/</code>.</p>
                        </div>
                    </div>
                </div>

                <div class="doc-info">
                    <strong>Astuce :</strong> Vous pouvez publier en une seule action en utilisant le formulaire "Publication finale" qui combine toutes les etapes (PDF, DOI, publication).
                </div>

                {{-- ========================================== --}}
                {{-- 8. Recapitulatif des statuts et actions    --}}
                {{-- ========================================== --}}

                <h3>Recapitulatif des statuts et actions</h3>
                <p>Depuis avril 2026, le workflow est géré par une machine à états stricte côté serveur. Les boutons de décision affichés dans l'interface sont dérivés des transitions autorisées pour le statut courant et pour le rôle de l'utilisateur.</p>

                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Signification</th>
                                <th>Transitions possibles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>draft</code></td>
                                <td>Brouillon, pas encore soumis</td>
                                <td>→ <code>submitted</code> (auteur)</td>
                            </tr>
                            <tr>
                                <td><code>submitted</code></td>
                                <td>Soumis, en attente de prise en charge</td>
                                <td>→ <code>under_initial_review</code> (auto à la prise en charge) · → <code>rejected</code></td>
                            </tr>
                            <tr>
                                <td><code>under_initial_review</code></td>
                                <td>Évaluation de recevabilité par l'éditeur</td>
                                <td>→ <code>revision_requested</code> · → <code>under_peer_review</code> · → <code>rejected</code></td>
                            </tr>
                            <tr>
                                <td><code>revision_requested</code></td>
                                <td>Retour auteur avant relecture</td>
                                <td>→ <code>under_initial_review</code> (auteur resoumet)</td>
                            </tr>
                            <tr>
                                <td><code>under_peer_review</code></td>
                                <td>Envoyé aux relecteurs</td>
                                <td>→ <code>revision_after_review</code> · → <code>accepted</code> · → <code>rejected</code></td>
                            </tr>
                            <tr>
                                <td><code>revision_after_review</code></td>
                                <td>Retours relecteurs transmis, auteur doit réviser</td>
                                <td>→ <code>under_peer_review</code> (nouvelle relecture) · → <code>accepted</code> · → <code>rejected</code></td>
                            </tr>
                            <tr>
                                <td><code>accepted</code></td>
                                <td>Article accepté, prêt pour maquettage. Passe automatiquement à <code>in_production</code> à l'ouverture de l'éditeur de maquette.</td>
                                <td>→ <code>in_production</code> (auto à l'ouverture du maquettage)</td>
                            </tr>
                            <tr>
                                <td><code>in_production</code></td>
                                <td>En cours de maquettage</td>
                                <td>→ <code>published</code></td>
                            </tr>
                            <tr>
                                <td><code>published</code></td>
                                <td>Publié, visible publiquement (terminal)</td>
                                <td>Aucune</td>
                            </tr>
                            <tr>
                                <td><code>rejected</code></td>
                                <td>Rejeté (terminal). Peut être redirigé vers Lepis.</td>
                                <td>Aucune</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="doc-info">
                    <strong>Machine à états stricte :</strong> toute transition hors des flèches listées ci-dessus est refusée par le service <code>SubmissionStateMachine</code> avec une exception <code>IllegalTransitionException</code>. Chaque transition est loguée dans <code>submission_transitions</code> avec l'acteur, le statut source, le statut cible, la date et les notes.<br>
                    <strong>Transitions automatiques :</strong> la transition <code>submitted → under_initial_review</code> est déclenchée automatiquement quand un éditeur prend en charge un article. Aucun humain ne la déclenche manuellement.<br>
                    <strong>Race condition :</strong> le service utilise un UPDATE conditionnel (<code>WHERE status = current_status</code>) pour empêcher deux actions simultanées de produire un état incohérent.<br>
                    <strong>Redirection Lepis :</strong> lors d'un rejet, une case "Recommander pour Lepis" peut être cochée (<code>redirected_to_lepis = true</code>).
                </div>

                <h3>DOI et exports de citations</h3>
                <p>Chaque article publié peut recevoir un DOI au format <code>10.XXXXX/chersotis.YYYY.NNNN</code> (numérotation séquentielle par année).</p>

                <h4>Attribution du DOI</h4>
                <p>Le bouton "Obtenir le DOI" dans la fiche admin de la soumission (<code>/extranet/submissions/{id}</code>) attribue un DOI via le service <code>CrossrefService</code>. Deux modes :</p>
                <ul>
                    <li><strong>Dry-run</strong> (<code>CROSSREF_DRY_RUN=true</code>, défaut) — le DOI est généré et stocké en base mais aucun dépôt n'est fait auprès de Crossref. Utile tant que l'ISSN n'est pas obtenu.</li>
                    <li><strong>Production</strong> (<code>CROSSREF_DRY_RUN=false</code>) — le DOI est déposé auprès de Crossref via leur API. Nécessite : ISSN obtenu + identifiants Crossref configurés dans <code>.env</code> (<code>CROSSREF_USERNAME</code>, <code>CROSSREF_PASSWORD</code>).</li>
                </ul>
                <p>Le DOI doit être attribué <strong>avant</strong> la génération du PDF final (pour qu'il figure dans le document).</p>

                <h4>Exports de citations</h4>
                <p>Sur la page publique d'un article publié (<code>/articles/{id}</code>), 3 formats sont disponibles :</p>
                <ul>
                    <li><strong>BibTeX</strong> — téléchargement <code>.bib</code> via <code>/articles/{id}/cite/bibtex</code></li>
                    <li><strong>RIS</strong> — téléchargement <code>.ris</code> via <code>/articles/{id}/cite/ris</code> (compatible Zotero, Mendeley, EndNote)</li>
                    <li><strong>Harvard</strong> — copie dans le presse-papier (format auteur-date, bouton "Copier Harvard")</li>
                </ul>
                <p>Les citations incluent automatiquement : auteurs, co-auteurs, titre, journal (Chersotis), tome, pages et DOI.</p>
            </section>

            {{-- Capacités éditoriales Chersotis --}}
            <section id="capacites-editoriales" class="doc-section">
                <h2>Capacités éditoriales Chersotis</h2>

                <p>Le système de <strong>capacités éditoriales</strong> permet d'attribuer à un utilisateur un ou plusieurs rôles dans le workflow de la revue, indépendamment de son rôle global. Introduit en avril 2026 suite à la réunion du comité de rédac du 7 avril.</p>

                <h3>Les 5 capacités</h3>
                <ul>
                    <li><code>chief_editor</code> — <strong>Rédacteur en chef</strong> : supervise l'ensemble du comité, assigne les éditeurs aux articles, modifie les capacités des membres.</li>
                    <li><code>editor</code> — <strong>Éditeur</strong> : prend en charge un article (auto-attribution), désigne les relecteurs, synthétise les retours, valide la version finale, gère les allers-retours auteur.</li>
                    <li><code>reviewer</code> — <strong>Relecteur</strong> : accède au manuscrit assigné, soumet un rapport de relecture. Non anonyme.</li>
                    <li><code>layout_editor</code> — <strong>Maquettiste</strong> : accède aux articles acceptés, crée la maquette, génère le PDF final.</li>
                    <li><code>lepis_editor</code> — <strong>Rédacteur en chef Lepis</strong> : reçoit par mail les articles que Chersotis transmet au bulletin Lepis (via la File Lepis — cf. <a href="#soumissions" style="color:#356B8A;">Rejet avec recommandation Lepis</a>). Peut consulter la fiche admin des articles <code>redirected_to_lepis</code> pour y lire le manuscrit, puis prend contact avec l'auteur <strong>hors plateforme</strong> pour négocier la publication dans Lepis. Introduit en avril 2026.</li>
                </ul>

                <h3>Gérer les capacités d'un utilisateur</h3>
                <p>Sur <code>/extranet/users/{id}/edit</code>, section "Capacités éditoriales Chersotis". Cocher/décocher les capacités voulues, enregistrer. Seuls les administrateurs et rédacteurs en chef peuvent faire cette action (policy <code>SubmissionPolicy::manageCapabilities</code>).</p>

                <h3>Règle de séparation des rôles</h3>
                <p>Pour éviter les conflits d'intérêt, un utilisateur <strong>ne peut pas être à la fois éditeur et relecteur du même article</strong>. Toute tentative d'assignation en conflit renvoie une exception <code>RoleConflictException</code> et un message d'erreur à l'utilisateur.</p>
                <p>Un <strong>override explicite</strong> est possible en cochant la case "forcer" lors de l'assignation (pour les exceptions ponctuelles validées par le groupe). Depuis avril 2026, une <strong>modale de confirmation</strong> s'ouvre au submit et impose de saisir un <strong>motif</strong> (3-500 caractères, obligatoire) — le motif est concaténé à la note standard et enregistré dans <code>submission_transitions.notes</code> sous la forme <code>Override: séparation des rôles forcée — Motif : &lt;texte&gt;</code>.</p>

                <h3>Traçabilité</h3>
                <p>Toutes les assignations (éditeur pris/assigné, maquettiste assigné, relecteur invité) sont loguées dans la table <code>submission_transitions</code> avec l'acteur, la cible, l'horodatage et les notes éventuelles. Cette table sera aussi utilisée pour tracer les transitions de statut (sous-projet C à venir).</p>

                <h3>Distinction capacité globale vs assignation par article</h3>
                <ul>
                    <li>La <strong>capacité</strong> (<code>editorial_capabilities</code>) dit "cet utilisateur <em>peut être</em> éditeur" — c'est l'éligibilité globale.</li>
                    <li>L'<strong>assignation</strong> (<code>submissions.editor_id</code>, <code>submissions.layout_editor_id</code>, <code>reviews.reviewer_id</code>) dit "cet utilisateur <em>est</em> éditeur <em>de cet article précis</em>".</li>
                </ul>
                <p>Un même utilisateur peut donc être éditeur de l'article 42 et relecteur de l'article 57, tant que la règle de séparation est respectée sur chaque article.</p>
            </section>

            {{-- File d'attente éditoriale --}}
            <section id="file-attente" class="doc-section">
                <h2>File d'attente éditoriale</h2>

                <p>La page <code>/extranet/revue/file-attente</code> liste toutes les soumissions qui n'ont pas encore d'éditeur assigné (statut <code>submitted</code> ou <code>under_initial_review</code>). Accessible aux utilisateurs ayant la capacité <code>editor</code>, <code>chief_editor</code> ou le rôle <code>admin</code>.</p>

                <h3>Colonnes affichées</h3>
                <ul>
                    <li><strong>Titre</strong> (avec résumé au survol)</li>
                    <li><strong>Auteur</strong> (auteur principal)</li>
                    <li><strong>Date de soumission</strong></li>
                    <li><strong>Actions</strong> (selon les droits de l'utilisateur courant)</li>
                </ul>

                <h3>Actions disponibles</h3>

                <h4>Prendre en charge (auto-attribution)</h4>
                <p>Un utilisateur avec capacité <code>editor</code> peut cliquer <strong>"Prendre en charge"</strong> sur une soumission sans éditeur. L'article lui est assigné instantanément (<code>submissions.editor_id</code> = son id). Transition loguée : <code>editor_taken</code>.</p>

                <div class="doc-info">
                    <strong>Race condition :</strong> si deux éditeurs cliquent "Prendre en charge" sur le même article au même moment, un seul réussit (update conditionnel SQL <code>WHERE editor_id IS NULL</code>). Le second reçoit un message "Cet article a déjà un éditeur assigné."
                </div>

                <h4>Assigner à... (pour rédac en chef)</h4>
                <p>Un utilisateur avec capacité <code>chief_editor</code> ou rôle <code>admin</code> voit un menu déroulant listant tous les éditeurs éligibles (capacité <code>editor</code>). Sélectionner un éditeur et valider → l'article lui est assigné. Transition loguée : <code>editor_assigned</code>.</p>

                <p>Les éditeurs déjà relecteurs sur l'article en question apparaissent <strong>grisés</strong> avec la mention "(déjà relecteur)" pour signaler le conflit. Une case <strong>"forcer"</strong> à cocher permet d'outrepasser la règle (tracé dans les notes).</p>

                <h3>Où aller ensuite</h3>
                <p>Une fois un article pris en charge, il apparaît dans le dashboard <a href="#mes-articles" class="doc-nav-link" style="display:inline">Mes articles</a> de l'éditeur concerné. La suite du workflow éditorial (invitation relecteurs, décision, publication) est décrite dans la section <a href="#soumissions" class="doc-nav-link" style="display:inline">Workflow editorial</a>.</p>
            </section>

            {{-- Mes articles (dashboard éditeur) --}}
            <section id="mes-articles" class="doc-section">
                <h2>Mes articles (dashboard éditeur)</h2>

                <p>La page <code>/extranet/revue/mes-articles</code> liste toutes les soumissions dont l'utilisateur courant est l'éditeur assigné (<code>submissions.editor_id = auth()->id()</code>). Accessible aux utilisateurs ayant la capacité <code>editor</code>.</p>

                <h3>Colonnes affichées</h3>
                <ul>
                    <li><strong>Titre</strong></li>
                    <li><strong>Auteur</strong></li>
                    <li><strong>Statut</strong> (soumis, en évaluation, révision demandée, accepté, etc.)</li>
                    <li><strong>Relectures</strong> — nombre de relectures complétées / total assignées (ex. <code>2 / 3</code>)</li>
                </ul>

                <div class="doc-info">
                    <strong>Sous-projet E à venir :</strong> ce dashboard sera enrichi avec le détail de chaque soumission (timeline, boutons pour inviter des relecteurs, décision éditoriale, synthèse des retours). Pour l'instant, c'est une vue de liste simple.
                </div>

                <h3>Transitions tracées</h3>
                <p>Chaque action de l'éditeur (prise en charge, invitation d'un relecteur, changement de statut) est loguée dans <code>submission_transitions</code> avec l'acteur, la cible, la description, et la transition de statut le cas échéant. La timeline complète sera accessible depuis la fiche soumission en sous-projet E.</p>
            </section>

            {{-- Reviews --}}
            <section id="reviews" class="doc-section">
                <h2>Reviews (Evaluations par les pairs)</h2>
                <p>Cette section concerne la gestion des evaluations dans l'extranet. Chaque review correspond a l'evaluation d'un manuscrit par un relecteur expert.</p>

                <div class="doc-info">
                    <strong>Politique de relecture — décision du 7 avril 2026 :</strong> les relecteurs <strong>ne sont pas anonymes</strong>. Leur identité est communiquée à l'auteur avec leur rapport. Il n'y a pas de mécanisme de masquage d'identité.<br>
                    <strong>Séparation des rôles :</strong> un relecteur ne peut pas être simultanément éditeur du même article (et inversement). Voir la section <a href="#capacites-editoriales" class="doc-nav-link" style="display:inline">Capacités éditoriales</a>.
                </div>

                <h3>Assigner un reviewer</h3>
                <p>Depuis l'extranet, accedez a <code>/extranet/reviews/create</code> pour creer une nouvelle evaluation. Selectionnez la soumission concernee, le reviewer (depuis le pool de relecteurs) et la date limite. Un email d'invitation est envoye automatiquement au reviewer selectionne.</p>

                <h3>Statuts des reviews</h3>
                <ul>
                    <li><span class="badge badge-info">Invite</span> -- Le reviewer a recu l'invitation, en attente de reponse</li>
                    <li><span class="badge badge-warning">Accepte</span> -- L'invitation a ete acceptee, l'evaluation est en cours</li>
                    <li><span class="badge badge-danger">Decline</span> -- Le reviewer a refuse l'invitation</li>
                    <li><span class="badge badge-success">Complete</span> -- L'evaluation a ete soumise avec une recommandation</li>
                    <li><span class="badge badge-secondary">Expire</span> -- Le delai est depasse sans reponse du reviewer</li>
                </ul>

                <h3>Recommandations possibles</h3>
                <ul>
                    <li><strong>Accepter</strong> -- Le manuscrit est publiable en l'etat</li>
                    <li><strong>Revisions mineures</strong> -- Corrections legeres requises</li>
                    <li><strong>Revisions majeures</strong> -- Modifications substantielles necessaires</li>
                    <li><strong>Rejeter</strong> -- Le manuscrit n'est pas publiable</li>
                </ul>

                <h3>Notifications email</h3>
                <p>Le systeme gere automatiquement les communications avec les reviewers :</p>
                <ul>
                    <li><strong>Invitation</strong> -- Email envoye automatiquement lors de l'assignation d'une review</li>
                    <li><strong>Rappel</strong> -- Envoi manuel depuis la liste des reviews (selectionner la review puis cliquer "Envoyer rappel")</li>
                </ul>

                <div class="doc-info">
                    <strong>Astuce :</strong> Les reviews en retard sont signalees en rouge dans la liste. Utilisez le filtre "En retard" pour les identifier rapidement et envoyer des rappels cibles.
                </div>
            </section>

            {{-- Utilisateurs --}}
            <section id="utilisateurs" class="doc-section">
                <h2>Gestion des Utilisateurs</h2>
                <p>Gerez les comptes utilisateurs et leurs droits d'acces a l'extranet.</p>

                <h3>Roles disponibles</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Description</th>
                                <th>Acces</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge badge-secondary">Utilisateur</span></td>
                                <td>Compte de base</td>
                                <td>Acces limite au profil personnel</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-info">Auteur</span></td>
                                <td>Auteur scientifique</td>
                                <td>Soumission de manuscrits, suivi de ses soumissions</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-primary">Reviewer</span></td>
                                <td>Evaluateur scientifique</td>
                                <td>Evaluation des manuscrits assignes</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-warning">Editeur</span></td>
                                <td>Editeur de la revue</td>
                                <td>Gestion des soumissions, assignation des reviewers</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-danger">Administrateur</span></td>
                                <td>Acces complet</td>
                                <td>Toutes les fonctionnalites, gestion des utilisateurs</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Capacités éditoriales</h3>
                <p>La page d'édition d'un utilisateur inclut une section pour gérer ses capacités éditoriales Chersotis (4 cases à cocher). Chaque modification est tracée dans <code>audit_logs</code>. Voir la section <a href="#capacites-editoriales" style="color:#356B8A;">Capacités éditoriales</a> pour le détail.</p>

                <h3>Actions disponibles</h3>
                <ul>
                    <li><strong>Creer un utilisateur</strong> : ajouter un nouveau compte avec role et mot de passe</li>
                    <li><strong>Modifier</strong> : changer les informations, le role ou le mot de passe</li>
                    <li><strong>Activer/Desactiver</strong> : bloquer l'acces sans supprimer le compte</li>
                    <li><strong>Supprimer</strong> : suppression definitive du compte</li>
                </ul>

                <h3>Actions groupees</h3>
                <ul>
                    <li><strong>Changer le role</strong> : modifier le role de plusieurs utilisateurs</li>
                    <li><strong>Activer/Desactiver</strong> : changer le statut de plusieurs comptes</li>
                    <li><strong>Exporter</strong> : telecharger la liste au format CSV</li>
                    <li><strong>Supprimer</strong> : suppression groupee (avec confirmation)</li>
                </ul>

                <div class="doc-warning">
                    <strong>Attention :</strong> Seuls les administrateurs peuvent gerer les utilisateurs. Vous ne pouvez pas supprimer votre propre compte.
                </div>
            </section>

            {{-- Permissions --}}
            <section id="permissions" class="doc-section">
                <h2>Permissions par Module</h2>
                <p>Le systeme de permissions permet de controler finement l'acces aux differentes fonctionnalites de l'extranet pour chaque utilisateur.</p>

                <h3>Fonctionnement</h3>
                <ul>
                    <li><strong>Administrateurs</strong> : ont automatiquement toutes les permissions. Pas besoin de configuration.</li>
                    <li><strong>Autres roles</strong> : les permissions doivent etre attribuees individuellement.</li>
                    <li><strong>Cumul</strong> : les permissions s'ajoutent aux droits du role de base.</li>
                </ul>

                <h3>Modules disponibles</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Module</th>
                                <th>Actions possibles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Contacts</strong></td>
                                <td>Voir, Creer, Modifier, Supprimer, Exporter, Importer</td>
                            </tr>
                            <tr>
                                <td><strong>Adhesions</strong></td>
                                <td>Voir, Creer, Modifier, Supprimer, Exporter</td>
                            </tr>
                            <tr>
                                <td><strong>Dons</strong></td>
                                <td>Voir, Creer, Modifier, Supprimer, Exporter, Generer recus</td>
                            </tr>
                            <tr>
                                <td><strong>Articles</strong></td>
                                <td>Voir, Creer, Modifier, Supprimer, Publier</td>
                            </tr>
                            <tr>
                                <td><strong>Evenements</strong></td>
                                <td>Voir, Creer, Modifier, Supprimer, Publier</td>
                            </tr>
                            <tr>
                                <td><strong>Revue</strong></td>
                                <td>Voir, Creer, Modifier, Supprimer, Publier les numeros</td>
                            </tr>
                            <tr>
                                <td><strong>Soumissions</strong></td>
                                <td>Voir, Creer, Modifier, Supprimer, Assigner reviewers, Decider</td>
                            </tr>
                            <tr>
                                <td><strong>Reviews</strong></td>
                                <td>Voir, Creer, Modifier, Supprimer</td>
                            </tr>
                            <tr>
                                <td><strong>Utilisateurs</strong></td>
                                <td>Voir, Creer, Modifier, Supprimer, Gerer permissions</td>
                            </tr>
                            <tr>
                                <td><strong>Parametres</strong></td>
                                <td>Voir, Modifier, Voir statistiques</td>
                            </tr>
                            <tr>
                                <td><strong>RGPD</strong></td>
                                <td>Voir, Traiter alertes, Anonymiser, Modifier parametres</td>
                            </tr>
                            <tr>
                                <td><strong>Carte</strong></td>
                                <td>Voir, Geocoder, Exporter par rayon</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Attribuer des permissions</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Acceder a la fiche utilisateur</strong>
                            <p>Depuis la liste des utilisateurs, cliquez sur un utilisateur.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Ouvrir les permissions</strong>
                            <p>Dans le panneau "Permissions" a droite, cliquez sur "Gerer".</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Selectionner les permissions</strong>
                            <p>Cochez les permissions souhaitees par module. Utilisez "Tout" pour selectionner toutes les actions d'un module.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">4</span>
                        <div class="step-content">
                            <strong>Enregistrer</strong>
                            <p>Cliquez sur "Enregistrer les permissions". Les changements sont immediats.</p>
                        </div>
                    </div>
                </div>

                <div class="doc-info">
                    <strong>Astuce :</strong> Les permissions sont mises en cache pour des performances optimales. Si un utilisateur ne voit pas ses nouvelles permissions, demandez-lui de se deconnecter et reconnecter.
                </div>

                <div class="doc-warning">
                    <strong>Attention :</strong> La permission "Utilisateurs > Gerer permissions" permet a un utilisateur de modifier les permissions d'autres utilisateurs. Attribuez-la avec precaution.
                </div>
            </section>

            {{-- Rapports PDF --}}
            <section id="rapports" class="doc-section">
                <h2>Rapports PDF</h2>
                <p>Le module Rapports permet de generer des documents PDF pour l'analyse et la communication.</p>

                <h3>Types de rapports</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Rapport</th>
                                <th>Description</th>
                                <th>Contenu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Rapport annuel</strong></td>
                                <td>Synthese globale de l'association</td>
                                <td>Resume adhesions, dons, benevolat</td>
                            </tr>
                            <tr>
                                <td><strong>Rapport adhesions</strong></td>
                                <td>Detail des adhesions de l'annee</td>
                                <td>Liste, statistiques par type et paiement</td>
                            </tr>
                            <tr>
                                <td><strong>Rapport dons</strong></td>
                                <td>Historique des dons</td>
                                <td>Evolution mensuelle, par mode de paiement</td>
                            </tr>
                            <tr>
                                <td><strong>Rapport benevolat</strong></td>
                                <td>Bilan des activites benevoles</td>
                                <td>Par type, mensuel, top benevoles</td>
                            </tr>
                            <tr>
                                <td><strong>Attestation individuelle</strong></td>
                                <td>Certificat de benevolat</td>
                                <td>Heures et activites d'un benevole</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Generer un rapport</h3>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Acceder aux rapports</strong>
                            <p>Cliquez sur "Rapports PDF" dans le menu Administration.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Selectionner l'annee</strong>
                            <p>Choisissez l'annee pour laquelle generer le rapport.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Telecharger</strong>
                            <p>Cliquez sur "PDF" pour telecharger le rapport.</p>
                        </div>
                    </div>
                </div>

                <h3>Attestations de benevolat</h3>
                <p>Pour generer une attestation individuelle :</p>
                <ul>
                    <li>Accedez au rapport d'un benevole depuis Benevolat > Tableau de bord</li>
                    <li>Cliquez sur "Attestation PDF" en haut de la page</li>
                    <li>Le document inclut le nom, les heures et la repartition par type d'activite</li>
                    <li>Un espace est prevu pour la signature et le cachet</li>
                </ul>

                <div class="doc-info">
                    <strong>Usage :</strong> Les attestations de benevolat peuvent etre utilisees pour valoriser l'engagement des membres, justifier des heures pour des formations, ou pour les dossiers de demande de subvention.
                </div>
            </section>

            {{-- Brevo --}}
            <section id="brevo" class="doc-section">
                <h2>Brevo (Emails)</h2>
                <p>Le module Brevo permet de synchroniser vos contacts avec la plateforme d'email marketing Brevo (anciennement Sendinblue).</p>

                <h3>Configuration</h3>
                <p>Pour activer l'integration, ajoutez votre cle API dans le fichier <code>.env</code> :</p>
                <pre style="background: #f3f4f6; padding: 1rem; border-radius: 4px; margin: 1rem 0;">BREVO_API_KEY=votre_cle_api</pre>
                <p>Vous pouvez obtenir une cle API depuis votre compte Brevo :</p>
                <ul>
                    <li>Connectez-vous a <a href="https://app.brevo.com" target="_blank">app.brevo.com</a></li>
                    <li>Allez dans Parametres > Cles API</li>
                    <li>Creez une nouvelle cle ou copiez une cle existante</li>
                </ul>

                <h3>Synchronisation</h3>
                <p>Deux methodes de synchronisation sont disponibles :</p>

                <h4>Vers une liste existante</h4>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Selectionner la liste</strong>
                            <p>Choisissez une liste Brevo existante dans le menu deroulant.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Choisir les contacts</strong>
                            <p>Selectionnez le type : tous, abonnes newsletter, ou adherents actifs.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Synchroniser</strong>
                            <p>Les contacts seront ajoutes ou mis a jour dans la liste.</p>
                        </div>
                    </div>
                </div>

                <h4>Creer une nouvelle liste</h4>
                <p>Vous pouvez creer une nouvelle liste et y exporter directement vos contacts en une seule operation.</p>

                <h3>Import depuis Brevo</h3>
                <p>Importez des contacts depuis une liste Brevo vers la base OREINA :</p>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Selectionner la liste source</strong>
                            <p>Choisissez la liste Brevo contenant les contacts a importer.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Choisir le mode d'import</strong>
                            <p><strong>Creer uniquement</strong> : importe les nouveaux contacts, ignore les existants.<br>
                            <strong>MAJ uniquement</strong> : met a jour les contacts existants, ignore les nouveaux.<br>
                            <strong>Creer et MAJ</strong> : importe les nouveaux et met a jour les existants.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Importer</strong>
                            <p>Les contacts seront crees ou mis a jour selon le mode choisi.</p>
                        </div>
                    </div>
                </div>

                <h3>Attributs synchronises</h3>
                <p>Les informations suivantes sont envoyees vers Brevo :</p>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Attribut Brevo</th>
                                <th>Donnee OREINA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>PRENOM</td><td>Prenom du contact</td></tr>
                            <tr><td>NOM</td><td>Nom de famille</td></tr>
                            <tr><td>MEMBRE_NUMERO</td><td>Numero d'adherent</td></tr>
                            <tr><td>VILLE</td><td>Ville</td></tr>
                            <tr><td>CODE_POSTAL</td><td>Code postal</td></tr>
                            <tr><td>EST_ADHERENT</td><td>Adhesion active (true/false)</td></tr>
                            <tr><td>EST_DONATEUR</td><td>A fait un don (true/false)</td></tr>
                            <tr><td>NEWSLETTER</td><td>Abonne newsletter (true/false)</td></tr>
                        </tbody>
                    </table>
                </div>

                <h3>Webhooks</h3>
                <p>Pour synchroniser les desabonnements automatiquement, configurez un webhook dans Brevo :</p>
                <ul>
                    <li>Dans Brevo, allez dans Parametres > Webhooks</li>
                    <li>Creez un nouveau webhook pour l'evenement "Desabonnement"</li>
                    <li>URL : <code>https://votre-domaine.com/api/webhooks/brevo</code></li>
                </ul>

                <div class="doc-warning">
                    <strong>Attention :</strong> La synchronisation met a jour les contacts existants. Les contacts supprimes dans OREINA ne sont pas automatiquement supprimes dans Brevo.
                </div>
            </section>

            {{-- Import/Export --}}
            <section id="import-export" class="doc-section">
                <h2>Import / Export</h2>
                <p>Le module Import/Export permet de gerer vos donnees en masse avec des modeles personnalisables et un historique complet.</p>

                <h3>Modeles d'export</h3>
                <p>Creez des modeles d'export reutilisables pour standardiser vos exports :</p>
                <ul>
                    <li><strong>Nom</strong> : identifiant du modele</li>
                    <li><strong>Type</strong> : contacts, adhesions, dons ou benevolat</li>
                    <li><strong>Colonnes</strong> : selectionnez les champs a inclure</li>
                    <li><strong>Par defaut</strong> : marquez un modele comme defaut pour chaque type</li>
                </ul>

                <h3>Modeles d'import</h3>
                <p>Definissez des mappings personnalises pour importer vos donnees :</p>
                <ul>
                    <li><strong>Mapping</strong> : associez les colonnes de votre fichier aux champs OREINA</li>
                    <li><strong>Synonymes</strong> : definissez plusieurs noms de colonnes possibles</li>
                    <li><strong>Options</strong> : mise a jour des existants, creation uniquement, etc.</li>
                </ul>

                <h3>Historique</h3>
                <p>Chaque operation d'import ou d'export est enregistree :</p>
                <ul>
                    <li><strong>Imports</strong> : nombre de lignes traitees, creees, mises a jour et en erreur</li>
                    <li><strong>Exports</strong> : colonnes exportees, filtres appliques, nombre de lignes</li>
                    <li><strong>Utilisateur</strong> : tracabilite de qui a effectue l'operation</li>
                </ul>

                <h3>Types de donnees</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Import</th>
                                <th>Export</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Contacts</td>
                                <td><span class="badge badge-success">Oui</span></td>
                                <td><span class="badge badge-success">Oui</span></td>
                            </tr>
                            <tr>
                                <td>Adhesions</td>
                                <td><span class="badge badge-success">Oui</span></td>
                                <td><span class="badge badge-success">Oui</span></td>
                            </tr>
                            <tr>
                                <td>Dons</td>
                                <td><span class="badge badge-success">Oui</span></td>
                                <td><span class="badge badge-success">Oui</span></td>
                            </tr>
                            <tr>
                                <td>Benevolat</td>
                                <td><span class="badge badge-secondary">Non</span></td>
                                <td><span class="badge badge-success">Oui</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="doc-info">
                    <strong>Conseil :</strong> Creez des modeles d'export pour vos besoins recurrents (rapport mensuel, export comptable, etc.) afin de gagner du temps.
                </div>
            </section>

            {{-- Emails transactionnels --}}
            <section id="emails" class="doc-section">
                <h2>Emails transactionnels</h2>

                <p>La plateforme envoie des emails automatiques à chaque étape clé du workflow éditorial. Tous les envois sont asynchrones (<code>ShouldQueue</code>) sauf si <code>QUEUE_CONNECTION=sync</code> (envoi immédiat).</p>

                <h3>Liste des emails envoyés</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Événement</th>
                                <th>Destinataire</th>
                                <th>Classe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Inscription (vérification email)</td>
                                <td>Nouvel utilisateur</td>
                                <td><code>App\Notifications\VerifyEmailNotification</code></td>
                            </tr>
                            <tr>
                                <td>Nouvelle soumission (confirmation)</td>
                                <td>Auteur</td>
                                <td><code>App\Mail\SubmissionReceived</code></td>
                            </tr>
                            <tr>
                                <td>Nouvelle soumission (alerte)</td>
                                <td>Éditeurs + Rédac en chef</td>
                                <td><code>App\Mail\NewSubmissionAlert</code></td>
                            </tr>
                            <tr>
                                <td>Invitation à relecture</td>
                                <td>Relecteur invité</td>
                                <td><code>App\Mail\ReviewInvitation</code></td>
                            </tr>
                            <tr>
                                <td>Relecteur accepte</td>
                                <td>Éditeur de l'article</td>
                                <td><code>App\Mail\ReviewerAccepted</code></td>
                            </tr>
                            <tr>
                                <td>Relecteur décline</td>
                                <td>Éditeur de l'article</td>
                                <td><code>App\Mail\ReviewerDeclined</code></td>
                            </tr>
                            <tr>
                                <td>Évaluation déposée</td>
                                <td>Éditeur de l'article</td>
                                <td><code>App\Mail\ReviewCompleted</code></td>
                            </tr>
                            <tr>
                                <td>Décision (accepté/rejeté)</td>
                                <td>Auteur</td>
                                <td><code>App\Mail\SubmissionDecision</code></td>
                            </tr>
                            <tr>
                                <td>Relance invitation / relecture</td>
                                <td>Relecteur en retard</td>
                                <td><code>App\Mail\ReviewReminder</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Configuration des envois</h3>

                <h4>En développement local</h4>
                <p>Le fichier <code>.env</code> doit contenir <code>MAIL_MAILER=log</code>. Les emails ne sont pas envoyés : ils sont écrits dans <code>storage/logs/laravel.log</code>. Utile pour vérifier le contenu et le déclenchement sans serveur SMTP.</p>

                <h4>En production (Brevo)</h4>
                <p>Configurer le driver SMTP Brevo dans <code>.env</code> :</p>
                <pre style="background:#f3f4f6;padding:1rem;border-radius:0.5rem;font-size:0.85rem;overflow-x:auto;">
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=votre-login-brevo
MAIL_PASSWORD=votre-clé-api-smtp
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=revue@oreina.org
MAIL_FROM_NAME="Chersotis — OREINA"</pre>

                <h4>En tests (PHPUnit)</h4>
                <p><code>phpunit.xml</code> force <code>MAIL_MAILER=array</code> : les emails sont interceptés en mémoire et vérifiables via <code>Mail::assertQueued()</code>. Aucun email réel n'est envoyé.</p>

                <h3>Queue et envoi asynchrone</h3>
                <p>Tous les mailables implémentent <code>ShouldQueue</code>. En production avec <code>QUEUE_CONNECTION=redis</code> (ou <code>database</code>), les emails sont mis en file d'attente et envoyés par un worker :</p>
                <pre style="background:#f3f4f6;padding:1rem;border-radius:0.5rem;font-size:0.85rem;">php artisan queue:work</pre>
                <p>En développement avec <code>QUEUE_CONNECTION=sync</code>, les emails sont envoyés immédiatement (synchrone) — peut ralentir les requêtes HTTP.</p>

                <h3>Templates</h3>
                <p>Les templates email sont dans <code>resources/views/emails/</code> au format Markdown Blade (<code>&lt;x-mail::message&gt;</code>). Structure :</p>
                <ul>
                    <li><code>emails/verify-email.blade.php</code> — vérification email (HTML inline, pas Markdown)</li>
                    <li><code>emails/submissions/received.blade.php</code> — confirmation soumission auteur</li>
                    <li><code>emails/submissions/new-alert.blade.php</code> — alerte éditeurs</li>
                    <li><code>emails/submissions/decision.blade.php</code> — décision accepté/rejeté</li>
                    <li><code>emails/review-invitation.blade.php</code> — invitation relecture (avec lien signé)</li>
                    <li><code>emails/review-reminder.blade.php</code> — relance relecture</li>
                    <li><code>emails/reviews/accepted.blade.php</code> — relecteur a accepté</li>
                    <li><code>emails/reviews/declined.blade.php</code> — relecteur a décliné</li>
                    <li><code>emails/reviews/completed.blade.php</code> — évaluation déposée</li>
                </ul>

                <h3>Scheduler</h3>
                <p>La commande <code>reviews:send-reminders</code> est exécutée quotidiennement à 08h00 via le scheduler Laravel. En production, le cron système doit être configuré :</p>
                <pre style="background:#f3f4f6;padding:1rem;border-radius:0.5rem;font-size:0.85rem;">* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1</pre>

                <div class="doc-info">
                    <strong>SPF/DKIM :</strong> en production, s'assurer que le domaine d'envoi (<code>revue@oreina.org</code>) est bien authentifié auprès de Brevo (SPF + DKIM) pour éviter les problèmes de délivrabilité. Configurer dans le dashboard Brevo → Senders → Domain authentication.
                </div>
            </section>

            {{-- Parametres --}}
            <section id="parametres" class="doc-section">
                <h2>Parametres</h2>
                <p>Configurez les options generales de la plateforme.</p>

                <h3>Categories de parametres</h3>

                <h4>Parametres generaux</h4>
                <ul>
                    <li><strong>Nom du site</strong> : nom affiche dans les emails et l'interface</li>
                    <li><strong>Description</strong> : description courte de l'association</li>
                    <li><strong>Email de contact</strong> : adresse principale de contact</li>
                    <li><strong>Mode maintenance</strong> : desactiver temporairement l'acces public</li>
                </ul>

                <h4>Revue scientifique</h4>
                <ul>
                    <li><strong>Nom de la revue</strong> : titre officiel de la publication</li>
                    <li><strong>ISSN</strong> : numero d'identification de la revue</li>
                    <li><strong>Delai d'evaluation</strong> : nombre de jours par defaut pour les reviews</li>
                    <li><strong>Reviewers max</strong> : nombre maximum de reviewers par soumission</li>
                </ul>

                <h4>Emails</h4>
                <ul>
                    <li><strong>Nom expediteur</strong> : nom affiche dans les emails envoyes</li>
                    <li><strong>Adresse expediteur</strong> : adresse "De:" des emails</li>
                    <li><strong>Rappels automatiques</strong> : activer l'envoi automatique de rappels</li>
                    <li><strong>Delai de rappel</strong> : jours avant echeance pour envoyer un rappel</li>
                </ul>

                <h4>Adhesions</h4>
                <ul>
                    <li><strong>Mois de debut</strong> : mois de debut de l'annee d'adhesion</li>
                    <li><strong>Rappel renouvellement</strong> : jours avant expiration pour rappeler</li>
                </ul>

                <h3>Vider le cache</h3>
                <p>Le bouton "Vider le cache" permet de reinitialiser les donnees mises en cache. Utilisez-le apres des modifications importantes ou en cas de probleme d'affichage.</p>

                <div class="doc-info">
                    <strong>Note :</strong> Les modifications de parametres sont appliquees immediatement apres sauvegarde.
                </div>
            </section>

            {{-- Statistiques --}}
            <section id="statistiques" class="doc-section">
                <h2>Statistiques</h2>
                <p>Consultez les indicateurs cles de l'association en temps reel.</p>

                <h3>Indicateurs disponibles</h3>

                <h4>Vue d'ensemble</h4>
                <ul>
                    <li><strong>Utilisateurs</strong> : nombre total et actifs, repartition par role</li>
                    <li><strong>Membres</strong> : nombre total et avec adhesion active</li>
                    <li><strong>Dons</strong> : total de l'annee en cours et cumul historique</li>
                    <li><strong>Numeros de revue</strong> : total et publies</li>
                </ul>

                <h4>Adhesions</h4>
                <ul>
                    <li>Repartition par type d'adhesion</li>
                    <li>Nombre d'adhesions actives vs expirees</li>
                </ul>

                <h4>Revue scientifique</h4>
                <ul>
                    <li><strong>Soumissions</strong> : repartition par statut (soumis, en review, accepte, etc.)</li>
                    <li><strong>Reviews</strong> : repartition par statut et par recommandation</li>
                    <li><strong>Reviews en retard</strong> : nombre d'evaluations depassant l'echeance</li>
                    <li><strong>Temps moyen d'evaluation</strong> : duree moyenne entre acceptation et completion</li>
                </ul>

                <h4>Contenu</h4>
                <ul>
                    <li><strong>Articles</strong> : total et publies, repartition par statut</li>
                    <li><strong>Evenements</strong> : total, a venir et passes</li>
                </ul>

                <h4>Graphiques</h4>
                <ul>
                    <li><strong>Dons par mois</strong> : evolution mensuelle des dons sur l'annee en cours</li>
                </ul>

                <div class="doc-info">
                    <strong>Astuce :</strong> Les statistiques sont calculees en temps reel. Actualisez la page pour obtenir les dernieres donnees.
                </div>
            </section>

            {{-- RGPD --}}
            <section id="rgpd" class="doc-section">
                <h2>RGPD - Conformite</h2>
                <p>Le module RGPD permet de gerer la conformite de vos donnees personnelles selon le Reglement General sur la Protection des Donnees.</p>

                <h3>Tableau de bord RGPD</h3>
                <p>Le tableau de bord presente une vue d'ensemble de la conformite :</p>
                <ul>
                    <li><strong>Alertes par type</strong> : contacts necessitant une revue (sans interaction, non mis a jour, adhesion expiree, donateur inactif)</li>
                    <li><strong>Statistiques des consentements</strong> : repartition des consentements (newsletter, communication, droit a l'image)</li>
                    <li><strong>Etat des donnees</strong> : nombre de contacts anonymises et dans la corbeille</li>
                    <li><strong>Dernieres actions</strong> : historique des actions RGPD recentes</li>
                </ul>

                <h3>Types d'alertes</h3>
                <div class="doc-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Delai par defaut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge badge-warning">Sans interaction</span></td>
                                <td>Contacts sans activite depuis un certain temps</td>
                                <td>36 mois</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-warning">Non mis a jour</span></td>
                                <td>Fiches contacts non modifiees</td>
                                <td>60 mois</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-danger">Adhesion expiree</span></td>
                                <td>Anciens adherents sans renouvellement</td>
                                <td>24 mois</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-secondary">Donateur inactif</span></td>
                                <td>Donateurs sans don recent</td>
                                <td>48 mois</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Traitement des alertes</h3>
                <p>Pour chaque alerte, vous pouvez effectuer une action :</p>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Conserver</strong>
                            <p>Marquer le contact comme revu. Il ne reapparaitra pas avant la prochaine echeance.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Mettre a jour</strong>
                            <p>Le contact necessite une mise a jour des informations. Marque comme revu.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">3</span>
                        <div class="step-content">
                            <strong>Contacter</strong>
                            <p>Envoyer un email au contact pour verifier ses informations.</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">4</span>
                        <div class="step-content">
                            <strong>Anonymiser</strong>
                            <p>Supprimer toutes les donnees personnelles (irreversible).</p>
                        </div>
                    </div>
                </div>

                <div class="doc-warning">
                    <strong>Attention :</strong> L'anonymisation est irreversible. Les donnees personnelles (nom, email, adresse, telephone) seront remplacees par "ANONYME" et ne pourront pas etre recuperees.
                </div>

                <h3>Gestion des consentements</h3>
                <p>Chaque contact dispose de trois types de consentement :</p>
                <ul>
                    <li><strong>Newsletter</strong> : reception des actualites par email</li>
                    <li><strong>Communication</strong> : reception de sollicitations (evenements, appels a dons)</li>
                    <li><strong>Droit a l'image</strong> : utilisation des photos dans les publications</li>
                </ul>

                <p>L'historique de chaque modification de consentement est conserve avec :</p>
                <ul>
                    <li>La date et l'heure du changement</li>
                    <li>La valeur precedente et nouvelle</li>
                    <li>La source (saisie manuelle, import, formulaire, API)</li>
                    <li>L'utilisateur ayant effectue le changement</li>
                </ul>

                <h3>Corbeille</h3>
                <p>Les contacts supprimes sont places dans la corbeille et peuvent etre :</p>
                <ul>
                    <li><strong>Restaures</strong> : remis dans la liste des contacts actifs</li>
                    <li><strong>Supprimes definitivement</strong> : effacement irreversible de toutes les donnees</li>
                </ul>

                <h3>Export de donnees (Droit d'acces)</h3>
                <p>Pour repondre a une demande d'acces aux donnees :</p>
                <div class="doc-steps">
                    <div class="doc-step">
                        <span class="step-number">1</span>
                        <div class="step-content">
                            <strong>Acceder a l'historique</strong>
                            <p>Depuis la fiche contact, cliquez sur "Historique RGPD".</p>
                        </div>
                    </div>
                    <div class="doc-step">
                        <span class="step-number">2</span>
                        <div class="step-content">
                            <strong>Exporter les donnees</strong>
                            <p>Cliquez sur "Exporter les donnees" pour telecharger un fichier JSON contenant toutes les informations du contact.</p>
                        </div>
                    </div>
                </div>

                <h3>Parametres RGPD</h3>
                <p>Les durees de retention peuvent etre configurees dans Parametres > RGPD :</p>
                <ul>
                    <li>Delai sans interaction (defaut: 36 mois)</li>
                    <li>Delai sans mise a jour (defaut: 60 mois)</li>
                    <li>Delai apres expiration adhesion (defaut: 24 mois)</li>
                    <li>Delai d'inactivite donateur (defaut: 48 mois)</li>
                </ul>

                <div class="doc-info">
                    <strong>Recommandation :</strong> Effectuez une revue RGPD mensuelle pour traiter les alertes et maintenir la conformite de vos donnees.
                </div>
            </section>

            {{-- Footer --}}
            <div class="doc-footer">
                <p>Cette documentation est mise a jour regulierement. Pour toute question, contactez l'equipe technique.</p>
            </div>
        </div>
    </div>
</div>

<style>
.documentation-page {
    max-width: 1200px;
    margin: 0 auto;
}

.doc-header {
    text-align: center;
    padding: 2rem;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-sidebar-dark) 100%);
    color: white;
    border-radius: 0.75rem;
    margin-bottom: 2rem;
}

.doc-header h1 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
}

.doc-header p {
    opacity: 0.9;
}

.doc-grid {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 2rem;
}

/* Navigation */
.doc-nav {
    position: sticky;
    top: calc(var(--navbar-height) + 1.5rem);
    height: fit-content;
    background: white;
    border-radius: 0.75rem;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.doc-nav-section {
    margin-bottom: 1.25rem;
}

.doc-nav-section:last-child {
    margin-bottom: 0;
}

.doc-nav-title {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #9ca3af;
    padding: 0.25rem 0.5rem;
    margin-bottom: 0.25rem;
}

.doc-nav-link {
    display: block;
    padding: 0.4rem 0.5rem;
    font-size: 0.875rem;
    color: #4b5563;
    text-decoration: none;
    border-radius: 0.375rem;
    transition: all 0.15s ease;
}

.doc-nav-link:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.doc-nav-link.active {
    background: rgba(53, 107, 138, 0.1);
    color: var(--color-primary);
    font-weight: 500;
}

/* Content */
.doc-content {
    background: white;
    border-radius: 0.75rem;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.doc-section {
    padding-bottom: 2rem;
    margin-bottom: 2rem;
    border-bottom: 1px solid #e5e7eb;
}

.doc-section:last-of-type {
    border-bottom: none;
    margin-bottom: 0;
}

.doc-section h2 {
    font-size: 1.5rem;
    color: #1f2937;
    margin-bottom: 1rem;
    padding-top: 1rem;
}

.doc-section h3 {
    font-size: 1.1rem;
    color: #374151;
    margin: 1.5rem 0 0.75rem;
}

.doc-section h4 {
    font-size: 0.95rem;
    color: #4b5563;
    margin: 1.25rem 0 0.5rem;
    font-weight: 600;
}

.doc-section p {
    color: #4b5563;
    line-height: 1.7;
    margin-bottom: 1rem;
}

.doc-section ul {
    margin: 0.5rem 0 1rem 1.5rem;
    color: #4b5563;
}

.doc-section li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.doc-section code {
    background: #f3f4f6;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    color: #e11d48;
}

/* Info box */
.doc-info {
    background: rgba(53, 107, 138, 0.1);
    border-left: 4px solid var(--color-primary);
    padding: 1rem;
    border-radius: 0 0.5rem 0.5rem 0;
    color: #1f2937;
    line-height: 1.8;
}

/* Warning box */
.doc-warning {
    background: rgba(251, 99, 64, 0.1);
    border-left: 4px solid var(--color-warning);
    padding: 1rem;
    border-radius: 0 0.5rem 0.5rem 0;
    color: #1f2937;
}

/* Steps */
.doc-steps {
    margin: 1rem 0;
}

.doc-step {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px dashed #e5e7eb;
}

.doc-step:last-child {
    border-bottom: none;
}

.step-number {
    width: 32px;
    height: 32px;
    background: var(--color-primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
}

.step-content strong {
    display: block;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.step-content p {
    margin: 0;
    font-size: 0.875rem;
}

/* Workflow */
.doc-workflow {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin: 1rem 0;
}

.workflow-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.workflow-status {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.workflow-status.pending { background: #fef3c7; color: #92400e; }
.workflow-status.info { background: rgba(53, 107, 138, 0.15); color: #2d5a75; }
.workflow-status.warning { background: rgba(251, 99, 64, 0.15); color: #c2410c; }
.workflow-status.success { background: rgba(45, 206, 137, 0.15); color: #059669; }

.workflow-arrow {
    color: #9ca3af;
    font-size: 1.25rem;
}

/* Enhanced Workflow Diagram */
.workflow-diagram {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 1rem;
    padding: 2rem;
    margin: 1.5rem 0;
    border: 1px solid #e2e8f0;
}

.workflow-main-path {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 1rem;
}

.workflow-stage {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 80px;
    text-align: center;
}

.workflow-stage-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-bottom: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.workflow-stage:hover .workflow-stage-icon {
    transform: scale(1.1);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15);
}

.workflow-stage-icon.draft { background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%); }
.workflow-stage-icon.submitted { background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%); }
.workflow-stage-icon.desk-review { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
.workflow-stage-icon.in-review { background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%); }
.workflow-stage-icon.accepted { background: linear-gradient(135deg, #34d399 0%, #10b981 100%); }
.workflow-stage-icon.published { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }

.workflow-stage-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.workflow-stage-actor {
    font-size: 0.65rem;
    color: #6b7280;
    background: white;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    border: 1px solid #e5e7eb;
}

.workflow-connector {
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 20px;
    max-width: 60px;
    margin-top: 1.25rem;
}

.workflow-connector-line {
    flex: 1;
    height: 3px;
    background: linear-gradient(90deg, #cbd5e1 0%, #94a3b8 100%);
    border-radius: 2px;
}

.workflow-connector-arrow {
    width: 0;
    height: 0;
    border-left: 8px solid #94a3b8;
    border-top: 5px solid transparent;
    border-bottom: 5px solid transparent;
}

.workflow-branches {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 2px dashed #e2e8f0;
}

.workflow-branch {
    flex: 1;
    padding: 1rem;
    border-radius: 0.75rem;
    border-left: 4px solid;
}

.workflow-branch.revision {
    background: rgba(251, 191, 36, 0.1);
    border-left-color: #f59e0b;
}

.workflow-branch.rejected {
    background: rgba(239, 68, 68, 0.1);
    border-left-color: #ef4444;
}

.branch-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.workflow-branch.revision .branch-label { color: #b45309; }
.workflow-branch.rejected .branch-label { color: #dc2626; }

.branch-description {
    font-size: 0.8rem;
    color: #6b7280;
    line-height: 1.5;
}

/* Workflow Detail Cards */
.workflow-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin: 1.5rem 0;
}

.workflow-detail-card {
    background: white;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: box-shadow 0.2s;
}

.workflow-detail-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.detail-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: white;
}

.detail-header.draft { background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%); }
.detail-header.submitted { background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%); }
.detail-header.desk-review { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
.detail-header.in-review { background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%); }
.detail-header.revision { background: linear-gradient(135deg, #fb923c 0%, #f97316 100%); }
.detail-header.accepted { background: linear-gradient(135deg, #34d399 0%, #10b981 100%); }
.detail-header.published { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }

.detail-number {
    width: 28px;
    height: 28px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
}

.detail-title {
    font-weight: 600;
    font-size: 0.9rem;
}

.detail-content {
    padding: 1rem;
}

.detail-content p {
    font-size: 0.8rem;
    color: #4b5563;
    margin-bottom: 0.5rem;
    line-height: 1.5;
}

.detail-content p:last-child {
    margin-bottom: 0;
}

.detail-content ul,
.detail-content ol {
    font-size: 0.8rem;
    color: #4b5563;
    margin: 0.5rem 0;
    padding-left: 1.25rem;
}

.detail-content li {
    margin-bottom: 0.25rem;
}

/* Footer */
.doc-footer {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
    text-align: center;
}

.doc-footer p {
    color: #9ca3af;
    font-size: 0.875rem;
}

/* Mobile */
@media (max-width: 768px) {
    .doc-grid {
        grid-template-columns: 1fr;
    }

    .doc-nav {
        position: static;
        margin-bottom: 1rem;
    }

    .doc-workflow {
        flex-direction: column;
        align-items: flex-start;
    }

    .workflow-arrow {
        transform: rotate(90deg);
    }

    .workflow-diagram {
        padding: 1rem;
    }

    .workflow-main-path {
        flex-direction: column;
        align-items: center;
        gap: 0;
    }

    .workflow-stage {
        min-width: unset;
    }

    .workflow-connector {
        flex-direction: column;
        height: 40px;
        width: auto;
        margin-top: 0;
        max-width: unset;
    }

    .workflow-connector-line {
        width: 3px;
        height: 100%;
        flex: 1;
    }

    .workflow-connector-arrow {
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 8px solid #94a3b8;
        border-bottom: none;
    }

    .workflow-branches {
        flex-direction: column;
    }

    .workflow-details {
        grid-template-columns: 1fr;
    }
}

/* Tables */
.doc-section table.doc-table,
.doc-section .doc-table table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
    font-size: 0.875rem;
}

.doc-section table.doc-table th,
.doc-section .doc-table th,
.doc-section table.doc-table td,
.doc-section .doc-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.doc-section table.doc-table th,
.doc-section .doc-table th {
    background: #f9fafb;
    font-weight: 600;
    color: #374151;
}

.doc-section table.doc-table tbody tr:hover,
.doc-section .doc-table tbody tr:hover {
    background: #f9fafb;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for navigation links
    document.querySelectorAll('.doc-nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Update active link
                document.querySelectorAll('.doc-nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // Update active link on scroll
    const sections = document.querySelectorAll('.doc-section');
    const navLinks = document.querySelectorAll('.doc-nav-link');

    window.addEventListener('scroll', function() {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            if (scrollY >= sectionTop) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
});
</script>
@endsection
