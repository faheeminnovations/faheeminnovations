<?php
// ONE-TIME — auto-deletes after run
require_once 'includes/config.php';

// Add pricing_plans column if not exists
try { $pdo->exec("ALTER TABLE ai_tools ADD COLUMN pricing_plans JSON NULL AFTER features"); } catch(Exception $e) {}

$plans = [
    ['name'=>'Free',     'icon'=>'🆓', 'price'=>0,     'period'=>'month',    'credits'=>30,    'label'=>'30 Credits/month',    'highlight'=>false],
    ['name'=>'Pro',      'icon'=>'⭐', 'price'=>9.99,  'period'=>'month',    'credits'=>300,   'label'=>'300 Credits/month',   'highlight'=>false],
    ['name'=>'Premium',  'icon'=>'🚀', 'price'=>19.99, 'period'=>'month',    'credits'=>1000,  'label'=>'1,000 Credits/month', 'highlight'=>true],
    ['name'=>'Lifetime', 'icon'=>'♾️', 'price'=>99,    'period'=>'one-time', 'credits'=>5000,  'label'=>'5,000 Credits',       'highlight'=>false],
];

$pdo->prepare("UPDATE ai_tools SET
    is_free=0,
    price=9.99,
    pricing_plans=?,
    tool_url=?,
    button_text='Try Free'
WHERE slug='ai-video-hd'")
->execute([
    json_encode($plans),
    'http://localhost/faheeminnovations/ai-tools/video-to-hd/'
]);

@unlink(__FILE__);
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Done</title>
<style>body{font-family:Inter,sans-serif;max-width:500px;margin:60px auto;padding:0 20px}h2{color:#0B3C33}.box{background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:16px 20px}a{color:#0B3C33;font-weight:600}</style>
</head><body>
<h2>✅ AI Video HD Updated</h2>
<div class="box">
  <p>✅ Pricing plans added (Free / Pro / Premium / Lifetime)</p>
  <p>✅ Tool URL linked to video-to-hd form</p>
  <p>✅ Button text → "Try Free"</p>
  <p>🗑️ File deleted.</p>
</div>
<p><a href="http://localhost/faheeminnovations/ai-tools">→ View AI Tools Page</a></p>
</body></html>
