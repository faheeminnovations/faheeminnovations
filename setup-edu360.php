<?php
// ONE-TIME — auto-deletes after run
require_once 'includes/config.php';

// Add packages column if not exists
try { $pdo->exec("ALTER TABLE products ADD COLUMN packages JSON NULL AFTER features"); } catch(Exception $e) {}

$features = [
    'Student Admission & Records Management',
    'Classes, Sections & Academic Sessions',
    'Student & Staff Attendance',
    'Fee Collection & Payment Records',
    'Examinations & Result Management',
    'Staff & Teacher Management',
    'Salary & Payroll Management',
    'Financial Reports & Analytics',
    'User Login & Role-Based Access Control',
    'Administrative Management',
    'Dashboard & Detailed Reports',
    'Initial Setup & Staff Training',
];

$packages = [
    [
        'name'        => 'Basic',
        'subtitle'    => 'Small Schools & Colleges',
        'price'       => 25000,
        'currency'    => 'PKR',
        'price_label' => 'One-time setup fee',
        'installment' => 'Rs 10,000 × 3 installments',
        'maintenance' => 'Rs 8,000/year',
        'support'     => '30 days initial support',
        'recommended' => false,
        'features'    => [
            'Student admission and records',
            'Classes, sections and academic sessions',
            'Student attendance',
            'Fee collection and payment records',
            'Examinations and results',
            'Basic reports and dashboard',
            'User login and access control',
            'Initial setup and training',
        ],
    ],
    [
        'name'        => 'Professional',
        'subtitle'    => 'Growing Institutions',
        'price'       => 40000,
        'currency'    => 'PKR',
        'price_label' => 'One-time setup fee',
        'installment' => 'Rs 15,000 × 3 installments',
        'maintenance' => 'Rs 12,000/year',
        'support'     => '60 days initial support',
        'recommended' => true,
        'features'    => [
            'All Basic package features',
            'Staff and teacher management',
            'Staff attendance and salary management',
            'Advanced examination and result reports',
            'Detailed fee and financial reports',
            'Administrative management',
            'Additional available modules',
            'Initial setup and staff training',
        ],
    ],
];

$techStack = ['PHP', 'MySQL', 'Bootstrap 5', 'JavaScript', 'REST API'];

$pdo->prepare("UPDATE products SET
    name=?, slug=?, tagline=?, short_description=?, full_description=?,
    category=?, icon=?, price=?, price_label=?, currency=?,
    is_featured=?, button_text=?, features=?, packages=?, tech_stack=?,
    status=?, sort_order=?
WHERE slug='faheem-edu-360' OR name LIKE '%FaheemEdu%' OR name LIKE '%Faheem Edu%'")
->execute([
    'FaheemEdu360',
    'faheem-edu-360',
    'Smart Management for Schools & Colleges',
    'Manage student records, attendance, fees, examinations, staff and salaries through one integrated platform.',
    'FaheemEdu360 is a complete digital management solution for schools, colleges and educational institutions. It covers every aspect of institutional management — from student admissions and fee collection to staff management, examination results and detailed reporting — all through one easy-to-use platform.',
    'ERP Solutions',
    'fas fa-graduation-cap',
    25000,
    'Starting from',
    'PKR',
    1,
    'Book a Free Demo',
    json_encode($features),
    json_encode($packages),
    json_encode($techStack),
    1,
    1,
]);

@unlink(__FILE__);
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Done</title>
<style>body{font-family:Inter,sans-serif;max-width:560px;margin:60px auto;padding:0 20px}h2{color:#0B3C33}.box{background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:16px 20px}a{color:#0B3C33;font-weight:600}</style>
</head><body>
<h2>✅ FaheemEdu360 Updated</h2>
<div class="box">
  <p>✅ Product info updated with correct tagline, description</p>
  <p>✅ Basic & Professional packages with pricing added</p>
  <p>✅ Tech stack updated</p>
  <p>🗑️ This file deleted itself.</p>
</div>
<p style="margin-top:20px">
  <a href="http://localhost/faheeminnovations/products/faheem-edu-360">→ View Product Page</a> &nbsp;|&nbsp;
  <a href="http://localhost/faheeminnovations/products">→ All Products</a>
</p>
</body></html>
