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
        if (env('APP_ENV') != 'production') {
            $this->call(CampaignsTableSeeder::class);
            $this->call(BookingsTableSeeder::class);
            $this->call(CompanyTableSeeder::class);
        }
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);
        $this->call(ChannelsTableSeeder::class);
    }
}
