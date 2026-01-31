<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Member;
use App\Models\BookCopy;
use App\Models\Book;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Borrow::with(['member.user', 'bookCopy.book']);
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('borrow_code', 'like', "%{$search}%")
                  ->orWhereHas('member.user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('bookCopy.book', function($bookQuery) use ($search) {
                      $bookQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('borrow_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('borrow_date', '<=', $request->date_to);
        }
        
        // Order by
        $orderBy = $request->get('order_by', 'borrow_date');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);
        
        $borrows = $query->paginate(20);
        
        return view('admin.borrows.index', compact('borrows'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::where('status', 'active')
            ->with('user')
            ->get();
            
        $books = Book::with(['copies' => function($query) {
            $query->where('status', 'available');
        }])->whereHas('copies', function($query) {
            $query->where('status', 'available');
        })->get();
        
        return view('admin.borrows.create', compact('members', 'books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_copy_id' => 'required|exists:book_copies,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            'notes' => 'nullable|string',
        ]);
        
        // Check member can borrow
        $member = Member::findOrFail($request->member_id);
        $activeBorrows = $member->borrows()->whereIn('status', ['borrowed', 'overdue'])->count();
        
        if ($activeBorrows >= 5) {
            return back()->with('error', 'Anggota sudah meminjam 5 buku (maksimal).');
        }
        
        // Check book copy availability
        $bookCopy = BookCopy::findOrFail($request->book_copy_id);
        if ($bookCopy->status !== 'available') {
            return back()->with('error', 'Copy buku tidak tersedia.');
        }
        
        DB::beginTransaction();
        
        try {
            $borrow = Borrow::create([
                'borrow_code' => Borrow::generateBorrowCode(),
                'member_id' => $request->member_id,
                'book_copy_id' => $request->book_copy_id,
                'borrow_date' => $request->borrow_date,
                'due_date' => $request->due_date,
                'notes' => $request->notes,
                'status' => 'borrowed',
            ]);
            
            $bookCopy->update(['status' => 'borrowed']);
            
            DB::commit();
            
            return redirect()->route('admin.borrows.index')
                ->with('success', 'Peminjaman berhasil dibuat.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrow $borrow)
    {
        $borrow->load(['member.user', 'bookCopy.book', 'member.class']);
        return view('admin.borrows.show', compact('borrow'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Borrow $borrow)
    {
        $borrow->load(['member.user', 'bookCopy.book']);
        return view('admin.borrows.edit', compact('borrow'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Borrow $borrow)
    {
        $request->validate([
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            'return_date' => 'nullable|date',
            'status' => 'required|in:borrowed,returned,overdue',
            'fine_amount' => 'nullable|numeric|min:0',
            'fine_paid' => 'boolean',
            'extension_count' => 'integer|min:0|max:1',
            'notes' => 'nullable|string',
        ]);
        
        $oldStatus = $borrow->status;
        $newStatus = $request->status;
        
        DB::beginTransaction();
        
        try {
            // Update borrow
            $borrow->update([
                'borrow_date' => $request->borrow_date,
                'due_date' => $request->due_date,
                'return_date' => $request->return_date,
                'status' => $newStatus,
                'fine_amount' => $request->fine_amount ?? 0,
                'fine_paid' => $request->fine_paid ?? false,
                'extension_count' => $request->extension_count ?? 0,
                'notes' => $request->notes,
            ]);
            
            // Update book copy status if status changed
            if ($oldStatus !== $newStatus) {
                if ($newStatus === 'returned') {
                    $borrow->bookCopy->update(['status' => 'available']);
                } elseif ($newStatus === 'borrowed' || $newStatus === 'overdue') {
                    $borrow->bookCopy->update(['status' => 'borrowed']);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.borrows.show', $borrow)
                ->with('success', 'Data peminjaman berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Borrow $borrow)
    {
        // Only allow deletion of returned borrows
        if ($borrow->status !== 'returned') {
            return back()->with('error', 'Hanya peminjaman yang sudah dikembalikan yang dapat dihapus.');
        }
        
        DB::beginTransaction();
        
        try {
            // Update book copy back to available
            $borrow->bookCopy->update(['status' => 'available']);
            
            // Delete borrow
            $borrow->delete();
            
            DB::commit();
            
            return redirect()->route('admin.borrows.index')
                ->with('success', 'Data peminjaman berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Extend borrow period
     */
    public function extend(Borrow $borrow)
    {
        if (!$borrow->canBeExtended()) {
            return back()->with('error', 'Peminjaman tidak dapat diperpanjang.');
        }
        
        $borrow->update([
            'due_date' => Carbon::parse($borrow->due_date)->addDays(7),
            'extension_count' => 1,
        ]);
        
        return back()->with('success', 'Peminjaman berhasil diperpanjang 7 hari.');
    }

    /**
     * Mark fine as paid
     */
    public function markPaid(Borrow $borrow)
    {
        $borrow->update(['fine_paid' => true]);
        
        return back()->with('success', 'Denda telah ditandai sebagai lunas.');
    }
}