<?php

namespace App\Services;

use App\Http\Requests\Video\UploadVideoRequest;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VideoService extends BaseService
{
    public static function uploadVideo(UploadVideoRequest $request)
    {
        try {
            $user = auth()->user();

            $video = $request->file('video');
            $fileName = time() . Str::random(10);
            $path = public_path('videos/tmp/');
            $video->move($path, $fileName);

            return response(['video' => $fileName], 200);
        } catch (Exception $exception) {
            Log::error($exception);

            return response(['message' => 'خطایی رخ داده است'], 500);
        }
    }
}
