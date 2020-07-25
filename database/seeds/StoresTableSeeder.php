<?php

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;

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

        Schema::disableForeignKeyConstraints();
        DB::table('stores')->truncate();
        Schema::enableForeignKeyConstraints();

        $stores = [];
        for ($i=0; $i < 5; $i++) {
            $stores[] = [
                'name' => strtoupper('Store ' . $faker->randomLetter . $faker->randomNumber(5)),
                'description' => $faker->sentence,
                'level' => Store::LEVEL_A,
                'location' => 'Hồ Chí Minh',
                'status' => Store::COMING_SOON
            ];
        }
        for ($i=0; $i < 6; $i++) {
            $stores[] = [
                    'name' => strtoupper('Store ' . $faker->randomLetter . $faker->randomNumber(5)),
                    'description' => $faker->sentence,
                    'level' => Store::LEVEL_A,
                    'location' => 'Hồ Chí Minh',
                    'status' => Store::IN_OPERATING
                ];
        }
        for ($i=0; $i < 7; $i++) {
            $stores[] = [
                'name' => strtoupper('Store ' . $faker->randomLetter . $faker->randomNumber(5)),
                'description' => $faker->sentence,
                'level' => Store::LEVEL_B,
                'location' => 'Vũng Tàu',
                'status' => Store::IN_OPERATING
            ];
        }
        for ($i=0; $i < 8; $i++) {
            $stores[] = [
                'name' => strtoupper('Store ' . $faker->randomLetter . $faker->randomNumber(5)),
                'description' => $faker->sentence,
                'level' => Store::LEVEL_C,
                'location' => 'Hồ Chí Minh',
                'status' => Store::IN_OPERATING
            ];
        }
        for ($i=0; $i < 5; $i++) {
            $stores[] = [
                'name' => strtoupper('Store ' . $faker->randomLetter . $faker->randomNumber(5)),
                'description' => $faker->sentence,
                'level' => Store::LEVEL_D,
                'location' => 'Hồ Chí Minh',
                'status' => Store::IN_OPERATING
            ];
        }

        for ($i=0; $i < 5; $i++) {
            $stores[] = [
                'name' => strtoupper('Store ' . $faker->randomLetter . $faker->randomNumber(5)),
                'description' => $faker->sentence,
                'level' => Store::LEVEL_D,
                'location' => 'Hồ Chí Minh',
                'status' => Store::CLOSED
            ];
        }

        DB::table('stores')->insert($stores);
    }
}
