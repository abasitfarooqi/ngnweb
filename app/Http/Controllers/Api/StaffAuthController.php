<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ])) {
            return response()->json([
                'message' => 'Invalid credentials provided.',
            ], 401);
        }

        /** @var User $user */
        $user = User::query()->where('email', $validated['email'])->firstOrFail();
        if (! $this->isBackpackStaff($user)) {
            return response()->json([
                'message' => 'This account is not authorised for staff APIs.',
            ], 403);
        }

        $token = $user->createToken($validated['device_name'] ?: 'staff-mobile-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'role' => 'staff',
            'user' => [
                'id' => $user->id,
                'full_name' => trim((string) $user->full_name),
                'email' => $user->email,
                'is_admin' => (bool) $user->is_admin,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->staffUser($request);
        if (! $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'id' => $user->id,
            'full_name' => trim((string) $user->full_name),
            'email' => $user->email,
            'is_admin' => (bool) $user->is_admin,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $this->staffUser($request);
        if (! $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    private function staffUser(Request $request): ?User
    {
        $actor = $request->user('sanctum');
        if (! $actor instanceof User) {
            return null;
        }

        return $this->isBackpackStaff($actor) ? $actor : null;
    }

    private function isBackpackStaff(User $user): bool
    {
        if ((bool) $user->is_admin) {
            return true;
        }

        if (property_exists($user, 'is_client') && (bool) $user->is_client) {
            return false;
        }

        try {
            if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super-admin', 'manager', 'staff', 'support'])) {
                return true;
            }
        } catch (\Throwable) {
            // Fall through to permissive check below.
        }

        return true;
    }
}
