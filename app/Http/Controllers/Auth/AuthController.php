<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        // Validate the request data
        $validatedData = $request->validated();

        // Create the user
        $user = User::create($validatedData);

        // Log the user in
        auth()->login($user);

        // Return a success response with user data
        return response()->json([
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 201);

    }

    public function login(LoginRequest $request)
    {
        // Validate the request data
        $credentials = $request->validated();

        // Attempt to authenticate the user
        if (auth()->attempt($credentials)) {
            $user = auth()->user();

            // Generate API token for stateless authentication
            $token = $user->createToken('api-token')->plainTextToken;

            // Return a success response with user data and token
            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token,
            ], 200);
        }

        // Authentication failed, return an error response
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
