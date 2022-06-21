<?php

namespace App\Services;

use App\Http\Requests\Tag\CreateTagRequest;
use App\Models\Tag;

class TagService extends BaseService
{
	
	/**
	 * Get all Tags
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public static function index()
	{
		return Tag::all();
	}
	
	
	/**
	 * Create a new Tag
	 *
	 * @param CreateTagRequest $request
	 *
	 * @return mixed
	 */
	public static function create(CreateTagRequest $request)
	{
		$data = $request->validated();
		return Tag::create($data);
	}
}