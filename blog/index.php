<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (get_setting('maintenance_mode') === '1' && !is_logged_in()) {
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bakım Modu — <?php echo get_setting('site_title'); ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        <style>body { font-family: 'Inter', sans-serif; background: #0c0f14; color: #f1f5f9; }</style>
    </head>
    <body class="flex items-center justify-center min-h-screen p-6">
        <div class="max-w-md text-center">
            <img src="assets/img/logo.png" class="w-12 h-12 mx-auto mb-6 rounded-lg">
            <h1 class="text-3xl font-bold tracking-tight mb-3">Şu an bakımdayız.</h1>
            <p class="text-slate-400 text-sm leading-relaxed mb-6">Blodato güncelleniyor. Çok yakında geri dönüyoruz.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

log_visit($_SERVER['REQUEST_URI']);

$stmt = $pdo->prepare("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC");
$stmt->execute();
$posts = $stmt->fetchAll();

$site_title = get_setting('site_title', 'Artado Blog');
$hero_text = get_setting('hero_text', 'Açık kaynak dünyasından gelişmeler, teknik makaleler ve Artado ekosistemine dair yazılar.');
$primary_btn_text = get_setting('primary_button_text', 'Artado.xyz');
$primary_btn_url = get_setting('primary_button_url', 'https://artado.xyz');
$secondary_btn_text = get_setting('secondary_button_text', 'Forum');
$secondary_btn_url = get_setting('secondary_button_url', 'https://forum.artado.xyz');
$accent_color = get_setting('accent_color', '#2563eb');
$border_radius = get_setting('border_radius', '12px');

$is_logged_in = is_logged_in();
?>
<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($site_title); ?></title>
    <link rel="icon" href="<?php echo base_url(get_setting('site_favicon', 'assets/img/logo.png')); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '<?php echo $accent_color; ?>',
                            600: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <?php echo get_setting('custom_head_code'); ?>
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
        #progress-bar { 
            position: fixed; 
            top: 0; 
            left: 0; 
            height: 2px; 
            background: #2563eb; 
            width: 0; 
            z-index: 100; 
            transition: width 0.1s; 
        }
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
<body class="antialiased min-h-screen flex flex-col">
    <div id="progress-bar"></div>

    <?php if($is_logged_in): ?>
    <div class="bg-[#131720] text-slate-300 text-xs py-2 px-6 flex items-center justify-between sticky top-0 z-50 border-b border-slate-800">
        <div class="flex items-center space-x-6">
            <a href="<?php echo base_url('admin/index.php'); ?>" class="font-bold text-blue-400">
                <i class="fas fa-gauge-high mr-1"></i> Blodato Panel
            </a>
            <a href="<?php echo base_url('admin/post-edit.php'); ?>" class="hover:text-white">
                <i class="fas fa-plus mr-1"></i> Yeni Yazı
            </a>
        </div>
        <div class="flex items-center space-x-4">
            <span>Yazar: <strong><?php echo e($_SESSION['username']); ?></strong></span>
            <a href="<?php echo base_url('admin/logout.php'); ?>" class="text-red-400 font-semibold">Çıkış</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-[#0c0f14]/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80 transition-colors h-16 flex items-center">
        <div class="max-w-4xl mx-auto px-6 w-full flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <a href="<?php echo base_url(); ?>" class="flex items-center space-x-2.5">
                    <img src="<?php echo base_url(get_setting('site_logo', 'assets/img/logo.png')); ?>" alt="Logo" class="w-7 h-7 object-contain rounded-md">
                    <span class="font-heading font-bold text-lg text-slate-900 dark:text-white">
                        <?php echo e($site_title); ?>
                    </span>
                </a>
            </div>

            <nav class="flex items-center space-x-5 text-sm font-medium">
                <a href="https://artado.xyz" class="text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hidden sm:inline-block">Ana Sayfa</a>
                <a href="https://forum.artado.xyz" target="_blank" rel="noopener" class="text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hidden sm:inline-block">Forum</a>
                
                <button id="theme-toggle" class="w-8 h-8 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-blue-600 flex items-center justify-center text-xs" aria-label="Tema">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline-block"></i>
                </button>
            </nav>
        </div>
    </header>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="max-w-3xl mx-auto px-6 pt-16 pb-12 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold font-heading text-slate-900 dark:text-white mb-4 tracking-tight">
                <?php echo e($site_title); ?>
            </h1>

            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-400 max-w-xl mx-auto mb-8 leading-relaxed">
                <?php echo nl2br(e($hero_text)); ?>
            </p>

            <div class="flex justify-center items-center gap-3">
                <?php if($primary_btn_text): ?>
                <a href="<?php echo e($primary_btn_url); ?>" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-xs transition-colors">
                    <?php echo e($primary_btn_text); ?>
                </a>
                <?php endif; ?>

                <?php if($secondary_btn_text): ?>
                <a href="<?php echo e($secondary_btn_url); ?>" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-700 rounded-lg font-semibold text-xs transition-colors">
                    <?php echo e($secondary_btn_text); ?>
                </a>
                <?php endif; ?>
            </div>
        </section>

        <!-- Post List Section -->
        <section class="max-w-3xl mx-auto px-6 pb-24">
            <div class="border-b border-slate-200 dark:border-slate-800 mb-6 pb-3 flex justify-between items-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Yazılar</span>
                <span class="text-xs text-slate-400"><?php echo count($posts); ?> içerik</span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800/60">
                <?php if(empty($posts)): ?>
                    <p class="text-center text-slate-400 text-sm py-12">Henüz yazı bulunmuyor.</p>
                <?php else: ?>
                    <?php foreach($posts as $post): ?>
                        <article class="py-6 group">
                            <div class="flex items-center gap-2.5 text-xs text-slate-400 mb-2">
                                <span class="text-blue-600 dark:text-blue-400 font-semibold"><?php echo e($post['category'] ?? 'Genel'); ?></span>
                                <span>•</span>
                                <span><?php echo get_read_time($post['content']); ?> dk okuma</span>
                                <span>•</span>
                                <span>
                                    <?php 
                                    $months = ['Jan'=>'Oca','Feb'=>'Şub','Mar'=>'Mar','Apr'=>'Nis','May'=>'May','Jun'=>'Haz','Jul'=>'Tem','Aug'=>'Ağu','Sep'=>'Eyl','Oct'=>'Eki','Nov'=>'Kas','Dec'=>'Ara'];
                                    echo strtr(date('d M Y', strtotime($post['created_at'])), $months);
                                    ?>
                                </span>
                            </div>

                            <h2 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <a href="post.php?slug=<?php echo e($post['slug']); ?>">
                                    <?php echo e($post['title']); ?>
                                </a>
                            </h2>

                            <?php if(!empty($post['summary'])): ?>
                            <p class="text-slate-600 dark:text-slate-400 text-sm mt-2 line-clamp-2">
                                <?php echo e($post['summary']); ?>
                            </p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Clean Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 py-8 px-6 text-center text-xs text-slate-400">
        &copy; <?php echo date('Y'); ?> <?php echo e($site_title); ?> — Açık Kaynak Blog
    </footer>

    <script>
        window.onscroll = function() {
            let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            let scrolled = (winScroll / height) * 100;
            const bar = document.getElementById("progress-bar");
            if (bar) bar.style.width = scrolled + "%";
        };
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            });
        }
    </script>
    <?php echo get_setting('custom_footer_code'); ?>
</body>
</html>
