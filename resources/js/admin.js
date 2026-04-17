/**
 * Admin Panel JavaScript
 */

// Sidebar toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isMobile = window.innerWidth <= 1024;

    if (isMobile) {
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    } else {
        sidebar.classList.toggle('collapsed');
        document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }
}

// Restore sidebar state on load
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed && window.innerWidth > 1024) {
        sidebar.classList.add('collapsed');
        document.body.classList.add('sidebar-collapsed');
    }

    // Restore sidebar nav scroll position
    const sidebarNav = sidebar.querySelector('.sidebar-nav');
    if (sidebarNav) {
        const savedScrollPos = sessionStorage.getItem('sidebarNavScroll');
        if (savedScrollPos) {
            sidebarNav.scrollTop = parseInt(savedScrollPos, 10);
        }

        // Save scroll position before page unload
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('sidebarNavScroll', sidebarNav.scrollTop);
        });

        // Also save on link click for immediate persistence
        sidebarNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                sessionStorage.setItem('sidebarNavScroll', sidebarNav.scrollTop);
            });
        });
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Auto-resize textareas to fit content
    initAutoResizeTextareas();
});

/**
 * Auto-resize textareas to fit content.
 * Sets height to scrollHeight on input and initial load.
 * Skips textareas that opt out via data-no-autoresize.
 */
function initAutoResizeTextareas() {
    const selector = 'textarea:not([data-no-autoresize])';

    const resize = (el) => {
        // Reset height to recompute scrollHeight correctly (shrinks when text removed)
        el.style.height = 'auto';
        const padding = 2; // small buffer to avoid scrollbar flicker on borders
        el.style.height = (el.scrollHeight + padding) + 'px';
    };

    const attach = (el) => {
        if (el.dataset.autoresizeBound === '1') return;
        el.dataset.autoresizeBound = '1';
        el.style.overflowY = 'hidden';
        resize(el);
        el.addEventListener('input', () => resize(el));
    };

    document.querySelectorAll(selector).forEach(attach);

    // Also observe dynamically-added textareas (Livewire, Alpine, modals)
    const observer = new MutationObserver((mutations) => {
        for (const m of mutations) {
            m.addedNodes.forEach(node => {
                if (node.nodeType !== 1) return;
                if (node.matches && node.matches(selector)) attach(node);
                node.querySelectorAll && node.querySelectorAll(selector).forEach(attach);
            });
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });

    // Resize again when Livewire updates
    if (window.Livewire) {
        document.addEventListener('livewire:updated', () => {
            document.querySelectorAll(selector).forEach(resize);
        });
    }

    // Resize on window resize (line wrapping can change)
    window.addEventListener('resize', () => {
        document.querySelectorAll(selector).forEach(resize);
    });
}

// Close mobile sidebar on resize
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar || !overlay) return;

    if (window.innerWidth > 1024) {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    }
});

// Make toggleSidebar available globally
window.toggleSidebar = toggleSidebar;
