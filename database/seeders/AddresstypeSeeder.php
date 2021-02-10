<?php

namespace Database\Seeders;

use App\Models\Master\Addresstype;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddresstypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    	Addresstype::truncate();

        Addresstype::flushEventListeners();
        Addresstype::create(["id" => 1, "name" => "Correspondence"]);
        Addresstype::create(["id" => 2, "name" => "Residential"]);
        Addresstype::create(["id" => 3, "name" => "Business"]);
        Addresstype::create(["id" => 4, "name" => "Permanent"]);
    }
}
