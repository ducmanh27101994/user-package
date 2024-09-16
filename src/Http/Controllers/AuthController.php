<?php

namespace FmcExample\UserPackage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use FmcExample\UserPackage\services;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $jwtService;

    public function __construct(services\JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function register(Request $request)
    {
        $validate = $this->validateInput($request, [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
        ]);
        if (!$validate) return $validate;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Đăng ký thành công',
            'status' => 200
        ]);
    }

    public function login(Request $request)
    {
        $validate = $this->validateInput($request, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if (!$validate) return $validate;

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $payload['user_id'] = $user->id;
            $token = $this->jwtService->createToken($payload);

            return response()->json([
                'status' => 200,
                'data' => $user,
                'message' => 'Đăng nhập thành công',
                'token' => $token,
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'Unauthorized',
        ]);
    }

    private function validateInput(Request $request, array $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()
            ]);
        }

        return true;
    }
}
