<?php
function admin_header($title = 'Dashboard') {
    $accent = get_setting('accent_color', '#2563eb');
    ?>
    <!DOCTYPE html>
    <html lang="tr" class="h-full bg-slate-50">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title; ?> — Artado CMS</title>
        <link rel="icon" href="../<?php echo get_setting('site_favicon', 'assets/img/logo.png'); ?>">
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Inter', sans-serif; }
            
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

            .card { background-color: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
            
            input, select, textarea { 
                background-color: #f8fafc !important; 
                border: 1px solid #e2e8f0 !important; 
                color: #0f172a !important; 
                padding: 10px 16px !important; 
                border-radius: 12px !important; 
                font-size: 14px !important;
                transition: all 0.2s ease;
            }
            input:focus, select:focus, textarea:focus { 
                background-color: #ffffff !important;
                border-color: <?php echo $accent; ?> !important; 
                box-shadow: 0 0 0 4px <?php echo $accent; ?>1a !important;
                outline: none; 
            }
            label { display: block; margin-bottom: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; }
        </style>
    </head>
    <body class="min-h-full flex flex-col lg:flex-row antialiased text-slate-900 overflow-x-hidden">
    <?php
}

function admin_sidebar($active_page = 'dashboard') {
    $accent = get_setting('accent_color', '#2563eb');
    ?>
    <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm hidden lg:hidden transition-opacity duration-300 opacity-0"></div>

    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-950 text-slate-400 transform -translate-x-full lg:translate-x-0 lg:static lg:h-screen lg:flex lg:flex-col shrink-0 border-r border-slate-900 shadow-2xl lg:shadow-none transition-transform duration-300 ease-in-out">
        
        <!-- Sidebar Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-900 shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 flex items-center justify-center">
                    <img src="https://artado.xyz/blog/assets/img/logo.png" alt="Logo" class="w-full h-full object-contain">
                </div>
                <span class="font-bold text-base tracking-tight text-white">Artado <span class="text-xs font-medium text-slate-500">CMS</span></span>
            </div>
            <button id="close-sidebar-btn" class="lg:hidden text-slate-500 hover:text-white focus:outline-none p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-6 overflow-y-auto">
            <div>
                <div class="px-3 text-[10px] font-black text-slate-600 uppercase tracking-widest mb-2">Main</div>
                <div class="space-y-1">
                    <a href="index.php" class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 <?php echo $active_page === 'dashboard' ? 'text-white shadow-sm' : 'hover:bg-slate-900 hover:text-slate-200'; ?>" style="<?php echo $active_page === 'dashboard' ? 'background-color: '.$accent.';' : ''; ?>">
                        Dashboard
                    </a>
                    <?php if(has_role('admin')): ?>
                    <a href="settings.php" class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 <?php echo $active_page === 'settings' ? 'text-white shadow-sm' : 'hover:bg-slate-900 hover:text-slate-200'; ?>" style="<?php echo $active_page === 'settings' ? 'background-color: '.$accent.';' : ''; ?>">
                        Site Ayarları
                    </a>
                    <a href="approvals.php" class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 <?php echo $active_page === 'approvals' ? 'text-white shadow-sm' : 'hover:bg-slate-900 hover:text-slate-200'; ?>" style="<?php echo $active_page === 'approvals' ? 'background-color: '.$accent.';' : ''; ?>">
                        Onay Bekleyenler
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="px-3 text-[10px] font-black text-slate-600 uppercase tracking-widest mb-2">İçerik</div>
                <div class="space-y-1">
                    <a href="posts.php" class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 <?php echo $active_page === 'posts' ? 'text-white shadow-sm' : 'hover:bg-slate-900 hover:text-slate-200'; ?>" style="<?php echo $active_page === 'posts' ? 'background-color: '.$accent.';' : ''; ?>">
                        Yazılar
                    </a>
                    <?php if(has_role('editor')): ?>
                    <a href="pages.php" class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 <?php echo $active_page === 'pages' ? 'text-white shadow-sm' : 'hover:bg-slate-900 hover:text-slate-200'; ?>" style="<?php echo $active_page === 'pages' ? 'background-color: '.$accent.';' : ''; ?>">
                        Sayfalar
                    </a>
                    <a href="comments.php" class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 <?php echo $active_page === 'comments' ? 'text-white shadow-sm' : 'hover:bg-slate-900 hover:text-slate-200'; ?>" style="<?php echo $active_page === 'comments' ? 'background-color: '.$accent.';' : ''; ?>">
                        Yorumlar
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="px-3 text-[10px] font-black text-slate-600 uppercase tracking-widest mb-2">Sistem</div>
                <div class="space-y-1">
                    <?php if(has_role('admin')): ?>
                    <a href="users.php" class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 <?php echo $active_page === 'users' ? 'text-white shadow-sm' : 'hover:bg-slate-900 hover:text-slate-200'; ?>" style="<?php echo $active_page === 'users' ? 'background-color: '.$accent.';' : ''; ?>">
                        Kullanıcılar
                    </a>
                    <?php endif; ?>
                    <a href="profile.php" class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 <?php echo $active_page === 'profile' ? 'text-white shadow-sm' : 'hover:bg-slate-900 hover:text-slate-200'; ?>" style="<?php echo $active_page === 'profile' ? 'background-color: '.$accent.';' : ''; ?>">
                        Profilim
                    </a>
                    <a href="post-edit.php" class="flex items-center justify-center gap-2 px-3 py-2.5 text-xs font-bold rounded-xl text-slate-300 hover:bg-slate-900 hover:text-white border border-dashed border-slate-800 hover:border-slate-700 transition-all duration-200 mt-3">
                        <span>➕</span> Yeni Yazı Ekle
                    </a>
                </div>
            </div>
        </nav>
        
        <div class="p-4 border-t border-slate-900 bg-slate-950/80 shrink-0 space-y-2">
            <a href="logout.php" class="text-xs font-bold text-red-400 hover:text-red-300 tracking-wider block bg-red-500/10 hover:bg-red-500/20 text-center py-2.5 rounded-xl transition-colors">ÇIKIŞ YAP</a>
            <a href="../" target="_blank" class="text-[10px] font-bold text-slate-500 hover:text-slate-300 uppercase tracking-wide block text-center pt-1">Siteyi Görüntüle →</a>
        </div>
    </aside>
    
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <header class="h-16 bg-white border-b border-slate-200 px-4 md:px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm shadow-slate-100">
            <div class="flex items-center space-x-4">
                <button id="open-sidebar-btn" class="lg:hidden text-slate-600 hover:text-slate-900 focus:outline-none p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="flex items-center space-x-2 text-xs font-semibold text-slate-400">
                    <span class="hover:text-slate-600 transition-colors">Yönetim Paneli</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800 font-bold"><?php echo ucfirst($active_page); ?></span>
                </div>
            </div>

            <div class="flex items-center space-x-3.5">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-bold text-slate-800 leading-none">Merhaba, <?php echo e($_SESSION['username'] ?? 'Yönetici'); ?></div>
                    <div class="text-[10px] text-slate-400 font-medium mt-0.5 uppercase tracking-wider"><?php echo e($_SESSION['role'] ?? 'Yetkili'); ?></div>
                </div>
                <?php
                    $u_id = $_SESSION['user_id'] ?? 0;
                    $u_pic = '';
                    if ($u_id && isset($GLOBALS['pdo'])) {
                        try {
                            $u_stmt = $GLOBALS['pdo']->prepare("SELECT profile_pic FROM users WHERE id = ?");
                            $u_stmt->execute([$u_id]);
                            $u_pic = $u_stmt->fetchColumn();
                        } catch (Exception $e) {}
                    }
                ?>
                <div class="w-10 h-10 rounded-full border border-slate-200 overflow-hidden ring-2 ring-slate-100 shrink-0 shadow-sm">
                    <img src="../<?php echo $u_pic ?: 'assets/img/default-avatar.png'; ?>" class="w-full h-full object-cover">
                </div>
            </div>
        </header>
    <?php
}

function admin_footer() {
    ?>
    </div> 
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const openBtn = document.getElementById('open-sidebar-btn');
            const closeBtn = document.getElementById('close-sidebar-btn');
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (!openBtn || !sidebar || !overlay) return;

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.add('opacity-100');
                }, 10);
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
            }

            openBtn.addEventListener('click', openSidebar);
            if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);
        });
    </script>
    </body>
    </html>
    <?php
}