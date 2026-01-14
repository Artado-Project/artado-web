<?php
require_once 'core/db.php';
require_once 'core/functions.php';

$pageTitle = "Destekçilerimiz";
$pageDescription = "Artado projesine katkıda bulunan tüm destekçilerimiz, geliştiricilerimiz ve sponsorlarımız. Topluluğumuzun değerli üyeleri.";
$pageUrl = BASE_URL . "katki.php";

include 'includes/header.php';

$contributors = getSupporters('supporter');
$devs = getSupporters('developer');
$plugins = getSupporters('plugin');
$themes = getSupporters('theme');
$sponsors = getSupporters('sponsor');
?>

    <section class="hero-main">
        <div class="logo-large">
            <img src="<?php echo BASE_URL; ?>assest/img/artado-yeni.png" class="feature-image" alt="Artado Logo">
        </div>
        <h1>Katkıda Bulunanlar</h1>
        <p>Artado ekibi olarak topluluğumuzun desteğine minnettarız. Projelerimize ruh katan tüm destekçilerimize teşekkürler.</p>
    </section>

    <section class="contributors-section">
        <div class="container">
            <?php if (!empty($contributors)): ?>
            <h2 class="section-title">En Değerli Destekçilerimiz</h2>
            <div class="supporter-grid">
                <?php foreach ($contributors as $c): ?>
                <div class="supporter-card">
                    <a href="<?php echo BASE_URL; ?>profile.php?slug=<?php echo $c['slug']; ?>">
                        <div class="card-glow"></div>
                        <img src="<?php echo $c['image_url']; ?>" alt="<?php echo $c['name']; ?>">
                        <h3 class="supporter-name"><?php echo $c['name']; ?></h3>
                        <span class="supporter-type">Destekçi</span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!empty($devs) || !empty($plugins) || !empty($themes)): ?>
    <section class="contributors-section">
        <div class="container">
            <?php if (!empty($devs)): ?>
            <div class="contributor-group">
                <h2 class="section-title">Geliştiricilerimiz</h2>
                <div class="supporter-grid">
                    <?php foreach ($devs as $d): ?>
                    <div class="supporter-card">
                        <a href="<?php echo BASE_URL; ?>profile.php?slug=<?php echo $d['slug']; ?>">
                            <div class="card-glow"></div>
                            <img src="<?php echo $d['image_url']; ?>" alt="<?php echo $d['name']; ?>">
                            <h3 class="supporter-name"><?php echo $d['name']; ?></h3>
                            <span class="supporter-type devs">Geliştirici</span>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($plugins)): ?>
            <div class="contributor-group">
                <h2 class="section-title">Eklenti Geliştiricilerimiz</h2>
                <div class="supporter-grid">
                    <?php foreach ($plugins as $p): ?>
                    <div class="supporter-card">
                        <a href="<?php echo BASE_URL; ?>profile.php?slug=<?php echo $p['slug']; ?>">
                            <div class="card-glow"></div>
                            <img src="<?php echo $p['image_url']; ?>" alt="<?php echo $p['name']; ?>">
                            <h3 class="supporter-name"><?php echo $p['name']; ?></h3>
                            <span class="supporter-type plugins">Eklenti Yazarı</span>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($themes)): ?>
            <div class="contributor-group">
                <h2 class="section-title">Tema Yazarlarımız</h2>
                <div class="supporter-grid">
                    <?php foreach ($themes as $t): ?>
                    <div class="supporter-card">
                        <a href="<?php echo BASE_URL; ?>profile.php?slug=<?php echo $t['slug']; ?>">
                            <div class="card-glow"></div>
                            <img src="<?php echo $t['image_url']; ?>" alt="<?php echo $t['name']; ?>">
                            <h3 class="supporter-name"><?php echo $t['name']; ?></h3>
                            <span class="supporter-type themes">Tema Yazarı</span>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($sponsors)): ?>
    <section class="contributors-section">
        <div class="container">
            <h2 class="section-title sponsors">Sponsorlarımız</h2>
            <div class="supporter-grid">
                <?php foreach ($sponsors as $s): ?>
                <div class="supporter-card sponsor">
                    <a href="<?php echo BASE_URL; ?>profile.php?slug=<?php echo $s['slug']; ?>">
                        <div class="card-glow"></div>
                        <img src="<?php echo $s['image_url']; ?>" alt="<?php echo $s['name']; ?>">
                        <h3 class="supporter-name"><?php echo $s['name']; ?></h3>
                        <span class="supporter-type">Sponsor</span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

<?php include 'includes/footer.php'; ?>
