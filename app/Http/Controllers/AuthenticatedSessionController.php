<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated()['email'])
            ->get()
            ->first();

        if (empty($user->email)) {
            return response()->json([
                'status' => false,
                'message' => "Please enter valid email."
            ], 401);
        }
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'status' => false,
                'message' => "Please enter valid password."
            ], 401);
        }
        
        $token = $user->createToken($user->name)->plainTextToken;
        return response()->json([
            'status' => true,
            'data' => [
                'access_token' => $token,
                'usrer' => $user
            ],
            'message' => "Successfully logged in!"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request):Response
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
