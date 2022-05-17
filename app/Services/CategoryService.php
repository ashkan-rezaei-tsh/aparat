<?php

namespace App\Services;

use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UploadCategoryBannerRequest;
use Illuminate\Support\Str;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryService extends BaseService
{
    public static function getAll()
    {
        $categories = Category::all();
        return response($categories, 200);
    }

    public static function getMyCategories()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        return response($categories, 200);
    }

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

    public static function Create(CreateCategoryRequest $request)
    {
        dd($request->validated());
    }
}
