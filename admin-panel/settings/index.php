<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/crud.php';
$adminTitle = 'Website Settings';

$tab = $_GET['tab'] ?? 'general';
$validTabs = ['general','branding','contact','social','analytics'];
if (!in_array($tab, $validTabs)) $tab = 'general';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $keys = $_POST['keys'] ?? [];
    $values = $_POST['values'] ?? [];
    foreach ($keys as $i => $key) {
        $key = preg_replace('/[^a-z0-9_]/', '', $key);
        if (!$key) continue;
        $val = $values[$i] ?? '';
        $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")
            ->execute([$key, $val, $val]);
    }
    // Handle logo/favicon upload
    if (!empty($_FILES['site_logo']['name'])) {
        $path = uploadFile($_FILES['site_logo'], 'branding');
        if ($path) $pdo->prepare("INSERT INTO settings (setting_key,setting_value) VALUES ('site_logo',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$path,$path]);
    }
    if (!empty($_FILES['site_favicon']['name'])) {
        $path = uploadFile($_FILES['site_favicon'], 'branding');
        if ($path) $pdo->prepare("INSERT INTO settings (setting_key,setting_value) VALUES ('site_favicon',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$path,$path]);
    }
    // Social links
    if (isset($_POST['social_platform'])) {
        foreach ($_POST['social_platform'] as $i => $platform) {
            $url = trim($_POST['social_url'][$i] ?? '');
            $pdo->prepare("UPDATE social_links SET url=? WHERE platform=?")->execute([$url, $platform]);
        }
    }
    flashMessage('success', 'Settings saved successfully.');
    redirect(ADMIN_URL . '/settings/index.php?tab=' . $tab);
}

$s = getAllSettings();
$socials = $pdo->query("SELECT * FROM social_links ORDER BY sort_order")->fetchAll();
require_once '../includes/layout-top.php';
?>
<?php showFlash(); ?>

<!-- Tab Nav -->
<div style="display:flex;gap:4px;margin-bottom:24px;flex-wrap:wrap">
  <?php
  $tabs = [
    'general'   => ['fas fa-sliders-h','General'],
    'branding'  => ['fas fa-palette','Branding'],
    'contact'   => ['fas fa-map-marker-alt','Contact'],
    'social'    => ['fas fa-share-alt','Social Media'],
    'analytics' => ['fas fa-chart-bar','Analytics'],
  ];
  foreach ($tabs as $t => [$icon, $label]):
  ?>
  <a href="?tab=<?= $t ?>" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;font-size:0.88rem;font-weight:600;transition:0.2s;<?= $tab===$t?'background:var(--primary);color:#fff':'background:var(--white);border:1px solid var(--border);color:var(--text)' ?>">
    <i class="<?= $icon ?>"></i> <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

  <?php if ($tab === 'general'): ?>
  <div class="admin-form">
    <h3 style="margin-bottom:20px">General Settings</h3>
    <?php
    $fields = [
      ['site_name',    'Website Name',   'text',     'Faheem Innovations'],
      ['site_tagline', 'Tagline',         'text',     'Innovation starts with a vision.'],
      ['footer_description', 'Footer Description', 'textarea', ''],
    ];
    foreach ($fields as [$key, $label, $type, $placeholder]):
    ?>
    <input type="hidden" name="keys[]" value="<?= $key ?>">
    <div class="form-group">
      <label><?= $label ?></label>
      <?php if ($type === 'textarea'): ?>
      <textarea name="values[]" rows="3"><?= e($s[$key] ?? '') ?></textarea>
      <?php else: ?>
      <input type="<?= $type ?>" name="values[]" value="<?= e($s[$key] ?? '') ?>" placeholder="<?= $placeholder ?>">
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
    </div>
  </div>

  <?php elseif ($tab === 'branding'): ?>
  <div class="admin-form">
    <h3 style="margin-bottom:20px">Branding</h3>
    <div class="form-grid">
      <div class="form-group">
        <label>Primary Color</label>
        <input type="hidden" name="keys[]" value="primary_color">
        <div style="display:flex;gap:10px;align-items:center">
          <input type="color" name="values[]" value="<?= e($s['primary_color'] ?? '#0B3C33') ?>" style="width:60px;height:40px;border:1px solid var(--border);border-radius:6px;cursor:pointer">
          <span style="font-size:0.85rem;color:var(--text-light)">Current: <?= e($s['primary_color'] ?? '#0B3C33') ?></span>
        </div>
      </div>
      <div class="form-group">
        <label>Secondary Color</label>
        <input type="hidden" name="keys[]" value="secondary_color">
        <div style="display:flex;gap:10px;align-items:center">
          <input type="color" name="values[]" value="<?= e($s['secondary_color'] ?? '#1a6b5a') ?>" style="width:60px;height:40px;border:1px solid var(--border);border-radius:6px;cursor:pointer">
          <span style="font-size:0.85rem;color:var(--text-light)">Current: <?= e($s['secondary_color'] ?? '#1a6b5a') ?></span>
        </div>
      </div>
    </div>
    <div class="form-grid" style="margin-top:8px">
      <div class="form-group">
        <label>Logo</label>
        <?php if (!empty($s['site_logo'])): ?>
        <img src="<?= SITE_URL ?>/<?= e($s['site_logo']) ?>" style="height:48px;margin-bottom:10px;display:block">
        <?php endif; ?>
        <input type="file" name="site_logo" accept="image/*">
        <span class="form-hint">Upload PNG with transparent background recommended.</span>
      </div>
      <div class="form-group">
        <label>Favicon</label>
        <?php if (!empty($s['site_favicon'])): ?>
        <img src="<?= SITE_URL ?>/<?= e($s['site_favicon']) ?>" style="height:32px;margin-bottom:10px;display:block">
        <?php endif; ?>
        <input type="file" name="site_favicon" accept="image/*">
        <span class="form-hint">32×32 or 64×64 PNG recommended.</span>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Branding</button>
    </div>
  </div>

  <?php elseif ($tab === 'contact'): ?>
  <div class="admin-form">
    <h3 style="margin-bottom:20px">Contact Information</h3>
    <?php
    $fields = [
      ['site_email',    'Email Address',   'email', 'hello@faheeminnovations.online'],
      ['site_phone',    'Phone Number',    'text',  '0304-1277320'],
      ['site_whatsapp', 'WhatsApp Number', 'text',  '923041277320 (with country code, no +)'],
      ['site_address',  'Address',         'textarea', ''],
      ['google_maps_url','Google Maps Embed URL','text','https://maps.google.com/...'],
    ];
    foreach ($fields as [$key, $label, $type, $placeholder]):
    ?>
    <input type="hidden" name="keys[]" value="<?= $key ?>">
    <div class="form-group">
      <label><?= $label ?></label>
      <?php if ($type === 'textarea'): ?>
      <textarea name="values[]" rows="2"><?= e($s[$key] ?? '') ?></textarea>
      <?php else: ?>
      <input type="<?= $type ?>" name="values[]" value="<?= e($s[$key] ?? '') ?>" placeholder="<?= $placeholder ?>">
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Contact Info</button>
    </div>
  </div>

  <?php elseif ($tab === 'social'): ?>
  <div class="admin-form">
    <h3 style="margin-bottom:4px">Social Media Links</h3>
    <p style="color:var(--text-light);font-size:0.88rem;margin-bottom:20px">Leave blank to hide from the website footer.</p>
    <?php foreach ($socials as $social): ?>
    <input type="hidden" name="social_platform[]" value="<?= e($social['platform']) ?>">
    <div class="form-group">
      <label><i class="<?= e($social['icon']) ?>" style="margin-right:6px;color:var(--primary)"></i><?= e($social['platform']) ?></label>
      <input type="url" name="social_url[]" value="<?= e($social['url']) ?>" placeholder="https://...">
    </div>
    <?php endforeach; ?>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Social Links</button>
    </div>
  </div>

  <?php elseif ($tab === 'analytics'): ?>
  <div class="admin-form">
    <h3 style="margin-bottom:20px">Analytics & Tracking</h3>
    <?php
    $fields = [
      ['google_analytics',    'Google Analytics ID (GA4)',  'text', 'G-XXXXXXXXXX'],
      ['google_tag_manager',  'Google Tag Manager ID',      'text', 'GTM-XXXXXXX'],
    ];
    foreach ($fields as [$key, $label, $type, $placeholder]):
    ?>
    <input type="hidden" name="keys[]" value="<?= $key ?>">
    <div class="form-group">
      <label><?= $label ?></label>
      <input type="<?= $type ?>" name="values[]" value="<?= e($s[$key] ?? '') ?>" placeholder="<?= $placeholder ?>">
    </div>
    <?php endforeach; ?>
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:14px 16px;font-size:0.85rem;color:#166534;margin-bottom:20px">
      <i class="fas fa-check-circle" style="margin-right:6px"></i>
      The tracking codes are automatically inserted into the website's <code>&lt;head&gt;</code> once saved.
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Analytics</button>
    </div>
  </div>
  <?php endif; ?>

</form>
<?php require_once '../includes/layout-bottom.php'; ?>
