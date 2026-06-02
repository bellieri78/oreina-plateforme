<div x-data="{ searchOpen: false, bellOpen: false }" class="topbar-tools-wrapper" style="display:flex;align-items:center;gap:12px;flex:1;">

    <div style="flex:1;min-width:0;"></div>

    <div class="topbar-search" style="position:relative;flex:0 1 460px;" @click.outside="searchOpen = false">
        <i data-lucide="search" style="width:16px;height:16px;color:var(--muted);"></i>
        <input type="text" placeholder="Rechercher un taxon, un article, un membre…"
               @focus="searchOpen = true"
               @keydown.escape="searchOpen = false; $event.target.blur()">
        <kbd>⌘ K</kbd>

        <div x-show="searchOpen" x-transition class="topbar-popover" style="left:0;right:0;">
            <p style="margin:0 0 8px;font-weight:700;font-size:13px;">Recherche bientôt disponible</p>
            <p style="margin:0;color:var(--muted);">En attendant, utilisez :</p>
            <ul style="margin:8px 0 0;padding-left:18px;">
                <li><a href="{{ route('member.directory.index') }}" style="color:var(--blue);">L'annuaire des adhérents</a></li>
                <li><a href="{{ route('journal.articles.index') }}" style="color:var(--blue);">Les articles de Chersotis</a></li>
            </ul>
        </div>
    </div>

    <div style="flex:1;min-width:0;display:flex;align-items:center;justify-content:flex-end;gap:12px;">
    <div class="topbar-tools">
        <div style="position:relative;" @click.outside="bellOpen = false">
            <button type="button" class="topbar-icon" @click="bellOpen = !bellOpen">
                <i data-lucide="bell"></i>
            </button>
            <div x-show="bellOpen" x-transition class="topbar-popover">
                <p style="margin:0;font-weight:700;font-size:13px;">Notifications bientôt disponibles</p>
                <p style="margin:6px 0 0;color:var(--muted);">Vous y retrouverez l'actualité de vos groupes, soumissions et événements.</p>
            </div>
        </div>
        <a href="mailto:contact@oreina.org" class="topbar-icon" title="Contact">
            <i data-lucide="mail"></i>
        </a>
        @if($isAuthCurrentMember)
        <a href="{{ route('member.chat') }}" class="topbar-icon" title="Messagerie adhérents" style="position:relative;">
            <i data-lucide="message-circle"></i>
            @if($chatUnreadCount > 0)
            <span style="position:absolute;top:4px;right:4px;width:8px;height:8px;border-radius:50%;background:var(--coral);border:1.5px solid var(--bg);"></span>
            @endif
        </a>
        @else
        <a href="{{ route('hub.events.index') }}" class="topbar-icon" title="Agenda">
            <i data-lucide="calendar-days"></i>
        </a>
        @endif
    </div>

    @yield('topbar-actions')
    </div>
</div>
