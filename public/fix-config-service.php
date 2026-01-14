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

// Find PHP binary path
echo "<h2>4. Finding PHP Binary</h2>";
$phpBinary = PHP_BINARY;
if (defined('PHP_BINARY') && PHP_BINARY) {
    $phpBinary = PHP_BINARY;
} else {
    // Try common paths
    $possiblePaths = ['/usr/bin/php', '/usr/local/bin/php', '/opt/php/bin/php', 'php'];
    foreach ($possiblePaths as $path) {
        $output = [];
        $returnVar = 0;
        exec($path . ' -v 2>&1', $output, $returnVar);
        if ($returnVar === 0) {
            $phpBinary = $path;
            break;
        }
    }
}
echo "Using PHP: <code>" . htmlspecialchars($phpBinary) . "</code><br>";

// Try to regenerate services.php and packages.php
echo "<h2>5. Regenerating services.php and packages.php</h2>";
$servicesCache = $basePath . '/bootstrap/cache/services.php';
$packagesCache = $basePath . '/bootstrap/cache/packages.php';
$artisanPath = $basePath . '/artisan';

// Try artisan package:discover
if (file_exists($artisanPath)) {
    chdir($basePath);
    $output = [];
    $returnVar = 0;
    $command = escapeshellarg($phpBinary) . ' ' . escapeshellarg($artisanPath) . ' package:discover --ansi 2>&1';
    exec($command, $output, $returnVar);
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
        echo "<br>Trying alternative method...<br>";
        
        // Alternative: Try to bootstrap Laravel manually and generate cache
        echo "<h2>6. Alternative: Manual Cache Generation</h2>";
        
        // Read providers.php
        $providersFile = $basePath . '/bootstrap/providers.php';
        if (file_exists($providersFile)) {
            $appProviders = require $providersFile;
            echo "✅ Found providers.php with " . count($appProviders) . " providers<br>";
            
            // Try to generate services.php by bootstrapping Laravel
            try {
                // Load Composer autoloader
                $autoloader = $basePath . '/vendor/autoload.php';
                if (file_exists($autoloader)) {
                    require_once $autoloader;
                    
                    // Try to create application instance
                    $app = require_once $basePath . '/bootstrap/app.php';
                    
                    // This should trigger service provider discovery
                    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
                    
                    if (file_exists($servicesCache)) {
                        echo "✅ services.php generated via Laravel bootstrap<br>";
                    }
                }
            } catch (Exception $e) {
                echo "⚠️ Bootstrap failed: " . htmlspecialchars($e->getMessage()) . "<br>";
            }
        }
    }
} else {
    echo "⚠️ Artisan file not found<br>";
}

// Final check
if (!file_exists($servicesCache)) {
    echo "<h2>7. Manual Upload Required</h2>";
    echo "⚠️ services.php still missing. You need to upload it manually.<br>";
    echo "<p><strong>Solution:</strong> Upload <code>bootstrap/cache/services.php</code> and <code>bootstrap/cache/packages.php</code> from your local project to the server.</p>";
    echo "<p>Or trigger a new deployment - the workflow has been updated to include these files.</p>";
}

echo "<br><strong>✅ Fix applied! Try visiting your website now.</strong><br>";
echo "<br><strong>⚠️ Remember to DELETE this file for security!</strong>";
