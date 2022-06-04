<?php

namespace App\Services;

use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UploadCategoryBannerRequest;
use Illuminate\Support\Str;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryService extends BaseService
{
	/**
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public static function getAll()
    {
        $categories = Category::all();
        return response($categories, 200);
    }
	
	
	/**
	 * @return mixed
	 */
	public static function getMyCategories()
    {
        return auth()->user()->categories;
    }
	
	/**
	 * @param UploadCategoryBannerRequest $request
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public static function uploadBanner(UploadCategoryBannerRequest $request)
    {
        try {
            $banner = $request->file('banner');
            $fileName = time() . Str::random(10) . '-banner';
            Storage::disk('categories')->put('/tmp/' . $fileName, $banner->get());

            return response(['banner' => $fileName], 200);
        } catch (Exception $exception) {
            Log::error($exception);
            return response(['message' => 'خطایی رخ داده است'], 500);
        }
    }
	
	/**
	 * @param CreateCategoryRequest $request
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public static function Create(CreateCategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $user = auth()->user();

            if ($request->banner_id) {
                Storage::disk('categories')->move('/tmp/' . $request->banner_id, auth()->id() . '/' . $request->banner_id);
            }

            $category = $user->categories()->create($data);

            DB::commit();

            return response(['data' => $category], 200);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return response(['message' => 'خطایی رخ داده است'], 500);
        }
    }
}
