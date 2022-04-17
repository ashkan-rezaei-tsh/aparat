<?php

namespace App\Services;

use App\Helpers\SiteHelper;
use App\Http\Requests\Video\CreateVideoRequest;
use App\Http\Requests\Video\UploadVideoBannerRequest;
use App\Http\Requests\Video\UploadVideoRequest;
use App\Models\Playlist;
use App\Models\Video;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoService extends BaseService
{

    /**
     * Uploading video
     *
     * @param UploadVideoRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public static function uploadVideo(UploadVideoRequest $request)
    {
        try {
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

    /**
     * Uploading video banner
     *
     * @param UploadVideoBannerRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public static function uploadVideoBanner(UploadVideoBannerRequest $request)
    {
        try {
            $banner = $request->file('banner');
            $fileName = time() . Str::random(10) . '-banner';
            $path = public_path('videos/tmp/');
            $banner->move($path, $fileName);

            return response(['banner' => $fileName], 200);
        } catch (Exception $exception) {
            Log::error($exception);
            return response(['message' => 'خطایی رخ داده است'], 500);
        }
    }

    public static function create(CreateVideoRequest $request)
    {
        // dd(Storage::disk('videos'));
        try {
            DB::beginTransaction();

            $video = Video::create([
                'user_id'               => auth()->id(),
                'category_id'           => $request->category,
                'channel_category_id'   => $request->channel_category,
                'slug'                  => '',
                'title'                 => $request->title,
                'info'                  => $request->info,
                'duration'              => 0, //TODO: get video duration
                'banner'                => $request->banner,
                'publish_at'            => $request->publish_at,
            ]);

            $video->slug = SiteHelper::uniqueId($video->id);
            $video->banner = $video->slug . '-banner';
            $video->save();

            Storage::disk('videos')->move('/tmp/' . $request->video_id, auth()->id() . '/' . $video->slug);

            if ($request->banner) {
                Storage::disk('videos')->move('/tmp/' . $request->banner, auth()->id() . '/' . $video->banner);
            }


            if ($request->playlist) {
                $playlist = Playlist::find($request->playlist);
                $playlist->videos()->attach($video->id);
            }

            if (!empty($request->tags)) {
                $video->tags()->attach($request->tags);
            }

            DB::commit();
            return response(['data' => $video], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return response(['message' => 'خطایی رخ داده است'], 500);
        }
    }
}
