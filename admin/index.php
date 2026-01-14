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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Siteyi Görüntüle</a></li>
                <li class="logout-item"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
            </ul>
        </aside>
        <main class="admin-content">
            <div class="admin-header">
                <h1>Hoş geldin, <?php echo $_SESSION['username']; ?>!</h1>
                <div class="date"><?php echo date('d.m.Y'); ?></div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Toplam Destekçi</h3>
                    <div class="value"><?php echo $supporterCount; ?></div>
                </div>
                <!-- Extend with more stats later -->
            </div>

            <section>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2>Son Eklenen Destekçiler</h2>
                    <a href="supporters.php" class="button button--suggested" style="font-size: 13px; padding: 8px 15px;">Tümünü Yönet</a>
                </div>
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
                        <?php foreach($recentSupporters as $s): ?>
                        <tr>
                            <td><?php echo $s['name']; ?></td>
                            <td><span class="badge badge-<?php echo $s['type']; ?>"><?php echo strtoupper($s['type']); ?></span></td>
                            <td><?php echo $s['github_link']; ?></td>
                            <td><?php echo date('d.m.Y', strtotime($s['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
