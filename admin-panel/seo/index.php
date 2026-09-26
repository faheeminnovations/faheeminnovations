<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'SEO Management';

$pages = [
    'home'     => 'Home Page',
    'about'    => 'About Page',
    'services' => 'Services Page',
    'ai-tools' => 'AI Tools Page',
    'projects' => 'Projects Page',
    'process'  => 'Process Page',
    'faq'      => 'FAQ Page',
    'contact'  => 'Contact Page',
];

$selected = $_GET['page'] ?? 'home';
if (!array_key_exists($selected, $pages)) $selected = 'home';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $pg = $_POST['page_identifier'] ?? $selected;
    // Check if exists
    $exists = $pdo->prepare("SELECT id FROM seo_meta WHERE page_identifier = ?");
    $exists->execute([$pg]);
    if ($exists->fetchColumn()) {
        $pdo->prepare("UPDATE seo_meta SET meta_title=?,meta_description=?,meta_keywords=?,og_title=?,og_description=?,og_image=?,robots=?,canonical_url=? WHERE page_identifier=?")
            ->execute([$_POST['meta_title'],$_POST['meta_description'],$_POST['meta_keywords'],$_POST['og_title'],$_POST['og_description'],$_POST['og_image'],$_POST['robots'],$_POST['canonical_url'],$pg]);
    } else {
        $pdo->prepare("INSERT INTO seo_meta (page_identifier,meta_title,meta_description,meta_keywords,og_title,og_description,og_image,robots,canonical_url) VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute([$pg,$_POST['meta_title'],$_POST['meta_description'],$_POST['meta_keywords'],$_POST['og_title'],$_POST['og_description'],$_POST['og_image'],$_POST['robots'],$_POST['canonical_url']]);
    }
    // Also update the pages table
    $pdo->prepare("UPDATE pages SET meta_title=?,meta_description=?,meta_keywords=? WHERE slug=?")
        ->execute([$_POST['meta_title'],$_POST['meta_description'],$_POST['meta_keywords'],$pg]);
    flashMessage('success', 'SEO settings saved for "' . $pages[$pg] . '".');
    redirect(ADMIN_URL . '/seo/index.php?page=' . $pg);
}

$stmt = $pdo->prepare("SELECT * FROM seo_meta WHERE page_identifier = ?");
$stmt->execute([$selected]);
$seo = $stmt->fetch() ?: [];

// Fallback from pages table
if (!$seo) {
    $stmt2 = $pdo->prepare("SELECT * FROM pages WHERE slug = ?");
    $stmt2->execute([$selected]);
    $pg = $stmt2->fetch();
    if ($pg) $seo = ['meta_title'=>$pg['meta_title'],'meta_description'=>$pg['meta_description']];
}

require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<div style="display:grid;grid-template-columns:220px 1fr;gap:24px">

  <!-- Page Selector -->
  <div class="admin-table-wrap" style="align-self:start">
    <div class="table-header"><h3>Pages</h3></div>
    <div style="padding:8px 0">
      <?php foreach ($pages as $slug => $label): ?>
      <a href="?page=<?= $slug ?>" style="display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:0.88rem;font-weight:<?= $selected===$slug?'700':'500' ?>;color:<?= $selected===$slug?'var(--primary)':'var(--text)' ?>;background:<?= $selected===$slug?'rgba(11,60,51,0.06)':'' ?>;border-left:3px solid <?= $selected===$slug?'var(--primary)':'transparent' ?>;transition:0.2s">
        <i class="fas fa-file-alt" style="width:16px;text-align:center;font-size:0.8rem;color:var(--text-light)"></i>
        <?= $label ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SEO Form -->
  <div>
    <div class="admin-table-wrap">
      <div class="table-header">
        <h3>SEO Settings — <?= $pages[$selected] ?></h3>
        <a href="<?= SITE_URL ?>/<?= $selected === 'home' ? '' : $selected ?>" target="_blank" class="btn btn-outline btn-sm"><i class="fas fa-external-link-alt"></i> View Page</a>
      </div>
      <div style="padding:24px">
        <form method="POST">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="page_identifier" value="<?= e($selected) ?>">

          <h4 style="margin-bottom:16px;font-size:0.9rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.05em">Basic SEO</h4>
          <div class="form-group">
            <label>Meta Title</label>
            <input type="text" name="meta_title" value="<?= e($seo['meta_title'] ?? '') ?>" placeholder="Page title for search engines (50-60 chars recommended)" maxlength="200">
            <span class="form-hint">Appears in browser tab and search results.</span>
          </div>
          <div class="form-group">
            <label>Meta Description</label>
            <textarea name="meta_description" rows="3" placeholder="Description for search engines (150-160 chars recommended)"><?= e($seo['meta_description'] ?? '') ?></textarea>
            <span class="form-hint">Shown as the snippet in search results.</span>
          </div>
          <div class="form-group">
            <label>Keywords</label>
            <input type="text" name="meta_keywords" value="<?= e($seo['meta_keywords'] ?? '') ?>" placeholder="web development, software development, AI solutions (comma separated)">
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
              <label>Robots</label>
              <select name="robots">
                <?php foreach (['index,follow','noindex,follow','index,nofollow','noindex,nofollow'] as $r): ?>
                <option value="<?= $r ?>" <?= ($seo['robots']??'index,follow')===$r?'selected':'' ?>><?= $r ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Canonical URL</label>
              <input type="text" name="canonical_url" value="<?= e($seo['canonical_url'] ?? '') ?>" placeholder="https://faheeminnovations.online/<?= $selected !== 'home' ? $selected : '' ?>">
            </div>
          </div>

          <h4 style="margin:8px 0 16px;font-size:0.9rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.05em">Open Graph (Social Sharing)</h4>
          <div class="form-group">
            <label>OG Title</label>
            <input type="text" name="og_title" value="<?= e($seo['og_title'] ?? '') ?>" placeholder="Title shown when shared on Facebook, LinkedIn...">
          </div>
          <div class="form-group">
            <label>OG Description</label>
            <textarea name="og_description" rows="2" placeholder="Description for social sharing"><?= e($seo['og_description'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>OG Image URL</label>
            <input type="text" name="og_image" value="<?= e($seo['og_image'] ?? '') ?>" placeholder="https://... full URL to social share image (1200x630 recommended)">
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save SEO Settings</button>
          </div>
        </form>
      </div>
    </div>

    <!-- SEO Preview -->
    <div style="margin-top:16px;background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:20px">
      <h4 style="font-size:0.85rem;font-weight:700;margin-bottom:12px;color:var(--text-light)">SEARCH RESULT PREVIEW</h4>
      <div style="max-width:500px">
        <div style="font-size:0.78rem;color:#202124;margin-bottom:2px"><?= SITE_URL ?>/<?= $selected !== 'home' ? $selected : '' ?></div>
        <div style="font-size:1.05rem;color:#1a0dab;font-weight:400;margin-bottom:4px" id="previewTitle"><?= e($seo['meta_title'] ?? 'Page Title') ?></div>
        <div style="font-size:0.85rem;color:#3c4043;line-height:1.5" id="previewDesc"><?= e($seo['meta_description'] ?? 'Meta description will appear here...') ?></div>
      </div>
    </div>
  </div>

</div>

<script>
document.querySelector('[name=meta_title]').addEventListener('input', function() {
  document.getElementById('previewTitle').textContent = this.value || 'Page Title';
});
document.querySelector('[name=meta_description]').addEventListener('input', function() {
  document.getElementById('previewDesc').textContent = this.value || 'Meta description will appear here...';
});
</script>
<?php require_once '../includes/layout-bottom.php'; ?>
