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

        DB::table('users')->truncate();

        $users = [
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
            ]
        ];
        for($i=0; $i<20; $i++) {
            $users[] = [
                'name' => sprintf('Partner User %d', $i),
                'email' => sprintf('partneruser_%d@pmc.com', $i),
                // 'company_id' => $company->id,
                'password' => bcrypt('A123456!'),
                'type' => User::PARTNER_USER,
                'status' => User::ACTIVE,
                'created_at' => \Carbon\Carbon::now()
            ];
        }

        DB::table('users')->insert($users);
        Schema::enableForeignKeyConstraints();

    }
}
