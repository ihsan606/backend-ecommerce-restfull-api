<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Fetch Rest Api
        $response = Http::withHeaders([
            'key'=>config('services.rajaongkir.key'),
        ])->get('https://api.rajaongkir.com/starter/city');

        //loop data from Rest api
        foreach ($response['rajaongkir']['results'] as $city) {

            //insert city ke table
            City::create([
                'province_id' => $city['province_id'],
                'city_id' => $city['city_id'],
                'name' => $city['city_name'] .'-'.'('.$city['type'].')',
            ]);
        }
    }
}
