<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('users')->truncate();
        DB::table('users')->insert([
            'name' => 'Long Nguyen',
            'email' => 'nguyentienlong88@gmail.com',
            'password' => bcrypt('A123456!'),
            'type' => 'admin',
        ]);

        Schema::enableForeignKeyConstraints();

    }
}
