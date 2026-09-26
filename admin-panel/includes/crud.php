<?php
// Admin CRUD helpers

function handleStatusToggle($table, $id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE $table SET status = 1 - status WHERE id = ?");
    $stmt->execute([$id]);
}

function handleDelete($table, $id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->execute([$id]);
}

function handleSortOrder($table, $id, $order) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE $table SET sort_order = ? WHERE id = ?");
    $stmt->execute([(int)$order, (int)$id]);
}

function uploadFile($file, $subdir = 'media') {
    $allowed = ['jpg','jpeg','png','webp','svg','gif'];
    $maxSize = 5 * 1024 * 1024;
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > $maxSize) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return null;
    $dir = UPLOAD_PATH . $subdir . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $filename = uniqid() . '_' . time() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
        return 'uploads/' . $subdir . '/' . $filename;
    }
    return null;
}

function getPaginatedResults($table, $page = 1, $perPage = 15, $where = '1', $params = [], $orderBy = 'id DESC') {
    global $pdo;
    $offset = ($page - 1) * $perPage;
    $total = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $where");
    $total->execute($params);
    $totalCount = $total->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $where ORDER BY $orderBy LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    return [
        'data'       => $stmt->fetchAll(),
        'total'      => $totalCount,
        'pages'      => ceil($totalCount / $perPage),
        'current'    => $page,
        'per_page'   => $perPage,
    ];
}

function flashMessage($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function showFlash() {
    $f = getFlash();
    if ($f) {
        echo '<div class="alert alert-' . $f['type'] . '"><i class="fas fa-' . ($f['type']==='success'?'check-circle':'exclamation-circle') . '"></i> ' . htmlspecialchars($f['msg']) . '</div>';
    }
}

function paginationLinks($result, $baseUrl) {
    if ($result['pages'] <= 1) return;
    echo '<div class="pagination">';
    for ($i = 1; $i <= $result['pages']; $i++) {
        $active = $i == $result['current'] ? 'active' : '';
        echo "<a href=\"{$baseUrl}&page={$i}\" class=\"page-btn {$active}\">{$i}</a>";
    }
    echo '</div>';
}
