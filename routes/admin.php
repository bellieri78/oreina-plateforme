<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\DonationController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\JournalIssueController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\DocumentationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\RgpdController;
use App\Http\Controllers\Admin\StructureController;
use App\Http\Controllers\Admin\MemberCardController;
use App\Http\Controllers\Admin\VolunteerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\BrevoController;
use App\Http\Controllers\Admin\ImportExportController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\WorkGroupController;
use App\Http\Controllers\Admin\LepisBulletinController;
use App\Http\Controllers\Admin\LepisSuggestionController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Authentication routes (guest only)
Route::middleware(['web', 'guest'])->prefix('extranet')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
});

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('web');

// Protected admin routes
Route::middleware(['web', 'admin'])->prefix('extranet')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Members (Contacts)
    Route::get('members/export', [MemberController::class, 'export'])->name('members.export');
    Route::get('members/import', [MemberController::class, 'importForm'])->name('members.import');
    Route::post('members/import', [MemberController::class, 'import'])->name('members.import.process');
    Route::post('members/bulk-delete', [MemberController::class, 'bulkDelete'])->name('members.bulk-delete');
    Route::post('members/bulk-status', [MemberController::class, 'bulkStatus'])->name('members.bulk-status');
    Route::resource('members', MemberController::class);

    // Structures (Antennes, groupes locaux)
    Route::get('structures/export', [StructureController::class, 'export'])->name('structures.export');
    Route::get('structures/{structure}/members', [StructureController::class, 'members'])->name('structures.members');
    Route::post('structures/{structure}/members', [StructureController::class, 'addMember'])->name('structures.members.add');
    Route::put('structures/{structure}/members/{member}', [StructureController::class, 'updateMember'])->name('structures.members.update');
    Route::delete('structures/{structure}/members/{member}', [StructureController::class, 'removeMember'])->name('structures.members.remove');
    Route::resource('structures', StructureController::class);

    // Member Cards (Cartes d'adherent)
    Route::prefix('member-cards')->name('member-cards.')->group(function () {
        Route::get('/', [MemberCardController::class, 'index'])->name('index');
        Route::get('/{member}/download', [MemberCardController::class, 'download'])->name('download');
        Route::get('/{member}/preview', [MemberCardController::class, 'preview'])->name('preview');
        Route::get('/{member}/send', [MemberCardController::class, 'send'])->name('send');
        Route::post('/batch', [MemberCardController::class, 'downloadBatch'])->name('batch');
        Route::post('/batch-send', [MemberCardController::class, 'sendBatch'])->name('batch-send');
    });

    // Memberships (Adhesions)
    Route::get('memberships/export', [MembershipController::class, 'export'])->name('memberships.export');
    Route::post('memberships/bulk-delete', [MembershipController::class, 'bulkDelete'])->name('memberships.bulk-delete');
    Route::resource('memberships', MembershipController::class);

    // Products (Produits)
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::resource('products', ProductController::class);

    // Purchases (Achats)
    Route::get('purchases/export', [PurchaseController::class, 'export'])->name('purchases.export');
    Route::post('purchases/bulk-delete', [PurchaseController::class, 'bulkDelete'])->name('purchases.bulk-delete');
    Route::resource('purchases', PurchaseController::class);

    // Donations
    Route::get('donations/export', [DonationController::class, 'export'])->name('donations.export');
    Route::post('donations/bulk-delete', [DonationController::class, 'bulkDelete'])->name('donations.bulk-delete');
    Route::post('donations/bulk-receipt', [DonationController::class, 'bulkReceipt'])->name('donations.bulk-receipt');
    Route::resource('donations', DonationController::class);

    // Articles
    Route::get('articles/export', [ArticleController::class, 'export'])->name('articles.export');
    Route::post('articles/bulk-delete', [ArticleController::class, 'bulkDelete'])->name('articles.bulk-delete');
    Route::post('articles/bulk-status', [ArticleController::class, 'bulkStatus'])->name('articles.bulk-status');
    Route::resource('articles', ArticleController::class);

    // Events
    Route::get('events/export', [EventController::class, 'export'])->name('events.export');
    Route::post('events/bulk-delete', [EventController::class, 'bulkDelete'])->name('events.bulk-delete');
    Route::post('events/bulk-status', [EventController::class, 'bulkStatus'])->name('events.bulk-status');
    Route::resource('events', EventController::class);

    // Journal Issues
    Route::get('journal-issues/export', [JournalIssueController::class, 'export'])->name('journal-issues.export');
    Route::post('journal-issues/bulk-delete', [JournalIssueController::class, 'bulkDelete'])->name('journal-issues.bulk-delete');
    Route::post('journal-issues/bulk-status', [JournalIssueController::class, 'bulkStatus'])->name('journal-issues.bulk-status');
    Route::resource('journal-issues', JournalIssueController::class);

    // Submissions
    Route::get('submissions/export', [SubmissionController::class, 'export'])->name('submissions.export');
    Route::post('submissions/bulk-delete', [SubmissionController::class, 'bulkDelete'])->name('submissions.bulk-delete');
    Route::post('submissions/bulk-status', [SubmissionController::class, 'bulkStatus'])->name('submissions.bulk-status');
    Route::post('submissions/bulk-assign-issue', [SubmissionController::class, 'bulkAssignIssue'])->name('submissions.bulk-assign-issue');
    Route::get('submissions/{submission}/download/{type}', [SubmissionController::class, 'download'])->name('submissions.download');

    // PDF Generation
    Route::post('submissions/{submission}/generate-pdf', [SubmissionController::class, 'generatePdf'])->name('submissions.generate-pdf');
    Route::get('submissions/{submission}/preview-pdf', [SubmissionController::class, 'previewPdf'])->name('submissions.preview-pdf');
    Route::get('submissions/{submission}/download-pdf', [SubmissionController::class, 'downloadPdf'])->name('submissions.download-pdf');

    // DOI Management
    Route::post('submissions/{submission}/register-doi', [SubmissionController::class, 'registerDoi'])->name('submissions.register-doi');
    Route::post('submissions/{submission}/assign-doi', [SubmissionController::class, 'assignDoi'])->name('submissions.assign-doi');

    // Publication
    Route::post('submissions/{submission}/publish', [SubmissionController::class, 'publish'])->name('submissions.publish');

    // Document import + title update
    Route::post('submissions/{submission}/import-markdown', [SubmissionController::class, 'importMarkdown'])
        ->name('submissions.import-markdown');
    Route::patch('submissions/{submission}/update-title', [SubmissionController::class, 'updateTitle'])
        ->name('submissions.update-title');

    // Pagination continue
    Route::post('submissions/{submission}/assign-pages', [SubmissionController::class, 'assignPages'])->name('submissions.assign-pages');

    // Layout / Maquettage
    Route::get('submissions/{submission}/layout', [SubmissionController::class, 'layout'])->name('submissions.layout');
    Route::put('submissions/{submission}/layout', [SubmissionController::class, 'updateLayout'])->name('submissions.layout.update');

    // Conformity checklist
    Route::patch('submissions/{submission}/conformity', [\App\Http\Controllers\Admin\SubmissionController::class, 'updateConformity'])
        ->name('submissions.conformity.update');

    Route::resource('submissions', SubmissionController::class);

    // Reviews
    Route::get('reviews/export', [ReviewController::class, 'export'])->name('reviews.export');
    Route::post('reviews/bulk-delete', [ReviewController::class, 'bulkDelete'])->name('reviews.bulk-delete');
    Route::post('reviews/send-reminder', [ReviewController::class, 'sendReminder'])->name('reviews.send-reminder');
    Route::get('reviews/{review}/download', [ReviewController::class, 'download'])->name('reviews.download');
    Route::resource('reviews', ReviewController::class);

    // Users
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::post('users/bulk-role', [UserController::class, 'bulkRole'])->name('users.bulk-role');
    Route::post('users/bulk-status', [UserController::class, 'bulkStatus'])->name('users.bulk-status');
    Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
    Route::put('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
    Route::put('users/{user}/capabilities', [UserController::class, 'updateCapabilities'])
        ->name('users.capabilities.update');
    Route::resource('users', UserController::class);

    // Chersotis — file d'attente éditoriale
    Route::get('revue/file-attente', [\App\Http\Controllers\Admin\Journal\EditorialQueueController::class, 'index'])
        ->name('journal.queue.index');
    Route::post('revue/file-attente/{submission}/prendre', [\App\Http\Controllers\Admin\Journal\EditorialQueueController::class, 'take'])
        ->name('journal.queue.take');
    Route::post('revue/file-attente/{submission}/assigner', [\App\Http\Controllers\Admin\Journal\EditorialQueueController::class, 'assign'])
        ->name('journal.queue.assign');
    Route::get('revue/mes-articles', [\App\Http\Controllers\Admin\Journal\EditorialQueueController::class, 'mine'])
        ->name('journal.mine');
    Route::post('revue/soumissions/{submission}/transition', [\App\Http\Controllers\Admin\Journal\EditorialQueueController::class, 'transition'])
        ->name('journal.submissions.transition');
    Route::post('revue/soumissions/{submission}/invite-reviewer', [\App\Http\Controllers\Admin\Journal\EditorialQueueController::class, 'inviteReviewer'])
        ->name('journal.submissions.invite-reviewer');
    Route::post('revue/soumissions/{submission}/assign-editor', [\App\Http\Controllers\Admin\Journal\EditorialQueueController::class, 'reassignEditor'])
        ->name('journal.submissions.assign-editor');
    Route::post('revue/soumissions/{submission}/assign-layout-editor', [\App\Http\Controllers\Admin\Journal\EditorialQueueController::class, 'assignLayoutEditor'])
        ->name('journal.submissions.assign-layout-editor');
    Route::get('revue/file-lepis', [\App\Http\Controllers\Admin\Journal\LepisQueueController::class, 'index'])
        ->name('journal.lepis-queue');

    // Documentation
    Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation');

    // Settings & Statistics
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
    Route::get('/statistics', [SettingsController::class, 'statistics'])->name('settings.statistics');

    // Map
    Route::get('/map', [MapController::class, 'index'])->name('map.index');
    Route::get('/map/members', [MapController::class, 'members'])->name('map.members');
    Route::post('/map/geocode', [MapController::class, 'geocode'])->name('map.geocode');
    Route::post('/map/geocode-member/{member}', [MapController::class, 'geocodeMember'])->name('map.geocode-member');
    Route::post('/map/bulk-geocode', [MapController::class, 'bulkGeocode'])->name('map.bulk-geocode');
    Route::get('/map/export-radius', [MapController::class, 'exportRadius'])->name('map.export-radius');
    Route::get('/map/stats', [MapController::class, 'stats'])->name('map.stats');

    // Volunteer (Benevolat)
    Route::prefix('volunteer')->name('volunteer.')->group(function () {
        Route::get('/', [VolunteerController::class, 'index'])->name('index');
        Route::get('/activities', [VolunteerController::class, 'activities'])->name('activities');
        Route::get('/activities/export', [VolunteerController::class, 'export'])->name('export');
        Route::get('/activities/create', [VolunteerController::class, 'create'])->name('create');
        Route::post('/activities', [VolunteerController::class, 'store'])->name('store');
        Route::get('/activities/{activity}', [VolunteerController::class, 'show'])->name('show');
        Route::get('/activities/{activity}/edit', [VolunteerController::class, 'edit'])->name('edit');
        Route::put('/activities/{activity}', [VolunteerController::class, 'update'])->name('update');
        Route::delete('/activities/{activity}', [VolunteerController::class, 'destroy'])->name('destroy');
        Route::post('/activities/{activity}/participants', [VolunteerController::class, 'addParticipant'])->name('participants.add');
        Route::put('/activities/{activity}/participants/{member}', [VolunteerController::class, 'updateParticipant'])->name('participants.update');
        Route::delete('/activities/{activity}/participants/{member}', [VolunteerController::class, 'removeParticipant'])->name('participants.remove');
        Route::post('/activities/{activity}/mark-attended', [VolunteerController::class, 'markAllAttended'])->name('mark-attended');
        Route::get('/members/{member}', [VolunteerController::class, 'memberReport'])->name('member-report');
    });

    // Reports (Rapports)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/memberships', [ReportController::class, 'memberships'])->name('memberships');
        Route::get('/donations', [ReportController::class, 'donations'])->name('donations');
        Route::get('/volunteer', [ReportController::class, 'volunteer'])->name('volunteer');
        Route::get('/volunteer/certificate/{member}', [ReportController::class, 'volunteerCertificate'])->name('volunteer-certificate');
        Route::get('/annual', [ReportController::class, 'annual'])->name('annual');
    });

    // Brevo (Email marketing)
    Route::prefix('brevo')->name('brevo.')->group(function () {
        Route::get('/', [BrevoController::class, 'index'])->name('index');
        Route::post('/create-list', [BrevoController::class, 'createList'])->name('create-list');
        Route::post('/sync', [BrevoController::class, 'sync'])->name('sync');
        Route::post('/export-new', [BrevoController::class, 'exportToNewList'])->name('export-new');
        Route::post('/import', [BrevoController::class, 'importFromList'])->name('import');
        Route::get('/test', [BrevoController::class, 'testConnection'])->name('test');
    });

    // Import/Export
    Route::prefix('import-export')->name('import-export.')->group(function () {
        Route::get('/', [ImportExportController::class, 'index'])->name('index');

        // Import templates
        Route::get('/import-template/create', [ImportExportController::class, 'createImportTemplate'])->name('import-template.create');
        Route::post('/import-template', [ImportExportController::class, 'storeImportTemplate'])->name('import-template.store');
        Route::get('/import-template/{template}/edit', [ImportExportController::class, 'editImportTemplate'])->name('import-template.edit');
        Route::put('/import-template/{template}', [ImportExportController::class, 'updateImportTemplate'])->name('import-template.update');
        Route::delete('/import-template/{template}', [ImportExportController::class, 'destroyImportTemplate'])->name('import-template.destroy');

        // Export templates
        Route::get('/export-template/create', [ImportExportController::class, 'createExportTemplate'])->name('export-template.create');
        Route::post('/export-template', [ImportExportController::class, 'storeExportTemplate'])->name('export-template.store');
        Route::get('/export-template/{template}/edit', [ImportExportController::class, 'editExportTemplate'])->name('export-template.edit');
        Route::put('/export-template/{template}', [ImportExportController::class, 'updateExportTemplate'])->name('export-template.update');
        Route::delete('/export-template/{template}', [ImportExportController::class, 'destroyExportTemplate'])->name('export-template.destroy');

        // Export with template
        Route::get('/export/{template}', [ImportExportController::class, 'exportWithTemplate'])->name('export');

        // Import log
        Route::get('/import-log/{log}', [ImportExportController::class, 'showImportLog'])->name('import-log');

        // API
        Route::get('/columns/{type}', [ImportExportController::class, 'getColumnsForType'])->name('columns');
        Route::get('/mapping/{type}', [ImportExportController::class, 'getMappingForType'])->name('mapping');
    });

    // Work Groups (Groupes de travail)
    Route::resource('work-groups', WorkGroupController::class)->except(['show'])->names('work-groups');
    Route::post('work-groups/{workGroup}/members', [WorkGroupController::class, 'addMember'])->name('work-groups.add-member');
    Route::delete('work-groups/{workGroup}/members/{member}', [WorkGroupController::class, 'removeMember'])->name('work-groups.remove-member');

    // Lepis Bulletins
    Route::resource('lepis', LepisBulletinController::class)->except(['show'])->names('lepis');
    Route::post('lepis/{bulletin}/toggle-publish', [LepisBulletinController::class, 'togglePublish'])->name('lepis.toggle-publish');

    // Lepis Suggestions
    Route::get('lepis-suggestions', [LepisSuggestionController::class, 'index'])->name('lepis-suggestions.index');
    Route::get('lepis-suggestions/{suggestion}', [LepisSuggestionController::class, 'show'])->name('lepis-suggestions.show');
    Route::post('lepis-suggestions/{suggestion}/noted', [LepisSuggestionController::class, 'markAsNoted'])->name('lepis-suggestions.noted');
    Route::delete('lepis-suggestions/{suggestion}', [LepisSuggestionController::class, 'destroy'])->name('lepis-suggestions.destroy');

    // RGPD
    Route::prefix('rgpd')->name('rgpd.')->group(function () {
        Route::get('/', [RgpdController::class, 'index'])->name('index');
        Route::get('/alerts', [RgpdController::class, 'alerts'])->name('alerts');
        Route::post('/process/{member}', [RgpdController::class, 'process'])->name('process');
        Route::post('/bulk-process', [RgpdController::class, 'bulkProcess'])->name('bulk-process');
        Route::get('/settings', [RgpdController::class, 'settings'])->name('settings');
        Route::post('/settings', [RgpdController::class, 'updateSettings'])->name('settings.update');
        Route::get('/trash', [RgpdController::class, 'trash'])->name('trash');
        Route::post('/restore/{id}', [RgpdController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [RgpdController::class, 'forceDelete'])->name('force-delete');
        Route::post('/anonymize/{member}', [RgpdController::class, 'anonymize'])->name('anonymize');
        Route::get('/consent-history/{member}', [RgpdController::class, 'consentHistory'])->name('consent-history');
        Route::post('/update-consents/{member}', [RgpdController::class, 'updateConsents'])->name('update-consents');
        Route::get('/export-member-data/{member}', [RgpdController::class, 'exportMemberData'])->name('export-member-data');
    });
});
