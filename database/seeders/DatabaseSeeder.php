<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategoriesTableSeeder::class,
            ClassesTableSeeder::class,
            BooksTableSeeder::class,
            BookCopiesTableSeeder::class,
        ]);

        // Optional: buat admin user
        \App\Models\User::factory()->create([
            'name' => 'Admin Perpustakaan',
            'is_admin' => 1,
            'email' => 'admin@perpustakaan.test',
            'password' => bcrypt('password123'),
        ]);
    }
}