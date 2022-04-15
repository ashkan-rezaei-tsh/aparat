<?php

namespace App\Http\Controllers;

use App\Http\Requests\Video\CreateVideoRequest;
use App\Http\Requests\Video\UploadVideoBannerRequest;
use App\Http\Requests\Video\UploadVideoRequest;
use App\Services\VideoService;

class VideoController extends Controller
{
    public function uploadVideo(UploadVideoRequest $request)
    {
        return VideoService::uploadVideo($request);
    }

    public function uploadVideoBanner(UploadVideoBannerRequest $request)
    {
        return VideoService::uploadVideoBanner($request);
    }

    public function create(CreateVideoRequest $request)
    {
        return VideoService::create($request);
    }
}
