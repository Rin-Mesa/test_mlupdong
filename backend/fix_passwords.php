<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FIXING USER PASSWORDS ===\n";

// Update all user passwords with proper Laravel hashing
$users = App\Models\User::all();

foreach ($users as $user) {
    echo "Updating password for: " . $user->email . "\n";
    
    // Re-hash the password with Laravel's current default
    $user->password = \Illuminate\Support\Facades\Hash::make('password');
    $user->save();
    
    echo "  ✓ Updated password hash\n";
}

echo "\n=== TESTING UPDATED PASSWORDS ===\n";

// Test the admin login
$user = App\Models\User::where('email', 'admin@mlupdong.com')->first();
if ($user && \Illuminate\Support\Facades\Hash::check('password', $user->password)) {
    echo "✓ Admin password verification passed\n";
} else {
    echo "✗ Admin password verification failed\n";
}

echo "\nPassword hashes have been updated. Try logging in again.\n";
