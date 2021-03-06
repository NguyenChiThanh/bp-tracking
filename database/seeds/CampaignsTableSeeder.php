<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Campaign;
use Illuminate\Support\Facades\Schema;

class CampaignsTableSeeder extends Seeder
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
        DB::table('campaigns')->truncate();

        $positions = DB::table('positions')->where('unit', 'day')->limit(5)->get();
        $brand = DB::table('brands')->first();
        $user = DB::table('users')->first();

        $position_list = [];
        $price_2_days = 0;
        foreach ($positions as $position) {
            array_push($position_list, $position->id);
            $price_2_days += $position->price * 2;
        }

        $price_5_days = 0;
        foreach ($positions as $position) {
            $price_5_days += $position->price * 5;
        }

        $campaigns = [
            [
                'name' => trim(strtoupper($faker->text(5))) . $faker->randomNumber(5),
                'contract_code' => trim('CC_' . strtoupper($faker->text(10))),
                'license_code' => trim('LC_' . strtoupper($faker->text(10))),
                'brand_id' => $brand->id,
                'days_diff' => 2,
                'position_price' => $price_2_days,
                'from_ts' => (time() + 1 * 60 * 60), // curr time + 1h
                'to_ts' => (time() + 1 * 60 * 60) + 2 * 24 * 3600, // curr time + 2 days
                'created_by' => $user->id,
                'discount_type' => 'flat',
                'discount_value' => 1000,
                'total_discount' => 1000,
                'total_price' => $price_2_days - 1000000,
                'position_list' => json_encode($position_list)
            ],
            [
                'name' => trim(strtoupper($faker->text(5))) . $faker->randomNumber(5),
                'contract_code' => trim('CC_' . strtoupper($faker->text(10))),
                'license_code' => trim('LC_' . strtoupper($faker->text(10))),
                'brand_id' => $brand->id,
                'days_diff' => 5,
                'position_price' => $price_5_days,
                'from_ts' => (time() + 5 * 24 * 3600), // curr time + 5 days
                'to_ts' => (time() + 1 * 60 * 60) + 10 * 24 * 3600, // curr time + 10 days
                'created_by' => $user->id,
                'discount_type' => 'flat',
                'discount_value' => 1000,
                'total_discount' => 1000,
                'total_price' => $price_5_days - 1000000,
                'position_list' => json_encode($position_list)
            ]
        ];


        DB::table('campaigns')->insert($campaigns);
        Schema::enableForeignKeyConstraints();
    }
}
