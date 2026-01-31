<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrow_code',
        'member_id',
        'book_copy_id',
        'borrow_date',
        'due_date',
        'return_date',
        'extension_count',
        'fine_amount',
        'fine_paid',
        'status',
        'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'boolean',
    ];  

    /**
     * Relasi ke Member
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Relasi ke BookCopy
     */
    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }

    /**
     * Scope untuk peminjaman aktif
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['borrowed', 'overdue']);
    }

    /**
     * Scope untuk peminjaman yang sudah dikembalikan
     */
    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    /**
     * Scope untuk peminjaman terlambat
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Cek apakah peminjaman bisa diperpanjang
     */
    public function canBeExtended()
    {
        return $this->status === 'borrowed' 
            && $this->extension_count < 1 
            && Carbon::now()->lt($this->due_date);
    }

    /**
     * Hitung denda otomatis
     */
    public function calculateFine()
    {
        if ($this->status !== 'overdue' || $this->fine_paid) {
            return 0;
        }

        $daysLate = Carbon::now()->diffInDays($this->due_date);
        $finePerDay = 1000; // Rp 1.000 per hari
        $totalFine = $daysLate * $finePerDay;

        return max(0, $totalFine);
    }

    /**
     * Generate borrow code
     */
    public static function generateBorrowCode()
    {
        $today = Carbon::now()->format('Ymd');
        $lastBorrow = self::where('borrow_code', 'like', "PINJ-{$today}-%")
            ->orderBy('borrow_code', 'desc')
            ->first();

        if ($lastBorrow) {
            $lastNumber = (int) substr($lastBorrow->borrow_code, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "PINJ-{$today}-{$newNumber}";
    }
}