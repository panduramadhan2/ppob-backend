<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function AuthLogin(Request $request)
    {


        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // Ambil user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Unauthorized! User tidak ditemukan'
            ], 401);
        }

        // Buat token untuk autentikasi
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json(['message' => 'Login successfull', 'data' => ['users' => $user, 'token' => $token]], 200);
    }

    public function AuthLogout(Request $request)
    {
        Auth::user()->tokens()->delete();
        return response()->json('Logout successfull');
    }

    // public function AuthLogout(Request $request)
    // {
    //     // Cek apakah ada user yang sedang login
    //     $user = Auth::user();

    //     if ($user) {
    //         // Hapus token autentikasi yang digunakan oleh user
    //         $user->tokens->each(function ($token) {
    //             $token->delete();
    //         });

    //         return response()->json([
    //             'message' => 'Logout successful',
    //         ], 200);
    //     }

    //     return response()->json([
    //         'message' => 'No authenticated user',
    //     ], 401);
    // }
}
