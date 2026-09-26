<?php
require_once 'includes/auth.php';
requireLogin();
$adminTitle = 'Dashboard';

// Stats
$totalEnquiries = $pdo->query("SELECT COUNT(*) FROM enquiries")->fetchColumn();
$newEnquiries   = $pdo->query("SELECT COUNT(*) FROM enquiries WHERE status='new'")->fetchColumn();
$totalServices  = $pdo->query("SELECT COUNT(*) FROM services WHERE status=1")->fetchColumn();
$totalTools     = $pdo->query("SELECT COUNT(*) FROM ai_tools WHERE status=1")->fetchColumn();
$totalProjects  = $pdo->query("SELECT COUNT(*) FROM projects WHERE status=1")->fetchColumn();
$totalFaqs      = $pdo->query("SELECT COUNT(*) FROM faqs WHERE status=1")->fetchColumn();
$totalTestimonials = $pdo->query("SELECT COUNT(*) FROM testimonials WHERE status=1")->fetchColumn();

$recentEnquiries = $pdo->query("SELECT * FROM enquiries ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentProjects  = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5")->fetchAll();

require_once 'includes/layout-top.php';
?>

<div class="stat-cards">
  <div class="stat-card">
    <div class="stat-card-icon red"><i class="fas fa-envelope"></i></div>
    <div class="stat-card-info"><h3><?= $newEnquiries ?></h3><p>New Enquiries</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon primary"><i class="fas fa-envelope-open"></i></div>
    <div class="stat-card-info"><h3><?= $totalEnquiries ?></h3><p>Total Enquiries</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon blue"><i class="fas fa-cogs"></i></div>
    <div class="stat-card-info"><h3><?= $totalServices ?></h3><p>Services</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon orange"><i class="fas fa-robot"></i></div>
    <div class="stat-card-info"><h3><?= $totalTools ?></h3><p>AI Tools</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon green"><i class="fas fa-briefcase"></i></div>
    <div class="stat-card-info"><h3><?= $totalProjects ?></h3><p>Projects</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon primary"><i class="fas fa-question-circle"></i></div>
    <div class="stat-card-info"><h3><?= $totalFaqs ?></h3><p>FAQs</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon blue"><i class="fas fa-star"></i></div>
    <div class="stat-card-info"><h3><?= $totalTestimonials ?></h3><p>Testimonials</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon green"><i class="fas fa-file-alt"></i></div>
    <div class="stat-card-info"><h3>8</h3><p>Published Pages</p></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">

  <!-- Recent Enquiries -->
  <div class="admin-table-wrap">
    <div class="table-header">
      <h3>Recent Enquiries</h3>
      <a href="enquiries/index.php" class="btn btn-outline btn-sm">View All</a>
    </div>
    <table>
      <thead><tr><th>Name</th><th>Subject</th><th>Status</th><th>Date</th></tr></thead>
      <tbody>
        <?php if ($recentEnquiries): foreach ($recentEnquiries as $e): ?>
        <tr>
          <td><?= htmlspecialchars($e['name']) ?></td>
          <td><?= htmlspecialchars(substr($e['subject'],0,30)) ?>...</td>
          <td><span class="badge badge-<?= $e['status']==='new'?'danger':($e['status']==='converted'?'success':'info') ?>"><?= ucfirst(str_replace('_',' ',$e['status'])) ?></span></td>
          <td><?= date('M d', strtotime($e['created_at'])) ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="4" style="text-align:center;color:var(--text-light);padding:24px">No enquiries yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Recent Projects -->
  <div class="admin-table-wrap">
    <div class="table-header">
      <h3>Recent Projects</h3>
      <a href="projects/index.php" class="btn btn-outline btn-sm">View All</a>
    </div>
    <table>
      <thead><tr><th>Project</th><th>Category</th><th>Status</th></tr></thead>
      <tbody>
        <?php if ($recentProjects): foreach ($recentProjects as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= htmlspecialchars($p['category']) ?></td>
          <td><span class="badge badge-<?= $p['status']?'success':'danger' ?>"><?= $p['status']?'Published':'Draft' ?></span></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="3" style="text-align:center;color:var(--text-light);padding:24px">No projects yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- Quick Actions -->
<div style="margin-top:24px;background:var(--white);border-radius:var(--radius);border:1px solid var(--border);padding:24px">
  <h3 style="margin-bottom:16px;font-size:0.95rem">Quick Actions</h3>
  <div style="display:flex;gap:12px;flex-wrap:wrap">
    <a href="services/add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Service</a>
    <a href="projects/add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Project</a>
    <a href="ai-tools/add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add AI Tool</a>
    <a href="faqs/add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add FAQ</a>
    <a href="testimonials/add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Testimonial</a>
    <a href="clients/add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Client</a>
    <a href="enquiries/index.php" class="btn btn-outline btn-sm"><i class="fas fa-envelope"></i> View Enquiries</a>
    <a href="settings/index.php" class="btn btn-outline btn-sm"><i class="fas fa-sliders-h"></i> Settings</a>
  </div>
</div>

<?php require_once 'includes/layout-bottom.php'; ?>
