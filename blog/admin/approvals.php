<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'layout.php';

require_role('admin');

$msg = '';

if (isset($_GET['approve_post'])) {
    $stmt = $pdo->prepare("UPDATE posts SET status = 'published' WHERE id = ?");
    $stmt->execute([(int)$_GET['approve_post']]);
    $msg = "Yazı başarıyla yayınlandı.";
}

if (isset($_GET['approve_comment'])) {
    $id = (int)$_GET['approve_comment'];
    $stmt = $pdo->prepare("UPDATE comments SET status = 'approved', editor_request = NULL WHERE id = ?");
    $stmt->execute([$id]);
    $msg = "Yorum onaylandı.";
}

if (isset($_GET['spam_comment'])) {
    $id = (int)$_GET['spam_comment'];
    $stmt = $pdo->prepare("UPDATE comments SET status = 'spam', editor_request = NULL WHERE id = ?");
    $stmt->execute([$id]);
    $msg = "Yorum spam olarak işaretlendi.";
}

if (isset($_GET['delete_comment'])) {
    $id = (int)$_GET['delete_comment'];
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$id]);
    $msg = "Yorum istek doğrultusunda silindi.";
}

if (isset($_GET['reject_comment'])) {
    $stmt = $pdo->prepare("UPDATE comments SET editor_request = NULL WHERE id = ?");
    $stmt->execute([(int)$_GET['reject_comment']]);
    $msg = "Editör isteği reddedildi.";
}

$stmt = $pdo->prepare("SELECT p.*, u.username as author_name FROM posts p JOIN users u ON p.author_id = u.id WHERE p.status = 'draft' AND p.author_id != ? ORDER BY p.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$pending_posts = $stmt->fetchAll();

$stmt = $pdo->query("SELECT c.*, p.title as post_title FROM comments c JOIN posts p ON c.post_id = p.id WHERE c.editor_request IS NOT NULL ORDER BY c.created_at DESC");
$pending_comments = $stmt->fetchAll();

admin_header('Onay Bekleyenler');
admin_sidebar('approvals');
?>

<main class="p-4 md:p-8 lg:p-10 bg-slate-50 min-h-screen text-slate-800">
    
    <div class="mb-8 md:mb-10">
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Onay Bekleyen İşlemler</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Editör ve yazarların inceleme bekleyen içeriklerini buradan yönetin.</p>
    </div>

    <?php if($msg): ?>
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 mb-8 text-xs font-semibold flex items-center gap-2 shadow-sm">
            <span>✨</span> <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Bekleyen Yazılar -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 font-black text-xs uppercase tracking-widest text-slate-500 flex items-center justify-between">
                <span>Yazı Onayları</span>
                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-600 rounded-full text-[10px] font-bold"><?php echo count($pending_posts); ?></span>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-black uppercase text-slate-400 tracking-wider">
                        <tr>
                            <th class="py-3 px-6">Yazı</th>
                            <th class="py-3 px-6 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if(empty($pending_posts)): ?>
                            <tr>
                                <td colspan="2" class="text-center py-12 text-slate-400 font-medium text-xs italic">Bekleyen yazı bulunmuyor.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($pending_posts as $p): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-900"><?php echo e($p['title']); ?></div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">Yazar: <span class="text-slate-600"><?php echo e($p['author_name']); ?></span></div>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="?approve_post=<?php echo $p['id']; ?>" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200 transition-all">Yayınla</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bekleyen Yorum İstekleri -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 font-black text-xs uppercase tracking-widest text-slate-500 flex items-center justify-between">
                <span>Yorum İstekleri</span>
                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-600 rounded-full text-[10px] font-bold"><?php echo count($pending_comments); ?></span>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-black uppercase text-slate-400 tracking-wider">
                        <tr>
                            <th class="py-3 px-6">Yorum / İstek</th>
                            <th class="py-3 px-6 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if(empty($pending_comments)): ?>
                            <tr>
                                <td colspan="2" class="text-center py-12 text-slate-400 font-medium text-xs italic">Bekleyen yorum isteği bulunmuyor.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($pending_comments as $c): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6 max-w-xs">
                                        <div class="text-xs text-slate-700 font-medium line-clamp-1 mb-1.5">"<?php echo e($c['content']); ?>"</div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-black text-[9px] uppercase tracking-wider rounded-md border border-blue-100">İstek: <?php echo strtoupper($c['editor_request']); ?></span>
                                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider truncate max-w-[150px]">Yazı: <?php echo e($c['post_title']); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            <?php if($c['editor_request'] === 'approve'): ?>
                                                <a href="?approve_comment=<?php echo $c['id']; ?>" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-bold rounded-lg border border-emerald-200 transition-all">Onayla</a>
                                            <?php elseif($c['editor_request'] === 'delete'): ?>
                                                <a href="?delete_comment=<?php echo $c['id']; ?>" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-[11px] font-bold rounded-lg border border-red-200 transition-all">Silmeyi Onayla</a>
                                            <?php elseif($c['editor_request'] === 'spam'): ?>
                                                <a href="?spam_comment=<?php echo $c['id']; ?>" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-[11px] font-bold rounded-lg border border-amber-200 transition-all">Spam Onayla</a>
                                            <?php endif; ?>
                                            <a href="?reject_comment=<?php echo $c['id']; ?>" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[11px] font-bold rounded-lg transition-all">Reddet</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<?php admin_footer(); ?>