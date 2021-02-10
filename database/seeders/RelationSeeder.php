<?php

namespace Database\Seeders;

use App\Models\Master\Relation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

    	Relation::truncate();

        Relation::flushEventListeners();

        Relation::create(["id" => 1, "name" => "Father's father(grandfather)"]);
        Relation::create(["id" => 2, "name" => "Father's mother(grandmother)"]);
        Relation::create(["id" => 3, "name" => "Mother's father(materal grandfather)"]);
        Relation::create(["id" => 4, "name" => "Mother's mother(materal grandmother)"]);
        Relation::create(["id" => 5, "name" => "Mother"]);
        Relation::create(["id" => 6, "name" => "Father"]);
        Relation::create(["id" => 7, "name" => "Brother"]);
        Relation::create(["id" => 8, "name" => "Elder Brother"]);
        Relation::create(["id" => 9, "name" => "Younger Brother"]);
        Relation::create(["id" => 10, "name" => "Sister"]);
        Relation::create(["id" => 11, "name" => "Elder Sister"]);
        Relation::create(["id" => 12, "name" => "Younger Sister"]);
        Relation::create(["id" => 13, "name" => "Husband's sister"]);
        Relation::create(["id" => 14, "name" => "Father's sister"]);
        Relation::create(["id" => 15, "name" => "Elder Sister's husband"]);
        Relation::create(["id" => 16, "name" => "Younger Sister's husband"]);
        Relation::create(["id" => 17, "name" => "Husband's elder brother(brother-in-law)"]);
        Relation::create(["id" => 18, "name" => "Husband's younger brother"]);
        Relation::create(["id" => 19, "name" => "Elder brother's wife"]);
        Relation::create(["id" => 20, "name" => "Younger brother's wife"]);
        Relation::create(["id" => 21, "name" => "Wife's Sister"]);
        Relation::create(["id" => 22, "name" => "Wife's elder Brother"]);
        Relation::create(["id" => 23, "name" => "Wife's younger Brother"]);
        Relation::create(["id" => 24, "name" => "Wife's Brother's wife"]);
        Relation::create(["id" => 25, "name" => "Husband's Sister's Husband"]);
        Relation::create(["id" => 26, "name" => "Wife's sister's husband"]);
        Relation::create(["id" => 27, "name" => "Husband's elder brother's wife"]);
        Relation::create(["id" => 28, "name" => "Husband's younger brother's wife"]);
        Relation::create(["id" => 29, "name" => "Father's brother's son (cousin)"]);
        Relation::create(["id" => 30, "name" => "Father's brother's daughter (cousin)"]);
        Relation::create(["id" => 31, "name" => "Father's sister's son (cousin)"]);
        Relation::create(["id" => 32, "name" => "Father's sister's daughter (cousin)"]);
        Relation::create(["id" => 33, "name" => "Mother's brother's son (cousin)"]);
        Relation::create(["id" => 34, "name" => "Mother's brother's daughter (cousin)"]);
        Relation::create(["id" => 35, "name" => "Mother's sister's son (cousin)"]);
        Relation::create(["id" => 36, "name" => "Mother's sister's daughter (cousin)"]);
        Relation::create(["id" => 37, "name" => "Son"]);
        Relation::create(["id" => 38, "name" => "Daughter"]);
        Relation::create(["id" => 39, "name" => "Son's Wife"]);
        Relation::create(["id" => 40, "name" => "Daugther's Husband"]);
        Relation::create(["id" => 41, "name" => "Son's son (grandson)"]);
        Relation::create(["id" => 42, "name" => "Son's daughter (granddaughter)"]);
        Relation::create(["id" => 43, "name" => "Daughter's son (grandson)"]);
        Relation::create(["id" => 44, "name" => "Daugther's daughter (granddaughter)"]);
        Relation::create(["id" => 45, "name" => "Husband"]);
        Relation::create(["id" => 46, "name" => "Wife"]);
        Relation::create(["id" => 47, "name" => "Spouses' Mother (Mother-in-law)"]);
        Relation::create(["id" => 48, "name" => "Spouse's Father (Father-in-law))"]);
        Relation::create(["id" => 49, "name" => "Fiancé or Fiancée"]);
        Relation::create(["id" => 50, "name" => "Father's younger brother (uncle)"]);
        Relation::create(["id" => 51, "name" => "Father's younger brother's wife (aunt)"]);
        Relation::create(["id" => 52, "name" => "Father's elder brother's (Uncle)"]);
        Relation::create(["id" => 53, "name" => "Father's elder brother's wife (Aunt)"]);
        Relation::create(["id" => 54, "name" => "Father's sister (aunt)"]);
        Relation::create(["id" => 55, "name" => "Father's sister's husband"]);
        Relation::create(["id" => 56, "name" => "Mother's brother"]);
        Relation::create(["id" => 57, "name" => "Mother's brother's wife"]);
        Relation::create(["id" => 58, "name" => "Mother's younger sister"]);
        Relation::create(["id" => 59, "name" => "Mother's younger sister's husband"]);
        Relation::create(["id" => 60, "name" => "Mother's elder sister's husband (Uncle)"]);
        Relation::create(["id" => 61, "name" => "Mother's elder sister (Aunt)"]);
    }
}
