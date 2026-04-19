<?php

/**
 * Routes de la revue Chersotis.
 *
 * Ce fichier est chargé par routes/web.php, soit sous le sous-domaine
 * chersotis.oreina.org (prod), soit sous le préfixe /revue (dev local).
 * Les routes n'ont PAS de préfixe ici — c'est le fichier appelant qui décide.
 */

use App\Http\Controllers\Journal\JournalController;
use App\Http\Controllers\Journal\ReviewFormController;
use App\Http\Controllers\Journal\ReviewResponseController;
use App\Http\Controllers\Journal\SubmissionController;
use App\Http\Controllers\Journal\SubmissionFileController;

// Page d'accueil revue
Route::get('/', [JournalController::class, 'home'])->name('home');

// Recherche
Route::get('/recherche', [JournalController::class, 'search'])->name('search');

// Articles scientifiques
Route::get('/articles', [JournalController::class, 'articles'])->name('articles.index');
Route::get('/articles/{submission}', [JournalController::class, 'showArticle'])->name('articles.show');
Route::get('/articles/{submission}/cite/{format}', [JournalController::class, 'cite'])
    ->where('format', 'bibtex|ris')
    ->name('articles.cite');

Route::post('/articles/{submission}/share', [JournalController::class, 'trackShare'])
    ->name('articles.share');

// Numéros / Archives
Route::get('/numeros', [JournalController::class, 'issues'])->name('issues.index');
Route::get('/numeros/{issue:slug}', [JournalController::class, 'showIssue'])->name('issues.show');

// Soumission (page statique)
Route::get('/soumettre', [JournalController::class, 'submit'])->name('submit');

// Pages statiques
Route::get('/instructions-auteurs', [JournalController::class, 'authors'])->name('authors');
Route::get('/a-propos', [JournalController::class, 'about'])->name('about');

// Soumissions auteur (authentifiées + email vérifié)
Route::prefix('mes-soumissions')->name('submissions.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [SubmissionController::class, 'index'])->name('index');
    Route::get('/nouvelle', [SubmissionController::class, 'create'])->name('create');
    Route::post('/', [SubmissionController::class, 'store'])->name('store');
    Route::get('/{submission}', [SubmissionController::class, 'show'])->name('show');
    Route::get('/{submission}/revision', [SubmissionController::class, 'edit'])->name('edit');
    Route::put('/{submission}', [SubmissionController::class, 'update'])->name('update');

    Route::post('/{submission}/approuver', [SubmissionController::class, 'approve'])->name('approve');
    Route::post('/{submission}/signaler-corrections', [SubmissionController::class, 'requestCorrections'])->name('request-corrections');

    Route::get('/{submission}/fichier/{path}', [SubmissionFileController::class, 'download'])
        ->where('path', '.*')
        ->name('file.download');
});

// Relecture — réponse invitation (URL signée, pas de login requis)
Route::middleware('signed')->prefix('relecture')->group(function () {
    Route::get('{review}/repondre', [ReviewResponseController::class, 'show'])->name('review.respond');
    Route::post('{review}/accepter', [ReviewResponseController::class, 'accept'])->name('review.accept');
    Route::post('{review}/decliner', [ReviewResponseController::class, 'decline'])->name('review.decline');
});

// Relecture — formulaire d'évaluation (authentifié)
Route::middleware(['auth', 'verified'])->prefix('relecture')->group(function () {
    Route::get('{review}/evaluer', [ReviewFormController::class, 'show'])->name('review.form');
    Route::post('{review}/evaluer', [ReviewFormController::class, 'store'])->name('review.form.store');
});
