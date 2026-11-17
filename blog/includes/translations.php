<?php


function __($key) {
    $lang = $_SESSION['lang'] ?? 'tr';
    
    $translations = [
        'tr' => [
            // General
            'home' => 'Ana Sayfa',
            'forum' => 'Forum',
            'docs' => 'Docs',
            'about' => 'Hakkımızda',
            'projects' => 'Projelerimiz',
            'login' => 'Oturum aç',
            'logout' => 'Çıkış Yap',
            'register' => 'Kayıt Ol',
            'view_posts' => 'Yazıları Gör',
            'recent_posts' => 'Son Yazılar',
            'no_posts' => 'Henüz yazı bulunmuyor.',
            'back' => 'Geri',
            'hello' => 'Merhaba',
            'new' => 'Yeni',
            'settings' => 'Ayarlar',
            'save' => 'Kaydet',
            'delete' => 'Sil',
            'edit' => 'Düzenle',
            'status' => 'Durum',
            'published' => 'Yayında',
            'draft' => 'Taslak',
            
            // Admin Sidebar
            'dashboard' => 'Panel',
            'posts' => 'Yazılar',
            'pages' => 'Sayfalar',
            'comments' => 'Yorumlar',
            'users' => 'Kullanıcılar',
            'site_settings' => 'Site Ayarları',
            
            // Admin Settings
            'identity' => 'Site Kimliği',
            'site_title' => 'Site Başlığı',
            'site_slogan' => 'Site Sloganı',
            'hero_content' => 'Hero İçeriği',
            'buttons' => 'Butonlar',
            'seo' => 'SEO ve Meta',
            'social' => 'Sosyal Medya',
            'custom_code' => 'Özel Kod (Header/Footer)',
            'menu_links' => 'Menü Linkleri',
        ],
        'en' => [
            // General
            'home' => 'Ana',
            'forum' => 'Forum',
            'docs' => 'Docs',
            'about' => 'About',
            'projects' => 'Our Projects',
            'login' => 'Sign In',
            'logout' => 'Sign Out',
            'register' => 'Register',
            'view_posts' => 'View Posts',
            'recent_posts' => 'Recent Posts',
            'no_posts' => 'No posts found yet.',
            'back' => 'Back',
            'hello' => 'Hello',
            'new' => 'New',
            'settings' => 'Settings',
            'save' => 'Save',
            'delete' => 'Delete',
            'edit' => 'Edit',
            'status' => 'Status',
            'published' => 'Published',
            'draft' => 'Draft',

            // Admin Sidebar
            'dashboard' => 'Dashboard',
            'posts' => 'Posts',
            'pages' => 'Pages',
            'comments' => 'Comments',
            'users' => 'Users',
            'site_settings' => 'Site Settings',

            // Admin Settings
            'identity' => 'Site Identity',
            'site_title' => 'Site Title',
            'site_slogan' => 'Site Slogan',
            'hero_content' => 'Hero Content',
            'buttons' => 'Buttons',
            'seo' => 'SEO and Meta',
            'social' => 'Social Media',
            'custom_code' => 'Custom Code (Header/Footer)',
            'menu_links' => 'Menu Links',
        ]
    ];

    return $translations[$lang][$key] ?? $key;
}
