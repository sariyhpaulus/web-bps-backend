<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Login method to authenticate users
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Email atau password salah'], 401);
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat login.',
                'error' => $e->getMessage(), // Untuk debug. Hapus di produksi
            ], 500);
        }
    }

    // Logout method to revoke current token
    public function logout(Request $request)
    {
        // Revoke the current user's token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
            'status' => 'success'
        ]);
    }

    // Get current authenticated user
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'status' => 'success'
        ]);
    }
}
