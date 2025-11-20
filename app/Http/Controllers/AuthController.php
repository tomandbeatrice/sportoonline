<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SellerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Kullanıcıyı bul
        $user = User::where('email', $request->email)->first();

        // Şifre kontrolü
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email veya şifre hatalı'
            ], 401);
        }

        // Token oluştur
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'buyer'
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Çıkış yapıldı'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function applySeller(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'business_description' => 'required|string',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'tax_number' => 'nullable|string|max:50',
            'bank_account' => 'nullable|string|max:100'
        ]);

        $user = $request->user();
        
        // Zaten başvuru var mı kontrol et
        $existingApplication = SellerApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingApplication) {
            return response()->json([
                'message' => 'Zaten beklemede olan bir başvurunuz var.'
            ], 409);
        }

        $application = SellerApplication::create([
            'user_id' => $user->id,
            'store_name' => $validated['store_name'],
            'business_description' => $validated['business_description'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'tax_number' => $validated['tax_number'] ?? null,
            'bank_account' => $validated['bank_account'] ?? null,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Başvurunuz alındı. İnceleme süreci başlatıldı.',
            'application' => $application
        ]);
    }
}
