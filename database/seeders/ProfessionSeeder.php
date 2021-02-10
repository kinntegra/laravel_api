<?php

namespace Database\Seeders;

use App\Models\Master\Profession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    	Profession::truncate();

        Profession::flushEventListeners();

        Profession::create(["id" => 1, "name" => "MFD"]);
        Profession::create(["id" => 2, "name" => "RIA"]);
        Profession::create(["id" => 3, "name" => "MFD & RIA"]);
        Profession::create(["id" => 4, "name" => "CA"]);
        Profession::create(["id" => 5, "name" => "CS"]);
        Profession::create(["id" => 6, "name" => "OTHERS"]);
    }
}
