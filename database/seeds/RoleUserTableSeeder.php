<?php

use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('role_user')->truncate();

        $adminUser = DB::table('users')->where('name', 'PMC Admin')->first();
        $adminRole = DB::table('roles')->where('name', 'Admin')->first();
        DB::table('role_user')->insert([
           'role_id' => $adminRole->id,
           'user_id' => $adminUser->id,
        ]);

        $modUser = DB::table('users')->where('name', 'PMC Mod')->first();
        $modRole = DB::table('roles')->where('name', 'Mod')->first();
        DB::table('role_user')->insert([
            'role_id' => $modRole->id,
            'user_id' => $modUser->id,
        ]);

        $pmcUser = DB::table('users')->where('name', 'PMC User')->first();
        $pmcUserRole = DB::table('roles')->where('name', 'PMC User')->first();
        DB::table('role_user')->insert([
            'role_id' => $pmcUser->id,
            'user_id' => $pmcUserRole->id,
        ]);

        $partnerUser = DB::table('users')->where('name', 'like', '%Partner User%')->first();
        $partnerUSerRole = DB::table('roles')->where('name', 'Partner User')->first();
        DB::table('role_user')->insert([
            'role_id' => $partnerUser->id,
            'user_id' => $partnerUSerRole->id,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
