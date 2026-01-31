<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;

class BooksTableSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil beberapa kategori
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->info('Tidak ada kategori! Jalankan CategoriesTableSeeder dulu.');
            return;
        }

        $books = [
            // Buku Pelajaran (000-099, 500-599)
            [
                'isbn' => '9786024271223',
                'title' => 'Matematika SMA Kelas 10',
                'category_id' => $categories->where('notation', '500-599')->first()->id,
                'author' => 'Dr. Budi Santoso, M.Pd.',
                'publisher' => 'Erlangga',
                'publication_year' => 2023,
                'description' => 'Buku matematika kurikulum terbaru untuk kelas 10',
            ],
            [
                'isbn' => '9786020324816',
                'title' => 'Fisika Dasar Kelas 10',
                'category_id' => $categories->where('notation', '500-599')->first()->id,
                'author' => 'Prof. Sari Wijaya, Ph.D.',
                'publisher' => 'Yudhistira',
                'publication_year' => 2023,
                'description' => 'Buku fisika dengan pendekatan praktis',
            ],
            [
                'isbn' => '9789790752641',
                'title' => 'Biologi SMA Kelas 11',
                'category_id' => $categories->where('notation', '500-599')->first()->id,
                'author' => 'Dra. Ani Rahmawati',
                'publisher' => 'Grafindo',
                'publication_year' => 2022,
                'description' => 'Pembahasan biologi lengkap dengan ilustrasi',
            ],
            
            // Buku Fiksi & Sastra (800-899)
            [
                'isbn' => '9786020324786',
                'title' => 'Laskar Pelangi',
                'category_id' => $categories->where('notation', '800-899')->first()->id,
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'publication_year' => 2005,
                'description' => 'Novel tentang perjuangan anak-anak Belitung',
            ],
            [
                'isbn' => '9789792234351',
                'title' => 'Bumi Manusia',
                'category_id' => $categories->where('notation', '800-899')->first()->id,
                'author' => 'Pramoedya Ananta Toer',
                'publisher' => 'Lentera Dipantara',
                'publication_year' => 1980,
                'description' => 'Novel sejarah Indonesia',
            ],
            
            // Buku Agama (200-299)
            [
                'isbn' => '9789793577395',
                'title' => 'Pendidikan Agama Islam Kelas 10',
                'category_id' => $categories->where('notation', '200-299')->first()->id,
                'author' => 'Tim Penyusun Kemenag',
                'publisher' => 'Kementerian Agama',
                'publication_year' => 2023,
                'description' => 'Buku pelajaran agama Islam',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }

        $this->command->info('Berhasil menambahkan ' . count($books) . ' buku contoh.');
    }
}