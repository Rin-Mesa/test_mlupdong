<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== AVAILABLE ROUTES ===\n";

// Get all routes
$routes = app('router')->getRoutes();

foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'login') !== false) {
        $methods = implode(', ', $route->methods());
        echo "Method: $methods | URI: $uri\n";
    }
}

echo "\n=== ALL API ROUTES ===\n";
foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'api') !== false) {
        $methods = implode(', ', $route->methods());
        echo "Method: $methods | URI: $uri\n";
    }
}
