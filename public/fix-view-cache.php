<?php
/**
 * Fix View Cache Path Issue
 * This will create the necessary directories and fix the view cache configuration
 * DELETE THIS FILE after running for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fixing View Cache Path</h1>";

$basePath = __DIR__ . '/..';

// Create storage/framework/views directory
$viewsPath = $basePath . '/storage/framework/views';
if (!is_dir($viewsPath)) {
    if (mkdir($viewsPath, 0755, true)) {
        echo "✅ Created storage/framework/views directory<br>";
    } else {
        echo "❌ Failed to create storage/framework/views directory<br>";
    }
} else {
    echo "✅ storage/framework/views directory exists<br>";
}

// Set permissions
if (is_dir($viewsPath)) {
    chmod($viewsPath, 0755);
    echo "✅ Set permissions on storage/framework/views to 755<br>";
}

// Ensure all storage subdirectories exist
$storageDirs = [
    'storage/framework/sessions',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/logs',
];

foreach ($storageDirs as $dir) {
    $fullPath = $basePath . '/' . $dir;
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✅ Created $dir directory<br>";
        }
    }
    if (is_dir($fullPath)) {
        chmod($fullPath, 0755);
    }
}

// Try to bootstrap Laravel and check config
echo "<h2>Checking View Configuration</h2>";
try {
    require $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    
    // Check view.compiled config
    try {
        $config = $app->make('config');
        $compiledPath = $config->get('view.compiled');
        echo "View compiled path from config: " . ($compiledPath ?: 'NOT SET') . "<br>";
        
        if (empty($compiledPath)) {
            echo "⚠️ view.compiled is not set in config<br>";
            echo "This should be: storage/framework/views<br>";
        } else {
            echo "✅ View compiled path is set<br>";
            
            // Check if the path exists
            $fullCompiledPath = $basePath . '/' . $compiledPath;
            if (is_dir($fullCompiledPath)) {
                echo "✅ View compiled directory exists at: $fullCompiledPath<br>";
            } else {
                echo "❌ View compiled directory does NOT exist at: $fullCompiledPath<br>";
                if (mkdir($fullCompiledPath, 0755, true)) {
                    echo "✅ Created view compiled directory<br>";
                }
            }
        }
    } catch (Exception $e) {
        echo "⚠️ Could not check config: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "⚠️ Could not bootstrap Laravel: " . $e->getMessage() . "<br>";
}

echo "<br><strong>✅ Fix applied! Try visiting your website now.</strong><br>";
echo "<br><strong>⚠️ Remember to DELETE this file for security!</strong>";
