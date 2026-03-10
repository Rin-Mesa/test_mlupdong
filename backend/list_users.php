<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::all();
echo "=== USERS IN DATABASE ===\n";
foreach($users as $user) {
    echo 'ID: '.$user->id.', Email: '.$user->email.', Role: '.$user->role."\n";
}
echo "Total users: " . $users->count() . "\n";
