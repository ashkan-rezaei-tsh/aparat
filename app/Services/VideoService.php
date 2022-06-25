<?php

namespace App\Services;

use App\Helpers\SiteHelper;
use App\Http\Requests\Video\CreateVideoRequest;
use App\Http\Requests\Video\UploadVideoBannerRequest;
use App\Http\Requests\Video\UploadVideoRequest;
use App\Models\Playlist;
use App\Models\Video;
use Exception;
use FFMpeg\Filters\Video\CustomFilter;
use FFMpeg\Format\Video\WMV;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

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
			$uploadedVideoPath = '/tmp/' . $request->video_id;
			$video = FFMpeg::fromDisk('videos')->open($uploadedVideoPath);
			$filter = new CustomFilter("drawtext=text='https\\://ashkanrezaei.ir': fontcolor=white: fontsize=30:
				box=1: boxcolor=black@0.25: boxborderw=5:
				x=10: y=(h - text_h - 10)");
			$videoFile = $video
				->addFilter($filter)
				->export()
				->toDisk('videos')
				->inFormat(new X264('libmp3lame'));
			
			DB::beginTransaction();
			
			$video = Video::create([
				'user_id' => auth()->id(),
				'category_id' => $request->category,
				'channel_category_id' => $request->channel_category,
				'slug' => '',
				'title' => $request->title,
				'info' => $request->info,
				'duration' => $video->getDurationInSeconds(),
				'banner' => $request->banner,
				'enable_comments' => $request->enable_comments,
				'publish_at' => $request->publish_at,
			]);
			
			$video->slug = SiteHelper::uniqueId($video->id);
			$video->banner = $video->slug . '-banner';
			$video->save();
			
			$videoFile->save(auth()->id() . '/' . $video->slug . '.mp4');
			Storage::disk('videos')->delete($uploadedVideoPath);
			
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
}
