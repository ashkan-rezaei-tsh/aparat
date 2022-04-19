<?php

namespace App\Services;

use App\Models\Category;

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
}
