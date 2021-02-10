<?php

namespace Database\Seeders;

use App\Models\Master\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    	Status::truncate();

        Status::flushEventListeners();

        Status::create(['id' => 1, 'title' => 'User Created']);
        Status::create(['id' => 2, 'title' => 'Supervisior Verification']);
        Status::create(['id' => 3, 'title' => 'Supervisior Accepted']);
        Status::create(['id' => 4, 'title' => 'Supervisior Rejected']);
        Status::create(['id' => 5, 'title' => 'User Verification']);
        Status::create(['id' => 6, 'title' => 'User Accepted']);
        Status::create(['id' => 7, 'title' => 'User Rejected']);
        Status::create(['id' => 8, 'title' => 'User Active']);
        Status::create(['id' => 9, 'title' => 'User Inactive']);
    }
}
