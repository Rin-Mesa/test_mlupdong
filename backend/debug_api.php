<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Add a simple debug route
use Illuminate\Support\Facades\Route;

Route::get('/api/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now(),
        'server' => $_SERVER['HTTP_HOST'] ?? 'unknown'
    ]);
});

// Test the login endpoint directly
Route::post('/api/test-login', function () {
    $request = new Illuminate\Http\Request();
    $request->merge([
        'email' => 'admin@mlupdong.com',
        'password' => 'password',
        'role' => 'admin'
    ]);

    try {
        $controller = new App\Http\Controllers\AuthController();
        $response = $controller->login($request);
        return response()->json([
            'success' => true,
            'data' => $response->getData()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

echo "Debug API endpoints added. Visit:\n";
echo "- GET http://localhost:8000/api/test\n";
echo "- POST http://localhost:8000/api/test-login\n";
