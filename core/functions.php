<?php
/**
 * Artado - Core Helper Functions
 */

session_start();
require_once 'config.php';

// Security: Sanitize input
function clean($data) {
    if (is_array($data)) {
        return array_map('clean', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Redirect helper
function redirect($path) {
    header("Location: $path");
    exit;
}

// Create a slug from string
function createSlug($string) {
    $string = mb_strtolower($string, 'UTF-8');
    $string = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $string);
    $string = preg_replace('/[^a-z0-9-]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

// Get supporters by type
function getSupporters($type = null) {
    global $pdo;
    if ($type) {
        $stmt = $pdo->prepare("SELECT * FROM supporters WHERE type = ? ORDER BY order_priority DESC, created_at DESC");
        $stmt->execute([$type]);
    } else {
        $stmt = $pdo->query("SELECT * FROM supporters ORDER BY order_priority DESC, created_at DESC");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
