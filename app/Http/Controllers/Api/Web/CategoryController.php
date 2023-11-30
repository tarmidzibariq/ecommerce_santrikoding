<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
class CategoryController extends Controller
{
    public function index() {
        $categories = Category::latest()->get();

        return new CategoryResource(true, 'List Data Categories', $categories);
    }
    public function show($slug) {
        $category = Category::with('products.category')
        // get count review and average review
        ->with('products', function ($query){
            $query->withCount('reviews');
            $query->withAvg('reviews', 'rating');
        })
        ->where('slug', $slug)->first();

        if ($category) {
            // return success with Api Resource
            return new CategoryResource(true, 'Data Product By Category : '. $category->name.' ', $category);
        }
        // return failed with Api Resource
        return new CategoryResource(false, 'Detail Data Category Tidak Ditemukan!', null);
    }
    
}
