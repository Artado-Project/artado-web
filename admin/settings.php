<?php
require_once '../core/db.php';
require_once '../core/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Get user from DB
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
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - Artado Admin</title>
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
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="supporters.php"><i class="fas fa-users"></i> Destekçiler</a></li>
                <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Ayarlar</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Siteyi Görüntüle</a></li>
                <li class="logout-item"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
            </ul>
        </aside>
        <main class="admin-content">
            <h1>Panel Ayarları</h1>
            <p style="color: #888; margin-bottom: 30px;">Şifrenizi ve panel ayarlarınızı buradan güncelleyebilirsiniz.</p>

            <div class="form-card">
                <h3 style="margin-bottom: 20px;"><i class="fas fa-key"></i> Şifre Değiştir</h3>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Mevcut Şifre</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>Yeni Şifre</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Yeni Şifre (Tekrar)</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="button button--suggested" style="width: 100%;">Şifreyi Güncelle</button>
                </form>
            </div>
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
