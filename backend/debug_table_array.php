<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Debug the table array conversion
$tables = App\Models\Table::with(['activeOrder.items.menuItem'])->get();

echo "=== DEBUG TABLE ARRAY ===\n";

$firstTable = $tables->first();
echo "First table ID: " . $firstTable->id . "\n";

$tableArray = $firstTable->toArray();
echo "Array keys:\n";
foreach (array_keys($tableArray) as $key) {
    echo "  - $key\n";
}

echo "\nActive Order exists: " . ($firstTable->activeOrder ? "Yes" : "No") . "\n";

// Check if active_order is in the array
if (array_key_exists('active_order', $tableArray)) {
    echo "✓ active_order key exists in array\n";
} else {
    echo "✗ active_order key missing from array\n";
    
    // Try to manually add it
    $tableArray['active_order'] = $firstTable->activeOrder ? $firstTable->activeOrder->toArray() : null;
    echo "Manually added active_order\n";
    
    echo "Updated array keys:\n";
    foreach (array_keys($tableArray) as $key) {
        echo "  - $key\n";
    }
}
