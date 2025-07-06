# Konfigurasi CORS Laravel 12.19.3

## Overview
Laravel 12 sudah memiliki built-in CORS support yang terintegrasi dengan framework. Tidak perlu menginstall package tambahan seperti `barryvdh/laravel-cors` yang diperlukan di versi Laravel sebelumnya.

## Konfigurasi yang Sudah Diterapkan

### 1. File Konfigurasi CORS (`config/cors.php`)
```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

### 2. Middleware CORS (`app/Http/Kernel.php`)
Middleware CORS sudah terdaftar di global middleware:
```php
protected $middleware = [
    \App\Http\Middleware\TrustProxies::class,
    \Illuminate\Http\Middleware\HandleCors::class, // ← CORS Middleware
    // ... middleware lainnya
];
```

### 3. Route Test CORS (`routes/api.php`)
```php
Route::get('/test-cors', function () {
    return response()->json([
        'message' => 'CORS is working!',
        'timestamp' => now(),
        'status' => 'success'
    ]);
});
```

## Penjelasan Konfigurasi

### Paths
- `'api/*'` - Semua route yang dimulai dengan `/api/`
- `'sanctum/csrf-cookie'` - Route untuk Laravel Sanctum

### Allowed Methods
- `['*']` - Mengizinkan semua HTTP methods (GET, POST, PUT, DELETE, PATCH, OPTIONS)

### Allowed Origins
- `['*']` - Mengizinkan semua domain (development)
- Untuk production, sebaiknya spesifik: `['https://yourdomain.com', 'https://app.yourdomain.com']`

### Allowed Headers
- `['*']` - Mengizinkan semua headers
- Bisa spesifik: `['Content-Type', 'Authorization', 'X-Requested-With']`

### Supports Credentials
- `false` - Tidak mengizinkan cookies/credentials
- Set `true` jika menggunakan cookies atau authentication

## Penggunaan

### 1. Jalankan Server Laravel
```bash
php artisan serve
```

### 2. Test CORS dengan File HTML
Buka browser dan akses: `http://localhost:8000/test-cors.html`

### 3. Test dengan cURL
```bash
# Test GET request
curl -X GET http://localhost:8000/api/test-cors \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"

# Test POST request
curl -X POST http://localhost:8000/api/test-cors \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"test": "data"}'
```

### 4. Test dengan JavaScript/Fetch
```javascript
fetch('http://localhost:8000/api/test-cors', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => console.log(data));
```

## Konfigurasi untuk Production

### 1. Restrict Origins
```php
'allowed_origins' => [
    'https://yourdomain.com',
    'https://app.yourdomain.com'
],
```

### 2. Restrict Methods
```php
'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
```

### 3. Restrict Headers
```php
'allowed_headers' => [
    'Content-Type',
    'Authorization',
    'X-Requested-With',
    'Accept'
],
```

### 4. Enable Credentials (jika diperlukan)
```php
'supports_credentials' => true,
```

## Troubleshooting

### 1. CORS Error di Browser
- Pastikan middleware `HandleCors` terdaftar di `Kernel.php`
- Periksa file `config/cors.php` ada dan benar
- Clear cache: `php artisan config:clear`

### 2. Preflight Request Gagal
- Pastikan method OPTIONS diizinkan
- Periksa `allowed_headers` mencakup header yang diperlukan

### 3. Credentials Tidak Terkirim
- Set `supports_credentials` ke `true`
- Pastikan frontend mengirim `credentials: 'include'`

## Referensi
- [Laravel 12 CORS Documentation](https://laravel.com/docs/12.x/http-client#cors)
- [MDN CORS Guide](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS)
- [Laravel Sanctum CORS](https://laravel.com/docs/12.x/sanctum#cors) 