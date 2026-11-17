<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'layout.php';

require_role('author'); // All staff can see the dashboard

// Stats (Resilient)
$post_count = 0; $page_count = 0; $comment_count = 0; $pending_comments = 0;
$user_id = $_SESSION['user_id'];
$is_staff = !is_admin();

try {
    if (is_admin()) {
        $post_count = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
        $page_count = $pdo->query("SELECT COUNT(*) FROM pages")->fetchColumn();
        $comment_count = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
        $pending_comments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE author_id = ?");
        $stmt->execute([$user_id]);
        $post_count = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments c JOIN posts p ON c.post_id = p.id WHERE p.author_id = ?");
        $stmt->execute([$user_id]);
        $comment_count = $stmt->fetchColumn();
    }
} catch (Exception $e) {}

$recent_posts = []; $recent_comments = [];
try {
    if (is_admin()) {
        $recent_posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT 5")->fetchAll();
        $recent_comments = $pdo->query("SELECT c.*, p.title as post_title FROM comments c JOIN posts p ON c.post_id = p.id ORDER BY c.created_at DESC LIMIT 3")->fetchAll();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE author_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$user_id]);
        $recent_posts = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("SELECT c.*, p.title as post_title FROM comments c JOIN posts p ON c.post_id = p.id WHERE p.author_id = ? ORDER BY c.created_at DESC LIMIT 3");
        $stmt->execute([$user_id]);
        $recent_comments = $stmt->fetchAll();
    }
} catch (Exception $e) {}

admin_header('Dashboard');
admin_sidebar('dashboard');
?>

<main class="p-4 md:p-8 lg:p-10 bg-slate-50 min-h-screen text-slate-800">
    
    <!-- Üst Başlık Alanı -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Dashboard</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Blodato CMS içerik ve site genel görünüm özetine hoş geldiniz.</p>
        </div>
        <div class="text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm self-start sm:self-auto flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
            Sistem Aktif • <?php echo date('F d, Y'); ?>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 hover:border-blue-500/50 transition-all group">
            <div class="text-[11px] font-bold text-slate-400 group-hover:text-blue-600 uppercase tracking-widest mb-2 transition-colors">Toplam Gönderi</div>
            <div class="text-3xl font-black text-slate-900 tracking-tight"><?php echo $post_count; ?></div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 hover:border-indigo-500/50 transition-all group">
            <div class="text-[11px] font-bold text-slate-400 group-hover:text-indigo-600 uppercase tracking-widest mb-2 transition-colors">Sayfalar</div>
            <div class="text-3xl font-black text-slate-900 tracking-tight"><?php echo $page_count; ?></div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 hover:border-emerald-500/50 transition-all group">
            <div class="text-[11px] font-bold text-slate-400 group-hover:text-emerald-600 uppercase tracking-widest mb-2 transition-colors">Yorumlar</div>
            <div class="text-3xl font-black text-slate-900 tracking-tight"><?php echo $comment_count; ?></div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 hover:border-orange-500/50 transition-all group border-l-4 border-l-orange-500">
            <div class="text-[11px] font-bold text-orange-500 uppercase tracking-widest mb-2">Onay Bekleyen</div>
            <div class="text-3xl font-black text-orange-600 tracking-tight"><?php echo $pending_comments; ?></div>
        </div>
        
    </div>

    <!-- İçerik Bölümleri -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Hızlı Taslak Alanı -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">Hızlı Taslak Oluştur</h3>
                </div>
                <div class="p-6">
                    <form action="posts.php" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Yazı Başlığı</label>
                            <input type="text" name="title" placeholder="Başlık girin..." required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">İçerik Özeti</label>
                            <textarea name="content" rows="4" placeholder="Aklınızdan ne geçiyor?" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all"></textarea>
                        </div>
                        <button type="submit" name="quick_draft" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold uppercase tracking-widest text-xs transition-all shadow-sm shadow-blue-600/30">
                            Taslak Olarak Kaydet
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Son Aktiviteler (Son Gönderiler & Son Yorumlar) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 overflow-hidden h-full flex flex-col">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">Son Aktiviteler</h3>
                </div>
                
                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8 flex-1">
                    
                    <!-- Son Gönderiler -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase mb-4 tracking-wider flex items-center gap-2">
                            <span>📝</span> Son Yazılar
                        </h4>
                        <div class="space-y-3">
                            <?php if(empty($recent_posts)): ?>
                                <p class="text-xs text-slate-400">Henüz yazı eklenmemiş.</p>
                            <?php else: ?>
                                <?php foreach($recent_posts as $p): ?>
                                    <div class="flex items-center justify-between gap-4 p-3 bg-slate-50 rounded-xl border border-slate-100 hover:border-slate-200 transition-all">
                                        <a href="post-edit.php?id=<?php echo $p['id']; ?>" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors truncate"><?php echo e($p['title']); ?></a>
                                        <span class="text-[10px] font-semibold text-slate-400 shrink-0"><?php echo date('M d', strtotime($p['created_at'])); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Son Yorumlar -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase mb-4 tracking-wider flex items-center gap-2">
                            <span>💬</span> Son Yorumlar
                        </h4>
                        <div class="space-y-3">
                            <?php if(empty($recent_comments)): ?>
                                <p class="text-xs text-slate-400">Henüz yorum yapılmamış.</p>
                            <?php else: ?>
                                <?php foreach($recent_comments as $c): ?>
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-1">
                                        <div class="font-bold text-slate-800 text-xs flex items-center justify-between">
                                            <span><?php echo e($c['author_name']); ?></span>
                                            <span class="text-[9px] text-slate-400 font-normal"><?php echo date('M d', strtotime($c['created_at'])); ?></span>
                                        </div>
                                        <p class="text-slate-500 text-[11px] line-clamp-2">"<?php echo e($c['content']); ?>"</p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
    </div>
</main>

<?php admin_footer(); ?>