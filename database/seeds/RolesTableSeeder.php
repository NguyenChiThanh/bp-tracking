<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('roles')->truncate();
        DB::table('roles')->insert([
            [
                'name' => 'Admin',
                'description' => 'Admin',
            ],
            [
                'name' => 'Mod',
                'description' => 'Moderator',
            ],
            [
                'name' => 'PMC User',
                'description' => 'PMC User',
            ],
            [
                'name' => 'Partner User',
                'description' => 'Partner User',
            ]
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
