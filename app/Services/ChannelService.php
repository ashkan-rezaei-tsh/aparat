<?php

namespace App\Services;

use App\Http\Requests\Channel\UpdateChannelRequest;
use App\Models\Channel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
}
