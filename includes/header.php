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
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assest/css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
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
    <script src="<?php echo BASE_URL; ?>assest/js/script.js?v=<?php echo time(); ?>"></script>
</head>
<body data-theme="dark"> <!-- Default to dark -->
    <header>
        <nav>
            <div class="logo-small"><a href="<?php echo BASE_URL; ?>" style="text-decoration: none; color: inherit;">Artado</a></div>
            <button id="menuButton" class="menu-button" aria-label="Menüyü aç">☰</button>
        </nav>
    </header>
    <div id="sideMenu" class="side-menu">
        <a href="javascript:void(0)" id="closeButton" class="close-btn" aria-label="Menüyü kapat">
            <i class="fas fa-times"></i>
        </a>
        
        <div class="side-menu-header">
            <div class="brand-info">
                <img src="<?php echo BASE_URL; ?>assest/img/artado-yeni.png" alt="Artado">
                <span>Artado Menu</span>
            </div>
        </div>

        <div class="side-menu-theme">
            <div class="theme-switch-wrapper">
                <span class="theme-label">Görünüm</span>
                <div class="theme-switch-container">
                    <button id="lightTheme" class="theme-btn" aria-label="Aydınlık Tema">
                        <i class="fas fa-sun"></i>
                    </button>
                    <button id="darkTheme" class="theme-btn theme-btn-active" aria-label="Karanlık Tema">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
        </div>
        <a href="<?php echo BASE_URL; ?>"><i class="fas fa-home"></i> Ana Sayfa</a>
        <a href="<?php echo BASE_URL; ?>katki.php"><i class="fas fa-heart"></i> Destekçilerimiz</a>
        <a href="https://artadosearch.com/manifesto"><i class="fas fa-book"></i> Dökümentasyon</a>
        <a href="https://artadosearch.com/manifesto"><i class="fas fa-info-circle"></i> Hakkımızda</a>
        <a href="#"><i class="fas fa-code"></i> Geliştiriciler</a>
        <a href="mailto:support@artadosearch.com"><i class="fas fa-envelope"></i> İletişim</a>
    </div>
