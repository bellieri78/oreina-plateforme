<?php

use App\Http\Controllers\Admin\TaxReceiptController;
use App\Http\Controllers\Hub\ArticleController;
use App\Http\Controllers\Hub\EventController;
use App\Http\Controllers\Hub\HomeController;
use App\Http\Controllers\Hub\PageController;
use App\Http\Controllers\Journal\JournalController;
use App\Http\Controllers\Journal\SubmissionController;
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
