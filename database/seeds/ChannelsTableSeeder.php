<?php

use App\Models\Channel;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;


class ChannelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table('channels')->truncate();
        $channels = [
            [
                'name' => 'Billboard',
                'image_url' => $faker->image(),
                'status' => Channel::ACTIVE,
            ],
            [
                'name' => 'Poster',
                'image_url' => $faker->image(),
                'status' => Channel::ACTIVE,
            ],
            [
                'name' => 'Lightbox',
                'image_url' => $faker->image(),
                'status' => Channel::ACTIVE,
            ],
            [
                'name' => 'Frontdoor',
                'image_url' => $faker->image(),
                'status' => Channel::INACTIVE,
            ]
        ];
        DB::table('channels')->insert($channels);
    }
}
