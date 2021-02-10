<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Associate\Associate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    	User::truncate();

        User::flushEventListeners();

        $user = User::create([
            'name' => 'SUPERADMIN',
            'email' => 'shashivarma88@gmail.com',
            'mobile' => 9833980003,
            'username' => 'SUPERADMIN',
            'is_active' => '1',
            'in_house' => '1',
            'pin' => 123456,
            'activation_token' => User::generateActivationCode(),
            'password' => Hash::make('password'),
        ]);

        $user->roles()->attach([1]);
    }
}
