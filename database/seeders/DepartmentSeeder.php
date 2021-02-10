<?php

namespace Database\Seeders;

use App\Models\Master\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;


class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    	Department::truncate();

        Department::flushEventListeners();

        Department::create(['id' => 1,'name' => 'Sales','code' => 'SC','is_active' => 1, 'parent_id' => 0]);
        Department::create(['id' => 2,'name' => 'B2B','code' => 'SC-B2B','is_active' => 1, 'parent_id' => 1]);
        Department::create(['id' => 3,'name' => 'B2C','code' => 'SC-B2C','is_active' => 1, 'parent_id' => 1]);
        Department::create(['id' => 4,'name' => 'B2B&B2C','code' => 'SC-B2B-B2C','is_active' => 1, 'parent_id' => 1]);

        Department::create(['id' => 5,'name' => 'Operation','code' => 'OP','is_active' => 1, 'parent_id' => 0]);
        Department::create(['id' => 6,'name' => 'Account Opening','code' => 'OP-AO','is_active' => 1, 'parent_id' => 5]);
        Department::create(['id' => 7,'name' => 'Customer Service','code' => 'OP-CS','is_active' => 1, 'parent_id' => 5]);
        Department::create(['id' => 8,'name' => 'Settlement Team','code' => 'OP-ST','is_active' => 1, 'parent_id' => 5]);

        Department::create(['id' => 9,'name' => 'Management','code' => 'MG','is_active' => 1, 'parent_id' => 0]);
        Department::create(['id' => 10,'name' => 'Business Management','code' => 'MG-BMG','is_active' => 1, 'parent_id' => 9]);
    }
}
