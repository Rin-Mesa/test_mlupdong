<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the actual API endpoint
$request = Illuminate\Http\Request::create('/api/login', 'POST', [
    'email' => 'admin@mlupdong.com',
    'password' => 'password',
    'role' => 'admin'
]);

$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');

try {
    $response = $kernel->handle($request);
    
    echo "=== API ENDPOINT TEST ===\n";
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Response Content:\n";
    echo $response->getContent() . "\n";
    
    $kernel->terminate($request, $response);
    
} catch (Exception $e) {
    echo "✗ API Test Failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
