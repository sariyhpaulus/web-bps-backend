# API Authentication Documentation - Laravel 12

## Overview
API ini menggunakan Laravel Sanctum untuk autentikasi token-based. Semua endpoint yang memerlukan autentikasi harus menyertakan header `Authorization: Bearer {token}`.

## Base URL
```
http://localhost:8000/api
```

## Endpoints

### 1. Login
**POST** `/login`

Mengautentikasi user dan mengembalikan token akses.

#### Request Body
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

#### Response Success (200)
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "email_verified_at": "2024-01-01T00:00:00.000000Z",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "token": "1|abc123def456ghi789..."
}
```

#### Response Error (401)
```json
{
    "message": "Login gagal"
}
```

### 2. Logout
**POST** `/logout`

Menghapus token autentikasi yang sedang aktif.

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

#### Response Success (200)
```json
{
    "message": "Logout berhasil",
    "status": "success"
}
```

#### Response Error (401)
```json
{
    "message": "Unauthenticated."
}
```

### 3. Get Current User
**GET** `/me`

Mengambil data user yang sedang terautentikasi.

#### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

#### Response Success (200)
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "email_verified_at": "2024-01-01T00:00:00.000000Z",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "status": "success"
}
```

#### Response Error (401)
```json
{
    "message": "Unauthenticated."
}
```

## Protected Endpoints

Semua endpoint di bawah ini memerlukan autentikasi:

### Publikasi
- **GET** `/publikasi` - Mengambil daftar publikasi
- **POST** `/publikasi` - Membuat publikasi baru

## Cara Penggunaan

### 1. Login untuk Mendapatkan Token
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

### 2. Gunakan Token untuk Request Terproteksi
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abc123def456ghi789..."
```

### 3. Logout untuk Menghapus Token
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abc123def456ghi789..."
```

## JavaScript/Fetch Example

### Login
```javascript
fetch('http://localhost:8000/api/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        email: 'user@example.com',
        password: 'password123'
    })
})
.then(response => response.json())
.then(data => {
    const token = data.token;
    // Simpan token untuk digunakan di request selanjutnya
    localStorage.setItem('auth_token', token);
});
```

### Request dengan Token
```javascript
const token = localStorage.getItem('auth_token');

fetch('http://localhost:8000/api/me', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
    }
})
.then(response => response.json())
.then(data => console.log(data));
```

### Logout
```javascript
const token = localStorage.getItem('auth_token');

fetch('http://localhost:8000/api/logout', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
    }
})
.then(response => response.json())
.then(data => {
    // Hapus token dari localStorage
    localStorage.removeItem('auth_token');
    console.log('Logout berhasil');
});
```

## Testing

### 1. Menggunakan File HTML Test
Buka browser dan akses: `http://localhost:8000/test-auth.html`

### 2. Menggunakan Postman
1. Import collection dengan endpoint yang sudah didefinisikan
2. Set environment variable untuk token
3. Test setiap endpoint secara berurutan

### 3. Menggunakan cURL
```bash
# Test login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

# Test logout (ganti TOKEN dengan token yang didapat dari login)
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json"
```

## Keamanan

### Token Management
- Token akan otomatis dihapus saat logout
- Token memiliki masa berlaku (default: tidak ada expiry)
- Setiap user bisa memiliki multiple token aktif

### CORS Configuration
- CORS sudah dikonfigurasi untuk development
- Untuk production, sesuaikan `allowed_origins` di `config/cors.php`

### Rate Limiting
- API menggunakan throttle middleware
- Default: 60 requests per minute per user

## Error Handling

### Common Error Codes
- `401 Unauthorized` - Token tidak valid atau tidak ada
- `422 Unprocessable Entity` - Validasi gagal
- `500 Internal Server Error` - Error server

### Error Response Format
```json
{
    "message": "Error description",
    "errors": {
        "field": ["Error message"]
    }
}
```

## Implementation Details

### AuthController Methods
```php
// Login
public function login(Request $request)

// Logout - menghapus token aktif
public function logout(Request $request)

// Get current user
public function me(Request $request)
```

### Middleware
- `auth:sanctum` - Memastikan user terautentikasi
- `throttle:api` - Rate limiting

### Database
- Token disimpan di tabel `personal_access_tokens`
- Token dihapus saat logout menggunakan `currentAccessToken()->delete()` 