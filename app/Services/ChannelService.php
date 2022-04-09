<?php

namespace App\Services;

use App\Http\Requests\Channel\UpdateChannelRequest;
use App\Http\Requests\channel\UpdateSocialNetworksRequest;
use App\Http\Requests\channel\UploadBannerRequest;
use App\Models\Channel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChannelService extends BaseService
{
    public static function updateChannelInfo(UpdateChannelRequest $request)
    {
        try {
            if ($channelId = $request->route('id')) {
                $channel = Channel::findOrFail($channelId);
                $user = $channel->user;
            } else {
                $user = auth()->user();
                $channel = $user->channel;
            }

            DB::beginTransaction();

            $channel->name = $request->name;
            $channel->info = $request->info;
            $channel->save();

            $user->website = $request->website;
            $user->save();

            DB::commit();

            return response(['message' => 'اطلاعات کانال با موفقیت تغییر یافت'], 200);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی رخ داده است'], 500);
        }
    }

    public static function uploadChannerBanner(UploadBannerRequest $request)
    {
        try {
            $banner = $request->file('banner');
            $fileName = md5(auth()->id()) . '-' . Str::random(15);
            $banner->move(public_path('channel-banners'), $fileName);

            $channel = auth()->user()->channel;

            if ($channel->banner) {
                unlink($channel->banner);
            }

            $channel->banner = 'channel-banners/' . $fileName;
            $channel->save();

            return response(['banner' => url('channel-banners/' . $fileName)]);
        } catch (Exception $exception) {
            Log::error($exception);
            return response(['message' => 'خطایی رخ داده است'], 500);
        }
    }

    public static function updateSocialNetworks(UpdateSocialNetworksRequest $request)
    {
        try {
            $socials = $request->validated();

            $channel = auth()->user()->channel;
            $channel->update([
                'socials' => $socials,
            ]);

            return response(['message' => 'شبکه های اجتماعی با موفقیت بروزرسانی شد']);
        } catch (Exception $exception) {
            Log::error($exception);

            return response(['message' => 'خطایی رخ داده است'], 500);
        }
    }
}
