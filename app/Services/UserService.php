<?php

namespace App\Services;

use App\Exceptions\UserAlreadyRegisteredException;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\ResendVerificationCodeRequest;
use App\Http\Requests\Auth\VerifyUserRequest;
use App\Http\Requests\User\ChangeEmailRequest;
use App\Http\Requests\User\ChangeEmailSubmitRequest;
use App\Http\Requests\user\ChangePasswordRequest;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use SiteHelper;

class UserService extends BaseService
{
    const CHANGE_EMAIL_CACHE_KEY = 'email-change-for-user-';


    /**
     * Login user
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ValidationException
     */
    public static function loginUser(Request $request)
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


    /**
     * Register New user
     *
     * @param RegisterUserRequest $request
     * @return response
     */
    public static function registerNewUser(RegisterUserRequest $request)
    {
        try {
            $field = $request->getFieldName();
            $value = $request->getFieldValue();

            $code = SiteHelper::generateVerificationCode();

            DB::beginTransaction();

            $user = User::where($field, $value)->first();

            if ($user) {
                if ($user->verified_at) {
                    throw new UserAlreadyRegisteredException('شما قبلا ثبت نام کرده اید');
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

            DB::commit();

            return response(['message' => 'اطلاعات شما ثبت و کد فعالسازی برای شما ارسال شد.'], 200);
        } catch (Exception $exception) {
            DB::rollBack();

            if ($exception instanceof UserAlreadyRegisteredException) {
                throw $exception;
            }

            Log::error($exception);
            return response(['message' => 'خطا در ثبت اطلاعات']);
        }
    }


    /**
     * Verfiying registered user
     *
     * @param VerifyUserRequest $request
     * @return Collection $user
     * @throws ModelNotFoundException
     */
    public static function registerNewUserVerify(VerifyUserRequest $request)
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

    /**
     * Resend Verification Code
     *
     * @param ResendVerificationCodeRequest $request
     * @return response
     * @throws ModelNotFoundException
     */
    public static function resendVerificationCodeToUser(ResendVerificationCodeRequest $request)
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

    /**
     * Change User's Email
     *
     * @param ChangeEmailRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public static function changeEmail(ChangeEmailRequest $request)
    {
        try {
            $userId = auth()->id();
            $cacheName = self::CHANGE_EMAIL_CACHE_KEY . $userId;
            $email = $request->email;
            $code = SiteHelper::generateVerificationCode();

            Cache::put($cacheName, compact('email', 'code'), now()->addMinutes(config('auth.change_email_cache_time', 1440)));

            return response(['message' => 'کد به ایمیل شما ارسال شد.'], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response(['message' => 'خطا در ارسال ایمیل'], 500);
        }
    }

    /**
     * Submit Changing User Email
     *
     * @param ChangeEmailSubmitRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public static function changeEmailSubmit(ChangeEmailSubmitRequest $request)
    {
        $code = $request->code;
        $cacheKey = self::CHANGE_EMAIL_CACHE_KEY . auth()->id();

        $cache = Cache::get($cacheKey);

        if (empty($cache) || $cache['code'] != $code) {
            return response(['message' => 'کد نامعتبر است'], 400);
        }

        $user = auth()->user();
        $user->email = $cache['email'];
        $user->save();

        Cache::forget($cacheKey);

        return response(['message' => 'ایمیل با موفقیت تغییر کرد.'], 200);
    }


    /**
     * Change user password
     *
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public static function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = auth()->user();

            if (!Hash::check($request->oldPassword, $user->password)) {
                return response(['message' => 'گذرواژه وارد شده مطابقت ندارد'], 400);
            }

            $user->update([
                'password' => $request->newPassword
            ]);

            return response(['message' => 'گذرواژه با موفقیت بروزرسانی شد']);
        } catch (Exception $exception) {
            Log::error($exception);

            return response(['message' => 'خطایی رخ داده است'], 500);
        }
    }
}
