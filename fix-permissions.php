<?php
// ONE-TIME — auto-deletes after run
$uploadsPath = __DIR__ . '/uploads/';
$msgs = [];

$dirs = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($uploadsPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

// Fix root uploads folder
if (chmod($uploadsPath, 0755)) {
    $msgs[] = ['ok', 'uploads/ → 0755'];
} else {
    $msgs[] = ['err', 'uploads/ — chmod failed'];
}

// Fix all subdirectories
foreach ($dirs as $item) {
    if ($item->isDir()) {
        $path = $item->getRealPath();
        if (chmod($path, 0755)) {
            $msgs[] = ['ok', str_replace(__DIR__, '', $path) . ' → 0755'];
        } else {
            $msgs[] = ['err', str_replace(__DIR__, '', $path) . ' — chmod failed'];
        }
    }
}

// Verify clients writable
$clientsWritable = is_writable($uploadsPath . 'clients/');

@unlink(__FILE__);
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Permissions Fixed</title>
<style>body{font-family:Inter,sans-serif;max-width:620px;margin:60px auto;padding:0 20px}h2{color:#0B3C33}ul{line-height:2;font-size:.88rem;padding:0;list-style:none}.ok::before{content:'✅ '}.err::before{content:'❌ '}.box{border-radius:8px;padding:16px 20px;margin-bottom:20px}.green{background:#ecfdf5;border:1px solid #6ee7b7}.red{background:#fef2f2;border:1px solid #fca5a5}</style>
</head><body>
<h2>Permissions Fix</h2>
<div class="box <?= $clientsWritable ? 'green' : 'red' ?>">
  <strong>uploads/clients/ writable: <?= $clientsWritable ? '✅ YES — upload will work now!' : '❌ Still not writable. Run fix manually (see below).' ?></strong>
</div>
<ul>
  <?php foreach ($msgs as [$type, $msg]): ?>
  <li class="<?= $type ?>"><?= htmlspecialchars($msg) ?></li>
  <?php endforeach; ?>
</ul>
<?php if (!$clientsWritable): ?>
<div class="box red">
  <p><strong>Manual fix:</strong> Open Terminal and run:</p>
  <code>chmod -R 755 /Applications/XAMPP/xamppfiles/htdocs/faheeminnovations/uploads/</code>
</div>
<?php endif; ?>
<p style="font-size:.8rem;color:#888">🗑️ This file deleted itself.</p>
</body></html>
