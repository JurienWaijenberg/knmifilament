<?php
/**
 * Fix Service Provider Cache
 * This will regenerate the services.php file
 * DELETE THIS FILE after running for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fixing Laravel Service Providers</h1>";

$basePath = __DIR__ . '/..';

// Delete services.php cache
$servicesCache = $basePath . '/bootstrap/cache/services.php';
if (file_exists($servicesCache)) {
    if (unlink($servicesCache)) {
        echo "✅ Deleted services.php cache<br>";
    } else {
        echo "❌ Could not delete services.php cache (permissions issue?)<br>";
    }
} else {
    echo "ℹ️ No services.php cache to delete<br>";
}

// Delete packages.php cache
$packagesCache = $basePath . '/bootstrap/cache/packages.php';
if (file_exists($packagesCache)) {
    if (unlink($packagesCache)) {
        echo "✅ Deleted packages.php cache<br>";
    }
}

// Try to regenerate using composer
echo "<h2>Regenerating Service Provider Cache</h2>";

try {
    require $basePath . '/vendor/autoload.php';
    
    // Try to run composer dump-autoload via exec
    $composerPath = $basePath . '/vendor/bin/composer';
    if (file_exists($composerPath)) {
        chdir($basePath);
        $output = [];
        $returnVar = 0;
        exec('php ' . escapeshellarg($composerPath) . ' dump-autoload --no-interaction 2>&1', $output, $returnVar);
        if ($returnVar === 0) {
            echo "✅ Composer dump-autoload successful<br>";
            echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        } else {
            echo "⚠️ Composer dump-autoload had issues (return code: $returnVar)<br>";
            echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        }
    } else {
        echo "⚠️ Composer binary not found at expected location<br>";
    }
    
    // Try to bootstrap Laravel to regenerate cache
    echo "<h2>Bootstrap Laravel to Regenerate Cache</h2>";
    try {
        $app = require_once $basePath . '/bootstrap/app.php';
        echo "✅ Laravel bootstrap successful<br>";
        
        // Force service provider discovery
        $app->make('Illuminate\Foundation\PackageManifest')->build();
        echo "✅ Package manifest rebuilt<br>";
        
    } catch (Exception $e) {
        echo "⚠️ Could not bootstrap Laravel: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><strong>✅ Fix applied! Try visiting your website now.</strong><br>";
echo "<br><strong>⚠️ Remember to DELETE this file for security!</strong>";
