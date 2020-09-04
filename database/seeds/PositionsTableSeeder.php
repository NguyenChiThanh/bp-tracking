<?php

use Illuminate\Database\Seeder;
use App\Models\Position;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        Schema::disableForeignKeyConstraints();
        DB::table('positions')->truncate();

        $positions = [];
        $stores = DB::table('stores')->take(10)->get();

        foreach ($stores as $store) {
            $positions[] =
                [
                    'name' => 'Lightbox_' . $faker->randomNumber(2),
                    'description' => $faker->text,
                    'image_url'=> '',
                    'store_id' => $store->id,
                    'channel' => 'Lightbox',
                    'buffer_days' => 2,
                    'unit' => 'day',
                    'price' => 2000000,
                ];
            $positions[] =
                [
                    'name' => 'Billboard_' . $faker->randomNumber(2),
                    'description' => $faker->text,
                    'image_url'=> '',
                    'store_id' => $store->id,
                    'channel' => 'Billboard',
                    'buffer_days' => 2,
                    'unit' => 'day',
                    'price' => 5000000,
                ];
        }

        DB::table('positions')->insert($positions);
        Schema::enableForeignKeyConstraints();
    }
}
