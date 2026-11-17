<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Primary Meta Tags -->
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . " - Artado" : "Artado - Açık Kaynak Yazılım Projesi"; ?></title>
    <meta name="title" content="<?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . " - Artado" : "Artado - Açık Kaynak Yazılım Projesi"; ?>">
    <meta name="description" content="<?php echo isset($pageDescription) ? htmlspecialchars($pageDescription) : "Artado, açık kaynak kodlu, özelleştirilebilir ve mahremiyetinize önem veren yazılım projeleri sunar. Artado Search, Artado Developers, Celer ve daha fazlası."; ?>">
    <meta name="keywords" content="Artado, açık kaynak, özgür yazılım, mahremiyet, arama motoru, yazılım geliştirme, Artado Search, Celer, Arus, Artado Developers, open source, privacy">
    <meta name="author" content="Artado">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Turkish">
    <meta name="revisit-after" content="7 days">
    <meta name="theme-color" content="#3498db">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo isset($pageUrl) ? htmlspecialchars($pageUrl) : BASE_URL; ?>">
    <meta property="og:title" content="<?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . " - Artado" : "Artado - Açık Kaynak Yazılım Projesi"; ?>">
    <meta property="og:description" content="<?php echo isset($pageDescription) ? htmlspecialchars($pageDescription) : "Artado, açık kaynak kodlu, özelleştirilebilir ve mahremiyetinize önem veren yazılım projeleri sunar."; ?>">
    <meta property="og:image" content="<?php echo BASE_URL; ?>assest/img/artado-yeni.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Artado">
    <meta property="og:locale" content="tr_TR">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo isset($pageUrl) ? htmlspecialchars($pageUrl) : BASE_URL; ?>">
    <meta name="twitter:title" content="<?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . " - Artado" : "Artado - Açık Kaynak Yazılım Projesi"; ?>">
    <meta name="twitter:description" content="<?php echo isset($pageDescription) ? htmlspecialchars($pageDescription) : "Artado, açık kaynak kodlu, özelleştirilebilir ve mahremiyetinize önem veren yazılım projeleri sunar."; ?>">
    <meta name="twitter:image" content="<?php echo BASE_URL; ?>assest/img/artado-yeni.png">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo isset($pageUrl) ? htmlspecialchars($pageUrl) : BASE_URL; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>assest/img/favicon/favicon.png">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>assest/img/favicon/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>assest/img/favicon/favicon.png">
    
    <!-- Stylesheets & Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assest/css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Artado",
        "url": "<?php echo BASE_URL; ?>",
        "logo": "<?php echo BASE_URL; ?>assest/img/artado-yeni.png",
        "description": "Açık kaynak kodlu, özelleştirilebilir ve mahremiyetinize önem veren yazılım projeleri",
        "sameAs": [
            "https://github.com/Artado-Project",
            "https://discord.com/invite/WXCsr8zTN6",
            "https://artadosearch.com"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "email": "arda@artadosearch.com",
            "contactType": "customer service"
        }
    }
    </script>
    
    <?php if (isset($extraCss)) echo $extraCss; ?>
</head>
<body data-theme="dark">
    <!-- Mobil Menü Karartma Katmanı (Backdrop Overlay) -->
    <div id="menuOverlay" class="menu-overlay" aria-hidden="true"></div>

    <header class="site-header">
        <nav class="nav-container">
            <div class="logo-wrapper">
                <a href="<?php echo BASE_URL; ?>" class="brand-link">
                    <img src="<?php echo BASE_URL; ?>assest/img/artado-yeni.png" alt="Artado" class="nav-logo">
                    <span class="brand-text">Artado</span>
                </a>
            </div>
            
            <div class="nav-actions">
                <div class="nav-quick-links">
                    <a href="<?php echo BASE_URL; ?>" class="nav-item">Ana Sayfa</a>
                    <a href="<?php echo BASE_URL; ?>katki.php" class="nav-item">Destekçiler</a>
                    <a href="https://artado.xyz/blog" class="nav-item">Blog</a>
                    <a href="https://docs.artado.xyz" class="nav-item" target="_blank" rel="noopener">Dökümanlar</a>
                    <a href="https://forum.artado.xyz" class="nav-item" target="_blank" rel="noopener">Forum</a>
                </div>

                <div class="theme-quick-toggle">
                    <button id="quickThemeBtn" class="quick-theme-btn" aria-label="Temayı Değiştir" title="Temayı Değiştir">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>

                <button id="menuButton" class="menu-button" aria-label="Menüyü aç" aria-expanded="false">
                    <span class="menu-bar"></span>
                    <span class="menu-bar"></span>
                    <span class="menu-bar"></span>
                </button>
            </div>
        </nav>
    </header>

    <aside id="sideMenu" class="side-menu" aria-label="Yan Menü">
        <div class="side-menu-top">
            <div class="side-menu-brand">
                <img src="<?php echo BASE_URL; ?>assest/img/artado-yeni.png" alt="Artado Logo">
                <div class="brand-details">
                    <span class="title">Artado</span>
                    <span class="subtitle">Açık Kaynak Ekosistemi</span>
                </div>
            </div>
            <button id="closeButton" class="close-btn" aria-label="Menüyü kapat">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="side-menu-theme">
            <div class="theme-switch-wrapper">
                <span class="theme-label"><i class="fas fa-palette"></i> Görünüm</span>
                <div class="theme-switch-container">
                    <button id="lightTheme" class="theme-btn" aria-label="Aydınlık Tema">
                        <i class="fas fa-sun"></i>
                        <span>Aydınlık</span>
                    </button>
                    <button id="darkTheme" class="theme-btn theme-btn-active" aria-label="Karanlık Tema">
                        <i class="fas fa-moon"></i>
                        <span>Karanlık</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="side-menu-links">
            <span class="menu-group-title">Gezinme</span>
            <a href="<?php echo BASE_URL; ?>"><i class="fas fa-home"></i> Ana Sayfa</a>
            <a href="<?php echo BASE_URL; ?>katki.php"><i class="fas fa-heart"></i> Destekçilerimiz</a>
            <a href="https://artado.xyz/blog"><i class="fas fa-newspaper"></i> Blog</a>
            <a href="https://docs.artado.xyz" target="_blank" rel="noopener"><i class="fas fa-book"></i> Dökümantasyon</a>
            <a href="https://forum.artado.xyz" target="_blank" rel="noopener"><i class="fas fa-comments"></i> Topluluk Forumu</a>
            
            <span class="menu-group-title">Bağlantı & İletişim</span>
            <a href="https://artado.xyz/hakkimizda"><i class="fas fa-info-circle"></i> Hakkımızda</a>
            <a href="https://discord.com/invite/WXCsr8zTN6" target="_blank" rel="noopener"><i class="fab fa-discord"></i> Discord Sunucumuz</a>
            <a href="https://github.com/Artado-Project" target="_blank" rel="noopener"><i class="fab fa-github"></i> GitHub Organizasyonu</a>
            <a href="mailto:arda@artadosearch.com"><i class="fas fa-envelope"></i> İletişim (Arda)</a>
            <a href="mailto:sxi@artadosearch.com"><i class="fas fa-envelope"></i> İletişim (Sxinar)</a>
        </div>
    </aside>
