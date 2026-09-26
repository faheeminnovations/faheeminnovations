<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

function getSetting($key, $default = '') {
    global $pdo;
    static $cache = [];
    if (!isset($cache[$key])) {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $cache[$key] = $stmt->fetchColumn() ?: $default;
    }
    return $cache[$key];
}

function getAllSettings() {
    global $pdo;
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function getPageMeta($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND status = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getSocialLinks() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM social_links WHERE status = 1 ORDER BY sort_order");
    return $stmt->fetchAll();
}

function getMenuItems() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM menu_items WHERE status = 1 ORDER BY sort_order");
    return $stmt->fetchAll();
}

function getServices($limit = null) {
    global $pdo;
    $sql = "SELECT * FROM services WHERE status = 1 ORDER BY sort_order";
    if ($limit) $sql .= " LIMIT " . (int)$limit;
    return $pdo->query($sql)->fetchAll();
}

function getAITools($limit = null) {
    global $pdo;
    $sql = "SELECT * FROM ai_tools WHERE status = 1 ORDER BY sort_order";
    if ($limit) $sql .= " LIMIT " . (int)$limit;
    return $pdo->query($sql)->fetchAll();
}

function getProjects($limit = null, $featured = false) {
    global $pdo;
    $sql = "SELECT * FROM projects WHERE status = 1";
    if ($featured) $sql .= " AND is_featured = 1";
    $sql .= " ORDER BY sort_order, created_at DESC";
    if ($limit) $sql .= " LIMIT " . (int)$limit;
    return $pdo->query($sql)->fetchAll();
}

function getProcessSteps() {
    global $pdo;
    return $pdo->query("SELECT * FROM processes WHERE status = 1 ORDER BY sort_order")->fetchAll();
}

function getTestimonials() {
    global $pdo;
    return $pdo->query("SELECT * FROM testimonials WHERE status = 1 ORDER BY sort_order")->fetchAll();
}

function getClients($limit = null) {
    global $pdo;
    $sql = "SELECT * FROM clients WHERE status = 1 ORDER BY sort_order";
    if ($limit) $sql .= " LIMIT " . (int)$limit;
    return $pdo->query($sql)->fetchAll();
}

function getFAQs() {
    global $pdo;
    return $pdo->query("SELECT * FROM faqs WHERE status = 1 ORDER BY sort_order")->fetchAll();
}

function getStatistics() {
    global $pdo;
    return $pdo->query("SELECT * FROM statistics ORDER BY sort_order")->fetchAll();
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        die('Invalid request.');
    }
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function isActive($menuUrl) {
    // Get full current URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $currentFull = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $currentPath = urldecode(parse_url($currentFull, PHP_URL_PATH));

    // Handle both absolute URLs (stored in DB) and relative paths
    $menuPath = urldecode(parse_url($menuUrl, PHP_URL_PATH));
    $menuPath = rtrim($menuPath, '/') ?: '/';
    $currentPath = rtrim($currentPath, '/') ?: '/';

    if ($currentPath === $menuPath) return 'active';
    // Partial match for sub-pages (but not homepage)
    if ($menuPath !== '/' && $menuPath !== urldecode(parse_url(SITE_URL, PHP_URL_PATH)) && strpos($currentPath, $menuPath) === 0) return 'active';
    return '';
}

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    return preg_replace('/-+/', '-', $text);
}

function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return $diff . 's ago';
    if ($diff < 3600) return floor($diff/60) . 'm ago';
    if ($diff < 86400) return floor($diff/3600) . 'h ago';
    return floor($diff/86400) . 'd ago';
}

function getProducts($limit = null, $featured = false) {
    global $pdo;
    $sql = "SELECT * FROM products WHERE status = 1";
    if ($featured) $sql .= " AND is_featured = 1";
    $sql .= " ORDER BY sort_order, created_at DESC";
    if ($limit) $sql .= " LIMIT " . (int)$limit;
    return $pdo->query($sql)->fetchAll();
}

function getProductBySlug($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND status = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}
