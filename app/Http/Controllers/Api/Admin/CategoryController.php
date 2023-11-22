<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::when(request()->q, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        return new CategoryResource(true, 'List Data Categories', $categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2000',
            'name'  => 'required|unique:categories'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // upload image
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        // create category
        $category = Category::create([
            'image' => $image->hashName(),
            'name'  => $request->name,
            'slug'  => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            // return success with Api Resource
            return new CategoryResource(true, 'Data Category Berhasil Disimpan!', $category);
        }
        // return failed with Api Resource
        return new CategoryResource(false, 'Data Category Gagal Disimpan', null);
    }

    public function show($id)
    {
        $category = Category::whereId($id)->first();

        if ($category) {
            // return success with Api Resource
            return new CategoryResource(true, 'Detail Data Category!', $category);
        }
        // return failed with Api Resource
        return new CategoryResource(false, 'Detail Data Category Tidak Ditemukan!', null);
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|unique:categories,name,' . $category->id,
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // check image update
        if ($request->file('image')) {
            // remove old image
            Storage::disk('local')->delete('public/categories/' . basename($category->image));

            // upload new image
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            // create category
            $category->update([
                'image' => $image->hashName(),
                'name'  => $request->name,
                'slug'  => Str::slug($request->name, '-'),
            ]);
        }
        // update category without image
        $category->update([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            // return success with Api Resource
            return new CategoryResource(true, 'Data Category! Berhasil Diupdate', $category);
        }
        // return failed with Api Resource
        return new CategoryResource(false, 'Data Category Gagal Diupdate', null);
    }
    public function destroy(Category $category){
        Storage::disk('local')->delete('public/categories/'.basename($category->image));
        if ($category->delete()) {
            // return success with Api Resource
            return new CategoryResource(true, 'Data Category! Berhasil Dihapus', $category);
        }
        // return failed with Api Resource
        return new CategoryResource(false, 'Data Category Gagal Dihapus', null);
    }
}
