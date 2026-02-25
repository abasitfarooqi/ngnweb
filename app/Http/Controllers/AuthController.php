<?php

// File: app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        \Log::info('API: Login attempt', ['email' => $request->email]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            \Log::warning('API: Invalid credentials', ['email' => $request->email]);

            return response()->json([
                'message' => 'Invalid credentials provided.',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('api-token')->plainTextToken;

        \Log::info('API: Login successful', ['user_id' => $user->id, 'token' => $token]);

        return response()->json([
            'message' => 'Login successful',
            'user_id' => $user->id,
            'full_name' => $user->first_name.' '.$user->last_name,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }
}
