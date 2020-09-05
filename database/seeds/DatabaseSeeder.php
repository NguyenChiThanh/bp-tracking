<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(ChannelsTableSeeder::class);
        $this->call(CampaignsTableSeeder::class);
        $this->call(BookingsTableSeeder::class);
        $this->call(CompanyTableSeeder::class);
    }
}
