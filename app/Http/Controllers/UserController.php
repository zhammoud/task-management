<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function currentUser(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'result' => "success",
            'user' => new UserResource($user)
        ]);
    }

    public function register(UserRegisterRequest $request)
    {
        $validated = $request->validated();
        $email = $validated['email'];
        $password = $validated['password'];
        $name = $validated['name'];
        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        if (!$user) {
            return response()->json([
                "result" => "error",
                "message" => "Something went wrong, Error 10025"
            ], 500);
        }
        return response()->json([
            'result' => "success",
            'user' => new UserResource($user)
        ]);
    }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->validated();
        $email = $credentials['email'];
        $password = $credentials['password'];
        $deviceName = $credentials['device_name'];

        $user = User::query()->where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->getAuthPassword())) {
            return response()->json([
                'result' => 'error',
                'message' => 'Invalid email or password'
            ], 400);
        }
        $token = $user->createToken($deviceName, ['*'], now()->addDay())->plainTextToken;
        return response()->json([
            'result' => 'success',
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'result' => 'success',
            'message' => 'Logged out successfully',
        ]);
    }


}
