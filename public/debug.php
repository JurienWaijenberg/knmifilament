<?php
/**
 * Laravel Debug Script
 * Upload this to your public directory to diagnose 500 errors
 * DELETE THIS FILE after debugging for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Laravel Debug Information</h1>";

// Check if vendor directory exists
$vendorPath = __DIR__ . '/../vendor/autoload.php';
echo "<h2>1. Vendor Directory</h2>";
if (file_exists($vendorPath)) {
    echo "✅ Vendor directory exists<br>";
    require $vendorPath;
} else {
    echo "❌ Vendor directory NOT found at: $vendorPath<br>";
    echo "This means composer install was not run or vendor files were not uploaded.<br>";
    exit;
}

// Check .env file
echo "<h2>2. Environment File</h2>";
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    echo "✅ .env file exists<br>";
    $envContent = file_get_contents($envPath);
    if (strpos($envContent, 'APP_KEY=') !== false) {
        $keyLine = preg_match('/APP_KEY=(.+)/', $envContent, $matches);
        if (!empty($matches[1]) && $matches[1] !== '') {
            if (strpos($matches[1], 'base64:') === 0) {
                echo "✅ APP_KEY is set and starts with base64:<br>";
            } else {
                echo "⚠️ APP_KEY exists but doesn't start with base64:<br>";
            }
        } else {
            echo "❌ APP_KEY is empty<br>";
        }
    } else {
        echo "❌ APP_KEY not found in .env<br>";
    }
} else {
    echo "❌ .env file NOT found at: $envPath<br>";
}

// Check storage permissions
echo "<h2>3. Storage Permissions</h2>";
$storagePath = __DIR__ . '/../storage';
if (is_dir($storagePath)) {
    echo "✅ Storage directory exists<br>";
    if (is_writable($storagePath)) {
        echo "✅ Storage directory is writable<br>";
    } else {
        echo "❌ Storage directory is NOT writable (permissions: " . substr(sprintf('%o', fileperms($storagePath)), -4) . ")<br>";
        echo "Should be 755 or 775<br>";
    }
} else {
    echo "❌ Storage directory NOT found<br>";
}

// Check bootstrap/cache permissions
echo "<h2>4. Bootstrap Cache Permissions</h2>";
$bootstrapCachePath = __DIR__ . '/../bootstrap/cache';
if (is_dir($bootstrapCachePath)) {
    echo "✅ Bootstrap cache directory exists<br>";
    if (is_writable($bootstrapCachePath)) {
        echo "✅ Bootstrap cache directory is writable<br>";
    } else {
        echo "❌ Bootstrap cache directory is NOT writable (permissions: " . substr(sprintf('%o', fileperms($bootstrapCachePath)), -4) . ")<br>";
        echo "Should be 755 or 775<br>";
    }
} else {
    echo "❌ Bootstrap cache directory NOT found<br>";
}

// Try to bootstrap Laravel
echo "<h2>5. Laravel Bootstrap Test</h2>";
try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "✅ Laravel bootstrap successful<br>";
    
    // Try to get config using the app instance
    try {
        $config = $app->make('config');
        $appName = $config->get('app.name');
        echo "✅ Config loaded successfully via app instance (APP_NAME: $appName)<br>";
        
        // Now try the helper function
        try {
            $appNameHelper = config('app.name');
            echo "✅ Config helper function works (APP_NAME: $appNameHelper)<br>";
        } catch (Exception $e) {
            echo "⚠️ Config helper function error: " . $e->getMessage() . "<br>";
            echo "But config via app instance works, so this might be a helper loading issue.<br>";
        }
    } catch (Exception $e) {
        echo "❌ Config error: " . $e->getMessage() . "<br>";
        echo "Error type: " . get_class($e) . "<br>";
    }
    
    // Try database connection
    try {
        $config = $app->make('config');
        $dbConnection = $config->get('database.default');
        echo "✅ Database config loaded (default: $dbConnection)<br>";
        
        // Try actual connection using the app instance
        try {
            $db = $app->make('db');
            $pdo = $db->connection()->getPdo();
            echo "✅ Database connection successful<br>";
        } catch (Exception $e) {
            echo "❌ Database connection error: " . $e->getMessage() . "<br>";
            echo "Error type: " . get_class($e) . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Database config error: " . $e->getMessage() . "<br>";
        echo "Error type: " . get_class($e) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "<br>";
    echo "Error type: " . get_class($e) . "<br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}

// Check for cached config files
echo "<h2>6. Cached Config Files</h2>";
$configCache = __DIR__ . '/../bootstrap/cache/config.php';
if (file_exists($configCache)) {
    echo "⚠️ Config cache file exists<br>";
    echo "This might be causing issues. Try deleting it.<br>";
    if (unlink($configCache)) {
        echo "✅ Deleted config cache file<br>";
    } else {
        echo "❌ Could not delete config cache file (permissions issue?)<br>";
    }
} else {
    echo "✅ No config cache file (this is good)<br>";
}

// Check error log
echo "<h2>7. Error Log</h2>";
$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logPath)) {
    $logSize = filesize($logPath);
    if ($logSize > 0) {
        echo "⚠️ Error log exists and has content ($logSize bytes)<br>";
        echo "Last 20 lines:<br><pre>";
        $lines = file($logPath);
        echo htmlspecialchars(implode('', array_slice($lines, -20)));
        echo "</pre>";
    } else {
        echo "✅ Error log exists but is empty<br>";
    }
} else {
    echo "ℹ️ Error log doesn't exist yet (this is normal if no errors occurred)<br>";
}

echo "<hr>";
echo "<p><strong>Remember to DELETE this file after debugging!</strong></p>";
