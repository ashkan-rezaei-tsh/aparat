<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\ResendVerificationCodeRequest;
use App\Http\Requests\Auth\VerifyUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return UserService::loginUser($request);
    }

    public function register(RegisterUserRequest $request)
    {
        return UserService::registerNewUser($request);
    }

    public function registerVerify(VerifyUserRequest $request)
    {
        return UserService::registerNewUserVerify($request);
    }

    public function resendVerificationCode(ResendVerificationCodeRequest $request)
    {
        return UserService::resendVerificationCodeToUser($request);
    }
}
