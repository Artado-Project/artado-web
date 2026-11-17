<?php


session_start();


function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function get_setting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}


function update_setting($key, $value) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    return $stmt->execute([$key, $value, $value]);
}


function create_slug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $slug;
}


function get_read_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200);
    return $reading_time > 0 ? $reading_time : 1;
}

function log_visit($url) {
    global $pdo;
    try {
        $date = date('Y-m-d');
        $stmt = $pdo->prepare("INSERT INTO visits (page_url, visit_date, visit_count) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE visit_count = visit_count + 1");
        $stmt->execute([$url, $date]);
    } catch (Exception $e) {
        // Table probably doesn't exist yet, ignore to prevent 500 error
    }
}


function embed_media($content) {
    // YouTube
    $content = preg_replace(
        '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]{11})/',
        '<div class="media-embed my-8"><iframe width="100%" height="400" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen class="rounded-3xl shadow-lg"></iframe></div>',
        $content
    );
    // Spotify
    $content = preg_replace(
        '/https:\/\/open\.spotify\.com\/(track|album|playlist)\/([\w\d]+)/',
        '<div class="media-embed my-8"><iframe src="https://open.spotify.com/embed/$1/$2" width="100%" height="152" frameborder="0" allowtransparency="true" allow="encrypted-media" class="rounded-3xl shadow-sm"></iframe></div>',
        $content
    );
    return $content;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function has_role($role = 'admin') {
    if (!is_logged_in()) return false;
    $current_role = $_SESSION['role'] ?? 'author';
    
    $roles = ['author' => 1, 'editor' => 2, 'admin' => 3];
    return ($roles[$current_role] ?? 0) >= ($roles[$role] ?? 0);
}


function require_role($role = 'admin') {
    if (!has_role($role)) {
        header("Location: login.php?error=Bu sayfaya erişim yetkiniz yok.");
        exit;
    }
}

function is_admin() { return has_role('admin'); }


function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}


function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
function require_admin() { require_role('admin'); }


function base_url($path = '') {

    $scriptName = $_SERVER['SCRIPT_NAME'];
    $scriptDir = rtrim(dirname($scriptName), '/');
    $base = $scriptDir === '' ? '' : $scriptDir;
    return $base . '/' . ltrim($path, '/');
}
?>
