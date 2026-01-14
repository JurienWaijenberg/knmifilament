<?php
/**
 * Clear Laravel Caches
 * Upload this to your public directory and visit it once
 * DELETE THIS FILE after running for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Clearing Laravel Caches</h1>";

$basePath = __DIR__ . '/..';

// Clear config cache
$configCache = $basePath . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    if (unlink($configCache)) {
        echo "✅ Deleted config cache<br>";
    } else {
        echo "❌ Could not delete config cache (permissions issue?)<br>";
    }
} else {
    echo "ℹ️ No config cache to delete<br>";
}

// Clear route cache
$routeCache = $basePath . '/bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    if (unlink($routeCache)) {
        echo "✅ Deleted route cache<br>";
    }
}

// Clear view cache
$viewCache = $basePath . '/storage/framework/views';
if (is_dir($viewCache)) {
    $files = glob($viewCache . '/*');
    $deleted = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $deleted++;
        }
    }
    if ($deleted > 0) {
        echo "✅ Cleared $deleted view cache files<br>";
    }
}

// Clear application cache
$appCache = $basePath . '/storage/framework/cache';
if (is_dir($appCache)) {
    $files = glob($appCache . '/data/*');
    $deleted = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $deleted++;
        }
    }
    if ($deleted > 0) {
        echo "✅ Cleared $deleted application cache files<br>";
    }
}

echo "<br><strong>✅ Cache cleared! Try visiting your website now.</strong><br>";
echo "<br><strong>⚠️ Remember to DELETE this file for security!</strong>";
