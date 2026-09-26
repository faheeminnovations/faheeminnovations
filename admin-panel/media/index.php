<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Media Library';

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    csrf_verify();
    $uploaded = 0;
    $files = $_FILES['files'];
    $count = count($files['name']);
    for ($i = 0; $i < $count; $i++) {
        $file = [
            'name'     => $files['name'][$i],
            'type'     => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error'    => $files['error'][$i],
            'size'     => $files['size'][$i],
        ];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $path = uploadFile($file, 'media');
            if ($path) {
                $pdo->prepare("INSERT INTO media (filename,original_name,file_path,file_type,file_size,uploaded_by) VALUES (?,?,?,?,?,?)")
                    ->execute([basename($path), $file['name'], $path, $file['type'], $file['size'], $_SESSION['admin_id']]);
                $uploaded++;
            }
        }
    }
    flashMessage('success', "$uploaded file(s) uploaded successfully.");
    redirect(ADMIN_URL . '/media/index.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $mid = (int)$_GET['delete'];
    $f = $pdo->prepare("SELECT file_path FROM media WHERE id=?");
    $f->execute([$mid]);
    $f = $f->fetchColumn();
    if ($f && file_exists(dirname(__DIR__, 2) . '/' . $f)) {
        unlink(dirname(__DIR__, 2) . '/' . $f);
    }
    $pdo->prepare("DELETE FROM media WHERE id=?")->execute([$mid]);
    flashMessage('success', 'File deleted.');
    redirect(ADMIN_URL . '/media/index.php');
}

// Handle alt text / title update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_media'])) {
    csrf_verify();
    $pdo->prepare("UPDATE media SET alt_text=?,title=? WHERE id=?")
        ->execute([trim($_POST['alt_text']), trim($_POST['title']), (int)$_POST['media_id']]);
    flashMessage('success', 'Media updated.');
    redirect(ADMIN_URL . '/media/index.php');
}

$search = trim($_GET['search'] ?? '');
$where  = '1'; $params = [];
if ($search) { $where = 'original_name LIKE ? OR alt_text LIKE ?'; $params = ["%$search%", "%$search%"]; }

$page   = max(1, (int)($_GET['page'] ?? 1));
$result = getPaginatedResults('media', $page, 24, $where, $params, 'created_at DESC');

require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<!-- Upload Area -->
<div class="admin-table-wrap" style="margin-bottom:24px">
  <div class="table-header"><h3>Upload Files</h3></div>
  <div style="padding:24px">
    <form method="POST" enctype="multipart/form-data" id="uploadForm">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div id="dropZone" style="border:2px dashed var(--border);border-radius:12px;padding:40px;text-align:center;cursor:pointer;transition:0.3s;background:var(--bg)" onclick="document.getElementById('fileInput').click()" ondragover="event.preventDefault();this.style.borderColor='var(--primary)'" ondragleave="this.style.borderColor='var(--border)'" ondrop="handleDrop(event)">
        <i class="fas fa-cloud-upload-alt" style="font-size:2.5rem;color:var(--text-light);margin-bottom:12px;display:block"></i>
        <p style="font-weight:600;margin-bottom:4px">Click to upload or drag and drop files here</p>
        <p style="color:var(--text-light);font-size:0.85rem">Supported: JPG, JPEG, PNG, WEBP, SVG &bull; Max 5MB per file</p>
        <input type="file" id="fileInput" name="files[]" multiple accept="image/*" style="display:none" onchange="previewFiles(this)">
      </div>
      <div id="previewArea" style="display:flex;flex-wrap:wrap;gap:12px;margin-top:16px"></div>
      <div id="uploadBtn" style="display:none;margin-top:16px">
        <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Files</button>
        <button type="button" class="btn btn-outline" onclick="clearUpload()">Clear</button>
      </div>
    </form>
  </div>
</div>

<!-- Search + Grid -->
<div style="display:flex;gap:12px;margin-bottom:20px;align-items:center">
  <form method="GET" style="display:flex;gap:8px">
    <div class="search-box"><i class="fas fa-search"></i><input type="text" name="search" placeholder="Search files..." value="<?= e($search) ?>"></div>
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
    <?php if ($search): ?><a href="index.php" class="btn btn-outline btn-sm">Reset</a><?php endif; ?>
  </form>
  <span style="margin-left:auto;color:var(--text-light);font-size:0.85rem"><?= $result['total'] ?> file<?= $result['total'] != 1 ? 's' : '' ?></span>
</div>

<!-- Media Grid -->
<?php if ($result['data']): ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;margin-bottom:20px">
  <?php foreach ($result['data'] as $file): ?>
  <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;overflow:hidden;transition:0.2s" class="media-item">
    <div style="height:120px;background:var(--bg);display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer" onclick="selectMedia('<?= SITE_URL ?>/<?= e($file['file_path']) ?>')">
      <?php $ext = strtolower(pathinfo($file['file_path'], PATHINFO_EXTENSION)); ?>
      <?php if (in_array($ext, ['jpg','jpeg','png','webp','gif'])): ?>
      <img src="<?= SITE_URL ?>/<?= e($file['file_path']) ?>" style="width:100%;height:100%;object-fit:cover" loading="lazy" alt="<?= e($file['alt_text']) ?>">
      <?php else: ?>
      <i class="fas fa-file-image" style="font-size:2.5rem;color:var(--text-light)"></i>
      <?php endif; ?>
    </div>
    <div style="padding:10px">
      <p style="font-size:0.78rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px" title="<?= e($file['original_name']) ?>"><?= e($file['original_name']) ?></p>
      <p style="font-size:0.72rem;color:var(--text-light);margin-bottom:8px"><?= number_format($file['file_size'] / 1024, 1) ?> KB</p>
      <div style="display:flex;gap:6px">
        <button class="btn btn-outline btn-sm btn-icon" onclick="copyUrl('<?= SITE_URL ?>/<?= e($file['file_path']) ?>')" title="Copy URL"><i class="fas fa-copy"></i></button>
        <button class="btn btn-outline btn-sm btn-icon" onclick="editMedia(<?= $file['id'] ?>,'<?= addslashes(e($file['alt_text'])) ?>','<?= addslashes(e($file['title'])) ?>')" title="Edit"><i class="fas fa-edit"></i></button>
        <a href="?delete=<?= $file['id'] ?>" class="btn btn-danger btn-sm btn-icon" data-confirm="Delete this file permanently?" title="Delete"><i class="fas fa-trash"></i></a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div style="text-align:center;padding:60px;color:var(--text-light)">
  <i class="fas fa-images" style="font-size:3rem;display:block;margin-bottom:12px;opacity:0.25"></i>
  <p>No media files yet. Upload some above.</p>
</div>
<?php endif; ?>

<?php paginationLinks($result, '?search=' . urlencode($search)); ?>

<!-- Edit Modal -->
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:28px;width:100%;max-width:420px">
    <h3 style="margin-bottom:20px">Edit Media Info</h3>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="update_media" value="1">
      <input type="hidden" name="media_id" id="editMediaId">
      <div class="form-group">
        <label>Alt Text</label>
        <input type="text" name="alt_text" id="editAltText" placeholder="Describe the image for accessibility">
      </div>
      <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" id="editTitle" placeholder="Optional title">
      </div>
      <div style="display:flex;gap:10px;margin-top:4px">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Save</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="closeEditModal()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Copy notification -->
<div id="copyNotif" style="display:none;position:fixed;bottom:24px;right:24px;background:#1a1a2e;color:#fff;padding:12px 20px;border-radius:8px;font-size:0.85rem;z-index:2000">
  URL copied to clipboard!
</div>

<script>
function previewFiles(input) {
  const area = document.getElementById('previewArea');
  area.innerHTML = '';
  [...input.files].forEach(file => {
    const reader = new FileReader();
    reader.onload = e => {
      area.innerHTML += `<div style="text-align:center"><img src="${e.target.result}" style="width:80px;height:60px;object-fit:cover;border-radius:6px;border:1px solid var(--border)"><p style="font-size:0.72rem;margin-top:4px;max-width:80px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${file.name}</p></div>`;
    };
    reader.readAsDataURL(file);
  });
  document.getElementById('uploadBtn').style.display = input.files.length ? 'block' : 'none';
}
function clearUpload() {
  document.getElementById('fileInput').value = '';
  document.getElementById('previewArea').innerHTML = '';
  document.getElementById('uploadBtn').style.display = 'none';
}
function handleDrop(e) {
  e.preventDefault();
  document.getElementById('dropZone').style.borderColor = 'var(--border)';
  const dt = new DataTransfer();
  [...e.dataTransfer.files].forEach(f => dt.items.add(f));
  const inp = document.getElementById('fileInput');
  inp.files = dt.files;
  previewFiles(inp);
}
function copyUrl(url) {
  navigator.clipboard.writeText(url).then(() => {
    const n = document.getElementById('copyNotif');
    n.style.display = 'block';
    setTimeout(() => n.style.display = 'none', 2500);
  });
}
function selectMedia(url) { copyUrl(url); }
function editMedia(id, alt, title) {
  document.getElementById('editMediaId').value = id;
  document.getElementById('editAltText').value = alt;
  document.getElementById('editTitle').value = title;
  document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
}
</script>
<?php require_once '../includes/layout-bottom.php'; ?>
