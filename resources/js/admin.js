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
});

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
