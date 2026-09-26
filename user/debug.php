<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h3>Step 1: Config</h3>";
require_once '../includes/config.php';
echo "✅ config.php OK<br>";

echo "<h3>Step 2: user-auth.php</h3>";
require_once '../includes/user-auth.php';
echo "✅ user-auth.php OK<br>";

echo "<h3>Step 3: credits.php</h3>";
require_once '../includes/credits.php';
echo "✅ credits.php OK<br>";

echo "<h3>Step 4: DB Tables Check</h3>";
$tables = ['tool_users','user_credits','credit_plans','credit_transactions'];
foreach ($tables as $t) {
    try {
        $pdo->query("SELECT 1 FROM $t LIMIT 1");
        echo "✅ $t exists<br>";
    } catch(Exception $e) {
        echo "❌ $t MISSING — " . $e->getMessage() . "<br>";
    }
}

echo "<h3>Step 5: ai_tools credit columns</h3>";
try {
    $pdo->query("SELECT credit_cost_720 FROM ai_tools LIMIT 1");
    echo "✅ credit_cost columns exist<br>";
} catch(Exception $e) {
    echo "❌ credit_cost columns MISSING<br>";
}

echo "<hr><b>All checks done.</b>";
