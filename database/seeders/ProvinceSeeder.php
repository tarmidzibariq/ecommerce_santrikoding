<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Province;
class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = Http::withHeaders([
            // api ket rajaongkir
            'key' =>config('services.rajaongkir.key'),
        ])->get('https://api.rajaongkir.com/starter/province');

        foreach ($response['rajaongkir']['results'] as $province) {
            Province::create([
                'province_id'   => $province['province_id'],
                'name'          => $province['province']
            ]);
        }
    }
}
