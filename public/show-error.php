<?php
/**
 * Show Exact Laravel Error
 * This will show the actual error that's causing the 500
 * DELETE THIS FILE after debugging for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Laravel Error Details</h1>";

$basePath = __DIR__ . '/..';

try {
    require $basePath . '/vendor/autoload.php';
    
    // Set APP_DEBUG temporarily to see errors
    $_ENV['APP_DEBUG'] = 'true';
    putenv('APP_DEBUG=true');
    
    $app = require_once $basePath . '/bootstrap/app.php';
    
    echo "<h2>1. Bootstrap Status</h2>";
    echo "✅ Laravel bootstrap successful<br>";
    
    // Try to simulate a request
    echo "<h2>2. Simulating Request</h2>";
    try {
        $request = Illuminate\Http\Request::create('/', 'GET');
        $response = $app->handleRequest($request);
        echo "✅ Request handled successfully<br>";
        echo "Response status: " . $response->getStatusCode() . "<br>";
    } catch (Exception $e) {
        echo "❌ Request failed: " . $e->getMessage() . "<br>";
        echo "Error type: " . get_class($e) . "<br>";
        echo "<h3>Stack Trace:</h3>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "❌ Bootstrap failed: " . $e->getMessage() . "<br>";
    echo "Error type: " . get_class($e) . "<br>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// Show error log
echo "<h2>3. Error Log (Last 50 lines)</h2>";
$logPath = $basePath . '/storage/logs/laravel.log';
if (file_exists($logPath)) {
    $lines = file($logPath);
    $lastLines = array_slice($lines, -50);
    echo "<pre style='background: #f5f5f5; padding: 15px; overflow-x: auto;'>";
    echo htmlspecialchars(implode('', $lastLines));
    echo "</pre>";
} else {
    echo "ℹ️ No error log file found<br>";
}

// Check .env for APP_DEBUG
echo "<h2>4. Environment Check</h2>";
$envPath = $basePath . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (strpos($envContent, 'APP_DEBUG=true') !== false) {
        echo "✅ APP_DEBUG is set to true in .env<br>";
    } else {
        echo "⚠️ APP_DEBUG is not set to true. Set it temporarily to see errors.<br>";
    }
} else {
    echo "❌ .env file not found<br>";
}

echo "<hr>";
echo "<p><strong>Remember to DELETE this file after debugging!</strong></p>";
