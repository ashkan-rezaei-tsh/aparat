<?php

namespace App\Http\Controllers;

use App\Http\Requests\Channel\UpdateChannelRequest;
use App\Http\Requests\channel\UpdateSocialNetworksRequest;
use App\Http\Requests\channel\UploadBannerRequest;
use App\Services\ChannelService;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function update(UpdateChannelRequest $request)
    {
        return ChannelService::updateChannelInfo($request);
    }

    public function uploadBanner(UploadBannerRequest $request)
    {
        return ChannelService::uploadChannelBanner($request);
    }

    public function updateSocialNetworks(UpdateSocialNetworksRequest $request)
    {
        return ChannelService::updateSocialNetworks($request);
    }
}
