<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'layout.php';

require_role('editor');

// Delete Page Logic
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM pages WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header("Location: pages.php?msg=Sayfa başarıyla silindi.");
    exit;
}

// Fetch Pages (Resilient)
$pages = [];
try {
    $stmt = $pdo->query("SELECT * FROM pages ORDER BY created_at DESC");
    $pages = $stmt->fetchAll();
} catch (Exception $e) {}

admin_header('Sayfalar');
admin_sidebar('pages');
?>

<main class="p-4 md:p-8 lg:p-10 bg-slate-50/50 min-h-screen">
    
    <header class="mb-8 md:mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Sayfalar</h1>
            <p class="text-sm text-slate-600 font-medium mt-1">Kurumsal sayfalarınızı ve statik içeriklerinizi yönetin.</p>
        </div>
        <a href="page-edit.php" class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md shadow-blue-500/10 hover:shadow-blue-500/20 active:scale-[0.98] transition-all self-start sm:self-auto">
            ➕ Yeni Sayfa Ekle
        </a>
    </header>

    <?php if(isset($_GET['msg'])): ?>
        <div class="p-4 mb-6 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-xl text-sm font-semibold flex items-center shadow-sm animate-fade-in">
            <span class="mr-2">✨</span> <?php echo e($_GET['msg']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_2px_8px_-3px_rgba(0,0,0,0.05)] overflow-hidden">
        
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-100/80">
                        <th class="py-4 px-6 text-xs font-extrabold uppercase tracking-wider text-slate-700">Sayfa Başlığı</th>
                        <th class="py-4 px-6 text-xs font-extrabold uppercase tracking-wider text-slate-700 w-36">Durum</th>
                        <th class="py-4 px-6 text-xs font-extrabold uppercase tracking-wider text-slate-700 w-44">Oluşturma</th>
                        <th class="py-4 px-6 text-xs font-extrabold uppercase tracking-wider text-slate-700 w-48 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if(empty($pages)): ?>
                        <tr>
                            <td colspan="4" class="py-16 text-center text-slate-600 italic font-semibold text-sm">
                                Henüz kurumsal bir sayfa oluşturulmamış.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($pages as $page): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-6 vertical-middle">
                                    <a href="page-edit.php?id=<?php echo $page['id']; ?>" class="text-[15px] font-bold text-slate-900 hover:text-blue-600 transition-colors block"><?php echo e($page['title']); ?></a>
                                    <span class="text-xs text-slate-400 font-medium block mt-0.5">slug: /<?php echo e($page['slug']); ?></span>
                                </td>
                                
                                <td class="py-4 px-6 vertical-middle">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-extrabold tracking-wide uppercase <?php echo $page['status'] === 'published' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-slate-200 text-slate-800 border border-slate-300'; ?>">
                                        <?php echo $page['status'] === 'published' ? 'Yayında' : 'Taslak'; ?>
                                    </span>
                                </td>
                                
                                <td class="py-4 px-6 vertical-middle">
                                    <div class="text-sm font-bold text-slate-600"><?php echo date('d M Y', strtotime($page['created_at'])); ?></div>
                                </td>

                                <td class="py-4 px-6 text-right vertical-middle">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <a href="../page.php?slug=<?php echo $page['slug']; ?>" target="_blank" title="Görüntüle" class="p-2 text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 border border-slate-300/60 rounded-lg transition-colors group">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        <a href="page-edit.php?id=<?php echo $page['id']; ?>" title="Düzenle" class="p-2 text-blue-600 hover:text-white bg-blue-50 hover:bg-blue-600 border border-blue-200 hover:border-blue-600 rounded-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11 20H8v-3l9.414-9.414z"></path></svg>
                                        </a>

                                        <a href="pages.php?delete=<?php echo $page['id']; ?>" onclick="return confirm('Bu sayfayı silmek istediğinize emin misiniz?')" title="Sil" class="p-2 text-red-600 hover:text-white bg-red-50 hover:bg-red-600 border border-red-200 hover:border-red-600 rounded-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="block md:hidden divide-y divide-slate-200">
            <?php if(empty($pages)): ?>
                <div class="py-12 text-center text-slate-600 italic font-medium text-sm">
                    Henüz kurumsal bir sayfa oluşturulmamış.
                </div>
            <?php else: ?>
                <?php foreach($pages as $page): ?>
                    <div class="p-5 space-y-4 bg-white">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <a href="page-edit.php?id=<?php echo $page['id']; ?>" class="text-[16px] font-black text-slate-900 active:text-blue-600 block"><?php echo e($page['title']); ?></a>
                                <div class="text-xs text-slate-600 font-bold mt-1"><?php echo date('d M Y', strtotime($page['created_at'])); ?></div>
                            </div>
                            <span class="inline-flex items-center shrink-0 px-2.5 py-0.5 rounded text-[10px] font-extrabold tracking-wide uppercase <?php echo $page['status'] === 'published' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-slate-200 text-slate-800 border border-slate-300'; ?>">
                                <?php echo $page['status'] === 'published' ? 'Yayında' : 'Taslak'; ?>
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                            <a href="page-edit.php?id=<?php echo $page['id']; ?>" class="flex-1 bg-blue-50 text-blue-700 font-bold text-center text-xs py-2.5 rounded-lg border border-blue-200 flex items-center justify-center gap-1">
                                📝 Düzenle
                            </a>
                            <a href="../page.php?slug=<?php echo $page['slug']; ?>" target="_blank" class="flex-1 bg-slate-100 text-slate-800 font-bold text-center text-xs py-2.5 rounded-lg border border-slate-300 flex items-center justify-center gap-1">
                                👁️ Görüntüle
                            </a>
                            <a href="pages.php?delete=<?php echo $page['id']; ?>" onclick="return confirm('Bu sayfayı silmek istediğinize emin misiniz?')" class="px-4 bg-red-100 text-red-900 font-bold text-center text-xs py-2.5 rounded-lg border border-red-300 flex items-center justify-center">
                                🗑️
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php admin_footer(); ?>