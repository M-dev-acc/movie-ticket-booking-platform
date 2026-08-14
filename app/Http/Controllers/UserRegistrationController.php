<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRegistrationController extends Controller
{
    use ApiResponse;

    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterUserRequest $request)
    {
        $inputs = $request->validated();

        $user = User::create([
            'name' => $inputs['name'],
            'email' => $inputs['email'],
            'password' => Hash::make($inputs['password']),
        ]);

        $userRole = Role::where('name', 'user')
            ->first();
        $user->assignRole($userRole);

        $token = $user->createToken($user->name)->plainTextToken;

        return $this->success(
            data: [
                'access_token' => $token,
                'user' => new UserResource($user),
            ],
            message: "User registered successfully!"
        );
    }
}

