<?php

namespace App\Http\Controllers;

use App\Http\Requests\Playlist\CreatePlaylistRequest;
use App\Services\PlaylistService;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function index()
    {
        return PlaylistService::getAll();
    }

    public function myPlaylists()
    {
        return PlaylistService::getMyPlaylists();
    }

    public function create(CreatePlaylistRequest $request)
    {
        return PlaylistService::create($request);
    }
}
