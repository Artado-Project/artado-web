<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/markdown.php';

$slug = $_GET['slug'] ?? '';

if (!$slug) {
    header("Location: index.php");
    exit;
}

log_visit($_SERVER['REQUEST_URI']);

$stmt = $pdo->prepare("SELECT p.*, u.username as author_name, u.bio as author_bio, u.profile_pic as author_pic, u.role as author_role FROM posts p LEFT JOIN users u ON p.author_id = u.id WHERE p.slug = ? AND p.status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    header("HTTP/1.0 404 Not Found");
    die("Yazı bulunamadı.");
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE author_id = ? AND status = 'published'");
$stmt->execute([$post['author_id']]);
$author_post_count = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $post_id = $_POST['post_id'];
    $author = trim($_POST['author_name']);
    $email = trim($_POST['author_email']);
    $content = trim($_POST['comment_content']);
    
    if ($author && $content) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, author_name, author_email, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$post_id, $author, $email, $content]);
        $comment_msg = "Yorumunuz gönderildi ve onay bekliyor.";
    }
}

$site_title = get_setting('site_title', 'Artado Blog');
$accent_color = get_setting('accent_color', '#2563eb');
$is_logged_in = is_logged_in();
?>
<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($post['title']); ?> — <?php echo e($site_title); ?></title>
    <link rel="icon" href="<?php echo base_url(get_setting('site_favicon', 'assets/img/logo.png')); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc; 
            color: #0f172a; 
            transition: background-color 0.2s, color 0.2s; 
        }
        .dark body { 
            background-color: #0c0f14; 
            color: #f1f5f9; 
        }
        h1, h2, h3, h4, .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* --- İÇERİK ALANI (POST CONTENT) STİLLERİ --- */
        .post-content p { margin-bottom: 1.5rem; line-height: 1.8; color: #334155; font-size: 1.05rem; }
        .dark .post-content p { color: #94a3b8; }
        
        .post-content h1, .post-content h2, .post-content h3, .post-content h4 { 
            color: #0f172a !important; 
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 700 !important;
            margin-top: 2rem; 
            margin-bottom: 1rem; 
        }
        .dark .post-content h1, .dark .post-content h2, .dark .post-content h3, .dark .post-content h4 { 
            color: #f8fafc !important; 
        }
        
        .post-content h2 { font-size: 1.65rem; letter-spacing: -0.02em; }
        .post-content h3 { font-size: 1.3rem; letter-spacing: -0.01em; }
        
        .post-content ul, .post-content ol { margin-bottom: 1.5rem; padding-left: 1.5rem; color: #334155; }
        .dark .post-content ul, .dark .post-content ol { color: #94a3b8; }
        .post-content ul { list-style-type: disc; }
        .post-content ol { list-style-type: decimal; }
        .post-content li { margin-bottom: 0.5rem; }

        .post-content code { background: #e2e8f0; color: #0f172a; padding: 3px 6px; border-radius: 4px; font-size: 0.88em; }
        .dark .post-content code { color: #93c5fd; background: #1e2636; }
        
        .post-content pre { background: #131720; color: #f1f5f9; padding: 1.25rem; border-radius: 8px; overflow-x: auto; margin-bottom: 1.5rem; }
        .post-content pre code { background: transparent; color: inherit; padding: 0; }

        .post-content blockquote { border-left: 3px solid #2563eb; padding-left: 1.25rem; color: #475569; margin: 1.5rem 0; font-style: italic; }
        .dark .post-content blockquote { color: #94a3b8; }
        
        .post-content img { border-radius: 8px; margin: 1.5rem 0; max-width: 100%; height: auto; }
        .post-content a { color: #2563eb; text-decoration: underline; font-weight: 500; }
        .dark .post-content a { color: #60a5fa; }
        /* --------------------------------------------- */
        
        #progress-bar { 
            position: fixed; 
            top: 0; 
            left: 0; 
            height: 3px; 
            background: #2563eb; 
            width: 0; 
            z-index: 100; 
            transition: width 0.1s; 
        }
        #author-modal { display: none; }
        #author-modal.active { display: flex; }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="antialiased min-h-screen flex flex-col selection:bg-blue-600 selection:text-white">
    <div id="progress-bar"></div>

    <?php if($is_logged_in): ?>
    <div class="bg-[#131720] text-slate-300 text-xs py-2 px-6 flex items-center justify-between sticky top-0 z-50 border-b border-slate-800 shadow-sm">
        <div class="flex items-center space-x-6">
            <a href="<?php echo base_url('admin/index.php'); ?>" class="font-bold text-blue-400">Blodato</a>
            <a href="<?php echo base_url('admin/post-edit.php?id=' . $post['id']); ?>" class="hover:text-white transition-colors">✎ Düzenle</a>
        </div>
        <div>Yazar: <strong><?php echo e($_SESSION['username']); ?></strong></div>
    </div>
    <?php endif; ?>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/80 dark:bg-[#0c0f14]/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 transition-colors h-16 flex items-center">
        <div class="max-w-3xl mx-auto px-6 w-full flex justify-between items-center">
            <a href="index.php" class="flex items-center space-x-2.5 group">
                <img src="<?php echo base_url(get_setting('site_logo', 'assets/img/logo.png')); ?>" alt="Logo" class="w-7 h-7 object-contain rounded-md">
                <span class="font-heading font-bold text-lg text-slate-900 dark:text-white group-hover:text-blue-600 transition-colors">
                    <?php echo e($site_title); ?>
                </span>
            </a>

            <div class="flex items-center space-x-3">
                <a href="index.php" class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 px-3 py-1.5 rounded-md bg-slate-100 dark:bg-slate-800/80 transition-colors">
                    ← Tüm Yazılar
                </a>
                <button id="theme-toggle" class="w-8 h-8 rounded-md bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 hover:text-blue-600 flex items-center justify-center text-xs transition-colors" aria-label="Tema">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline-block"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-3xl mx-auto px-6 pt-12 pb-20 w-full">
        <article>
            <header class="mb-8 pb-6 border-b border-slate-200 dark:border-slate-800/80">
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-3 font-medium">
                    <span class="text-blue-600 dark:text-blue-400 font-semibold"><?php echo e($post['category'] ?: 'Genel'); ?></span>
                    <span>•</span>
                    <span><?php echo get_read_time($post['content']); ?> dk okuma</span>
                    <span>•</span>
                    <span><?php echo date('d.m.Y', strtotime($post['created_at'])); ?></span>
                </div>

                <h1 class="font-heading text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-6 leading-snug">
                    <?php echo e($post['title']); ?>
                </h1>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 cursor-pointer group" onclick="toggleAuthorModal()">
                        <img src="<?php echo $post['author_pic'] ?: 'assets/img/default-avatar.png'; ?>" alt="Yazar" class="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700 shadow-sm">
                        <div>
                            <div class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-blue-600 transition-colors"><?php echo e($post['author_name'] ?: 'Artado'); ?></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($post['author_role'] ?? 'Yazar'); ?></div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="post-content max-w-none">
                <?php 
                $content = $post['content'];
                if (strip_tags($content) === $content) {
                    $content = markdown_to_html($content);
                }
                $content = embed_media($content);
                echo $content;
                ?>
            </div>
        </article>

        <!-- Comments -->
        <?php if(get_setting('global_comments', '1') == '1' && ($post['allow_comments'] ?? 1)): ?>
        <section class="mt-16 pt-8 border-t border-slate-200 dark:border-slate-800/80">
            <h3 class="font-heading text-xl font-bold text-slate-900 dark:text-white mb-6">Yorumlar</h3>

            <?php if(isset($comment_msg)): ?>
                <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900 p-4 rounded-lg mb-6 text-xs font-medium text-emerald-800 dark:text-emerald-300">
                    <?php echo $comment_msg; ?>
                </div>
            <?php endif; ?>
            
            <div class="space-y-4 mb-8">
                <?php
                $c_stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? AND status = 'approved' ORDER BY created_at DESC");
                $c_stmt->execute([$post['id']]);
                $comments = $c_stmt->fetchAll();
                
                if(empty($comments)): ?>
                    <p class="text-xs text-slate-400 italic">Henüz yorum yapılmamış. İlk yorumu sen yaz!</p>
                <?php else: ?>
                    <?php foreach($comments as $comment): ?>
                        <div class="p-4 rounded-xl bg-white dark:bg-[#131720] border border-slate-200 dark:border-slate-800 shadow-xs">
                            <div class="flex justify-between items-center mb-1.5 text-xs">
                                <span class="font-semibold text-slate-900 dark:text-white"><?php echo e($comment['author_name']); ?></span>
                                <span class="text-slate-400 text-[11px]"><?php echo date('d.m.Y', strtotime($comment['created_at'])); ?></span>
                            </div>
                            <p class="text-slate-700 dark:text-slate-300 text-xs leading-relaxed"><?php echo nl2br(e($comment['content'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Comment Form -->
            <div class="bg-white dark:bg-[#131720] p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <h4 class="text-xs font-bold uppercase tracking-wider mb-4 text-slate-400">Yorum Yazın</h4>
                <form method="POST" class="space-y-3.5">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <input type="text" name="author_name" placeholder="İsminiz" required class="w-full px-3.5 py-2.5 rounded-lg bg-slate-50 dark:bg-[#0c0f14] border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white outline-none text-xs focus:border-blue-600 transition-colors">
                        <input type="email" name="author_email" placeholder="E-posta (Yayınlanmayacak)" class="w-full px-3.5 py-2.5 rounded-lg bg-slate-50 dark:bg-[#0c0f14] border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white outline-none text-xs focus:border-blue-600 transition-colors">
                    </div>
                    <textarea name="comment_content" rows="4" placeholder="Mesajınız..." required class="w-full px-3.5 py-2.5 rounded-lg bg-slate-50 dark:bg-[#0c0f14] border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white outline-none text-xs focus:border-blue-600 transition-colors"></textarea>
                    <button type="submit" name="submit_comment" class="px-5 py-2.5 rounded-lg font-semibold text-xs text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm cursor-pointer">
                        Gönder
                    </button>
                </form>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <!-- Author Modal -->
    <div id="author-modal" class="fixed inset-0 z-[200] items-center justify-center p-6 bg-black/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-[#131720] w-full max-w-sm rounded-2xl p-6 relative border border-slate-200 dark:border-slate-800 shadow-2xl">
            <button onclick="toggleAuthorModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
            <div class="flex flex-col items-center text-center">
                <img src="<?php echo base_url($post['author_pic'] ?: 'assets/img/default-avatar.png'); ?>" alt="Yazar" class="w-16 h-16 rounded-full mb-3.5 object-cover border border-slate-200 dark:border-slate-700 shadow-sm">
                <h3 class="text-base font-bold mb-0.5 text-slate-900 dark:text-white"><?php echo e($post['author_name']); ?></h3>
                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium mb-3"><?php echo e($post['author_role'] ?? 'Yazar'); ?></p>
                <?php if(get_setting('show_author_bio', '1') == '1'): ?>
                <p class="text-xs text-slate-600 dark:text-slate-400 mb-4 leading-relaxed"><?php echo e($post['author_bio'] ?: 'Açık kaynak geliştiricisi.'); ?></p>
                <?php endif; ?>
                <div class="text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/60 px-3 py-1 rounded-full">Toplam <?php echo $author_post_count; ?> yazı</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800/80 py-8 text-center text-xs text-slate-400 bg-white/50 dark:bg-[#0c0f14]/50">
        &copy; <?php echo date('Y'); ?> <?php echo e($site_title); ?> — Tüm hakları saklıdır.
    </footer>

    <script>
        window.onscroll = function() {
            let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            let scrolled = (winScroll / height) * 100;
            const bar = document.getElementById("progress-bar");
            if (bar) bar.style.width = scrolled + "%";
        };

        function toggleAuthorModal() {
            const modal = document.getElementById('author-modal');
            if (modal) modal.classList.toggle('active');
        }

        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            });
        }
    </script>
</body>
</html>