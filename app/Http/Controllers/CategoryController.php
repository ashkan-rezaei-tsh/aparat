<?php

namespace App\Http\Controllers;

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
}
