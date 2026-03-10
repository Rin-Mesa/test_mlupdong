<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test which route handles the request
$request = Illuminate\Http\Request::create('/api/login', 'POST', [
    'email' => 'admin@mlupdong.com',
    'password' => 'password',
    'role' => 'admin'
]);

$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');

try {
    $route = $app['router']->getRoutes()->match($request);
    echo "Route matched:\n";
    echo "URI: " . $route->uri() . "\n";
    echo "Action: " . $route->getActionName() . "\n";
    echo "Methods: " . implode(', ', $route->methods()) . "\n";
    echo "Middleware: " . implode(', ', $route->middleware()) . "\n";
} catch (Exception $e) {
    echo "No route found: " . $e->getMessage() . "\n";
}
