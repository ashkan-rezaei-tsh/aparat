<?php

namespace App\Events;

use App\Models\Video;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class UploadNewVideo
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    private Video $video;
    private Request $request;
    
    /**
     * Create a new event instance.
     *
     * @param  Video  $video
     * @param  Request  $request
     */
    public function __construct(Video $video, Request $request)
    {
        //
        $this->video = $video;
        $this->request = $request;
    }
    
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
    
    /**
     * @return Video
     */
    public function getVideo(): Video
    {
        return $this->video;
    }
    
    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
