<?php

namespace App\Listeners;

use App\Events\UploadNewVideo;
use App\Jobs\ConvertAndAddWatermarkToUploadedVideo;

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
     * @param  UploadNewVideo  $event
     *
     * @return void
     */
    public function handle(UploadNewVideo $event)
    {
        ConvertAndAddWatermarkToUploadedVideo::dispatch($event->getVideo(), $event->getRequest()->video_id);
    }
}
