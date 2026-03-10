<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing login functionality...\n";

// Test 1: Check if user exists
$user = App\Models\User::where('email', 'admin@mlupdong.com')->first();
if ($user) {
    echo "✓ User found: " . $user->email . "\n";
    echo "  Role: " . $user->role . "\n";
    echo "  Password check: " . (\Illuminate\Support\Facades\Hash::check('password', $user->password) ? 'PASS' : 'FAIL') . "\n";
} else {
    echo "✗ User not found\n";
}

// Test 2: Simulate login request
$request = new Illuminate\Http\Request();
$request->merge([
    'email' => 'admin@mlupdong.com',
    'password' => 'password',
    'role' => 'admin'
]);

try {
    $controller = new App\Http\Controllers\AuthController();
    $response = $controller->login($request);
    echo "✓ Login successful\n";
    echo "  Token: " . substr($response->getData()->token, 0, 20) . "...\n";
} catch (Illuminate\Validation\ValidationException $e) {
    echo "✗ Login failed: " . $e->getMessage() . "\n";
    print_r($e->errors());
} catch (Exception $e) {
    echo "✗ Login error: " . $e->getMessage() . "\n";
}
