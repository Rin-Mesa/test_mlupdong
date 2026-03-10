<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::all();
foreach ($users as $user) {
    echo "ID: " . $user->id . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Check 'password': " . (\Illuminate\Support\Facades\Hash::check('password', $user->password) ? 'true' : 'false') . "\n";
    echo "---\n";
}
