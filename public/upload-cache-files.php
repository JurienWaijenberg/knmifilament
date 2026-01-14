<?php
/**
 * Upload Cache Files Helper
 * This script helps you understand what needs to be uploaded
 * DELETE THIS FILE after fixing for security!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Cache Files Upload Helper</h1>";
echo "<p>This script shows you which cache files need to be uploaded to fix the config service issue.</p>";

$basePath = __DIR__ . '/..';
$servicesCache = $basePath . '/bootstrap/cache/services.php';
$packagesCache = $basePath . '/bootstrap/cache/packages.php';

echo "<h2>Required Cache Files</h2>";

if (file_exists($servicesCache)) {
    $size = filesize($servicesCache);
    echo "✅ <strong>bootstrap/cache/services.php</strong> exists ({$size} bytes)<br>";
    echo "<details><summary>View file path</summary>";
    echo "<code>" . htmlspecialchars($servicesCache) . "</code></details><br>";
} else {
    echo "❌ <strong>bootstrap/cache/services.php</strong> is missing!<br>";
}

if (file_exists($packagesCache)) {
    $size = filesize($packagesCache);
    echo "✅ <strong>bootstrap/cache/packages.php</strong> exists ({$size} bytes)<br>";
    echo "<details><summary>View file path</summary>";
    echo "<code>" . htmlspecialchars($packagesCache) . "</code></details><br>";
} else {
    echo "❌ <strong>bootstrap/cache/packages.php</strong> is missing!<br>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>If both files exist above, upload them to your server via FTP/SFTP to: <code>bootstrap/cache/</code></li>";
echo "<li>Or trigger a new deployment - the workflow has been updated to include these files</li>";
echo "<li>After uploading, delete this file and try accessing your site again</li>";
echo "</ol>";

echo "<h2>Alternative: Regenerate on Server</h2>";
echo "<p>If you have SSH access, you can run:</p>";
echo "<pre>cd /path/to/your/project\nphp artisan package:discover</pre>";

echo "<br><strong>⚠️ Remember to DELETE this file for security!</strong>";
