<?php
/**
 * Fix 403 Forbidden After Login
 * This will diagnose and fix session/CSRF issues
 * DELETE THIS FILE after running for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fixing 403 Forbidden Issue</h1>";

$basePath = __DIR__ . '/..';

try {
    require $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    
    echo "<h2>1. Environment Check</h2>";
    $envPath = $basePath . '/.env';
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        
        // Check APP_URL
        if (preg_match('/APP_URL=(.+)/', $envContent, $matches)) {
            $appUrl = trim($matches[1]);
            echo "APP_URL: $appUrl<br>";
            if ($appUrl !== 'https://knmi.waijenbergmedia.nl') {
                echo "⚠️ APP_URL should be: https://knmi.waijenbergmedia.nl<br>";
            } else {
                echo "✅ APP_URL is correct<br>";
            }
        } else {
            echo "❌ APP_URL not found in .env<br>";
        }
        
        // Check SESSION_DRIVER
        if (preg_match('/SESSION_DRIVER=(.+)/', $envContent, $matches)) {
            $sessionDriver = trim($matches[1]);
            echo "SESSION_DRIVER: $sessionDriver<br>";
        } else {
            echo "SESSION_DRIVER: database (default)<br>";
        }
        
        // Check SESSION_SECURE_COOKIE
        if (preg_match('/SESSION_SECURE_COOKIE=(.+)/', $envContent, $matches)) {
            $secureCookie = trim($matches[1]);
            echo "SESSION_SECURE_COOKIE: $secureCookie<br>";
            if (strtolower($secureCookie) !== 'true') {
                echo "⚠️ SESSION_SECURE_COOKIE should be 'true' for HTTPS<br>";
            }
        } else {
            echo "⚠️ SESSION_SECURE_COOKIE not set (should be 'true' for HTTPS)<br>";
        }
    }
    
    echo "<h2>2. Session Storage Check</h2>";
    $config = $app->make('config');
    $sessionDriver = $config->get('session.driver', 'database');
    echo "Session driver: $sessionDriver<br>";
    
    if ($sessionDriver === 'database') {
        // Check if sessions table exists
        try {
            $db = $app->make('db');
            $tables = $db->select("SHOW TABLES LIKE 'sessions'");
            if (count($tables) > 0) {
                echo "✅ Sessions table exists<br>";
            } else {
                echo "❌ Sessions table does NOT exist<br>";
                echo "You need to run: php artisan session:table<br>";
                echo "Then: php artisan migrate<br>";
            }
        } catch (Exception $e) {
            echo "⚠️ Could not check database: " . $e->getMessage() . "<br>";
        }
    } else {
        // File driver - check directory
        $sessionPath = $basePath . '/storage/framework/sessions';
        if (is_dir($sessionPath)) {
            if (is_writable($sessionPath)) {
                echo "✅ Session directory exists and is writable<br>";
            } else {
                echo "❌ Session directory is NOT writable<br>";
                chmod($sessionPath, 0755);
                echo "✅ Set permissions to 755<br>";
            }
        } else {
            echo "❌ Session directory does NOT exist<br>";
            mkdir($sessionPath, 0755, true);
            echo "✅ Created session directory<br>";
        }
    }
    
    echo "<h2>3. Storage Permissions</h2>";
    $storageDirs = [
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/framework/cache',
        'storage/logs',
    ];
    
    foreach ($storageDirs as $dir) {
        $fullPath = $basePath . '/' . $dir;
        if (is_dir($fullPath)) {
            $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
            if (is_writable($fullPath)) {
                echo "✅ $dir (permissions: $perms) - writable<br>";
            } else {
                echo "❌ $dir (permissions: $perms) - NOT writable<br>";
                chmod($fullPath, 0755);
                echo "✅ Fixed permissions on $dir<br>";
            }
        } else {
            echo "⚠️ $dir does not exist<br>";
            mkdir($fullPath, 0755, true);
            echo "✅ Created $dir<br>";
        }
    }
    
    echo "<h2>4. Recommendations</h2>";
    echo "<p>Add these to your .env file if not already present:</p>";
    echo "<pre>";
    echo "APP_URL=https://knmi.waijenbergmedia.nl\n";
    echo "SESSION_DRIVER=database\n";
    echo "SESSION_SECURE_COOKIE=true\n";
    echo "SESSION_SAME_SITE=lax\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace:<br><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<hr>";
echo "<p><strong>Remember to DELETE this file after debugging!</strong></p>";
