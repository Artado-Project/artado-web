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

// Get setting from DB (Auto-creates table if needed)
function getSetting($key, $default = '') {
    global $pdo;
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `site_settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `setting_key` VARCHAR(100) UNIQUE NOT NULL,
            `setting_value` TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ? LIMIT 1");
        $stmt->execute([$key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false && $row['setting_value'] !== null) {
            return $row['setting_value'];
        }
    } catch (PDOException $e) {
        // Fallback to default
    }
    return $default;
}

// Set setting in DB
function setSetting($key, $value) {
    global $pdo;
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `site_settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `setting_key` VARCHAR(100) UNIQUE NOT NULL,
            `setting_value` TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        return $stmt->execute([$key, $value]);
    } catch (PDOException $e) {
        return false;
    }
}

// Get section display order for supporters page
function getSupporterSectionOrder() {
    $defaultOrder = ['supporter', 'developer', 'plugin', 'theme', 'sponsor'];
    $saved = getSetting('supporter_section_order');
    if (!empty($saved)) {
        $decoded = json_decode($saved, true);
        if (is_array($decoded) && count($decoded) > 0) {
            return $decoded;
        }
    }
    return $defaultOrder;
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
