<?php

namespace App\Http\Controllers;

use App\Http\Requests\Video\UploadVideoRequest;
use App\Services\VideoService;

class VideoController extends Controller
{
    public function uploadVideo(UploadVideoRequest $request)
    {
        return VideoService::uploadVideo($request);
    }
}
