<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Models\Product;
class ProductController extends Controller
{
    public function index() {
        $products = Product::with('category')
        // count and average
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
            // search
            ->when(request()->q, function ($products) {
                $products = $products->where('title', 'like', '%' . request()->q . '%');
            })->latest()->paginate(8);

        // return with Api Resource
        return new ProductResource(true, 'List Data Products', $products);
    }

    public function show($slug) {
        $product = Product::with('category', 'reviews.customer')
        // count and average
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->where('slug', $slug)->first();

        if ($product) {
            // return success with Api Resource
            return new ProductResource(true, 'Detail Data Product! ', $product);
        }
        // return failed with Api Resource
        return new ProductResource(false, 'Detail Data Product Tidak Ditemukan!', null);
    }
}
