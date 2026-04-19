{{-- resources/views/journal/articles/partials/_mobile-fab.blade.php --}}
<div x-data="{ open: false }"
     x-on:open-share.window="open = true"
     class="mobile-fab-wrapper">
    <button type="button"
            class="mobile-fab"
            x-on:click="open = true"
            aria-label="Ouvrir les actions">
        <i data-lucide="menu" style="width:22px;height:22px"></i>
    </button>

    <div x-show="open"
         x-cloak
         x-transition.opacity
         class="mobile-drawer-overlay"
         x-on:click="open = false"></div>

    <div x-show="open"
         x-cloak
         x-transition
         class="mobile-drawer"
         role="dialog"
         aria-modal="true">
        <div class="mobile-drawer-header">
            <span>Actions & Sommaire</span>
            <button type="button" x-on:click="open = false" aria-label="Fermer">
                <i data-lucide="x" style="width:18px;height:18px"></i>
            </button>
        </div>
        <div class="mobile-drawer-body">
            @include('journal.articles.partials._sidebar-actions')
            @include('journal.articles.partials._sidebar-toc')
            @include('journal.articles.partials._sidebar-metrics')
        </div>
    </div>
</div>
