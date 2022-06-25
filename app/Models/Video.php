<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
	use HasFactory;
	
	//region States
	const STATE_PENDING = 'pending';
	const STATE_CONVERTED = 'converted';
	const STATE_ACCEPTED = 'accepted';
	const STATE_BLOCKED = 'blocked';
	const STATES = [
		self::STATE_PENDING,
		self::STATE_CONVERTED,
		self::STATE_ACCEPTED,
		self::STATE_BLOCKED
	];
	//endregion States
	
	//region Configurations
	protected $table = 'videos';
	
	protected $fillable = [
		'user_id',
		'category_id',
		'channel_category_id',
		'slug',
		'title',
		'info',
		'duration',
		'banner',
		'publish_at',
		'enable_comments',
		'state',
	];
	//endregion Configurations
	
	//region Relations
	public function playlists()
	{
		return $this->belongsToMany(Playlist::class, 'playlist_videos');
	}
	
	public function tags()
	{
		return $this->belongsToMany(Tag::class, 'video_tags');
	}
	//endregion Relations
}
