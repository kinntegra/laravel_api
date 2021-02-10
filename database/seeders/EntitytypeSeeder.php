<?php

namespace Database\Seeders;

use App\Models\Master\Entitytype;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntitytypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    	Entitytype::truncate();

        Entitytype::flushEventListeners();

        Entitytype::create(["id" => 1, "name" => "Solo Proprietor"]);
        Entitytype::create(["id" => 2, "name" => "Partnership Firm"]);
        Entitytype::create(["id" => 3, "name" => "Corporate"]);
        Entitytype::create(["id" => 4, "name" => "Individual"]);

    }
}
