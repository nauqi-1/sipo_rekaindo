<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 56; $i <= 100; $i++) {
            $departments = array_merge(range(1, 5), range(8, 18));

            Unit::create([
                'name_unit' => 'Unit Testing ' . $i,
                'department_id_department' => $departments[array_rand($departments)]
            ]);
        }
    }
}
