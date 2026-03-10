<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DATABASE CONNECTION CHECK ===\n";
echo "DB Connection: " . config('database.default') . "\n";
echo "DB Host: " . config('database.connections.mysql.host') . "\n";
echo "DB Database: " . config('database.connections.mysql.database') . "\n";
echo "DB Username: " . config('database.connections.mysql.username') . "\n";

try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "✓ Database connection successful\n";
    
    // Check if users table exists
    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
    echo "Tables in database:\n";
    foreach($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "  - " . $tableName . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}
