<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::all();
foreach ($users as $user) {
    echo "{$user->id} | {$user->email} | {$user->role} | " . (\Illuminate\Support\Facades\Hash::check('password', $user->password) ? 'true' : 'false') . "\n";
}
