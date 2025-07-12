<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublikasiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Debug routes
Route::get('/debug', function () {
    return response()->json([
        'message' => 'API routes are working!',
        'timestamp' => now(),
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
        'laravel_version' => app()->version()
    ]);
});

Route::get('/routes', function () {
    $routes = [];
    foreach (Route::getRoutes() as $route) {
        $routes[] = [
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName()
        ];
    }
    return response()->json(['routes' => $routes]);
});

// Test CORS route
Route::get('/test-cors', function () {
    return response()->json([
        'message' => 'CORS is working!',
        'timestamp' => now(),
        'status' => 'success'
    ]);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Publikasi
    Route::get('/publikasi', [PublikasiController::class, 'index']);
    Route::post('/publikasi', [PublikasiController::class, 'store']);
});