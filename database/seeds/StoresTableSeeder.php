<?php

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class StoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        DB::table('stores')->truncate();

        $stores = [];
        for ($i=0; $i < 10; $i++) {
            $stores[] = [
                'name' => strtoupper('Store ' . $faker->randomLetter . $faker->randomNumber(5)),
                'description' => $faker->sentence,
                'status' => Store::COMING_SOON
            ];
        }
        for ($i=0; $i < 30; $i++) {
            $stores[] = [
                    'name' => strtoupper('Store ' . $faker->randomLetter . $faker->randomNumber(5)),
                    'description' => $faker->sentence,
                    'status' => Store::IN_OPERATING
                ];
        }
        for ($i=0; $i < 10; $i++) {
            $stores[] = [
                'name' => strtoupper('Store ' . $faker->randomLetter . $faker->randomNumber(5)),
                'description' => $faker->sentence,
                'status' => Store::CLOSED
            ];
        }

        DB::table('stores')->insert($stores);
    }
}
