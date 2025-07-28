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
     * Create and store an auth token.
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
     * Remove an auth token
     */
    public function destroy(Request $request):JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'status' => true,
            'data' => [],
            'message' => "Successfully logout!"
        ], 200);;
    }

    public function loggedUser(): JsonResponse {
        return response()->json([
            'status' => true,
            'data' => auth()->user()->toArray(),
            'message' => "I am in the zone!!!"
        ]);
    }
}
