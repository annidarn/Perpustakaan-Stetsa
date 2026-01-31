<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\BookCopy;

class BookCopiesTableSeeder extends Seeder
{
    public function run(): void
    {
        $books = Book::all();
        
        if ($books->isEmpty()) {
            $this->command->info('Tidak ada buku! Jalankan BooksTableSeeder dulu.');
            return;
        }

        $counter = 1;
        
        foreach ($books as $book) {
            // Setiap buku punya 2-3 copy fisik
            $copyCount = rand(2, 3);
            
            for ($i = 1; $i <= $copyCount; $i++) {
                BookCopy::create([
                    'book_id' => $book->id,
                    'book_code' => 'LIB-' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                    'purchase_date' => now()->subDays(rand(30, 365)),
                    'price' => rand(50000, 150000),
                    'notes' => $i == 1 ? 'Copy utama' : null,
                    'status' => 'available',
                ]);
                $counter++;
            }
        }

        $this->command->info('Berhasil menambahkan ' . ($counter - 1) . ' copy buku.');
    }
}