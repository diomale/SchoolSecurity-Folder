<?php

/**
 * Quick Cache Clear Script
 * 
 * Run this file to clear all Laravel caches:
 * - php clear-cache.php
 * 
 * Or access via browser (NOT recommended for production):
 * - http://localhost/ccsecurity-app/clear-cache.php
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Run as CLI or web
$isCli = php_sapi_name() === 'cli';

if ($isCli) {
    echo "========================================\n";
    echo "  Laravel Cache Clearer\n";
    echo "========================================\n\n";
} else {
    // Block web access for security
    http_response_code(403);
    echo "<h1>403 - Access Denied</h1>";
    echo "<p>For security reasons, this script can only be run from CLI.</p>";
    echo "<p>Run: <code>php clear-cache.php</code></p>";
    exit;
}

try {
    $app = \Illuminate\Foundation\Application::getInstance();
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    
    // Clear application cache
    echo "Clearing application cache...\n";
    $kernel->call('cache:clear');
    echo $kernel->output() . "\n";
    
    // Clear config cache
    echo "Clearing config cache...\n";
    $kernel->call('config:clear');
    echo $kernel->output() . "\n";
    
    // Clear route cache
    echo "Clearing route cache...\n";
    $kernel->call('route:clear');
    echo $kernel->output() . "\n";
    
    // Clear view cache
    echo "Clearing view cache...\n";
    $kernel->call('view:clear');
    echo $kernel->output() . "\n";
    
    // Clear event cache
    echo "Clearing event cache...\n";
    $kernel->call('event:clear');
    echo $kernel->output() . "\n";
    
    // Clear schedule cache
    echo "Clearing schedule cache...\n";
    $kernel->call('schedule:clear-cache');
    echo $kernel->output() . "\n";
    
    // Optimize (optional - comment out if not needed)
    // echo "Optimizing...\n";
    // $kernel->call('optimize');
    // echo $kernel->output() . "\n";
    
    echo "\n";
    echo "========================================\n";
    echo "  ✅ All caches cleared successfully!\n";
    echo "========================================\n";
    
} catch (\Exception $e) {
    echo "\n";
    echo "========================================\n";
    echo "  ❌ Error: " . $e->getMessage() . "\n";
    echo "========================================\n";
    exit(1);
}
