<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required', 
            'password' => 'required',
        ]);

        $identifier = $validated['identifier'];
        if (preg_match('/^\d+$/', $identifier)) { 
            if (substr($identifier, 0, 1) === '0') {
                $formattedNumber = '+963' . substr($identifier, 1);
            } else {
                $formattedNumber = $identifier;
            }
            $identifier = $formattedNumber;
        }

        $user = User::where('email', $identifier)
                    ->orWhere('phone_number', $identifier)
                    ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401); // Unauthorized
        }

        if ($user->role_id !== 3) {
            return response()->json([
                'message' => 'Access denied. You do not have the required role.',
            ], 403); // Forbidden
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ], 200); // OK
    }

    public function logout(Request $request)
    {

        
        $request->user()->tokens()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully',
        ], 200); // OK
    }
  
    public function userProfile(Request $request)
    {
        return response()->json([
            'message' => 'User profile fetched successfully',
            'user' => $request->user(),
        ], 200); // OK
    }
}
