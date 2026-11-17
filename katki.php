<?php
require_once 'core/db.php';
require_once 'core/functions.php';

$pageTitle = "Destekçilerimiz & Topluluk";
$pageDescription = "Artado projesine katkıda bulunan tüm destekçilerimiz, geliştiricilerimiz ve sponsorlarımız. Topluluğumuzun değerli üyeleri.";
$pageUrl = BASE_URL . "katki.php";

include 'includes/header.php';

// Dinamik sıralama ayarını al
$sectionOrder = getSupporterSectionOrder();

// Verileri çek
$allSections = [
    'supporter' => [
        'title' => 'En Değerli Destekçilerimiz',
        'icon'  => 'fas fa-heart',
        'type_class' => '',
        'type_label' => 'Destekçi',
        'data'  => getSupporters('supporter')
    ],
    'developer' => [
        'title' => 'Geliştiricilerimiz',
        'icon'  => 'fas fa-code',
        'type_class' => 'devs',
        'type_label' => 'Geliştirici',
        'data'  => getSupporters('developer')
    ],
    'plugin' => [
        'title' => 'Eklenti Geliştiricilerimiz',
        'icon'  => 'fas fa-puzzle-piece',
        'type_class' => 'plugins',
        'type_label' => 'Eklenti Yazarı',
        'data'  => getSupporters('plugin')
    ],
    'theme' => [
        'title' => 'Tema Yazarlarımız',
        'icon'  => 'fas fa-paint-brush',
        'type_class' => 'themes',
        'type_label' => 'Tema Yazarı',
        'data'  => getSupporters('theme')
    ],
    'sponsor' => [
        'title' => 'Sponsorlarımız',
        'icon'  => 'fas fa-star',
        'type_class' => 'sponsor',
        'type_label' => 'Sponsor',
        'data'  => getSupporters('sponsor')
    ]
];
?>

    <main>
        <!-- Hero Section (Ortalanmış) -->
        <section class="hero-main">
            <div class="hero-content-wrap">

                <div class="logo-large">
                    <img src="<?php echo BASE_URL; ?>assest/img/artado-yeni.png" class="feature-image" alt="Artado Logo">
                </div>
                <h1>Katkıda Bulunanlar</h1>
                <p>Artado ekibi olarak topluluğumuzun desteğine minnettarız. Projelerimize ruh katan tüm geliştirici, destekçi ve sponsorlarımıza teşekkür ederiz.</p>
            </div>
        </section>

        <!-- Dynamic Ordered Supporters List (Ortalanmış Grid) -->
        <div class="container" style="max-width: 1040px; margin: 0 auto;">
            <?php 
            foreach ($sectionOrder as $secKey):
                if (!isset($allSections[$secKey])) continue;
                $section = $allSections[$secKey];
                $items = $section['data'];
                if (empty($items)) continue;
            ?>
            <section class="contributors-section" style="text-align: center; margin-bottom: 48px;">
                <h2 class="section-title <?php echo $secKey === 'sponsor' ? 'sponsors' : ''; ?>" style="justify-content: center; text-align: center; margin-bottom: 24px;">
                    <i class="<?php echo $section['icon']; ?>" style="color: var(--accent);"></i> 
                    <span><?php echo htmlspecialchars($section['title']); ?></span>
                </h2>
                
                <div class="supporter-grid" style="justify-content: center;">
                    <?php foreach ($items as $item): ?>
                    <div class="supporter-card <?php echo $secKey === 'sponsor' ? 'sponsor' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>profile.php?slug=<?php echo htmlspecialchars($item['slug']); ?>">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <h3 class="supporter-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <span class="supporter-type <?php echo $section['type_class']; ?>">
                                <?php echo htmlspecialchars($section['type_label']); ?>
                            </span>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
