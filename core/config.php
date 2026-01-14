<?php
// Base URL detection logic
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

// Get the directory of the project root (one level up from /core)
$project_root = str_replace('\\', '/', dirname(__DIR__));
// Get the document root
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);

// Calculate the relative path from document root to project root
// We use a simple substr if the project is inside document root
$relative_path = '';
if (strpos($project_root, $doc_root) === 0) {
    $relative_path = substr($project_root, strlen($doc_root));
}

// Build the base URL
$baseUrl = $protocol . '://' . $host . '/' . trim($relative_path, '/') . '/';

// Clean up double slashes but keep the ones in protocol
$baseUrl = str_replace(':/', '://', str_replace('//', '/', $baseUrl));

define('BASE_URL', $baseUrl);
?>
