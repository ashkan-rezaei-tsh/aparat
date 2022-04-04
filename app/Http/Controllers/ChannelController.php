<?php

namespace App\Http\Controllers;

use App\Http\Requests\Channel\UpdateChannelRequest;
use App\Services\ChannelService;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function update(UpdateChannelRequest $request)
    {
        return ChannelService::updateChannelInfo($request);
    }
}
