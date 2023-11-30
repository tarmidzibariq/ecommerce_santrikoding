<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use App\Http\Resources\SliderResource;

class SliderController extends Controller
{
    public function index() {
        $sliders = Slider::latest()->get();

        // return with Api Resource
        return new SliderResource(true, 'List Data Sliders', $sliders);
    }
}
