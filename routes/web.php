<?php

use App\Http\Controllers\Admin\TaxReceiptController;
use App\Http\Controllers\Hub\ArticleController;
use App\Http\Controllers\Hub\EventController;
use App\Http\Controllers\Hub\HomeController;
use App\Http\Controllers\Hub\PageController;
use App\Http\Controllers\Journal\JournalController;
use App\Http\Controllers\Journal\ReviewFormController;
use App\Http\Controllers\Journal\SubmissionController;
use App\Http\Controllers\Journal\SubmissionFileController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\ProfileController as MemberProfileController;
use App\Http\Controllers\Member\MembershipController as MemberMembershipController;
use App\Http\Controllers\Member\DocumentController as MemberDocumentController;
use App\Http\Controllers\Member\JournalController as MemberJournalController;
use App\Http\Controllers\Member\WorkGroupController as MemberWorkGroupController;
use App\Http\Controllers\Member\LepisController as MemberLepisController;
use App\Http\Controllers\Member\CommunityController as MemberCommunityController;
use App\Http\Controllers\Member\ChatController as MemberChatController;
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
Route::get('/lepis', [PageController::class, 'lepis'])->name('hub.lepis');

// Authentification membre (depuis le hub)
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Hub\AuthController as HubAuthController;
Route::get('/connexion', [HubAuthController::class, 'showLogin'])->name('hub.login')->middleware('guest');
Route::post('/connexion', [HubAuthController::class, 'login'])->name('hub.login.submit')->middleware('guest');
Route::get('/inscription', [HubAuthController::class, 'showRegister'])->name('hub.register')->middleware('guest');
Route::post('/inscription', [HubAuthController::class, 'register'])->name('hub.register.submit')->middleware('guest');
Route::post('/deconnexion', [HubAuthController::class, 'logout'])->name('hub.logout')->middleware('auth');

// Email verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Journal Routes (Revue Chersotis)
|--------------------------------------------------------------------------
| En prod : sous-domaine chersotis.oreina.org (routes à la racine /)
| En dev  : préfixe /revue sur localhost (JOURNAL_DOMAIN=localhost)
| Les routes sont définies dans routes/journal.php (fichier dédié).
*/

$journalDomain = config('journal.domain');
$useSubdomain = $journalDomain && $journalDomain !== 'localhost';

if ($useSubdomain) {
    // Production : chersotis.oreina.org/ = racine de la revue
    Route::domain($journalDomain)->name('journal.')->group(base_path('routes/journal.php'));

    // Redirect /revue/* vers le sous-domaine (backward compat)
    Route::prefix('revue')->group(function () use ($journalDomain) {
        Route::get('{any?}', fn(string $any = '') => redirect()->away("https://{$journalDomain}/{$any}", 301))
            ->where('any', '.*');
    });
} else {
    // Dev local : /revue/* comme avant
    Route::prefix('revue')->name('journal.')->group(base_path('routes/journal.php'));
}

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
    Route::get('/groupes-de-travail/{workGroup:slug}', [MemberWorkGroupController::class, 'show'])->name('work-groups.show');

    // Lepis
    Route::get('/lepis', [MemberLepisController::class, 'index'])->name('lepis');
    Route::get('/lepis/suggerer', [MemberLepisController::class, 'suggest'])->name('lepis.suggest');
    Route::post('/lepis/suggerer', [MemberLepisController::class, 'storeSuggestion'])->name('lepis.suggest.store');
    Route::get('/lepis/{bulletin}/telecharger', [MemberLepisController::class, 'download'])->name('lepis.download');

    // Community
    Route::get('/communaute', [MemberCommunityController::class, 'index'])->name('community');
    Route::get('/carte', [MemberCommunityController::class, 'map'])->name('map');

    // Chat
    Route::get('/chat', [MemberChatController::class, 'index'])->name('chat');

    // Mes contributions
    Route::get('/contributions', [MemberWorkGroupController::class, 'contributions'])->name('contributions');
});

/*
|--------------------------------------------------------------------------
| Review Response + Form Routes
|--------------------------------------------------------------------------
| Incluses dans routes/journal.php (chargé ci-dessus via le bloc Journal).
| Les routes review.respond/accept/decline/form sont préfixées par /relecture
| (relatif au domaine journal ou au préfixe /revue selon l'environnement).
*/

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

/*
|--------------------------------------------------------------------------
| Claim Account Routes (Signed URL — invitation auteur Chersotis)
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Auth\ClaimAccountController;

Route::middleware('signed')->group(function () {
    Route::get('/claim-account/{user}', [ClaimAccountController::class, 'show'])
        ->name('account.claim');
    Route::post('/claim-account/{user}', [ClaimAccountController::class, 'store'])
        ->name('account.claim.store');
});

require __DIR__.'/admin.php';
