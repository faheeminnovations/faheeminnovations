<?php
// ONE-TIME SETUP — auto-deletes after run
require_once 'includes/config.php';

$msgs = [];

// 1. Create products table
$pdo->exec("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    tagline VARCHAR(255),
    short_description TEXT,
    full_description LONGTEXT,
    category VARCHAR(100),
    icon VARCHAR(100) DEFAULT 'fas fa-box',
    image VARCHAR(255),
    price DECIMAL(10,2) DEFAULT 0.00,
    price_label VARCHAR(50) DEFAULT 'Starting from',
    currency VARCHAR(10) DEFAULT 'USD',
    is_featured TINYINT DEFAULT 0,
    demo_url VARCHAR(255),
    purchase_url VARCHAR(255),
    button_text VARCHAR(100) DEFAULT 'Get Started',
    features JSON,
    tech_stack JSON,
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    meta_title VARCHAR(200),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
$msgs[] = '✅ products table created';

// 2. Insert sample product
$exists = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
if (!$exists) {
    $pdo->prepare("INSERT INTO products (name, slug, tagline, short_description, full_description, category, icon, price, price_label, currency, is_featured, button_text, features, tech_stack, status, sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
        ->execute([
            'FaheemEdu 360',
            'faheem-edu-360',
            'Cloud-Based School & Institute Management System',
            'A complete SaaS-based educational management platform covering admissions, attendance, fees, exams, timetable, HR, and more.',
            'FaheemEdu 360 is a powerful cloud-based ERP solution designed for schools, colleges and educational institutes. It covers every aspect of institutional management from student admissions to fee collection, attendance tracking, exam management, timetable scheduling, HR management and detailed reporting.',
            'ERP Solutions',
            'fas fa-graduation-cap',
            99.00,
            'Starting from',
            'USD',
            1,
            'Get a Demo',
            json_encode(['Student & Parent Portal','Fee & Accounting Management','Attendance & Timetable','Exam & Result Management','HR & Staff Management','Multi-Institute SaaS Support','Detailed Reports & Analytics']),
            json_encode(['Laravel 11','MySQL','Bootstrap 5','REST API']),
            1,
            1
        ]);
    $msgs[] = '✅ Sample product (FaheemEdu 360) inserted';
}

// 3. Add menu item for Products
$exists = $pdo->query("SELECT COUNT(*) FROM menu_items WHERE url LIKE '%/products%' AND parent_id=0")->fetchColumn();
if (!$exists) {
    $order = (int)$pdo->query("SELECT MAX(sort_order) FROM menu_items WHERE parent_id=0")->fetchColumn() + 1;
    $pdo->prepare("INSERT INTO menu_items (label, url, parent_id, target, status, sort_order) VALUES (?,?,0,'_self',1,?)")
        ->execute(['Products', 'http://localhost/faheeminnovations/products', $order]);
    $msgs[] = '✅ Products nav menu item added';
} else {
    $msgs[] = 'ℹ️ Products menu item already exists';
}

@unlink(__FILE__);
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Products Setup</title>
<style>body{font-family:Inter,sans-serif;max-width:620px;margin:60px auto;padding:0 20px}h2{color:#0B3C33}ul{line-height:2.2;font-size:.9rem}.box{background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:16px 20px}</style>
</head><body>
<h2>✅ Products Setup Complete</h2>
<div class="box"><ul><?php foreach($msgs as $m): ?><li><?= $m ?></li><?php endforeach; ?></ul>
<p style="margin:8px 0 0;font-size:.8rem;color:#065f46">🗑️ This file deleted itself.</p></div>
<p><a href="http://localhost/faheeminnovations/products">→ View Products Page</a> &nbsp; <a href="http://localhost/faheeminnovations/admin-panel/products/">→ Admin Products</a></p>
</body></html>
