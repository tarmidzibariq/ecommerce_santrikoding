<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    public function index(){
        // get sliders
        $sliders = Slider::latest()->paginate(5);
        // return with Api Resource
        return new SliderResource(true, 'List Data Sliders', $sliders);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'image'=> 'required|image|mimes:jpeg,png,jpg|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // upload image
        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        // create product
        $slider = Slider::create([
            'image' => $image->hashName(),
            'link'  => $request->link,
        ]);

        if ($slider) {
            // return success with Api Resource
            return new SliderResource(true, 'Data Product Berhasil Disimpan!', $slider);
        }
        // return failed with Api Resource
        return new SliderResource(false, 'Data Product Gagal Disimpan', null);
    }

    public function destroy(Slider $slider)
    {
        Storage::disk('local')->delete('public/sliders/' . basename($slider->image));
        if ($slider->delete()) {
            // return success with Api Resource
            return new SliderResource(true, 'Data Slider Berhasil Dihapus!', null);
        }
        // return failed with Api Resource
        return new SliderResource(false, 'Data Slider Gagal Dihapus!', null);
    }

}
