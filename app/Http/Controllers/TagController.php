<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tag\CreateTagRequest;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
	public function index()
	{
		return TagService::index();
    }
	
	public function create(CreateTagRequest $request)
	{
		return TagService::create($request);
	}
}

