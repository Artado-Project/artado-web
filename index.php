<?php
require_once 'core/db.php';
require_once 'core/functions.php';

$pageTitle = "Artado - Açık Kaynak Yazılım Projesi";
$pageDescription = "Artado, açık kaynak kodlu, özelleştirilebilir ve mahremiyetinize önem veren yazılım projeleri sunar. Artado Search, Artado Developers, Celer ve daha fazlası.";
$pageUrl = BASE_URL;

include 'includes/header.php';
?>

    <main>
        <section class="hero-main">
            <div class="logo-large">
                <img src="<?php echo BASE_URL; ?>assest/img/artado-yeni.png" class="feature-image" alt="Artado Logo">
            </div>
            <h1>Artado Manifestosu</h1>
            <p>Burada listelenenlerin tamamı Artado felsefesiyle inşa edilmiştir. Tutarlı ve sadedir. Dolayısıyla özgür yazılımdırlar, dostane topluluğun bir parçasıdırlar.</p>
            <a class="button button--suggested" href="https://artadosearch.com" style="display:inline-block; width:auto; padding: 15px 40px;">Ziyaret et</a>
        </section>

        <div class="content">

                <section class="card__container">
                        <article class="card">
                    <div class="card__image__container">
                        <img src="<?php echo BASE_URL; ?>assest/img/search.jpg" alt="Artado Search" class="card__image">
                    </div>
                            <span class="card__title">Artado Search</span>
                            <p class="card__description">
                                Açık kaynak kodlu, özelleştirilebilir ve mahremiyetinize önem veren arama motoru
                            </p>
                            <div class="card__button__container">
                                <a class="button button--suggested" href="https://artadosearch.com">Ziyaret et</a>
                                <a class="button" href="https://github.com/Artado-Project/artadosearch">Kaynak kodu</a>
                            </div>
                        </article>

                        <article class="card">
                    <div class="card__image__container">
                        <img src="<?php echo BASE_URL; ?>assest/img/devs.jpg" alt="Artado Developers" class="card__image">
                    </div>
                            <span class="card__title">Artado Developers</span>
                            <p class="card__description">
                                Uygulama, oyun, tema ve eklenti geliştiricilerinin buluşma noktası
                            </p>
                            <div class="card__button__container">
                                <a class="button button--suggested" href="https://devs.artado.xyz">Ziyaret et</a>
                                <a class="button" href="https://github.com/Artado-Project/devs">Kaynak kodu</a>
                            </div>
                        </article>

                        <article class="card">
                    <div class="card__image__container">
                        <img src="<?php echo BASE_URL; ?>assest/img/celer.jpg" alt="Celer" class="card__image">
                    </div>
                            <span class="card__title">Celer</span>
                            <p class="card__description">
                                Sade ve mahremiyetinize saygı duyan tarayıcı
                            </p>
                            <div class="card__button__container">
                                <a class="button button--disabled" href="https://github.com/Artado-Project/celerbrowser">Github</a>
                                <a class="button button--disabled" href="#">Geliştiriliyor</a>
                            </div>
                        </article>

                        <article class="card">
                    <div class="card__image__container">
                        <img src="<?php echo BASE_URL; ?>assest/img/arus.jpg" alt="Arus" class="card__image">
                    </div>
                            <span class="card__title">Arus</span>
                            <p class="card__description">
                                LFS'i temel alan, sadeliğie odaklanmış işletim sistemi
                            </p>
                            <div class="card__button__container">
                                <a href="#" class="button button--disabled">Geliştiriliyor</a>
                            </div>
                        </article>

                        <article class="card">
                    <div class="card__image__container">
                        <img src="<?php echo BASE_URL; ?>assest/img/soru.jpg" alt="İletişim" class="card__image">
                    </div>
                            <span class="card__title">Aklınızda başka fikir mi var?</span>
                            <p class="card__description">
                                Aramıza katılın! Yeni projeler ve işbirlikleri için kapımız her zaman açık.
                            </p>
                            <div class="card__button__container">
                                <a class="button button--suggested" href="mailto:arda@artadosearch.com">İletişime geç</a>
                            </div>
                        </article>
                    </section>
                </div>
    </main>

<?php include 'includes/footer.php'; ?>
