<?php

namespace Database\Seeders;

use App\Models\Master\Commercialtype;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommercialtypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    	Commercialtype::truncate();

        Commercialtype::flushEventListeners();

        Commercialtype::create(["id" => 1, "name" => "bps", "field_name" => "bps"]);
        Commercialtype::create(["id" => 2, "name" => "percentage", "field_name" => "perc"]);
    }
}
