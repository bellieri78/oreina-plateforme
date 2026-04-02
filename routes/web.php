<?php

use App\Http\Controllers\Admin\TaxReceiptController;
use App\Http\Controllers\Hub\ArticleController;
use App\Http\Controllers\Hub\EventController;
use App\Http\Controllers\Hub\HomeController;
use App\Http\Controllers\Hub\PageController;
use App\Http\Controllers\Journal\JournalController;
use App\Http\Controllers\Journal\SubmissionController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\ProfileController as MemberProfileController;
use App\Http\Controllers\Member\MembershipController as MemberMembershipController;
use App\Http\Controllers\Member\DocumentController as MemberDocumentController;
use App\Http\Controllers\Member\JournalController as MemberJournalController;
use App\Http\Controllers\Member\WorkGroupController as MemberWorkGroupController;
use App\Http\Controllers\Member\LepisController as MemberLepisController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hub Routes (Site vitrine public)
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('hub.home');

// Articles / Actualités
Route::prefix('actualites')->name('hub.articles.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');
    Route::get('/categorie/{category}', [ArticleController::class, 'category'])->name('category');
    Route::get('/{article:slug}', [ArticleController::class, 'show'])->name('show');
});

// Événements
Route::prefix('evenements')->name('hub.events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{event:slug}', [EventController::class, 'show'])->name('show');
});

// Pages statiques
Route::get('/adhesion', [PageController::class, 'membership'])->name('hub.membership');
Route::get('/a-propos', [PageController::class, 'about'])->name('hub.about');
Route::get('/contact', [PageController::class, 'contact'])->name('hub.contact');

/*
|--------------------------------------------------------------------------
| Journal Routes (Revue scientifique)
|--------------------------------------------------------------------------
*/

Route::prefix('revue')->name('journal.')->group(function () {
    // Page d'accueil revue
    Route::get('/', [JournalController::class, 'home'])->name('home');

    // Recherche
    Route::get('/recherche', [JournalController::class, 'search'])->name('search');

    // Articles scientifiques
    Route::get('/articles', [JournalController::class, 'articles'])->name('articles.index');
    Route::get('/articles/{submission}', [JournalController::class, 'showArticle'])->name('articles.show');

    // Numéros / Archives
    Route::get('/numeros', [JournalController::class, 'issues'])->name('issues.index');
    Route::get('/numeros/{issue:slug}', [JournalController::class, 'showIssue'])->name('issues.show');

    // Soumission
    Route::get('/soumettre', [JournalController::class, 'submit'])->name('submit');

    // Pages statiques
    Route::get('/instructions-auteurs', [JournalController::class, 'authors'])->name('authors');
    Route::get('/a-propos', [JournalController::class, 'about'])->name('about');

    // Soumissions (routes authentifiées)
    Route::prefix('mes-soumissions')->name('submissions.')->group(function () {
        Route::get('/', [SubmissionController::class, 'index'])->name('index');
        Route::get('/nouvelle', [SubmissionController::class, 'create'])->name('create');
        Route::post('/', [SubmissionController::class, 'store'])->name('store');
        Route::get('/{submission}', [SubmissionController::class, 'show'])->name('show');
        Route::get('/{submission}/revision', [SubmissionController::class, 'edit'])->name('edit');
        Route::put('/{submission}', [SubmissionController::class, 'update'])->name('update');
    });
});

/*
|--------------------------------------------------------------------------
| Member Routes (Espace membre)
|--------------------------------------------------------------------------
*/

Route::prefix('espace-membre')->name('member.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [MemberDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profil', [MemberProfileController::class, 'index'])->name('profile');
    Route::put('/profil', [MemberProfileController::class, 'update'])->name('profile.update');
    Route::get('/profil/preferences', [MemberProfileController::class, 'preferences'])->name('profile.preferences');
    Route::put('/profil/preferences', [MemberProfileController::class, 'updatePreferences'])->name('profile.preferences.update');

    // Membership
    Route::get('/adhesion', [MemberMembershipController::class, 'index'])->name('membership');
    Route::get('/adhesion/carte', [MemberMembershipController::class, 'downloadCard'])->name('membership.card');
    Route::get('/adhesion/attestation', [MemberMembershipController::class, 'downloadAttestation'])->name('membership.attestation');

    // Documents
    Route::get('/documents', [MemberDocumentController::class, 'index'])->name('documents');
    Route::get('/documents/cerfa/{donation}', [MemberDocumentController::class, 'downloadCerfa'])->name('documents.cerfa');
    Route::get('/documents/adhesion/{membership}', [MemberDocumentController::class, 'downloadMembershipReceipt'])->name('documents.membership-receipt');

    // Journal
    Route::get('/revue', [MemberJournalController::class, 'index'])->name('journal');
    Route::get('/revue/{issue}/telecharger', [MemberJournalController::class, 'download'])->name('journal.download');

    // Work Groups
    Route::get('/groupes-de-travail', [MemberWorkGroupController::class, 'index'])->name('work-groups');

    // Lepis
    Route::get('/lepis', [MemberLepisController::class, 'index'])->name('lepis');
    Route::get('/lepis/suggerer', [MemberLepisController::class, 'suggest'])->name('lepis.suggest');
    Route::post('/lepis/suggerer', [MemberLepisController::class, 'storeSuggestion'])->name('lepis.suggest.store');
    Route::get('/lepis/{bulletin}/telecharger', [MemberLepisController::class, 'download'])->name('lepis.download');

    // Mes contributions
    Route::get('/contributions', [MemberWorkGroupController::class, 'contributions'])->name('contributions');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Reçus fiscaux)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/donations/{donation}/receipt', [TaxReceiptController::class, 'view'])
        ->name('donation.receipt.view');
    Route::get('/admin/donations/{donation}/receipt/download', [TaxReceiptController::class, 'download'])
        ->name('donation.receipt.download');
});

/*
|--------------------------------------------------------------------------
| Custom Admin Routes (Extranet)
|--------------------------------------------------------------------------
*/

require __DIR__.'/admin.php';
