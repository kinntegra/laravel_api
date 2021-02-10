<?php

namespace Database\Seeders;

use App\Models\Master\Commercial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommercialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    	Commercial::truncate();

        Commercial::flushEventListeners();

        Commercial::create(["id" => 1, "name" => "EQUITY MF", "field_name" => "equitymf"]);
        Commercial::create(["id" => 2, "name" => "SHORT TERM MF", "field_name" => "stmf"]);
        Commercial::create(["id" => 3, "name" => "P2P", "field_name" => "p2p"]);
        Commercial::create(["id" => 4, "name" => "INSURANCE", "field_name" => "ins"]);
    }
}
