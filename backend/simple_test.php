<?php

// Simple test to see what the API returns
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
    'Accept: application/json',
    'Content-Length: ' . strlen($json_data)
));
curl_setopt($ch, CURLOPT_HEADER, true); // Include headers

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);

curl_close($ch);

echo "=== SIMPLE API TEST ===\n";
echo "HTTP Code: $http_code\n";
echo "Full Response:\n";
echo $response . "\n";
echo "========================\n";

// Parse response
$header_size = strpos($response, "\r\n\r\n");
$headers = substr($response, 0, $header_size);
$body = substr($response, $header_size + 4);

echo "Headers:\n$headers\n";
echo "Body:\n$body\n";

if ($curl_error) {
    echo "cURL Error: $curl_error\n";
}
