<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\BookCopy;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TerminalController extends Controller
{
    /**
     * Display the main terminal page
     */
    public function index()
    {
        return view('terminal.index');
    }

    /**
     * Search books for borrowing
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'nullable|string'
        ]);

        $searchQuery = $request->input('query');

        if (!$searchQuery) {
            return redirect()->route('terminal.index');
        }

        $books = Book::with(['category', 'copies' => function($query) {
                $query->where('status', 'available');
            }])
            ->where(function($q) use ($searchQuery) {
                $q->where('title', 'like', "%{$searchQuery}%")
                ->orWhere('author', 'like', "%{$searchQuery}%")
                ->orWhere('isbn', 'like', "%{$searchQuery}%");
            })
            ->whereHas('copies', function($q) {
                $q->where('status', 'available');
            })
            ->paginate(10);

        return view('terminal.search-results', compact('books'));
    }

    /**
     * Show borrow form after member validation
     */
    public function showBorrowForm(Member $member, Book $book)
    {
        // Validasi: cek apakah member bisa meminjam
        $validation = $this->validateMemberForBorrow($member);
        
        if (!$validation['can_borrow']) {
            return back()
                ->with('error', $validation['message'])
                ->withInput();
        }

        // Get available copies of this book
        $availableCopies = BookCopy::where('book_id', $book->id)
            ->where('status', 'available')
            ->get();

        if ($availableCopies->isEmpty()) {
            return back()
                ->with('error', 'Maaf, tidak ada copy tersedia untuk buku ini.');
        }

        return view('terminal.borrow-form', compact('member', 'book', 'availableCopies'));
    }

    /**
     * Process book borrowing
     */
    public function processBorrow(Request $request, Member $member)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'book_copy_id' => 'required|exists:book_copies,id',
            'terms' => 'required|accepted'
        ]);

        // Double-check member validation
        $validation = $this->validateMemberForBorrow($member);
        if (!$validation['can_borrow']) {
            return back()
                ->with('error', $validation['message'])
                ->withInput();
        }

        // Get book and copy === PERBAIKAN: DAPATKAN $book DARI $request ===
        $book = Book::findOrFail($request->book_id); // ← TAMBAHKAN INI
        $bookCopy = BookCopy::findOrFail($request->book_copy_id);

        // Verify copy is available
        if ($bookCopy->status !== 'available') {
            return redirect()->route('terminal.borrow.form', ['member' => $member, 'book' => $book->id])
                ->with('error', 'Copy buku tidak tersedia untuk dipinjam.');
        }

        // Verify copy belongs to the book
        if ($bookCopy->book_id != $book->id) {
            return redirect()->route('terminal.borrow.form', ['member' => $member, 'book' => $book->id])
                ->with('error', 'Copy tidak sesuai dengan buku.');
        }

        DB::beginTransaction();
        
        try {
            // Create borrow record
            $borrow = Borrow::create([
                'borrow_code' => Borrow::generateBorrowCode(),
                'member_id' => $member->id,
                'book_copy_id' => $bookCopy->id,
                'borrow_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays(7),
                'status' => 'borrowed',
            ]);

            // Update book copy status
            $bookCopy->update(['status' => 'borrowed']);

            DB::commit();

            // Success message
            $message = "PEMINJAMAN BERHASIL DIPROSES\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "KODE     : " . $borrow->borrow_code . "\n";
            $message .= "ANGGOTA  : " . $member->user->name . "\n";
            $message .= "BUKU     : " . $book->title . "\n";
            $message .= "COPY     : #" . str_pad($bookCopy->inventory_number, 5, '0', STR_PAD_LEFT) . "\n";
            $message .= "PINJAM   : " . Carbon::now()->format('d/m/Y') . "\n";
            $message .= "TEMPO    : " . Carbon::now()->addDays(7)->format('d/m/Y') . "\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "💡 INFORMASI:\n";
            $message .= "• Durasi pinjam: 7 hari\n";
            $message .= "• Maksimal: 5 buku/anggota\n";
            $message .= "• Denda: Rp 1.000/hari\n";
            $message .= "• Perpanjangan: 1x (+7 hari)\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "⚠️  CATAT KODE DI ATAS UNTUK PENGEMBALIAN!";

            return redirect()->route('terminal.borrow.form', ['member' => $member, 'book' => $book->id])
                ->with('success', $message)
                ->with('borrow_code', $borrow->borrow_code);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('terminal.borrow.form', ['member' => $member, 'book' => $book->id])
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Validate member for borrowing
     */
    private function validateMemberForBorrow(Member $member)
    {
        Borrow::updateOverdueStatuses();

        // 1. Cek status anggota
        if ($member->status !== 'active') {
            return [
                'can_borrow' => false,
                'message' => 'Anggota tidak aktif. Status: ' . 
                    ($member->status === 'inactive' ? 'Non-Aktif' : 'Lulus')
            ];
        }

        // 2. Cek jumlah buku sedang dipinjam
        $activeBorrows = $member->borrows()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->count();

        if ($activeBorrows >= 5) {
            return [
                'can_borrow' => false,
                'message' => 'Anggota sudah meminjam 5 buku (maksimal).'
            ];
        }

        // 3. Cek denda belum dibayar (Include returned but unpaid)
        $unpaidFines = $member->borrows()
            ->where(function($q) {
                $q->where('fine_paid', false)->orWhereNull('fine_paid');
            })
            ->where('fine_amount', '>', 0)
            ->exists();

        if ($unpaidFines) {
            return [
                'can_borrow' => false,
                'message' => 'Anggota memiliki denda belum dibayar.'
            ];
        }

        return [
            'can_borrow' => true,
            'message' => 'Anggota dapat meminjam.',
            'active_borrows' => $activeBorrows
        ];
    }

    /**
     * Show borrowing receipt
     */
    public function showReceipt(Borrow $borrow)
    {
        $borrow->load(['member.user', 'bookCopy.book']);
        
        return view('terminal.borrow-receipt', compact('borrow'));
    }

    /**
     * Process book return
     */
    public function showReturnForm()
    {
        return view('terminal.return-form');
    }
    public function processReturn(Request $request)
    {
        Borrow::updateOverdueStatuses();

        $request->validate([
            'borrow_code' => 'required|string|exists:borrows,borrow_code'
        ]);
        
        // Find borrow record
        $borrow = Borrow::where('borrow_code', $request->borrow_code)
            ->with(['member.user', 'bookCopy.book'])
            ->first();
        
        if (!$borrow) {
            return redirect()->route('terminal.return.form')
                ->with('error', 'Kode peminjaman tidak ditemukan.');
        }
        
        // Check if already returned
        if ($borrow->status === 'returned') {
            return redirect()->route('terminal.return.form')
                ->with('error', 'Buku sudah dikembalikan sebelumnya.');
        }
        
        // Calculate fine if overdue
        $fineAmount = 0;
        $isOverdue = false;
        $daysLate = 0;
        
        if (Carbon::now()->gt($borrow->due_date) && $borrow->status !== 'overdue') {
            $daysLate = Carbon::now()->diffInDays($borrow->due_date);
            $fineAmount = $daysLate * 1000; // Rp 1.000 per day
            $isOverdue = true;
        }
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Update borrow record
            $borrow->update([
                'return_date' => Carbon::now(),
                'status' => 'returned',
                'fine_amount' => $fineAmount,
                'fine_paid' => $fineAmount > 0 ? false : true,
            ]);
            
            // Update book copy status
            $borrow->bookCopy->update(['status' => 'available']);
            
            DB::commit();
            
            $message = "Pengembalian Berhasil!";
            $message .= "\nKode: " . $borrow->borrow_code;
            $message .= "\nBuku: " . $borrow->bookCopy->book->title;
            $message .= "\nAnggota: " . $borrow->member->user->name;
            $message .= "\nTanggal Kembali: " . Carbon::now()->format('d/m/Y H:i');
            
            if ($isOverdue) {
                $message .= "\n\n⚠️ TERLAMBAT: " . $daysLate . " hari";
                $message .= "\nDenda: Rp " . number_format($fineAmount, 0, ',', '.');
                $message .= "\nStatus: " . ($fineAmount > 0 ? "BELUM DIBAYAR" : "LUNAS");
            } else {
                $message .= "\n\n✓ TEPAT WAKTU";
            }
            
            return redirect()->route('terminal.return.form')
                ->with('success', $message);
                    
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('terminal.return.form')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Validate member identifier (NIS/NIP)
     */
    public function validateMember(Request $request)
    {
        $request->validate([
            'member_identifier' => 'required|string',
            'book_id' => 'required|exists:books,id'
        ]);
        
        // Cari member berdasarkan NIS atau NIP (trim for reliability)
        $identifier = trim($request->member_identifier);
        $member = Member::where('nis', $identifier)
            ->orWhere('nip', $identifier)
            ->first();
        
        if (!$member) {
            return back()
                ->with('error', 'Anggota dengan NIS/NIP tersebut tidak ditemukan.')
                ->withInput();
        }
        
        // Validasi apakah bisa meminjam
        $validation = $this->validateMemberForBorrow($member);
        
        if (!$validation['can_borrow']) {
            return back()
                ->with('error', $validation['message'])
                ->withInput();
        }
        
        // Redirect ke borrow form
        return redirect()->route('terminal.borrow.form', [
            'member' => $member,
            'book' => $request->book_id
        ]);
    }
}