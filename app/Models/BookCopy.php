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

    // Relasi ke book
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Relasi ke borrows (nanti)
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    // Method untuk mendapatkan peminjaman aktif
    public function currentBorrow()
    {
        return $this->hasOne(Borrow::class)
            ->whereIn('status', ['borrowed', 'overdue'])
            ->latest();
    }

    // Method untuk cek ketersediaan yang lebih akurat
    public function isActuallyAvailable()
    {
        // Cek dari status dan juga dari relasi borrow
        return $this->status === 'available' && !$this->currentBorrow()->exists();
    }
    
    // Helper: Cek apakah copy tersedia
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    // Helper: Format status
    public function getStatusTextAttribute()
    {
        return $this->status === 'available' ? 'Tersedia' : 'Dipinjam';
    }

    // Helper: Format inventory number dengan leading zeros
    public function getFormattedInventoryNumberAttribute()
    {
        return str_pad($this->inventory_number, 5, '0', STR_PAD_LEFT);
    }
}