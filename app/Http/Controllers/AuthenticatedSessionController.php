<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    use ApiResponse;

    /**
     * Create and store an auth token.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return $this->error(422, "Please enter valid password.");
        }

        $user = User::where([
            'email' => $credentials['email'],
        ])->first();
        $token = $user->createToken($user->name)->plainTextToken;

        return $this->success(
            data:[
                'access_token' => $token,
                'user' => new UserResource($user),
            ],
            message: "User logged in successfully!",
        );
    }

    /**
     * Remove an auth token
     */
    public function destroy(Request $request):JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->noContent("User Log out sucessfully!");
    }

    public function loggedUser(): JsonResponse {
        return $this->success(
            data: new UserResource(auth()->user()),
            message: "I am in the zone!!!",
        );
    }
}
