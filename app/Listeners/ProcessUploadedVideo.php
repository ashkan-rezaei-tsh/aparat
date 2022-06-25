<?php

namespace App\Listeners;

use App\Events\UploadNewVideo;
use FFMpeg\Filters\Video\CustomFilter;
use FFMpeg\Format\Video\X264;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ProcessUploadedVideo
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UploadNewVideo  $event
     * @return void
     */
    public function handle(UploadNewVideo $event)
    {
        $video = $event->getVideo();
        
        $uploadedVideoPath = '/tmp/' . $event->getRequest()->video_id;
        $uploadedVideo = FFMpeg::fromDisk('videos')->open($uploadedVideoPath);
        $filter = new CustomFilter("drawtext=text='https\\://ashkanrezaei.ir': fontcolor=white: fontsize=30:
				box=1: boxcolor=black@0.25: boxborderw=5:
				x=10: y=(h - text_h - 10)");
        $videoFile = $uploadedVideo->addFilter($filter)
            ->export()
            ->toDisk('videos')
            ->inFormat(new X264('libmp3lame'));
    
        $videoFile->save(auth()->id() . '/' . $video->slug . '.mp4');
        Storage::disk('videos')->delete($uploadedVideoPath);
    
        $video->duration = $uploadedVideo->getDurationInSeconds();
        $video->save();
    }
}
