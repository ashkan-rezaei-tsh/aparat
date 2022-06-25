<?php

namespace App\Services;

use App\Events\UploadNewVideo;
use App\Helpers\SiteHelper;
use App\Http\Requests\Video\ChangeVideoStateRequest;
use App\Http\Requests\Video\CreateVideoRequest;
use App\Http\Requests\Video\UploadVideoBannerRequest;
use App\Http\Requests\Video\UploadVideoRequest;
use App\Models\Playlist;
use App\Models\Video;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public static function uploadVideo(UploadVideoRequest $request)
	{
		try{
			$video = $request->file('video');
			$fileName = time() . Str::random(10);
			Storage::disk('videos')->put('/tmp/' . $fileName, $video->get());
			
			return response(['video' => $fileName], 200);
		}catch(Exception $exception){
			Log::error($exception);
			
			return response(['message' => 'خطایی رخ داده است'], 500);
		}
	}
	
	/**
	 * Uploading video banner
	 *
	 * @param UploadVideoBannerRequest $request
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public static function uploadVideoBanner(UploadVideoBannerRequest $request)
	{
		try{
			$banner = $request->file('banner');
			$fileName = time() . Str::random(10) . '-banner';
			Storage::disk('videos')->put('/tmp/' . $fileName, $banner->get());
			
			return response(['banner' => $fileName], 200);
		}catch(Exception $exception){
			Log::error($exception);
			return response(['message' => 'خطایی رخ داده است'], 500);
		}
	}
	
	/**
	 * Create a Video
	 *
	 * @param CreateVideoRequest $request
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public static function create(CreateVideoRequest $request)
	{
		try{
			DB::beginTransaction();
			
			$video = Video::create([
				'user_id' => auth()->id(),
				'category_id' => $request->category,
				'channel_category_id' => $request->channel_category,
				'slug' => '',
				'title' => $request->title,
				'info' => $request->info,
				'duration' => 0,
				'banner' => $request->banner,
				'enable_comments' => $request->enable_comments,
				'publish_at' => $request->publish_at,
				'state' => Video::STATE_PENDING,
			]);
			
			$video->slug = SiteHelper::uniqueId($video->id);
			$video->banner = $video->slug . '-banner';
			$video->save();
			
            event(new UploadNewVideo($video, $request));
//            ConvertAndAddWatermarkToUploadedVideo::dispatch($video, $request->video_id);
            
			if($request->banner){
				Storage::disk('videos')->move('/tmp/' . $request->banner, auth()->id() . '/' . $video->banner);
			}
			
			
			if($request->playlist){
				$playlist = Playlist::find($request->playlist);
				$playlist->videos()->attach($video->id);
			}
			
			if(!empty($request->tags)){
				$video->tags()->attach($request->tags);
			}
			
			DB::commit();
			return response($video, 201);
		}catch(Exception $exception){
			DB::rollBack();
			Log::error($exception);
			
			return response(['message' => 'خطایی رخ داده است'], 500);
		}
	}
    
    public static function changeState(ChangeVideoStateRequest $request){
        $video = $request->video;
        
        $video->state = $request->state;
        $video->save();
        
        return response($video);
    }

}
