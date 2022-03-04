<?php

namespace App\Http\Controllers;

use App\Exceptions\UserAlreadyRegisteredException;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\ResendVerificationCodeRequest;
use App\Http\Requests\Auth\VerifyUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use SiteHelper;
use Symfony\Component\Console\Input\Input;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->username)->orWhere('mobile', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }


        return response(['token' => $user->createToken($request->token_name)->plainTextToken], 200);


        /* if (Auth::attempt($credentials)) {
            // $request->session()->regenerate();

            $token = $request->user()->createToken($request->token_name);

            return ['token' => $token->plainTextToken];
            // return redirect()->intended('/');
        } */

        /* return response([
            'status' => false,
            'email' => 'The provided credentials do not math our records.'
        ], 401); */
    }

    public function register(RegisterUserRequest $request)
    {
        $field = $request->getFieldName();
        $value = $request->getFieldValue();

        $code = SiteHelper::generateVerificationCode();

        $user = User::where($field, $value)->first();

        if ($user) {
            if ($user->verified_at) {
                throw new UserAlreadyRegisteredException('شما قبلا ثبت نام کرده اید.');
            }

            if ($user->updated_at < now()->subMinutes(5)) {
                //TODO: Send another verification code to user

                Log::info('USER-REGISTERATION-CODE', ['code' => $code]);

                $user->verify_code = $code;
                $user->save();

                // $response = $this->sendCode($user, $code);
                return response(['message' => 'کد جدید برای شما ارسال شد'], 200);
            } else {
                return response(['message' => 'کد فعالسازی قبلا برای شما ارسال شده است'], 200);
            }
        }

        $user = User::create([
            $field => $value,
            'verify_code' => $code,
        ]);

        Log::info('USER-REGISTERATION-CODE', ['code' => $code]);

        return response(['message' => 'اطلاعات شما ثبت و کد فعالسازی برای شما ارسال شد.'], 200);
    }



    public function registerVerify(VerifyUserRequest $request)
    {
        $code = $request->code;

        $field = $request->getFieldName();
        $value = $request->getFieldValue();

        $user = User::where([
            'verify_code' => $code,
            $field => $value
        ])->first();

        if (!$user) {
            throw new ModelNotFoundException('کاربری با کد مورد نظر پیدا نشد.');
        }

        $user->verified_at = now();
        $user->verify_code = null;
        $user->save();

        return response($user, 200);
    }

    public function resendVerificationCode(ResendVerificationCodeRequest $request)
    {
        $field = $request->getFieldName();
        $value = $request->getFieldValue();

        $user = User::where($field, $value)->whereNull('verified_at')->first();

        if ($user) {
            $diffTime = now()->diffInMinutes($user->updated_at);

            if ($diffTime > config('auth.send_verification_code_time', 30)) {
                $user->verify_code = SiteHelper::generateVerificationCode();
                $user->save();
            }

            Log::info('RESEND-USER-REGISTERATION-CODE', ['code' => $user->verify_code]);

            return response(['message' => 'کد فعالسازی دوباره ارسال شد.'], 200);
        }


        throw new ModelNotFoundException('کاربر تایید نشده ای با این مشخصات پیدا نشد.');
    }
}
