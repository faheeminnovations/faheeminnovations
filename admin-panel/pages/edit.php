<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Edit Page';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM pages WHERE id=?");
$stmt->execute([$id]);
$page = $stmt->fetch();
if (!$page) { flashMessage('danger','Page not found.'); redirect(ADMIN_URL.'/pages/index.php'); }
$adminTitle = 'Edit Page — ' . $page['title'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $pdo->prepare("UPDATE pages SET meta_title=?,meta_description=?,meta_keywords=?,og_title=?,og_description=?,og_image=?,status=? WHERE id=?")
        ->execute([
            $_POST['meta_title'], $_POST['meta_description'], $_POST['meta_keywords'],
            $_POST['og_title'], $_POST['og_description'], $_POST['og_image'],
            (int)($_POST['status'] ?? 1), $id
        ]);
    // Sync to seo_meta
    $exists = $pdo->prepare("SELECT id FROM seo_meta WHERE page_identifier=?");
    $exists->execute([$page['slug']]);
    if ($exists->fetchColumn()) {
        $pdo->prepare("UPDATE seo_meta SET meta_title=?,meta_description=?,meta_keywords=?,og_title=?,og_description=?,og_image=? WHERE page_identifier=?")
            ->execute([$_POST['meta_title'],$_POST['meta_description'],$_POST['meta_keywords'],$_POST['og_title'],$_POST['og_description'],$_POST['og_image'],$page['slug']]);
    }
    flashMessage('success', 'Page updated.');
    redirect(ADMIN_URL . '/pages/index.php');
}
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<div style="display:flex;gap:12px;margin-bottom:20px;align-items:center">
  <a href="index.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Pages</a>
  <a href="<?= SITE_URL ?>/<?= $page['slug'] === 'home' ? '' : $page['slug'] ?>" target="_blank" class="btn btn-outline btn-sm"><i class="fas fa-external-link-alt"></i> View Live Page</a>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:24px">
  <div class="admin-form">
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <h3 style="margin-bottom:4px"><?= e($page['title']) ?></h3>
      <p style="color:var(--text-light);font-size:0.85rem;margin-bottom:20px">URL: <code>/<?= $page['slug'] === 'home' ? '' : $page['slug'] ?></code></p>

      <h4 style="margin-bottom:14px;font-size:0.85rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light)">SEO Meta</h4>
      <div class="form-group">
        <label>Meta Title</label>
        <input type="text" name="meta_title" value="<?= e($page['meta_title']) ?>" maxlength="200" placeholder="Page title for search engines (50-60 chars)">
      </div>
      <div class="form-group">
        <label>Meta Description</label>
        <textarea name="meta_description" rows="3" placeholder="Description for search results (150-160 chars)"><?= e($page['meta_description']) ?></textarea>
      </div>
      <div class="form-group">
        <label>Keywords</label>
        <input type="text" name="meta_keywords" value="<?= e($page['meta_keywords'] ?? '') ?>" placeholder="keyword1, keyword2, keyword3">
      </div>

      <h4 style="margin:8px 0 14px;font-size:0.85rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light)">Open Graph</h4>
      <div class="form-group">
        <label>OG Title</label>
        <input type="text" name="og_title" value="<?= e($page['og_title'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>OG Description</label>
        <textarea name="og_description" rows="2"><?= e($page['og_description'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>OG Image URL</label>
        <input type="text" name="og_image" value="<?= e($page['og_image'] ?? '') ?>" placeholder="https://... full URL to social share image">
      </div>

      <div class="form-group">
        <label>Page Status</label>
        <select name="status">
          <option value="1" <?= $page['status'] ? 'selected' : '' ?>>Published</option>
          <option value="0" <?= !$page['status'] ? 'selected' : '' ?>>Draft (not visible publicly)</option>
        </select>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Page Settings</button>
      </div>
    </form>
  </div>
  <div>
    <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:16px">
      <h4 style="font-size:0.82rem;font-weight:700;margin-bottom:12px;color:var(--text-light)">PAGE INFO</h4>
      <p style="font-size:0.82rem;margin-bottom:8px"><strong>Title:</strong> <?= e($page['title']) ?></p>
      <p style="font-size:0.82rem;margin-bottom:8px"><strong>Slug:</strong> <code><?= e($page['slug']) ?></code></p>
      <p style="font-size:0.82rem;margin-bottom:16px"><strong>Status:</strong> <span class="badge badge-<?= $page['status'] ? 'success' : 'danger' ?>"><?= $page['status'] ? 'Published' : 'Draft' ?></span></p>
      <a href="../seo/index.php?page=<?= e($page['slug']) ?>" class="btn btn-outline btn-sm" style="width:100%;justify-content:center"><i class="fas fa-search"></i> Full SEO Editor</a>
    </div>
    <div style="margin-top:12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:14px;font-size:0.82rem;color:#1e40af">
      <i class="fas fa-info-circle" style="margin-right:4px"></i>
      To edit page <em>content</em> (hero text, section headings, descriptions) go to the corresponding section manager: Services, Projects, Process, etc.
    </div>
  </div>
</div>
<?php require_once '../includes/layout-bottom.php'; ?>
