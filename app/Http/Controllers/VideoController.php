<?php

namespace App\Http\Controllers;

use App\Http\Requests\Video\ChangeVideoStateRequest;
use App\Http\Requests\Video\CreateVideoRequest;
use App\Http\Requests\Video\UploadVideoBannerRequest;
use App\Http\Requests\Video\UploadVideoRequest;
use App\Services\VideoService;

class VideoController extends Controller
{
    /**
     * @param  UploadVideoRequest  $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function uploadVideo(UploadVideoRequest $request)
    {
        return VideoService::uploadVideo($request);
    }
    
    
    /**
     * @param  UploadVideoBannerRequest  $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function uploadVideoBanner(UploadVideoBannerRequest $request)
    {
        return VideoService::uploadVideoBanner($request);
    }
    
    
    /**
     * @param  CreateVideoRequest  $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function create(CreateVideoRequest $request)
    {
        return VideoService::create($request);
    }
    
    
    
    public function changeState(ChangeVideoStateRequest $request)
    {
        return VideoService::changeState($request);
    }
}
