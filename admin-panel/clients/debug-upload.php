<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['logo']['name'])) {
    $f = $_FILES['logo'];
    $result = [
        'name'      => $f['name'],
        'size'      => $f['size'] . ' bytes (' . round($f['size']/1024, 1) . ' KB)',
        'type'      => $f['type'],
        'error'     => $f['error'],
        'error_msg' => match($f['error']) {
            0 => '✅ No error',
            1 => '❌ File too large (php.ini upload_max_filesize)',
            2 => '❌ File too large (HTML form max)',
            3 => '❌ Partial upload',
            4 => '❌ No file uploaded',
            6 => '❌ Missing temp folder',
            7 => '❌ Failed to write to disk',
            default => '❌ Unknown error ' . $f['error'],
        },
        'upload_path' => UPLOAD_PATH . 'clients/',
        'path_exists' => is_dir(UPLOAD_PATH . 'clients/') ? '✅ Yes' : '❌ No',
        'path_writable' => is_writable(UPLOAD_PATH . 'clients/') ? '✅ Yes' : '❌ Not writable',
        'ext'         => strtolower(pathinfo($f['name'], PATHINFO_EXTENSION)),
        'allowed'     => in_array(strtolower(pathinfo($f['name'], PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp','svg','gif']) ? '✅ Yes' : '❌ Not allowed',
        'max_size_ok' => $f['size'] <= 5*1024*1024 ? '✅ Under 5MB' : '❌ Over 5MB',
    ];

    // Try actual upload
    $path = uploadFile($f, 'clients');
    $result['upload_result'] = $path ? '✅ Success: ' . $path : '❌ uploadFile() returned null';
}

$adminTitle = 'Debug Upload';
require_once '../includes/layout-top.php';
?>
<div style="max-width:640px">
  <h3 style="margin-bottom:20px">Upload Debugger</h3>

  <?php if ($result): ?>
  <table style="width:100%;border-collapse:collapse;margin-bottom:28px">
    <?php foreach ($result as $k => $v): ?>
    <tr>
      <td style="padding:9px 14px;border:1px solid var(--border);font-weight:600;width:40%;background:#f9f9f9"><?= htmlspecialchars($k) ?></td>
      <td style="padding:9px 14px;border:1px solid var(--border)"><?= htmlspecialchars($v) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label>Test Logo Upload</label>
      <input type="file" name="logo" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Test Upload</button>
    <a href="index.php" class="btn btn-outline" style="margin-left:8px">Back to Clients</a>
  </form>
</div>

<?php require_once '../includes/layout-bottom.php'; ?>
