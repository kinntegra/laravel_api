<?php

namespace Database\Seeders;

use App\Models\Master\Designation;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;


class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    	Designation::truncate();

        Designation::flushEventListeners();

        Designation::create(['name' => 'Managing Director','code' => 'MD','is_active' => 1]);
        Designation::create(['name' => 'Director','code' => 'D','is_active' => 1]);
        Designation::create(['name' => 'President','code' => 'P','is_active' => 1]);
        Designation::create(['name' => 'Vice President','code' => 'VP','is_active' => 1]);
        Designation::create(['name' => 'Assistance Vice President','code' => 'AVP','is_active' => 1]);
        Designation::create(['name' => 'Manager','code' => 'M','is_active' => 1]);
        Designation::create(['name' => 'Sr.Executive','code' => 'SE','is_active' => 1]);
        Designation::create(['name' => 'Executive','code' => 'E','is_active' => 1]);

    }
}
