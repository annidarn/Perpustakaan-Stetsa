<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'title',
        'category_id',
        'author',
        'publisher',
        'publication_year',
        'receipt_date',
        'description',
    ];

    // Cast tanggal
    protected $casts = [
        'receipt_date' => 'date',
    ];

    // Relasi ke category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke book_copies
    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    // Helper: Generate inventory number berikutnya
    public static function getNextInventoryNumber()
    {
        $lastCopy = BookCopy::orderBy('inventory_number', 'desc')->first();
        return $lastCopy ? $lastCopy->inventory_number + 1 : 1;
    }

    // Helper: Buat copies berdasarkan qty
    public function createCopies($quantity, $condition = 'good')
    {
        $nextNumber = self::getNextInventoryNumber();
        $copies = [];

        for ($i = 0; $i < $quantity; $i++) {
            $copies[] = [
                'book_id' => $this->id,
                'inventory_number' => $nextNumber + $i,
                'status' => 'available',
            ];
        }

        BookCopy::insert($copies);
        return $quantity;
    }

    // Helper: Jumlah copy tersedia
    public function availableCopiesCount()
    {
        return $this->copies()->where('status', 'available')->count();
    }

    // Helper: Total semua copy
    public function totalCopiesCount()
    {
        return $this->copies()->count();
    }
}