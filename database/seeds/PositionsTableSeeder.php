<?php

use Illuminate\Database\Seeder;
use App\Models\Position;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table('positions')->truncate();

        $positions = [];
        $stores = DB::table('stores')->get();

        foreach ($stores as $store) {
            $positions[] =
                [
                    'name' => 'Lightbox ' . $faker->randomNumber(5),
                    'description' => $faker->text,
                    'status' => Position::AVAILABLE,
                    'image_url'=> '',
                    'store_id' => $store->id,
                    'channel' => 'Lightbox',
                    'buffer_days' => 2,
                    'unit' => 'day',
                    'price' => 100000,
                ];
            $positions[] =
                [
                    'name' => 'Billboard ' . $faker->randomNumber(5),
                    'description' => $faker->text,
                    'status' => Position::AVAILABLE,
                    'image_url'=> '',
                    'store_id' => $store->id,
                    'channel' => 'Billboard',
                    'buffer_days' => 2,
                    'unit' => 'week',
                    'price' => 100000,
                ];
        }

        DB::table('positions')->insert($positions);
    }
}
