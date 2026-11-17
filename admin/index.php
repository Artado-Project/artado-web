<?php
require_once '../core/db.php';
require_once '../core/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Stats
$supporterCount = $pdo->query("SELECT COUNT(*) FROM supporters")->fetchColumn();
$recentSupporters = $pdo->query("SELECT * FROM supporters ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artado Admin - Panel</title>
    <link rel="stylesheet" href="../assest/css/styles.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Menüyü aç/kapat">
        <i class="fas fa-bars"></i>
    </button>
    <div class="admin-container">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <h2>Artado Admin</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="supporters.php"><i class="fas fa-users"></i> Destekçiler</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Ayarlar</a></li>
                <li><a href="../katki.php" target="_blank"><i class="fas fa-heart"></i> Destekçiler Sayfası</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Siteyi Görüntüle</a></li>
                <li class="logout-item"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
            </ul>
        </aside>
        <main class="admin-content">
            <div class="admin-header">
                <div>
                    <h1>Hoş geldin, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                    <span class="date" style="border: none; padding: 4px 0; background: transparent;">Sistemin genel durumunu ve son eklenenleri buradan takip edebilirsiniz.</span>
                </div>
                <div class="date"><i class="far fa-calendar-alt" style="margin-right: 6px;"></i> <?php echo date('d.m.Y'); ?></div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Toplam Destekçi</h3>
                    <div class="value"><?php echo $supporterCount; ?></div>
                </div>
                <!-- İleride yeni istatistik kartları buraya eklenebilir -->
            </div>

            <section class="form-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;"><i class="fas fa-user-clock" style="margin-right: 8px;"></i> Son Eklenen Destekçiler</h3>
                    <a href="supporters.php" class="button button--suggested" style="font-size: 0.85rem; padding: 8px 16px;">Tümünü Yönet</a>
                </div>
                <div style="overflow-x: auto;">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>Ad</th>
                                <th>Tip</th>
                                <th>GitHub</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentSupporters)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--admin-text-muted); padding: 30px;">Henüz hiç destekçi eklenmemiş.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($recentSupporters as $s): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($s['name']); ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?php echo htmlspecialchars($s['type']); ?>">
                                            <?php echo htmlspecialchars($s['type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($s['github_link'])): ?>
                                            <a href="<?php echo htmlspecialchars($s['github_link']); ?>" target="_blank" style="color: var(--admin-primary); text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                                <i class="fab fa-github"></i> Profil <i class="fas fa-external-link-alt" style="font-size: 0.7rem;"></i>
                                            </a>
                                        <?php else: ?>
                                            <span style="color: var(--admin-text-muted);">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: var(--admin-text-muted);"><?php echo date('d.m.Y', strtotime($s['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <script>
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                adminSidebar.classList.toggle('open');
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                if (adminSidebar && adminSidebar.classList.contains('open')) {
                    if (!adminSidebar.contains(event.target) && event.target !== mobileMenuToggle) {
                        adminSidebar.classList.remove('open');
                    }
                }
            }
        });
    </script>
</body>
</html>