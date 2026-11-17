/**
 * Artado.xyz - Interactive Client Engine
 * Handles theme switching, slide-menu drawer, smooth scrolling, and scroll events.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const menuButton = document.getElementById('menuButton');
    const closeButton = document.getElementById('closeButton');
    const sideMenu = document.getElementById('sideMenu');
    const menuOverlay = document.getElementById('menuOverlay');
    const lightThemeBtn = document.getElementById('lightTheme');
    const darkThemeBtn = document.getElementById('darkTheme');
    const quickThemeBtn = document.getElementById('quickThemeBtn');
    const siteHeader = document.querySelector('.site-header');
    const backToTopBtn = document.getElementById('backToTop');

    // ==========================================
    // 1. Theme Management (System + Persistence)
    // ==========================================
    function getInitialTheme() {
        const saved = localStorage.getItem('theme');
        if (saved) return saved;
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
    }

    function applyTheme(theme) {
        document.body.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);

        const isLight = theme === 'light';

        if (lightThemeBtn) lightThemeBtn.classList.toggle('theme-btn-active', isLight);
        if (darkThemeBtn) darkThemeBtn.classList.toggle('theme-btn-active', !isLight);

        if (quickThemeBtn) {
            const icon = quickThemeBtn.querySelector('i');
            if (icon) {
                icon.className = isLight ? 'fas fa-sun' : 'fas fa-moon';
            }
        }
    }

    // Apply on load
    const currentTheme = getInitialTheme();
    applyTheme(currentTheme);

    // Event Listeners for Theme
    if (lightThemeBtn) {
        lightThemeBtn.addEventListener('click', () => applyTheme('light'));
    }

    if (darkThemeBtn) {
        darkThemeBtn.addEventListener('click', () => applyTheme('dark'));
    }

    if (quickThemeBtn) {
        quickThemeBtn.addEventListener('click', () => {
            const activeTheme = document.body.getAttribute('data-theme') || 'dark';
            applyTheme(activeTheme === 'light' ? 'dark' : 'light');
        });
    }

    // ==========================================
    // 2. Slide-over Side Menu
    // ==========================================
    function openMenu() {
        if (!sideMenu) return;
        sideMenu.classList.add('open');
        if (menuOverlay) menuOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        if (menuButton) menuButton.setAttribute('aria-expanded', 'true');
    }

    function closeMenu() {
        if (!sideMenu) return;
        sideMenu.classList.remove('open');
        if (menuOverlay) menuOverlay.classList.remove('active');
        document.body.style.overflow = '';
        if (menuButton) menuButton.setAttribute('aria-expanded', 'false');
    }

    if (menuButton) {
        menuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            if (sideMenu.classList.contains('open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });
    }

    if (closeButton) {
        closeButton.addEventListener('click', closeMenu);
    }

    if (menuOverlay) {
        menuOverlay.addEventListener('click', closeMenu);
    }

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sideMenu && sideMenu.classList.contains('open')) {
            closeMenu();
        }
    });

    // ==========================================
    // 3. Scroll Header Blur & Back To Top
    // ==========================================
    window.addEventListener('scroll', () => {
        const scrollY = window.pageYOffset || document.documentElement.scrollTop;

        // Header Scrolled State
        if (siteHeader) {
            if (scrollY > 20) {
                siteHeader.classList.add('scrolled');
            } else {
                siteHeader.classList.remove('scrolled');
            }
        }

        // Back to top button
        if (backToTopBtn) {
            if (scrollY > 350) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        }
    }, { passive: true });

    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});