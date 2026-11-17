<?php
require_once 'core/db.php';
require_once 'core/functions.php';

$pageTitle = "Artado - Açık Kaynak Yazılım Projesi";
$pageDescription = "Artado, açık kaynak kodlu, özelleştirilebilir ve mahremiyetinize önem veren yazılım projeleri sunar. Artado Search, Artado Developers, Celer ve daha fazlası.";
$pageUrl = BASE_URL;

include 'includes/header.php';
?>

    <main>
        <!-- Hero Section -->
        <section class="hero-main">
            <div class="hero-content-wrap">                
                <div class="logo-large">
                    <img src="<?php echo BASE_URL; ?>assest/img/artado-yeni.png" class="feature-image" alt="Artado Logo">
                </div>

                <h1>Artado Manifestosu</h1>
                <p>Burada listelenenlerin tamamı Artado felsefesiyle inşa edilmiştir. Tutarlı, yalın ve mahremiyetinize saygılıdır. Bağımsız bir açık kaynak yazılım ekosistemi geliştiriyoruz.</p>
                
                <div class="hero-cta-group">
                    <a class="button button--suggested" href="https://artadosearch.com" target="_blank" rel="noopener">
                        <i class="fas fa-search"></i> Artado Search'ü Kullan
                    </a>
                    <a class="button button--secondary" href="https://github.com/Artado-Project" target="_blank" rel="noopener">
                        <i class="fab fa-github"></i> GitHub Organizasyonu
                    </a>
                </div>
            </div>
        </section>

        <!-- Projects Grid Section -->
        <div class="content">
            <div class="section-header-mini">
                <span class="tag">Ekosistem</span>
                <h2>Geliştirilen Projeler</h2>
            </div>

            <section class="card__container">
                <!-- Artado Search -->
                <article class="card">
                    <div class="card-top-row">
                        <div class="card__image__container">
                            <img src="<?php echo BASE_URL; ?>assest/img/artado-yeni.png" alt="Artado Search" class="card__image">
                        </div>
                        <span class="status-badge badge-active">Yayında</span>
                    </div>
                    <h3 class="card__title">Artado Search</h3>
                    <p class="card__description">
                        Açık kaynak kodlu, kişiselleştirilebilir ve kullanıcı gizliliğini temel alan bağımsız arama motoru.
                    </p>
                    <div class="card__button__container">
                        <a class="button button--suggested" href="https://artadosearch.com" target="_blank" rel="noopener">
                            <i class="fas fa-arrow-up-right-from-square"></i> Ziyaret Et
                        </a>
                        <a class="button button--secondary" href="https://github.com/Artado-Project/artadosearch" target="_blank" rel="noopener">
                            <i class="fab fa-github"></i> Kaynak Kodu
                        </a>
                    </div>
                </article>

                <!-- Artado Developers -->
                <article class="card">
                    <div class="card-top-row">
                        <div class="card__image__container">
                            <img src="<?php echo BASE_URL; ?>assest/img/devs.png" alt="Artado Developers" class="card__image">
                        </div>
                        <span class="status-badge badge-active">Yayında</span>
                    </div>
                    <h3 class="card__title">Artado Developers</h3>
                    <p class="card__description">
                        Uygulama, tema, eklenti ve açık kaynak araç geliştiricilerinin bir araya geldiği topluluk platformu.
                    </p>
                    <div class="card__button__container">
                        <a class="button button--suggested" href="https://devs.artado.xyz" target="_blank" rel="noopener">
                            <i class="fas fa-arrow-up-right-from-square"></i> Geliştirici Portalı
                        </a>
                        <a class="button button--secondary" href="https://github.com/Artado-Project/devs" target="_blank" rel="noopener">
                            <i class="fab fa-github"></i> Kaynak Kodu
                        </a>
                    </div>
                </article>

                <!-- Celer Browser -->
                <article class="card">
                    <div class="card-top-row">
                        <div class="card__image__container">
                            <img src="<?php echo BASE_URL; ?>assest/img/celer.jpg" alt="Celer" class="card__image">
                        </div>
                        <span class="status-badge badge-dev">Geliştiriliyor</span>
                    </div>
                    <h3 class="card__title">Celer</h3>
                    <p class="card__description">
                        Hızlı, sade ve kullanıcı mahremiyetine odaklanan yeni nesil web tarayıcısı.
                    </p>
                    <div class="card__button__container">
                        <a class="button button--secondary" href="https://github.com/Artado-Project/celerbrowser" target="_blank" rel="noopener">
                            <i class="fab fa-github"></i> GitHub Deposu
                        </a>
                    </div>
                </article>

                <!-- Arus OS -->
                <article class="card">
                    <div class="card-top-row">
                        <div class="card__image__container">
                            <img src="<?php echo BASE_URL; ?>assest/img/arus.png" alt="Arus" class="card__image">
                        </div>
                        <span class="status-badge badge-dev">Geliştiriliyor</span>
                    </div>
                    <h3 class="card__title">Arus</h3>
                    <p class="card__description">
                        LFS (Linux From Scratch) temelinde, sadeliğe ve performansa odaklanmış bağımsız işletim sistemi.
                    </p>
                    <div class="card__button__container">
                        <a class="button button--disabled" href="javascript:void(0);">
                            Geliştirme Aşamasında
                        </a>
                    </div>
                </article>
                
                <!-- Wombat -->
                <article class="card">
                    <div class="card-top-row">
                        <div class="card__image__container">
                            <img src="<?php echo BASE_URL; ?>assest/img/wombat.png" alt="Wombat" class="card__image">
                        </div>
                        <span class="status-badge badge-beta">Erken Erişim</span>
                    </div>
                    <h3 class="card__title">Wombat</h3>
                    <p class="card__description">
                        Açık kaynaklı, mahremiyetinizi ve gizliliğinizi koruyan hafif gömülü yorum sistemi.
                    </p>
                    <div class="card__button__container">
                        <a class="button button--secondary" href="https://github.com/Sxinar/wombat" target="_blank" rel="noopener">
                            <i class="fab fa-github"></i> GitHub Deposu
                        </a>
                    </div>
                </article>

                <!-- Join Card -->
                <article class="card">
                    <div class="card-top-row">
                        <div class="card__image__container">
                            <img src="<?php echo BASE_URL; ?>assest/img/soru.jpg" alt="İletişim & Katkı" class="card__image">
                        </div>
                        <span class="status-badge badge-idea">İşbirliği</span>
                    </div>
                    <h3 class="card__title">Aklınızda Proje mi Var?</h3>
                    <p class="card__description">
                        Aramıza katılın! Yeni açık kaynak fikirleri, geliştirmeler ve katkılar için kapımız her zaman açık.
                    </p>
                    <div class="card__button__container">
                        <a class="button button--suggested" href="mailto:arda@artadosearch.com">
                            <i class="fas fa-envelope"></i> İletişime Geç
                        </a>
                    </div>
                </article>
            </section>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
