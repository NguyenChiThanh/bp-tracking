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
            ],
            [
                'name' => 'Poster',
                'image_url' => '',
            ],
            [
                'name' => 'Lightbox',
                'image_url' => '',
            ],
            [
                'name' => 'Frontdoor',
                'image_url' => '',
            ]
        ];
        DB::table('channels')->insert($channels);
    }
}
