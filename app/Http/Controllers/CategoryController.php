<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UploadCategoryBannerRequest;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryService::getAll();
    }

    public function myCategories()
    {
        return CategoryService::getMyCategories();
    }

    public function uploadBanner(UploadCategoryBannerRequest $request)
    {
        return CategoryService::uploadBanner($request);
    }

    public function create(CreateCategoryRequest $request)
    {
        return CategoryService::create($request);
    }
}
