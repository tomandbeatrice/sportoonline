<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 'buyer'
        ]);
        return response()->json($user);
    }

    public function applySeller(Request $request) {
        $user = auth()->user();
        if ($user) {
            $user->applySeller();
            return response()->json(['success' => true, 'role' => $user->role]);
        }
        return response()->json(['success' => false], 401);
    }
}