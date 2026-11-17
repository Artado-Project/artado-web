<?php
/**
 * Dynamic Sitemap Generator for Artado
 * This generates a sitemap dynamically from the database
 */

require_once 'core/db.php';
require_once 'core/functions.php';
require_once 'core/config.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = BASE_URL;
$currentDate = date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Ana Sayfa
echo "  <url>\n";
echo "    <loc>" . htmlspecialchars($baseUrl) . "</loc>\n";
echo "    <lastmod>{$currentDate}</lastmod>\n";
echo "    <changefreq>weekly</changefreq>\n";
echo "    <priority>1.0</priority>\n";
echo "  </url>\n";

// Destekçiler Sayfası
echo "  <url>\n";
echo "    <loc>" . htmlspecialchars($baseUrl . "katki.php") . "</loc>\n";
echo "    <lastmod>{$currentDate}</lastmod>\n";
echo "    <changefreq>weekly</changefreq>\n";
echo "    <priority>0.8</priority>\n";
echo "  </url>\n";

// Profil Sayfaları
try {
    $stmt = $pdo->query("SELECT slug, updated_at FROM supporters ORDER BY order_priority DESC, created_at DESC");
    $supporters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($supporters as $supporter) {
        $lastmod = $supporter['updated_at'] ? date('Y-m-d', strtotime($supporter['updated_at'])) : $currentDate;
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($baseUrl . "profile.php?slug=" . $supporter['slug']) . "</loc>\n";
        echo "    <lastmod>{$lastmod}</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.6</priority>\n";
        echo "  </url>\n";
    }
} catch (Exception $e) {
    // Hata durumunda sessizce devam et
}

echo '</urlset>';
