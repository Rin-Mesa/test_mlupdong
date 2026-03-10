<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test the tables API and check structure
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/tables');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/json'
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "=== TABLES API STRUCTURE ===\n";
echo "HTTP Code: $http_code\n";

if ($http_code === 200) {
    $data = json_decode($response, true);
    echo "Tables count: " . count($data) . "\n\n";
    
    if (!empty($data)) {
        $first_table = $data[0];
        echo "First table structure:\n";
        foreach ($first_table as $key => $value) {
            if (is_array($value)) {
                echo "  $key: [array with " . count($value) . " items]\n";
            } else {
                $display_value = is_string($value) && strlen($value) > 50 ? substr($value, 0, 50) . "..." : $value;
                echo "  $key: $display_value\n";
            }
        }
        
        // Check for active_order specifically
        if (isset($first_table['active_order'])) {
            echo "\n✓ active_order field exists\n";
            if ($first_table['active_order']) {
                echo "  active_order is not null\n";
            } else {
                echo "  active_order is null\n";
            }
        } else {
            echo "\n✗ active_order field missing\n";
        }
    }
} else {
    echo "API Error: $response\n";
}
