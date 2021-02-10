<?php

namespace Database\Seeders;

use App\Models\Employee\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    	Employee::truncate();

        Employee::flushEventListeners();

        Employee::create(["associate_id" => 1, "name" => "SHais"]);
        Employee::create(["associate_id" => 1, "name" => "Ravi"]);
    }
}
