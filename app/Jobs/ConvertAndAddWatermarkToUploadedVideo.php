<?php

namespace App\Jobs;

use App\Models\Video;
use FFMpeg\Filters\Video\CustomFilter;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertAndAddWatermarkToUploadedVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private Video $video;
    private string $videoId;
    private string|int|null $userId;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video, string $videoId)
    {
        $this->video = $video;
        $this->videoId = $videoId;
        $this->userId = auth()->id();
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $uploadedVideoPath = '/tmp/'.$this->videoId;
        $uploadedVideo = FFMpeg::fromDisk('videos')->open($uploadedVideoPath);
        $filter = new CustomFilter("drawtext=text='https\\://ashkanrezaei.ir': fontcolor=white: fontsize=30:
				box=1: boxcolor=black@0.25: boxborderw=5:
				x=10: y=(h - text_h - 10)");
        $videoFile = $uploadedVideo->addFilter($filter)
            ->export()
            ->toDisk('videos')
            ->inFormat(new X264('libmp3lame'));
        
        $videoFile->save($this->userId.'/'.$this->video->slug.'.mp4');
        Storage::disk('videos')->delete($uploadedVideoPath);
        
        $this->video->duration = $uploadedVideo->getDurationInSeconds();
        $this->video->state = Video::STATE_CONVERTED;
        $this->video->save();
    }
}
