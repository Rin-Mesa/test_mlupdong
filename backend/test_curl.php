<?php

// Test the API endpoint using PHP cURL
$ch = curl_init();

$data = array(
    'email' => 'admin@mlupdong.com',
    'password' => 'password',
    'role' => 'admin'
);

$json_data = json_encode($data);

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($json_data)
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);

curl_close($ch);

echo "=== API TEST ===\n";
echo "HTTP Code: $http_code\n";
echo "Response: $response\n";
if ($curl_error) {
    echo "cURL Error: $curl_error\n";
}
