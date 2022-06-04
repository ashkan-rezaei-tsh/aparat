<?php

namespace App\Services;

use App\Http\Requests\Playlist\CreatePlaylistRequest;
use App\Models\Playlist;

class PlaylistService extends BaseService
{
	
	/**
	 * Get all common playlists (Playlists that not belongs to any specific user)
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public static function getAll()
	{
		$playlists = Playlist::whereNull('user_id')->get();
		return response($playlists, 200);
	}
	
	
	/**
	 * Get user's playlists
	 *
	 * @return \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
	 */
	public static function getMyPlaylists()
	{
		$user = auth()->user();
		return response($user->playlists, 200);
	}
	
	
	/**
	 * Create a new playlist for logged in user
	 *
	 * @param CreatePlaylistRequest $request
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public static function create(CreatePlaylistRequest $request)
	{
		$data = $request->validated();
		$user = auth()->user();
		$playlist = $user->playlists()->create($data);
		return response(['data' => $playlist], 200);
	}
}
