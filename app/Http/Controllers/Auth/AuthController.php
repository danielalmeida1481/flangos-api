<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * Register a new user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request) {
        $validated = $request->validate([
            'name' => 'required|min:4|max:30',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $token = $user->createToken('authToken')->plainTextToken;

        return response(['message' => 'Registered successfully!', 'data' => [
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]], 200);
    }

    /**
     * Login a existing user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($validated)) {
            return response(['message' => 'Invalid login information!'], 401);
        }

        $user = User::whereEmail($validated['email'])->firstOrFail();
        $token = $user->createToken('authToken')->plainTextToken;

        return response(['message' => 'Logged in successfully!', 'data' => [
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]], 200);
    }

}
