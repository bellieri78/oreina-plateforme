<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use App\View\Composers\MemberLayoutComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.member', MemberLayoutComposer::class);

        \Illuminate\Support\Facades\Gate::policy(
            \App\Models\Submission::class,
            \App\Policies\SubmissionPolicy::class
        );

        \Illuminate\Support\Facades\Gate::define('create-submission-for-author', function (\App\Models\User $user) {
            return $user->hasCapability(\App\Models\EditorialCapability::CHIEF_EDITOR)
                || $user->hasCapability(\App\Models\EditorialCapability::EDITOR);
        });

        \Illuminate\Support\Facades\Gate::define('access-lepis-queue', function (\App\Models\User $user) {
            return $user->isAdmin();
        });

        \Illuminate\Support\Facades\Blade::directive('turnstile', function () {
            return "<?php if (config('services.turnstile.enabled')): ?>
                <div class=\"cf-turnstile my-3\" data-sitekey=\"<?= config('services.turnstile.site_key') ?>\"></div>
                <script src=\"https://challenges.cloudflare.com/turnstile/v0/api.js\" async defer></script>
            <?php endif; ?>";
        });
    }
}
