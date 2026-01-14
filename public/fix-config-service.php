<?php
/**
 * Fix Config Service Issue
 * This will regenerate the service provider cache
 * DELETE THIS FILE after running for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fixing Config Service Issue</h1>";

$basePath = __DIR__ . '/..';

// Delete all cache files
echo "<h2>1. Deleting Cache Files</h2>";
$cacheFiles = [
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php',
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes-v7.php',
];

foreach ($cacheFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            echo "✅ Deleted $file<br>";
        } else {
            echo "❌ Could not delete $file<br>";
        }
    } else {
        echo "ℹ️ $file does not exist<br>";
    }
}

// Clear view cache
echo "<h2>2. Clearing View Cache</h2>";
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

// Ensure bootstrap/cache directory exists and is writable
echo "<h2>3. Ensuring Bootstrap Cache Directory</h2>";
$bootstrapCache = $basePath . '/bootstrap/cache';
if (!is_dir($bootstrapCache)) {
    mkdir($bootstrapCache, 0755, true);
    echo "✅ Created bootstrap/cache directory<br>";
}
chmod($bootstrapCache, 0755);
echo "✅ Bootstrap cache directory is writable<br>";

// Try to regenerate using artisan commands via exec
echo "<h2>4. Regenerating Service Provider Cache</h2>";
chdir($basePath);

// Try to run composer dump-autoload
$composerPath = $basePath . '/vendor/bin/composer';
if (file_exists($composerPath)) {
    $output = [];
    $returnVar = 0;
    exec('php ' . escapeshellarg($composerPath) . ' dump-autoload --no-interaction 2>&1', $output, $returnVar);
    if ($returnVar === 0) {
        echo "✅ Composer dump-autoload successful<br>";
    } else {
        echo "⚠️ Composer dump-autoload had issues<br>";
    }
} else {
    echo "⚠️ Composer binary not found<br>";
}

// Try to run artisan commands
$artisanPath = $basePath . '/artisan';
if (file_exists($artisanPath)) {
    // Try config:clear
    $output = [];
    $returnVar = 0;
    exec('php ' . escapeshellarg($artisanPath) . ' config:clear 2>&1', $output, $returnVar);
    if ($returnVar === 0) {
        echo "✅ Config cache cleared<br>";
    }
    
    // Try package:discover
    $output = [];
    $returnVar = 0;
    exec('php ' . escapeshellarg($artisanPath) . ' package:discover 2>&1', $output, $returnVar);
    if ($returnVar === 0) {
        echo "✅ Package discovery successful<br>";
    }
} else {
    echo "⚠️ Artisan file not found<br>";
}

// Try to regenerate services.php and packages.php
echo "<h2>5. Regenerating services.php and packages.php</h2>";
$servicesCache = $basePath . '/bootstrap/cache/services.php';
$packagesCache = $basePath . '/bootstrap/cache/packages.php';

// Try artisan package:discover first
if (file_exists($artisanPath)) {
    $output = [];
    $returnVar = 0;
    exec('cd ' . escapeshellarg($basePath) . ' && php artisan package:discover --ansi 2>&1', $output, $returnVar);
    if ($returnVar === 0) {
        echo "✅ Package discovery successful<br>";
        if (file_exists($servicesCache)) {
            echo "✅ services.php regenerated<br>";
        }
        if (file_exists($packagesCache)) {
            echo "✅ packages.php regenerated<br>";
        }
    } else {
        echo "⚠️ Package discovery failed. Output:<br>";
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
}

// If services.php still doesn't exist, we need to manually bootstrap Laravel
if (!file_exists($servicesCache)) {
    echo "<h2>6. Manual Bootstrap (if needed)</h2>";
    echo "⚠️ services.php still missing. This might require manual intervention.<br>";
    echo "Try running: <code>php artisan package:discover</code> via SSH if available.<br>";
}

echo "<br><strong>✅ Fix applied! Try visiting your website now.</strong><br>";
echo "<br><strong>⚠️ Remember to DELETE this file for security!</strong>";
