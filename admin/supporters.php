<?php
require_once '../core/db.php';
require_once '../core/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM supporters WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    redirect('supporters.php?msg=deleted');
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name']);
    $type = clean($_POST['type']);
    $github = clean($_POST['github_link']);
    $image = clean($_POST['image_url']);
    $description = clean($_POST['description']);
    $contributions = clean($_POST['contributions']);
    $slug = createSlug($name);
    $priority = intval($_POST['order_priority']);

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE supporters SET name=?, type=?, github_link=?, image_url=?, description=?, contributions=?, order_priority=? WHERE id=?");
        $stmt->execute([$name, $type, $github, $image, $description, $contributions, $priority, $_POST['id']]);
        redirect('supporters.php?msg=updated');
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO supporters (name, slug, type, github_link, image_url, description, contributions, order_priority) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $type, $github, $image, $description, $contributions, $priority]);
        redirect('supporters.php?msg=added');
    }
}

$supporters = getSupporters();
$editItem = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM supporters WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $editItem = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destekçileri Yönet - Artado Admin</title>
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
                <li><a href="supporters.php" class="active"><i class="fas fa-users"></i> Destekçiler</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Ayarlar</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Siteyi Görüntüle</a></li>
                <li class="logout-item"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
            </ul>
        </aside>
        <main class="admin-content">
            <h1>Destekçileri Yönet</h1>
            
            <section class="form-card">
                <h3><?php echo $editItem ? 'Düzenle' : 'Yeni Destekçi Ekle'; ?></h3>
                <form method="POST">
                    <?php if ($editItem): ?>
                        <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
                    <?php endif; ?>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>İsim</label>
                            <input type="text" name="name" value="<?php echo $editItem ? $editItem['name'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Tip</label>
                            <select name="type">
                                <option value="supporter" <?php echo ($editItem && $editItem['type'] == 'supporter') ? 'selected' : ''; ?>>Destekçi (Contributor)</option>
                                <option value="developer" <?php echo ($editItem && $editItem['type'] == 'developer') ? 'selected' : ''; ?>>Geliştirici (Genel)</option>
                                <option value="plugin" <?php echo ($editItem && $editItem['type'] == 'plugin') ? 'selected' : ''; ?>>Eklenti Yazarı</option>
                                <option value="theme" <?php echo ($editItem && $editItem['type'] == 'theme') ? 'selected' : ''; ?>>Tema Yazarı</option>
                                <option value="sponsor" <?php echo ($editItem && $editItem['type'] == 'sponsor') ? 'selected' : ''; ?>>Sponsor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>GitHub Linki</label>
                            <input type="text" name="github_link" value="<?php echo $editItem ? $editItem['github_link'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Profil Resmi URL</label>
                            <input type="text" name="image_url" value="<?php echo $editItem ? $editItem['image_url'] : ''; ?>">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Açıklama (Profilde üstte görünür)</label>
                            <textarea name="description" rows="3"><?php echo $editItem ? $editItem['description'] : ''; ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Katkılar (Her satıra bir madde gelecek şekilde yazın)</label>
                            <textarea name="contributions" rows="4"><?php echo $editItem ? $editItem['contributions'] : ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Sıralama Önceliği (Büyük olan üstte çıkar)</label>
                            <input type="number" name="order_priority" value="<?php echo $editItem ? $editItem['order_priority'] : '0'; ?>">
                        </div>
                    </div>
                    <button type="submit" class="button button--suggested"><?php echo $editItem ? 'Güncelle' : 'Ekle'; ?></button>
                    <?php if ($editItem): ?>
                        <a href="supporters.php" class="button" style="display:inline-block; background: #333;">İptal</a>
                    <?php endif; ?>
                </form>
            </section>

            <section>
                <table class="supporter-list">
                    <thead>
                        <tr>
                            <th>Resim</th>
                            <th>İsim</th>
                            <th>Tip</th>
                            <th>Sıralama</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($supporters as $s): ?>
                        <tr>
                            <td><img src="<?php echo $s['image_url']; ?>" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;"></td>
                            <td><?php echo $s['name']; ?></td>
                            <td><span class="badge badge-<?php echo $s['type']; ?>"><?php echo strtoupper($s['type']); ?></span></td>
                            <td><?php echo $s['order_priority']; ?></td>
                            <td class="actions">
                                <a href="supporters.php?action=edit&id=<?php echo $s['id']; ?>"><i class="fas fa-edit"></i></a>
                                <a href="supporters.php?action=delete&id=<?php echo $s['id']; ?>" class="delete" onclick="return confirm('Emin misiniz?')"><i class="fas fa-trash"></i></a>
                            </td>
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
