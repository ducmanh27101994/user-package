<?php

namespace FmcExample\UserPackage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use FmcExample\UserPackage\services;

class AuthController extends Controller
{
    protected $jwtService;

    public function __construct(services\JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Đăng ký thành công'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $payload['user_id'] = $user->id;
            $token = $this->jwtService->createToken($payload);
            return response()->json(['data' => $user, 'message' => 'Đăng nhập thành công',  'token' => $token], 201);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
