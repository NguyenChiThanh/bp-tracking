<?php

use App\Models\User;
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

        $company = DB::table('company')->where('name', 'Novartis')->first();
        DB::table('users')->truncate();
        DB::table('users')->insert([
            [
                'name' => 'PMC Admin',
                'email' => 'pmcadmin@pmc.com',
                'password' => bcrypt('A123456!'),
                'type' => User::PMC_USER,
                'status' => User::ACTIVE,
                'created_at' => \Carbon\Carbon::now()
            ],
            [
                'name' => 'PMC Mod',
                'email' => 'pmcmod@pmc.com',
                'password' => bcrypt('A123456!'),
                'type' => User::PMC_USER,
                'status' => User::ACTIVE,
                'created_at' => \Carbon\Carbon::now()
            ],
            [
                'name' => 'PMC User',
                'email' => 'pmcuser@pmc.com',
                'password' => bcrypt('A123456!'),
                'type' => User::PMC_USER,
                'status' => User::ACTIVE,
                'created_at' => \Carbon\Carbon::now()
            ],
            [
                'name' => 'Partner User',
                'email' => 'partneruser@pmc.com',
                'company_id' => $company->id,
                'password' => bcrypt('A123456!'),
                'type' => User::PARTNER_USER,
                'status' => User::ACTIVE,
                'created_at' => \Carbon\Carbon::now()
            ],
        ]);


        Schema::enableForeignKeyConstraints();

    }
}
