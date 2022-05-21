<?php

namespace App\Services;

use App\Http\Requests\Playlist\CreatePlaylistRequest;
use App\Models\Playlist;

class PlaylistService extends BaseService
{
    /**
     * Get all common playlists (Playlists that not belongs to any specific user)
     *
     * @return void
     */
    public static function getAll()
    {
        $playlists = Playlist::whereNull('user_id')->get();
        return response($playlists, 200);
    }



    /**
     * Get user's playlists
     *
     * @return void
     */
    public static function getMyPlaylists()
    {
        $user = auth()->user();
        return response($user->playlists, 200);
    }

    public static function create(CreatePlaylistRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();
        $playlist = $user->playlists()->create($data);
        return response($playlist, 200);
    }
}
