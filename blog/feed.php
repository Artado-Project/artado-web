<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

header('Content-Type: application/rss+xml; charset=utf-8');

$site_title = get_setting('site_title', 'Blodato');
$site_slogan = get_setting('site_slogan', '');
$site_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

$stmt = $pdo->query("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 20");
$posts = $stmt->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<rss version="2.0">
<channel>
  <title><?php echo e($site_title); ?></title>
  <link><?php echo $site_url; ?></link>
  <description><?php echo e($site_slogan); ?></description>
  <language>tr</language>
  <?php foreach($posts as $post): ?>
  <item>
    <title><?php echo e($post['title']); ?></title>
    <link><?php echo $site_url; ?>/post.php?slug=<?php echo e($post['slug']); ?></link>
    <description><?php echo e(mb_substr(strip_tags($post['content']), 0, 200)); ?>...</description>
    <pubDate><?php echo date(DATE_RSS, strtotime($post['created_at'])); ?></pubDate>
    <guid><?php echo $site_url; ?>/post.php?slug=<?php echo e($post['slug']); ?></guid>
  </item>
  <?php endforeach; ?>
</channel>
</rss>
