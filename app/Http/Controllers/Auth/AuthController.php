<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully.',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request){
        $credintial = $request->validate([
            'email'=>['required' , 'email'],
            'password'=>['required' , 'string'],
        ]);
        if(!Auth::attempt($credintial)) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successfully.',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    public function me(Request $request) {
    return response()->json(['user' => $request->user()]);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out successfully.'
        ], 200);
    }
    
}
