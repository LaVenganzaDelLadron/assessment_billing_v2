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

        // Return a success response
        return response()->json(['message' => 'Registration successful'], 201);

    }


    public function login(LoginRequest $request)
    {
        // Validate the request data
        $credentials = $request->validated();

        // Attempt to authenticate the user
        if (auth()->attempt($credentials)) {
            // Authentication passed, return a success response
            return response()->json(['message' => 'Login successful'], 200);
        }

        // Authentication failed, return an error response
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
