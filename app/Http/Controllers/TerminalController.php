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
    // menampilkan halaman terminal utama
    public function index()
    {
        return view('terminal.index');
    }

    // pencarian buku untuk peminjaman
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

    // menampilkan formulir peminjaman setelah validasi anggota
    public function showBorrowForm(Member $member, Book $book)
    {
        // validasi: cek apakah member bisa meminjam
        $validation = $this->validateMemberForBorrow($member);
        
        if (!$validation['can_borrow'] && !session('success')) {
            return redirect()->route('terminal.index')
                ->with('error', $validation['message']);
        }

        // mendapatkan copy buku ini yang tersedia
        $availableCopies = BookCopy::where('book_id', $book->id)
            ->where('status', 'available')
            ->get();

        if ($availableCopies->isEmpty() && !session('success')) {
            return redirect()->route('terminal.index')
                ->with('error', 'Maaf, tidak ada copy tersedia untuk buku ini.');
        }

        return view('terminal.borrow-form', compact('member', 'book', 'availableCopies'));
    }

    // proses peminjaman buku
    public function processBorrow(Request $request, Member $member)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'book_copy_id' => 'required|exists:book_copies,id',
            'terms' => 'required|accepted'
        ]);

        // periksa kembali validasi anggota
        $validation = $this->validateMemberForBorrow($member);
        if (!$validation['can_borrow']) {
            return redirect()->route('terminal.index')
                ->with('error', $validation['message']);
        }

        // mendapatkan buku dan copy buku
        $book = Book::findOrFail($request->book_id);
        $bookCopy = BookCopy::findOrFail($request->book_copy_id);

        // verifikasi copy buku tersedia
        if ($bookCopy->status !== 'available') {
            return redirect()->route('terminal.borrow.form', ['member' => $member, 'book' => $book->id])
                ->with('error', 'Copy buku tidak tersedia untuk dipinjam.');
        }

        // verifikasi salinan tersebut milik buku tersebut
        if ($bookCopy->book_id != $book->id) {
            return redirect()->route('terminal.borrow.form', ['member' => $member, 'book' => $book->id])
                ->with('error', 'Copy tidak sesuai dengan buku.');
        }

        DB::beginTransaction();
        
        try {
            // buat catatan peminjaman
            $borrow = Borrow::create([
                'borrow_code' => Borrow::generateBorrowCode(),
                'member_id' => $member->id,
                'book_copy_id' => $bookCopy->id,
                'borrow_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays(7),
                'status' => 'borrowed',
            ]);

            // update status salinan buku
            $bookCopy->update(['status' => 'borrowed']);

            DB::commit();

            // pesan berhasil
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

    // verifikasi keanggotaan untuk peminjaman
    private function validateMemberForBorrow(Member $member)
    {
        Borrow::updateOverdueStatuses();

        // 1. cek status anggota
        if ($member->status !== 'active') {
            return [
                'can_borrow' => false,
                'message' => 'Anggota tidak aktif. Status: ' . 
                    ($member->status === 'inactive' ? 'Non-Aktif' : 'Lulus')
            ];
        }

        // 2. cek jumlah buku sedang dipinjam
        $activeBorrows = $member->borrows()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->count();

        if ($activeBorrows >= 5) {
            return [
                'can_borrow' => false,
                'message' => 'Anggota sudah mencapai maksimal peminjaman.'
            ];
        }

        // 3. cek denda belum dibayar
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

    // menunjukkan tanda terima peminjaman
    public function showReceipt(Borrow $borrow)
    {
        $borrow->load(['member.user', 'bookCopy.book']);
        
        return view('terminal.borrow-receipt', compact('borrow'));
    }

    // proses pengembalian buku
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
        
        // temukan catatan peminjaman
        $borrow = Borrow::where('borrow_code', $request->borrow_code)
            ->with(['member.user', 'bookCopy.book'])
            ->first();
        
        if (!$borrow) {
            return redirect()->route('terminal.return.form')
                ->with('error', 'Kode peminjaman tidak ditemukan.');
        }
        
        // periksa apakah sudah dikembalikan
        if ($borrow->status === 'returned') {
            return redirect()->route('terminal.return.form')
                ->with('error', 'Buku sudah dikembalikan sebelumnya.');
        }
        
        // hitung denda jika terlambat membayar
        $daysLate = $borrow->daysLate();
        $fineAmount = $borrow->calculateFine();
        $isOverdue = $daysLate > 0;
        
        // mulai transaksi
        DB::beginTransaction();
        
        try {
            // update catatan peminjaman
            $borrow->update([
                'return_date' => Carbon::now(),
                'status' => 'returned',
                'fine_amount' => $fineAmount,
                'fine_paid' => $fineAmount > 0 ? false : true,
            ]);
            
            // update status copy buku
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

    // validasi pengidentifikasi anggota
    public function validateMember(Request $request)
    {
        $request->validate([
            'member_identifier' => 'required|string',
            'book_id' => 'required|exists:books,id'
        ]);
        
        // Cari member berdasarkan NIS atau NIP
        $identifier = trim($request->member_identifier);
        $member = Member::where('nis', $identifier)
            ->orWhere('nip', $identifier)
            ->first();
        
        if (!$member) {
            return back()
                ->with('error', 'Anggota dengan NIS/NIP tersebut tidak ditemukan.')
                ->withInput();
        }
        
        // validasi apakah bisa meminjam
        $validation = $this->validateMemberForBorrow($member);
        
        if (!$validation['can_borrow']) {
            return back()
                ->with('error', $validation['message'])
                ->withInput();
        }
        
        // redirect ke borrow form
        return redirect()->route('terminal.borrow.form', [
            'member' => $member,
            'book' => $request->book_id
        ]);
    }
}