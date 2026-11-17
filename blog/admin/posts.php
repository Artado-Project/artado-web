<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'layout.php';

require_role('author');

// --- SİLME İŞLEMİ (LOGIC) ---
if (isset($_GET['delete'])) {
    try {
        $delete_id = (int)$_GET['delete'];
        
        // Güvenlik: Admin değilse sadece kendi postunu silebilir
        if (!is_admin()) {
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND author_id = ?");
            $stmt->execute([$delete_id, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$delete_id]);
        }
        
        header("Location: posts.php?msg=success");
        exit;
    } catch (Exception $e) {
        $error = "Silme işlemi sırasında bir hata oluştu.";
    }
}

// --- VERİ ÇEKME (READ) ---
$search = $_GET['s'] ?? '';
$status_filter = $_GET['status'] ?? 'all';
$posts = [];

try {
    $query = "SELECT * FROM posts WHERE (title LIKE ? OR content LIKE ?)";
    $params = ["%$search%", "%$search%"];
    
    // Kısıtlama: Admin olmayanlar sadece kendi yazılarını görür
    if (!is_admin()) {
        $query .= " AND author_id = ?";
        $params[] = $_SESSION['user_id'];
    }
    
    if ($status_filter !== 'all') {
        $query .= " AND status = ?";
        $params[] = $status_filter;
    }
    
    $query .= " ORDER BY created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $posts = $stmt->fetchAll();
} catch (Exception $e) {
    // Veritabanı hatası
}

admin_header('Posts');
admin_sidebar('posts');
?>

<main class="flex-1 p-8 bg-gray-50 min-h-screen">
    
    <!-- Header & Search -->
    <div class="max-w-7xl mx-auto flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Yazılar</h1>
            <p class="text-sm text-gray-500 mt-1">Sistemdeki tüm içeriklerinizi buradan yönetebilirsiniz.</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="" method="GET" class="flex gap-2">
                <input type="text" name="s" value="<?php echo e($search); ?>" placeholder="Ara..." class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none w-64">
                <button type="submit" class="bg-white border border-gray-300 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50">Ara</button>
            </form>
            <a href="post-edit.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition shadow-sm">Add New</a>
        </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto mb-6 flex items-center gap-6 text-sm font-medium text-gray-500">
        <a href="?status=all" class="<?php echo $status_filter === 'all' ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'hover:text-gray-900'; ?>">Tümü (<?php echo count($posts); ?>)</a>
        <a href="?status=published" class="<?php echo $status_filter === 'published' ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'hover:text-gray-900'; ?>">Yayında</a>
        <a href="?status=draft" class="<?php echo $status_filter === 'draft' ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'hover:text-gray-900'; ?>">Taslaklar</a>
    </div>

    <!-- Table -->
    <div class="max-w-7xl mx-auto bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="p-4 w-10"><input type="checkbox" class="rounded border-gray-300"></th>
                    <th class="p-4 text-xs font-bold text-gray-500 uppercase">Title</th>
                    <th class="p-4 text-xs font-bold text-gray-500 uppercase">Status</th>
                    <th class="p-4 text-xs font-bold text-gray-500 uppercase">Lang</th>
                    <th class="p-4 text-xs font-bold text-gray-500 uppercase">Category</th>
                    <th class="p-4 text-xs font-bold text-gray-500 uppercase text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if(empty($posts)): ?>
                    <tr><td colspan="6" class="p-10 text-center text-gray-500">Henüz bir yazı bulunamadı.</td></tr>
                <?php else: ?>
                    <?php foreach($posts as $post): ?>
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="p-4"><input type="checkbox" class="rounded border-gray-300"></td>
                        <td class="p-4">
                            <a href="post-edit.php?id=<?php echo $post['id']; ?>" class="font-semibold text-gray-900 hover:text-blue-600 block"><?php echo e($post['title']); ?></a>
                            <div class="flex gap-3 mt-1 text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition">
                                <a href="post-edit.php?id=<?php echo $post['id']; ?>" class="hover:text-blue-600">Edit</a>
                                <a href="posts.php?delete=<?php echo $post['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Bu yazıyı silmek istediğinize emin misiniz?')">Delete</a>
                                <a href="../post.php?slug=<?php echo $post['slug']; ?>" target="_blank" class="hover:text-gray-900">View</a>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-[11px] font-bold uppercase <?php echo $post['status'] === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                <?php echo e($post['status']); ?>
                            </span>
                        </td>
                        <td class="p-4 text-sm text-gray-600 font-medium"><?php echo strtoupper(e($post['lang'] ?? 'TR')); ?></td>
                        <td class="p-4 text-sm text-gray-600"><?php echo e($post['category'] ?? 'General'); ?></td>
                        <td class="p-4 text-sm text-gray-500 text-right"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Footer Actions -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
            <div class="flex gap-2">
                <select class="border border-gray-300 rounded px-2 py-1 text-sm text-gray-600">
                    <option>Bulk Actions</option>
                    <option>Delete</option>
                </select>
                <button class="px-4 py-1 border border-gray-300 rounded text-sm font-semibold hover:bg-gray-100 transition">Apply</button>
            </div>
            <div class="text-sm text-gray-500">Showing <?php echo count($posts); ?> items</div>
        </div>
    </div>
</main>

<?php admin_footer(); ?>