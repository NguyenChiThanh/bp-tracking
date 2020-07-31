<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Booking;
use Illuminate\Support\Facades\Schema;

class BookingsTableSeeder extends Seeder
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
        DB::table('bookings')->truncate();

        $bookings = [];

        $campaigns = Db::table('campaigns')->get();

        foreach ($campaigns as $campaign) {
            foreach (json_decode($campaign->position_list, true) as $pos_id) {
                $position = DB::table('positions')->where('id', $pos_id)->first();
                $bookings[] = [
                    'campaign_id' => $campaign->id,
                    'position_id' => $position->id,
                    'from_ts' => $campaign->from_ts,
                    'to_ts' => $campaign->to_ts,
                    'buffer_ts' => $position->buffer_days * 24 * 3600
                ];
            }
        }
        DB::table('bookings')->insert($bookings);

        Schema::enableForeignKeyConstraints();
    }
}
