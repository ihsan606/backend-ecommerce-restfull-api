<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index() {
        //get category
        $categories = Category::latest()->get();

        return new CategoryResource(true,'List Data Categories',$categories);
    }

    public function show($slug) {
        $category = Category::with('products.category')
            //get count review and average review
            ->with('products', function ($query) {
                $query->withCount('reviews');
                $query->withAvg('reviews', 'rating');
            })
            ->where('slug', $slug)->first();
        
        if($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Data Product By Category : '.$category->name.'', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Detail Data Category Tidak DItemukan!', null);
    }
}