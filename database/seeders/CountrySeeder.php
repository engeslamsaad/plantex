<?php

namespace Database\Seeders;
use DB;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    
    public function run()
    {
        $countries=['Egypt','Libya','KSA'];
        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'name' => $country,
            ]);
        }
    }
}
