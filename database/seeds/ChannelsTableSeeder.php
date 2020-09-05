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
        DB::table('channels')->truncate();
        $channels = [
            [
                'name' => 'Billboard',
                'image_url' => '',
                'buffer_days' => 3
            ],
            [
                'name' => 'Poster',
                'image_url' => '',
                'buffer_days' => 5
            ],
            [
                'name' => 'Lightbox',
                'image_url' => '',
                'buffer_days' => 4
            ],
            [
                'name' => 'Frontdoor',
                'image_url' => '',
                'buffer_days' => 2
            ],
            [
                'name' => 'TopBoard',
                'image_url' => '',
                'buffer_days' => 2
            ],
            [
                'name' => 'WallBoard',
                'image_url' => '',
                'buffer_days' => 2
            ],
            [
                'name' => 'CounterPoster',
                'image_url' => '',
                'buffer_days' => 2
            ]
        ];
        DB::table('channels')->insert($channels);
    }
}
