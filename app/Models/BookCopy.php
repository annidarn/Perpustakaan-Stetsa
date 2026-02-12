<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'inventory_number',
        'status',
        'notes',
    ];

    // relasi ke book
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // relasi ke borrows
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    // method untuk mendapatkan peminjaman aktif
    public function currentBorrow()
    {
        return $this->hasOne(Borrow::class)
            ->whereIn('status', ['borrowed', 'overdue'])
            ->latest();
    }

    // method untuk cek ketersediaan yang lebih akurat
    public function isActuallyAvailable()
    {
        // cek dari status dan juga dari relasi borrow
        return $this->status === 'available' && !$this->currentBorrow()->exists();
    }
    
    // cek apakah copy tersedia
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    // format status
    public function getStatusTextAttribute()
    {
        return $this->status === 'available' ? 'Tersedia' : 'Dipinjam';
    }

    // format inventory number dengan leading zeros
    public function getFormattedInventoryNumberAttribute()
    {
        return str_pad($this->inventory_number, 5, '0', STR_PAD_LEFT);
    }
}