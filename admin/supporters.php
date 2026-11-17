<?php
require_once '../core/db.php';
require_once '../core/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Handle Priority Change via Action (Move up/down)
if ($action == 'reorder' && isset($_GET['id']) && isset($_GET['dir'])) {
    $id = intval($_GET['id']);
    $dir = $_GET['dir'] === 'up' ? 1 : -1;
    
    $stmt = $pdo->prepare("UPDATE supporters SET order_priority = order_priority + ? WHERE id = ?");
    $stmt->execute([$dir, $id]);
    redirect('supporters.php?msg=reordered');
}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .reorder-btns {
            display: inline-flex;
            gap: 6px;
            margin-left: 10px;
        }
        .reorder-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--admin-border);
            color: var(--admin-text-muted);
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .reorder-btn:hover {
            background: var(--admin-primary-glow);
            color: var(--admin-primary);
            border-color: var(--admin-primary);
        }
        .supporter-profile-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .supporter-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--admin-border);
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
                <li><a href="supporters.php" class="active"><i class="fas fa-users"></i> Destekçiler</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Ayarlar</a></li>
                <li><a href="../katki.php" target="_blank"><i class="fas fa-heart"></i> Destekçiler Sayfası</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Siteyi Görüntüle</a></li>
                <li class="logout-item"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
            </ul>
        </aside>
        <main class="admin-content">
            <div class="admin-header">
                <div>
                    <h1>Destekçileri Yönet</h1>
                    <span class="date" style="border: none; padding: 4px 0; background: transparent;">Kişileri ve sıralama önceliklerini buradan yönetebilirsiniz.</span>
                </div>
                <a href="settings.php" class="button button--suggested" style="font-size: 0.85rem; padding: 10px 18px;">
                    <i class="fas fa-layer-group" style="margin-right: 6px;"></i> Kategori Sıralama Ayarları
                </a>
            </div>
            
            <section class="form-card">
                <h3><i class="fas fa-<?php echo $editItem ? 'user-edit' : 'user-plus'; ?>"></i> <?php echo $editItem ? 'Destekçiyi Düzenle' : 'Yeni Destekçi Ekle'; ?></h3>
                <form method="POST">
                    <?php if ($editItem): ?>
                        <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
                    <?php endif; ?>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>İsim</label>
                            <input type="text" name="name" value="<?php echo $editItem ? htmlspecialchars($editItem['name']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori (Tip)</label>
                            <select name="type">
                                <option value="supporter" <?php echo ($editItem && $editItem['type'] == 'supporter') ? 'selected' : ''; ?>>Destekçi (Supporter)</option>
                                <option value="developer" <?php echo ($editItem && $editItem['type'] == 'developer') ? 'selected' : ''; ?>>Geliştirici (Developer)</option>
                                <option value="plugin" <?php echo ($editItem && $editItem['type'] == 'plugin') ? 'selected' : ''; ?>>Eklenti Yazarı (Plugin)</option>
                                <option value="theme" <?php echo ($editItem && $editItem['type'] == 'theme') ? 'selected' : ''; ?>>Tema Yazarı (Theme)</option>
                                <option value="sponsor" <?php echo ($editItem && $editItem['type'] == 'sponsor') ? 'selected' : ''; ?>>Sponsor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>GitHub Linki</label>
                            <input type="text" name="github_link" value="<?php echo $editItem ? htmlspecialchars($editItem['github_link']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Profil Resmi URL</label>
                            <input type="text" name="image_url" value="<?php echo $editItem ? htmlspecialchars($editItem['image_url']) : ''; ?>">
                        </div>
                        <div class="form-group" style="grid-column: span 1;">
                            <label>Açıklama (Profil sayfasında görünür)</label>
                            <textarea name="description" rows="2"><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column: span 1;">
                            <label>Katkılar (Her satıra bir madde)</label>
                            <textarea name="contributions" rows="2"><?php echo $editItem ? htmlspecialchars($editItem['contributions']) : ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Sıralama Önceliği (Büyük sayı en üstte çıkar)</label>
                            <input type="number" name="order_priority" value="<?php echo $editItem ? $editItem['order_priority'] : '0'; ?>">
                        </div>
                    </div>
                    <div style="margin-top: 10px; display: flex; gap: 12px;">
                        <button type="submit" class="button button--suggested"><?php echo $editItem ? 'Değişiklikleri Kaydet' : 'Destekçi Ekle'; ?></button>
                        <?php if ($editItem): ?>
                            <a href="supporters.php" class="button" style="background: rgba(255,255,255,0.05); color: var(--admin-text);">İptal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="form-card">
                <h3><i class="fas fa-list"></i> Kayıtlı Destekçiler Listesi</h3>
                <div style="overflow-x: auto;">
                    <table class="supporter-list">
                        <thead>
                            <tr>
                                <th>Destekçi</th>
                                <th>Tip / Kategori</th>
                                <th>Öncelik / Sıra</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($supporters as $s): ?>
                            <tr>
                                <td>
                                    <div class="supporter-profile-cell">
                                        <img src="<?php echo htmlspecialchars($s['image_url']); ?>" alt="" class="supporter-avatar">
                                        <strong><?php echo htmlspecialchars($s['name']); ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo htmlspecialchars($s['type']); ?>">
                                        <?php echo htmlspecialchars($s['type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <strong><?php echo $s['order_priority']; ?></strong>
                                        <div class="reorder-btns">
                                            <a href="supporters.php?action=reorder&id=<?php echo $s['id']; ?>&dir=up" class="reorder-btn" title="Yukarı Taşı">
                                                <i class="fas fa-chevron-up"></i>
                                            </a>
                                            <a href="supporters.php?action=reorder&id=<?php echo $s['id']; ?>&dir=down" class="reorder-btn" title="Aşağı Taşı">
                                                <i class="fas fa-chevron-down"></i>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="actions">
                                    <a href="supporters.php?action=edit&id=<?php echo $s['id']; ?>" title="Düzenle"><i class="fas fa-edit"></i></a>
                                    <a href="supporters.php?action=delete&id=<?php echo $s['id']; ?>" class="delete" onclick="return confirm('Bu destekçiyi silmek istediğinize emin misiniz?')" title="Sil"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
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