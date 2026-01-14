<?php
/**
 * Test Routes and Laravel Bootstrap
 * DELETE THIS FILE after debugging!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Route and Bootstrap Test</h1>";

$basePath = __DIR__ . '/..';

// Test 1: Check if .htaccess exists
echo "<h2>1. .htaccess Check</h2>";
$htaccess = __DIR__ . '/.htaccess';
if (file_exists($htaccess)) {
    echo "✅ .htaccess exists<br>";
    $content = file_get_contents($htaccess);
    if (strpos($content, 'RewriteEngine On') !== false) {
        echo "✅ RewriteEngine is enabled<br>";
    } else {
        echo "⚠️ RewriteEngine might not be enabled<br>";
    }
} else {
    echo "❌ .htaccess is missing!<br>";
}

// Test 2: Check if index.php exists
echo "<h2>2. index.php Check</h2>";
$index = __DIR__ . '/index.php';
if (file_exists($index)) {
    echo "✅ index.php exists<br>";
} else {
    echo "❌ index.php is missing!<br>";
}

// Test 3: Try to bootstrap Laravel
echo "<h2>3. Laravel Bootstrap Test</h2>";
try {
    require $basePath . '/vendor/autoload.php';
    echo "✅ Autoloader loaded<br>";
    
    $app = require_once $basePath . '/bootstrap/app.php';
    echo "✅ Laravel app created<br>";
    
    // Try to get routes
    $kernel = $app->make('Illuminate\Contracts\Http\Kernel');
    echo "✅ HTTP Kernel loaded<br>";
    
    // Check if routes are loaded
    $routes = $app->make('router')->getRoutes();
    echo "✅ Routes loaded: " . count($routes) . " routes found<br>";
    
} catch (Exception $e) {
    echo "❌ Bootstrap failed: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// Test 4: Check Document Root
echo "<h2>4. Server Configuration</h2>";
echo "Document Root: <code>" . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</code><br>";
echo "Script Path: <code>" . htmlspecialchars(__FILE__) . "</code><br>";
echo "Request URI: <code>" . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'Not set') . "</code><br>";

// Test 5: Check if we can access index.php directly
echo "<h2>5. Direct index.php Test</h2>";
$indexPath = __DIR__ . '/index.php';
if (file_exists($indexPath)) {
    echo "Try accessing: <a href='index.php'>index.php</a><br>";
}

echo "<br><strong>⚠️ Remember to DELETE this file for security!</strong>";
