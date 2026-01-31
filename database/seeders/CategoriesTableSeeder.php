<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['notation' => '000-099', 'name' => 'Karya Umum', 'description' => 'Komputer, informasi, karya umum'],
            ['notation' => '100-199', 'name' => 'Filsafat', 'description' => 'Filsafat dan psikologi'],
            ['notation' => '200-299', 'name' => 'Agama', 'description' => 'Agama dan kepercayaan'],
            ['notation' => '300-399', 'name' => 'Ilmu Sosial', 'description' => 'Ilmu sosial, sosiologi, politik'],
            ['notation' => '400-499', 'name' => 'Bahasa', 'description' => 'Bahasa dan linguistik'],
            ['notation' => '500-599', 'name' => 'Ilmu Murni', 'description' => 'Matematika, fisika, kimia, biologi'],
            ['notation' => '600-699', 'name' => 'Ilmu Terapan', 'description' => 'Teknologi, kedokteran, pertanian'],
            ['notation' => '700-799', 'name' => 'Kesenian & Olahraga', 'description' => 'Seni, hiburan, olahraga'],
            ['notation' => '800-899', 'name' => 'Kesusastraan', 'description' => 'Sastra, puisi, drama'],
            ['notation' => '900-999', 'name' => 'Sejarah & Geografi', 'description' => 'Sejarah, geografi, biografi'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}