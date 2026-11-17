<?php
require_once 'core/db.php';
require_once 'core/functions.php';

$slug = isset($_GET['slug']) ? clean($_GET['slug']) : redirect('katki.php');

$stmt = $pdo->prepare("SELECT * FROM supporters WHERE slug = ?");
$stmt->execute([$slug]);
$supporter = $stmt->fetch();

if (!$supporter) {
    redirect('katki.php');
}

$pageTitle = htmlspecialchars($supporter['name']) . " - Destekçi Profili";
$pageDescription = $supporter['description'] ?: htmlspecialchars($supporter['name']) . " - Artado açık kaynak projesine katkıda bulunan topluluk üyesi.";
$pageUrl = BASE_URL . "profile.php?slug=" . htmlspecialchars($supporter['slug']);

include 'includes/header.php';
?>

    <main>
        <div class="profile-section">
            <div class="profile-card">
                <img src="<?php echo htmlspecialchars($supporter['image_url']); ?>" alt="<?php echo htmlspecialchars($supporter['name']); ?>" class="profile-avatar">
                <h1 class="profile-name"><?php echo htmlspecialchars($supporter['name']); ?></h1>
                <p class="profile-description"><?php echo htmlspecialchars($supporter['description'] ?: 'Bu kişi Artado projesine değerli katkılarda bulunmuştur.'); ?></p>
                
                <?php if (!empty($supporter['contributions'])): ?>
                <div class="contribution-box">
                    <h3><i class="fas fa-award"></i> Gerçekleştirilen Katkılar</h3>
                    <ul class="contribution-list">
                        <?php 
                        $lines = explode("\n", $supporter['contributions']);
                        foreach($lines as $line) {
                            $trimmed = trim($line);
                            if ($trimmed) {
                                echo "<li>" . htmlspecialchars($trimmed) . "</li>";
                            }
                        }
                        ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="profile-actions">
                    <?php if (!empty($supporter['github_link']) && $supporter['github_link'] !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($supporter['github_link']); ?>" target="_blank" rel="noopener" class="social-btn btn-github">
                        <i class="fab fa-github"></i> GitHub Profilini Görüntüle
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?php echo BASE_URL; ?>katki.php" class="social-btn btn-back">
                        <i class="fas fa-arrow-left"></i> Tüm Destekçilere Dön
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
