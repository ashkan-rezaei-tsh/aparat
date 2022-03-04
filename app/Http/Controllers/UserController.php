<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ChangeEmailRequest;
use App\Http\Requests\User\ChangeEmailSubmitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SiteHelper;

class UserController extends Controller
{
    const CHANGE_EMAIL_CACHE_KEY = 'email-change-for-user-';

    public function changeEmail(ChangeEmailRequest $request)
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

    public function changeEmailSubmit(ChangeEmailSubmitRequest $request)
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
}
