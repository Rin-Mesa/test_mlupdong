<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate the exact login request
$request = new Illuminate\Http\Request();
$request->merge([
    'email' => 'admin@mlupdong.com',
    'password' => 'password',
    'role' => 'admin'
]);

echo "=== DEBUG LOGIN ===\n";
echo "Request data:\n";
echo "  Email: " . $request->email . "\n";
echo "  Password: " . $request->password . "\n";
echo "  Role: " . $request->role . "\n\n";

// Step 1: Check validation
try {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'role' => 'required|in:admin,chef,waiter'
    ]);
    echo "✓ Validation passed\n";
} catch (Exception $e) {
    echo "✗ Validation failed: " . $e->getMessage() . "\n";
    exit;
}

// Step 2: Find user
$user = App\Models\User::where('email', $request->email)->first();
if ($user) {
    echo "✓ User found: " . $user->email . "\n";
    echo "  User ID: " . $user->id . "\n";
    echo "  User Role: " . $user->role . "\n";
    echo "  Password Hash: " . substr($user->password, 0, 20) . "...\n";
} else {
    echo "✗ User not found\n";
    exit;
}

// Step 3: Check password
if (\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
    echo "✓ Password check passed\n";
} else {
    echo "✗ Password check failed\n";
    exit;
}

// Step 4: Check role
if ($user->role !== $request->role) {
    echo "✗ Role mismatch: User role is '" . $user->role . "', requested role is '" . $request->role . "'\n";
    exit;
} else {
    echo "✓ Role check passed\n";
}

// Step 5: Create token
try {
    $token = $user->createToken('staff-token')->plainTextToken;
    echo "✓ Token created: " . substr($token, 0, 30) . "...\n";
    
    echo "\n=== LOGIN SUCCESS ===\n";
    echo "User: " . $user->name . " (" . $user->role . ")\n";
    echo "Token: " . $token . "\n";
    
} catch (Exception $e) {
    echo "✗ Token creation failed: " . $e->getMessage() . "\n";
    echo "Check if Sanctum is properly configured and migrations are run.\n";
}
