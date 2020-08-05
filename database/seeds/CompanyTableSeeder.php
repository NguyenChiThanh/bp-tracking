<?php

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('company')->truncate();

        $company = [
            [
                'name' => 'Novartis'
            ],
            [
                'name' => 'Merck'
            ],
            [
                'name' => 'GlaxoSmithKline'
            ],
            [
                'name' => 'Johnson & Johnson'
            ],
            [
                'name' => 'AbbVie'
            ],
            [
                'name' => 'Sanofi'
            ]
        ];

        Schema::enableForeignKeyConstraints();
        DB::table('company')->insert($company);
    }
}
