<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'layout.php';

require_role('editor');

$msg = '';

if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    if (is_admin()) {
        $stmt = $pdo->prepare("UPDATE comments SET status = 'approved', editor_request = NULL WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Comment approved successfully.";
    } else {
        $stmt = $pdo->prepare("UPDATE comments SET editor_request = 'approve' WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Approval request sent to admin.";
    }
}

if (isset($_GET['spam'])) {
    $id = (int)$_GET['spam'];
    if (is_admin()) {
        $stmt = $pdo->prepare("UPDATE comments SET status = 'spam', editor_request = NULL WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Marked as spam.";
    } else {
        $stmt = $pdo->prepare("UPDATE comments SET editor_request = 'spam' WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Spam report sent to admin.";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (is_admin()) {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Comment deleted.";
    } else {
        $stmt = $pdo->prepare("UPDATE comments SET editor_request = 'delete' WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Deletion request sent to admin.";
    }
}

if (isset($_GET['reject_request']) && is_admin()) {
    $stmt = $pdo->prepare("UPDATE comments SET editor_request = NULL WHERE id = ?");
    $stmt->execute([(int)$_GET['reject_request']]);
    $msg = "Editor request rejected.";
}

// Fetch Comments with Post/Page Titles
$stmt = $pdo->query("
    SELECT c.*, p.title as post_title, pg.title as page_title 
    FROM comments c 
    LEFT JOIN posts p ON c.post_id = p.id 
    LEFT JOIN pages pg ON c.page_id = pg.id 
    ORDER BY c.created_at DESC
");
$comments = $stmt->fetchAll();

admin_header('Comments');
admin_sidebar('comments');
?>

<main class="p-4 md:p-8 lg:p-10 bg-slate-50 min-h-screen text-slate-800">
    
    <header class="mb-8 md:mb-10">
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Yorumlar</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Yazılarınıza ve sayfalarınıza gelen okuyucu geri bildirimlerini yönetin.</p>
    </header>

    <?php if($msg): ?>
        <div class="p-4 mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-semibold flex items-center shadow-sm">
            <span class="mr-2">✨</span> <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 overflow-hidden">
        
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/75">
                        <th class="py-4 px-6 text-xs font-extrabold uppercase tracking-wider text-slate-500 w-64">Yazar</th>
                        <th class="py-4 px-6 text-xs font-extrabold uppercase tracking-wider text-slate-500">Yorum İçeriği</th>
                        <th class="py-4 px-6 text-xs font-extrabold uppercase tracking-wider text-slate-500 w-52">Kaynak</th>
                        <th class="py-4 px-6 text-xs font-extrabold uppercase tracking-wider text-slate-500 w-64 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(empty($comments)): ?>
                        <tr><td colspan="4" class="text-center py-16 text-slate-400 italic font-medium text-sm">Henüz hiç yorum yapılmamış.</td></tr>
                    <?php else: ?>
                        <?php foreach($comments as $c): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-5 px-6 align-top">
                                    <div class="font-bold text-slate-900 text-[15px]"><?php echo e($c['author_name']); ?></div>
                                    <div class="text-xs text-slate-500 font-semibold mt-0.5 break-all"><?php echo e($c['author_email']); ?></div>
                                    <div class="text-[11px] text-slate-400 font-medium mt-2 flex items-center gap-1">
                                        📅 <?php echo date('d M Y', strtotime($c['created_at'])); ?>
                                    </div>
                                </td>
                                
                                <td class="py-5 px-6 align-top">
                                    <div class="text-sm text-slate-700 leading-relaxed max-w-xl bg-slate-50 p-3.5 rounded-xl border border-slate-200/70 shadow-2xs">
                                        <?php echo nl2br(e($c['content'])); ?>
                                    </div>
                                    <div class="mt-2.5">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-extrabold tracking-wide uppercase border <?php 
                                            echo $c['status'] === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($c['status'] === 'spam' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200'); 
                                        ?>">
                                            <?php echo $c['status']; ?>
                                        </span>
                                    </div>
                                </td>
                                
                                <td class="py-5 px-6 align-top">
                                    <div class="text-xs font-bold text-blue-600 hover:underline line-clamp-2 max-w-[180px]">
                                        <?php echo e($c['post_title'] ?: $c['page_title']); ?>
                                    </div>
                                    <span class="inline-block mt-1 text-[9px] font-black uppercase tracking-wider bg-slate-100 border border-slate-200 text-slate-600 px-1.5 py-0.5 rounded">
                                        <?php echo $c['post_id'] ? 'Yazı' : 'Sayfa'; ?>
                                    </span>
                                </td>
                                
                                <td class="py-5 px-6 text-right align-top">
                                    <div class="flex flex-col items-end gap-2">
                                        <?php if($c['editor_request'] && is_admin()): ?>
                                            <div class="w-full max-w-[200px] p-2 bg-blue-50 border border-blue-200 rounded-lg text-[10px] font-black uppercase text-blue-700 text-center tracking-wider animate-pulse">
                                                🔔 İstek Var: <?php echo $c['editor_request']; ?>
                                            </div>
                                            <div class="inline-flex gap-1.5">
                                                <a href="?approve=<?php echo $c['id']; ?>" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">Onayla</a>
                                                <a href="?reject_request=<?php echo $c['id']; ?>" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg border border-slate-300 transition-all">Reddet</a>
                                            </div>
                                        <?php else: ?>
                                            <div class="inline-flex items-center gap-1.5">
                                                <?php if($c['status'] !== 'approved'): ?>
                                                    <a href="?approve=<?php echo $c['id']; ?>" class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white border border-blue-200 text-xs font-bold rounded-lg transition-all">
                                                        <?php echo is_admin() ? 'Onayla' : 'Onay İste'; ?>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if($c['status'] !== 'spam'): ?>
                                                    <a href="?spam=<?php echo $c['id']; ?>" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-600 text-amber-700 hover:text-white border border-amber-200 text-xs font-bold rounded-lg transition-all">
                                                        <?php echo is_admin() ? 'Spam' : 'Spam Raporla'; ?>
                                                    </a>
                                                <?php endif; ?>

                                                <a href="?delete=<?php echo $c['id']; ?>" onclick="return confirm('<?php echo is_admin() ? 'Kalıcı olarak silinsin mi?' : 'Silme talebi gönderilsin mi?'; ?>')" class="p-1.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 rounded-lg transition-all" title="Sil">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="block lg:hidden divide-y divide-slate-100">
            <?php if(empty($comments)): ?>
                <div class="py-12 text-center text-slate-400 italic text-sm">Henüz hiç yorum yapılmamış.</div>
            <?php else: ?>
                <?php foreach($comments as $c): ?>
                    <div class="p-5 space-y-4 bg-white">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <div class="font-bold text-slate-900 text-[15px]"><?php echo e($c['author_name']); ?></div>
                                <div class="text-xs text-slate-500 break-all"><?php echo e($c['author_email']); ?></div>
                                <div class="text-[10px] text-slate-400 mt-1">🎯 <?php echo e($c['post_title'] ?: $c['page_title']); ?></div>
                            </div>
                            <span class="inline-flex shrink-0 px-2 py-0.5 rounded text-[9px] font-extrabold tracking-wide uppercase border <?php 
                                echo $c['status'] === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($c['status'] === 'spam' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200'); 
                            ?>">
                                <?php echo $c['status']; ?>
                            </span>
                        </div>

                        <div class="text-sm text-slate-700 bg-slate-50 p-3.5 rounded-xl border border-slate-200/70 leading-relaxed">
                            <?php echo nl2br(e($c['content'])); ?>
                        </div>

                        <div class="pt-3 border-t border-slate-100">
                            <?php if($c['editor_request'] && is_admin()): ?>
                                <div class="flex flex-col gap-2">
                                    <div class="text-[10px] font-black uppercase text-blue-700 text-center bg-blue-50 p-2 rounded-lg border border-blue-200">🔔 İstek: <?php echo $c['editor_request']; ?></div>
                                    <div class="flex gap-2">
                                        <a href="?approve=<?php echo $c['id']; ?>" class="flex-1 bg-emerald-600 text-white font-bold text-center text-xs py-2 rounded-lg">Onayla</a>
                                        <a href="?reject_request=<?php echo $c['id']; ?>" class="flex-1 bg-slate-100 text-slate-700 font-bold text-center text-xs py-2 rounded-lg border border-slate-300">Reddet</a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center gap-2">
                                    <?php if($c['status'] !== 'approved'): ?>
                                        <a href="?approve=<?php echo $c['id']; ?>" class="flex-1 bg-blue-50 text-blue-700 font-bold text-center text-xs py-2 rounded-lg border border-blue-200">
                                            <?php echo is_admin() ? 'Onayla' : 'Onay İste'; ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if($c['status'] !== 'spam'): ?>
                                        <a href="?spam=<?php echo $c['id']; ?>" class="flex-1 bg-amber-50 text-amber-700 font-bold text-center text-xs py-2 rounded-lg border border-amber-200">
                                            <?php echo is_admin() ? 'Spam' : 'Spamla'; ?>
                                        </a>
                                    <?php endif; ?>
                                    <a href="?delete=<?php echo $c['id']; ?>" onclick="return confirm('Silinsin mi?')" class="px-4 bg-red-50 text-red-600 border border-red-200 text-center py-2 rounded-lg">
                                        🗑️
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php admin_footer(); ?>