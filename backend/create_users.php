<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create default users
$users = [
    [
        'name' => 'Admin User',
        'email' => 'admin@mlupdong.com',
        'password' => Hash::make('password'),
        'role' => 'admin'
    ],
    [
        'name' => 'Chef User',
        'email' => 'chef@mlupdong.com',
        'password' => Hash::make('password'),
        'role' => 'chef'
    ],
    [
        'name' => 'Waiter User',
        'email' => 'waiter@mlupdong.com',
        'password' => Hash::make('password'),
        'role' => 'waiter'
    ]
];

foreach ($users as $userData) {
    $user = App\Models\User::create($userData);
    echo "Created user: " . $user->email . " (Role: " . $user->role . ")\n";
}

echo "\n=== USERS CREATED ===\n";
echo "Login credentials:\n";
echo "Admin: admin@mlupdong.com / password\n";
echo "Chef: chef@mlupdong.com / password\n";
echo "Waiter: waiter@mlupdong.com / password\n";
