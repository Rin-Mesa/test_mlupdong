<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Table Management
Route::get('/tables', [TableController::class, 'index']);
Route::post('/tables', [TableController::class, 'store']);
Route::put('/tables/{id}', [TableController::class, 'update']);
Route::delete('/tables/{id}', [TableController::class, 'destroy']);
Route::post('/tables/{id}/generate-qr', [TableController::class, 'generateQr']);
Route::get('/tables/{id}/qrcode', [TableController::class, 'getQr']);
Route::post('/tables/generate-all', [TableController::class, 'generateAllQRs']);

// Staff Management
use App\Http\Controllers\Admin\StaffController;
Route::get('/staff', [StaffController::class, 'index']);
Route::post('/staff', [StaffController::class, 'store']);
Route::put('/staff/{id}', [StaffController::class, 'update']);
Route::delete('/staff/{id}', [StaffController::class, 'destroy']);

// Analytics & Reports
use App\Http\Controllers\Admin\ReportController;
Route::get('/reports/stats', [ReportController::class, 'dashboardStats']);
Route::get('/reports/sales', [ReportController::class, 'salesReport']);
Route::get('/reports/best-sellers', [ReportController::class, 'bestSellers']);
Route::get('/reports/peak-hours', [ReportController::class, 'peakHours']);

// Menu Item Management
Route::get('/menu-items', [MenuItemController::class, 'index']);
Route::post('/menu-items', [MenuItemController::class, 'store']);
Route::put('/menu-items/{id}', [MenuItemController::class, 'update']);
Route::delete('/menu-items/{id}', [MenuItemController::class, 'destroy']);

// Customer Order Routes
use App\Http\Controllers\OrderController;
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::post('/orders/history', [OrderController::class, 'orderHistory']);

// Kitchen Routes
Route::get('/kitchen/orders', [OrderController::class, 'kitchenIndex']);
Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
Route::post('/orders/{id}/transfer', [OrderController::class, 'transferTable']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

// Table details for customer
use App\Http\Controllers\Admin\TableController as AdminTableController;
Route::get('/tables/token/{token}', [AdminTableController::class, 'showByToken']);

// QR Code API routes
use App\Http\Controllers\QRCodeController;
Route::prefix('qr')->group(function () {
    Route::post('/generate',        [QRCodeController::class, 'generate']);
    Route::post('/bulk',            [QRCodeController::class, 'bulk']);
    Route::get('/download/{table}', [QRCodeController::class, 'download']);
});
