<?php
require_once '../core/db.php';
require_once '../core/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';
$error = '';
$section_message = '';

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch();

    if ($user && password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update->execute([$hashed_password, $_SESSION['admin_id']]);
                $message = "Şifreniz başarıyla güncellendi.";
            } else {
                $error = "Yeni şifre en az 6 karakter olmalıdır.";
            }
        } else {
            $error = "Yeni şifreler eşleşmiyor.";
        }
    } else {
        $error = "Mevcut şifreniz hatalı.";
    }
}

// Handle Supporter Section Order Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_sections'])) {
    $ranks = $_POST['section_rank'] ?? [];
    // Sort sections by their assigned rank
    asort($ranks);
    $orderedKeys = array_keys($ranks);
    
    setSetting('supporter_section_order', json_encode($orderedKeys));
    $section_message = "Destekçiler sayfası bölüm sıralaması başarıyla kaydedildi.";
}

$currentOrder = getSupporterSectionOrder();
$sectionNames = [
    'supporter' => 'En Değerli Destekçilerimiz (Supporters)',
    'developer' => 'Geliştiricilerimiz (Developers)',
    'plugin'    => 'Eklenti Geliştiricilerimiz (Plugins)',
    'theme'     => 'Tema Yazarlarımız (Themes)',
    'sponsor'   => 'Sponsorlarımız (Sponsors)'
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - Artado Admin</title>
    <link rel="stylesheet" href="../assest/css/styles.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .setting-grid-layout {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }
        .order-item-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .order-item-card select, .order-item-card input {
            width: 80px;
            padding: 6px 8px;
            border-radius: 6px;
            background: #1a202c;
            color: #fff;
            border: 1px solid var(--border-color);
            font-weight: bold;
            text-align: center;
        }
    </style>
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
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="supporters.php"><i class="fas fa-users"></i> Destekçiler</a></li>
                <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Ayarlar</a></li>
                <li><a href="../katki.php" target="_blank"><i class="fas fa-heart"></i> Destekçiler Sayfası</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Siteyi Görüntüle</a></li>
                <li class="logout-item"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
            </ul>
        </aside>
        <main class="admin-content">
            <h1>Site & Panel Ayarları</h1>
            <p style="color: var(--text-secondary); margin-bottom: 24px;">Destekçiler sayfasının bölüm sıralamasını ve yönetici şifrenizi buradan yapılandırabilirsiniz.</p>

            <div class="setting-grid-layout">
                <!-- Destekçiler Sayfası Bölüm Sıralaması -->
                <div class="form-card">
                    <h3 style="margin-bottom: 12px;"><i class="fas fa-layer-group text-blue-500"></i> Destekçiler Sayfası Bölüm Sırası</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 18px;">
                        Katkıda Bulunanlar (<code>katki.php</code>) sayfasında hangi kategorinin daha üstte veya altta çıkacağını belirleyin (1 = En Üstte).
                    </p>

                    <?php if ($section_message): ?>
                        <div class="alert alert-success" style="padding: 10px; background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; margin-bottom: 16px;">
                            <i class="fas fa-check-circle mr-1"></i> <?php echo $section_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="update_sections" value="1">
                        
                        <?php 
                        $rank = 1;
                        foreach($currentOrder as $secKey): 
                            if (!isset($sectionNames[$secKey])) continue;
                        ?>
                            <div class="order-item-card">
                                <div>
                                    <span style="font-weight: 600; font-size: 0.9rem;"><?php echo $sectionNames[$secKey]; ?></span>
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">Kod: <code><?php echo $secKey; ?></code></div>
                                </div>
                                <div>
                                    <label style="font-size: 0.75rem; color: var(--text-muted); margin-right: 6px;">Sıra:</label>
                                    <input type="number" name="section_rank[<?php echo $secKey; ?>]" value="<?php echo $rank++; ?>" min="1" max="10" required>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Eşleşmeyen varsa ekle -->
                        <?php 
                        foreach($sectionNames as $key => $title): 
                            if (!in_array($key, $currentOrder)):
                        ?>
                            <div class="order-item-card">
                                <div>
                                    <span style="font-weight: 600; font-size: 0.9rem;"><?php echo $title; ?></span>
                                </div>
                                <div>
                                    <label style="font-size: 0.75rem; color: var(--text-muted); margin-right: 6px;">Sıra:</label>
                                    <input type="number" name="section_rank[<?php echo $key; ?>]" value="<?php echo $rank++; ?>" min="1" max="10" required>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>

                        <button type="submit" class="button button--suggested" style="width: 100%; margin-top: 10px;">
                            <i class="fas fa-save mr-1"></i> Sıralamayı Kaydet
                        </button>
                    </form>
                </div>

                <!-- Şifre Değiştir -->
                <div class="form-card">
                    <h3 style="margin-bottom: 16px;"><i class="fas fa-key text-blue-500"></i> Şifre Değiştir</h3>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-success" style="padding: 10px; background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; margin-bottom: 16px;">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger" style="padding: 10px; background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; margin-bottom: 16px;">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="update_password" value="1">
                        <div class="form-group" style="margin-bottom: 14px;">
                            <label style="display: block; font-size: 0.85rem; margin-bottom: 6px;">Mevcut Şifre</label>
                            <input type="password" name="current_password" required style="width: 100%; padding: 10px; border-radius: 6px; background: #1a202c; border: 1px solid var(--border-color); color: #fff;">
                        </div>
                        <div class="form-group" style="margin-bottom: 14px;">
                            <label style="display: block; font-size: 0.85rem; margin-bottom: 6px;">Yeni Şifre</label>
                            <input type="password" name="new_password" required style="width: 100%; padding: 10px; border-radius: 6px; background: #1a202c; border: 1px solid var(--border-color); color: #fff;">
                        </div>
                        <div class="form-group" style="margin-bottom: 18px;">
                            <label style="display: block; font-size: 0.85rem; margin-bottom: 6px;">Yeni Şifre (Tekrar)</label>
                            <input type="password" name="confirm_password" required style="width: 100%; padding: 10px; border-radius: 6px; background: #1a202c; border: 1px solid var(--border-color); color: #fff;">
                        </div>
                        <button type="submit" class="button button--secondary" style="width: 100%;">
                            Şifreyi Güncelle
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script>
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                adminSidebar.classList.toggle('open');
            });
        }
    </script>
</body>
</html>
