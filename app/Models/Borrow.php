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

    // relasi ke Member
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // relasi ke BookCopy
    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }

    // scope untuk peminjaman aktif
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['borrowed', 'overdue']);
    }

    // scope untuk peminjaman yang sudah dikembalikan
    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    // scope untuk peminjaman terlambat
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    // cek apakah peminjaman bisa diperpanjang
    public function canBeExtended()
    {
        return $this->status === 'borrowed' 
            && $this->extension_count < 1 
            && Carbon::now()->lt($this->due_date);
    }

    public function daysLate()
    {
        if ($this->status === 'returned') {
            if (!$this->return_date) return 0;
            return max(0, Carbon::parse($this->due_date)->startOfDay()->diffInDays(Carbon::parse($this->return_date)->startOfDay(), false));
        }

        if (Carbon::now()->lte($this->due_date)) {
            return 0;
        }

        return Carbon::parse($this->due_date)->startOfDay()->diffInDays(Carbon::now()->startOfDay());
    }

    // hitung denda otomatis
    public function calculateFine()
    {
        if ($this->status === 'returned') {
            return $this->fine_amount;
        }

        if (Carbon::now()->lte($this->due_date)) {
            return 0;
        }

        $dueDate = Carbon::parse($this->due_date)->startOfDay();
        $now = Carbon::now()->startOfDay();
        
        $daysLate = $dueDate->diffInDays($now);
        $finePerDay = 1000; // Rp 1.000 per hari
        $totalFine = $daysLate * $finePerDay;

        return max(0, $totalFine);
    }

    // update status peminjaman yang terlambat secara massal
    public static function updateOverdueStatuses()
    {
        $overdueBorrows = self::where('status', 'borrowed')
            ->where('due_date', '<', Carbon::now())
            ->get();

        foreach ($overdueBorrows as $borrow) {
            $borrow->update([
                'status' => 'overdue',
                'fine_amount' => $borrow->calculateFine()
            ]);
        }

        $stillOverdue = self::where('status', 'overdue')
            ->whereNull('return_date')
            ->get();

        foreach ($stillOverdue as $borrow) {
            $borrow->update([
                'fine_amount' => $borrow->calculateFine()
            ]);
        }
    }

    // generate borrow code
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