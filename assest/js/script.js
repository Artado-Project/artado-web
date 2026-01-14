document.addEventListener('DOMContentLoaded', function () {
    var menuButton = document.getElementById('menuButton');
    var closeButton = document.getElementById('closeButton');
    var sideMenu = document.getElementById('sideMenu');
    var lightThemeBtn = document.getElementById('lightTheme');
    var darkThemeBtn = document.getElementById('darkTheme');

    // Sistem temasını algıla
    function getSystemTheme() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
            return 'light';
        }
        return 'dark'; // Varsayılan tema
    }

    // Tema ayarını localStorage'dan al, yoksa sistem temasını kullan
    const savedTheme = localStorage.getItem('theme');
    const currentTheme = savedTheme || getSystemTheme();

    // Sayfa yüklendiğinde tema ayarını uygula
    applyTheme(currentTheme);

    // Tema değiştirme fonksiyonu
    function applyTheme(theme) {
        if (theme === 'light') {
            document.body.setAttribute('data-theme', 'light');
            if (lightThemeBtn) lightThemeBtn.classList.add('theme-btn-active');
            if (darkThemeBtn) darkThemeBtn.classList.remove('theme-btn-active');
        } else {
            document.body.setAttribute('data-theme', 'dark'); // Force dark instead of removing
            if (darkThemeBtn) darkThemeBtn.classList.add('theme-btn-active');
            if (lightThemeBtn) lightThemeBtn.classList.remove('theme-btn-active');
        }
    }

    // Tema değiştirme butonları
    if (lightThemeBtn) {
        lightThemeBtn.addEventListener('click', function () {
            applyTheme('light');
            localStorage.setItem('theme', 'light');
        });
    }

    if (darkThemeBtn) {
        darkThemeBtn.addEventListener('click', function () {
            applyTheme('dark');
            localStorage.setItem('theme', 'dark');
        });
    }

    if (menuButton && sideMenu) {
        menuButton.addEventListener('click', function () {
            sideMenu.style.width = '300px';
            sideMenu.classList.add('open');
            document.body.style.overflow = 'hidden';

            // Reset and re-trigger link animations
            const menuLinks = sideMenu.querySelectorAll('a:not(.close-btn)');
            menuLinks.forEach((link, index) => {
                link.style.animation = 'none';
                link.offsetHeight; // Trigger reflow
                link.style.animation = '';
                link.style.setProperty('--order', index + 1);
            });
        });
    }

    if (closeButton && sideMenu) {
        closeButton.addEventListener('click', function () {
            sideMenu.style.width = '0';
            sideMenu.classList.remove('open');
            document.body.style.overflow = '';
        });
    }

    // Dışarı tıklandığında menüyü kapat
    document.addEventListener('click', function (event) {
        if (sideMenu && sideMenu.style.width !== '0' && sideMenu.style.width !== '' &&
            !sideMenu.contains(event.target) &&
            event.target !== menuButton) {
            sideMenu.style.width = '0';
            sideMenu.classList.remove('open');
            document.body.style.overflow = '';
        }
    });

    var pagesDropdown = document.getElementById('pagesDropdown');
    var socialsDropdown = document.getElementById('socialsDropdown');
    var pagesPopup = document.getElementById('pagesPopup');
    var socialsPopup = document.getElementById('socialsPopup');
    var pagesContainer = document.getElementById('pagesContainer');

    if (pagesDropdown && pagesPopup) {
        pagesDropdown.addEventListener('click', function (event) {
            event.stopPropagation();
            pagesPopup.classList.toggle('show');
            if (pagesContainer) pagesContainer.classList.toggle('active');
            if (socialsPopup) socialsPopup.classList.remove('show');
            toggleIcon(pagesDropdown);
            if (socialsDropdown) resetIcon(socialsDropdown);
        });
    }

    if (socialsDropdown && socialsPopup) {
        socialsDropdown.addEventListener('click', function (event) {
            event.stopPropagation();
            socialsPopup.classList.toggle('show');
            if (pagesPopup) pagesPopup.classList.remove('show');
            if (pagesContainer) pagesContainer.classList.remove('active');
            toggleIcon(socialsDropdown);
            if (pagesDropdown) resetIcon(pagesDropdown);
        });
    }

    // Sayfa herhangi bir yerine tıklandığında popup'ları kapat
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.dropdown-container')) {
            if (pagesPopup) pagesPopup.classList.remove('show');
            if (socialsPopup) socialsPopup.classList.remove('show');
            if (pagesContainer) pagesContainer.classList.remove('active');
            if (pagesDropdown) resetIcon(pagesDropdown);
            if (socialsDropdown) resetIcon(socialsDropdown);
        }
    });

    function toggleIcon(dropdown) {
        var icon = dropdown.querySelector('.dropdown-icon');
        if (icon) {
            icon.style.transform = icon.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    }

    function resetIcon(dropdown) {
        var icon = dropdown.querySelector('.dropdown-icon');
        if (icon) {
            icon.style.transform = 'rotate(0deg)';
        }
    }

    // Yukarı Çık Butonu İşlevselliği
    const backToTopBtn = document.getElementById('backToTop');
    if (backToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.display = 'flex';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Footer Genişletme İşlevselliği (Mobil için)
    const expandFooterBtn = document.getElementById('expandFooter');
    const expandedFooterContent = document.getElementById('expandedFooterContent');
    if (expandFooterBtn && expandedFooterContent) {
        expandFooterBtn.addEventListener('click', () => {
            expandedFooterContent.classList.toggle('active');
            const icon = expandFooterBtn.querySelector('i');
            if (expandedFooterContent.classList.contains('active')) {
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            } else {
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
            }
        });
    }
});