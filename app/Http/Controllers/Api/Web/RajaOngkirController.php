<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\City;
use App\Models\Province;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\RajaOngkirResource;

class RajaOngkirController extends Controller
{
    public function getProvinces(){
        // get all provinces
        $provinces = Province::all();

        // return with Api Resource
        return new RajaOngkirResource(true, 'List Data Provinces',$provinces);
    }

    public function getCities(Request $request){
        $provinces = Province::where('province_id',$request->province_id)->first();

        $cities = City::where('province_id',$request->province_id)->get();

        return new RajaOngkirResource(true, 'List Data City By Province : '.$provinces->name.'', $cities);
    }

    public function checkOngkir(Request $request){
        $response = Http::withHeaders([
            'key' => config('services.rajaongkir.key')
        ])->post('https://api.rajaongkir.com/starter/cost',[

            // send data
            'origin' =>113, //ID kota Demak
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier,
        ]);

        // return with Api Resource 
        return new RajaOngkirResource(true, 'List Data Biaya Ongkos Kirim : '.$request->courier.'', $response['rajaongkir']['results'][0]['costs']);
    }
}
