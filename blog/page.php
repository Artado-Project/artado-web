<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/translations.php';
require_once 'includes/markdown.php';

$slug = $_GET['slug'] ?? '';
$current_lang = $_SESSION['lang'] ?? 'tr';

if (!$slug) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
$stmt->execute([$slug]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$page) {
    header("HTTP/1.0 404 Not Found");
    die("Hata: Sayfa bulunamadı veya henüz yayınlanmadı.");
}

$site_title = get_setting('site_title', 'Artado Blog');
$accent_color = get_setting('accent_color', '#2563eb');
$is_logged_in = is_logged_in();
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page['title']); ?> — <?php echo htmlspecialchars($site_title); ?></title>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
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
        .prose p { margin-bottom: 1.5rem; line-height: 1.8; color: #475569; font-size: 1.05rem; }
        .dark .prose p { color: #94a3b8; }
        .prose h2 { font-size: 1.5rem; font-weight: 700; margin-top: 2.5rem; margin-bottom: 1rem; color: #0f172a; }
        .dark .prose h2 { color: #f1f5f9; }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body class="antialiased min-h-screen flex flex-col">

    <?php if($is_logged_in): ?>
    <div class="bg-[#131720] text-slate-300 text-xs py-2 px-6 flex items-center justify-between sticky top-0 z-50 border-b border-slate-800">
        <div class="flex space-x-6">
            <a href="admin/index.php" class="text-blue-400 font-bold">Blodato Panel</a>
            <a href="admin/page-edit.php?id=<?php echo $page['id']; ?>" class="hover:text-white">✎ Sayfayı Düzenle</a>
        </div>
        <div>Yönetici: <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></strong></div>
    </div>
    <?php endif; ?>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-[#0c0f14]/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80 transition-colors h-16 flex items-center">
        <div class="max-w-3xl mx-auto px-6 w-full flex justify-between items-center">
            <a href="index.php" class="flex items-center space-x-2.5">
                <img src="assets/img/logo.png" alt="Logo" class="w-7 h-7 object-contain rounded-md">
                <span class="font-heading font-bold text-lg text-slate-900 dark:text-white">
                    <?php echo htmlspecialchars($site_title); ?>
                </span>
            </a>

            <nav class="flex items-center space-x-4 text-sm font-medium">
                <a href="index.php" class="text-slate-600 dark:text-slate-300 hover:text-blue-600">Blog</a>
                <a href="https://artado.xyz" class="text-slate-600 dark:text-slate-300 hover:text-blue-600 hidden sm:inline-block">Ana Sayfa</a>
                
                <button id="theme-toggle" class="w-8 h-8 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-blue-600 flex items-center justify-center text-xs" aria-label="Tema">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline-block"></i>
                </button>
            </nav>
        </div>
    </header>

    <main class="flex-grow max-w-3xl mx-auto px-6 pt-12 pb-20 w-full">
        <article>
            <h1 class="font-heading text-2xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white mb-8">
                <?php echo htmlspecialchars($page['title']); ?>
            </h1>
            
            <div class="prose max-w-none">
                <?php 
                    $content = $page['content'];
                    if ($content === strip_tags($content)) {
                        echo markdown_to_html($content);
                    } else {
                        echo $content;
                    }
                ?>
            </div>
        </article>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 py-6 text-center text-xs text-slate-400">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_title); ?>
    </footer>

    <script>
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