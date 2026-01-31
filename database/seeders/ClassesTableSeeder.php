<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;

class ClassesTableSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['grade' => '10', 'class_name' => 'Gotong Royong', 'academic_year' => 2024],
            ['grade' => '10', 'class_name' => 'Kewarganegaraan', 'academic_year' => 2024],
            ['grade' => '10', 'class_name' => 'Mandiri Belajar', 'academic_year' => 2024],
            ['grade' => '11', 'class_name' => 'Medical Science A', 'academic_year' => 2024],
            ['grade' => '11', 'class_name' => 'Medical Science B', 'academic_year' => 2024],
            ['grade' => '12', 'class_name' => 'Government Military Service A', 'academic_year' => 2024],
            ['grade' => '12', 'class_name' => 'Management Business & Accounting', 'academic_year' => 2024],
        ];

        foreach ($classes as $class) {
            ClassModel::create($class);
        }
    }
}