<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrow;
use App\Models\BookCopy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        Borrow::updateOverdueStatuses();

        $stats = [
            'total_members' => Member::count(),
            'total_books' => Book::count(),
            'available_copies' => BookCopy::where('status', 'available')->count(),
            'today_borrows' => Borrow::whereDate('borrow_date', today())->count(),
            'active_borrows' => Borrow::active()->count(),
            'overdue_borrows' => Borrow::overdue()->count(),
            'total_fines' => Borrow::where('fine_amount', '>', 0)
                ->where(function($q) {
                    $q->where('fine_paid', false)->orWhereNull('fine_paid');
                })
                ->sum('fine_amount'),
        ];

        $dueSoon = Borrow::with(['member.user', 'bookCopy.book'])
            ->where('status', 'borrowed')
            ->where('due_date', '<=', now()->addDays(2))
            ->where('due_date', '>', now())
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        // Filter untuk Grafik Peminjaman
        $month = request('month', now()->month);
        $year = request('year', now()->year);
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Data untuk Grafik Peminjaman (Bulan Terpilih)
        $borrowTrends = Borrow::select(
                DB::raw('DATE(borrow_date) as date'),
                DB::raw('count(*) as count')
            )
            ->whereBetween('borrow_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = [];
        $chartData = [];
        
        $daysInMonth = $startDate->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDate = Carbon::createFromDate($year, $month, $i)->format('Y-m-d');
            $label = $i;
            $chartLabels[] = $label;
            
            $trend = $borrowTrends->firstWhere('date', $currentDate);
            $chartData[] = $trend ? $trend->count : 0;
        }

        $selectedMonthName = $startDate->translatedFormat('F');
        $selectedYear = $year;

        // Data untuk Distribusi Buku per Kategori
        $categoriesStats = DB::table('categories')
            ->leftJoin('books', 'categories.id', '=', 'books.category_id')
            ->select('categories.name', DB::raw('count(books.id) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->having('total', '>', 0)
            ->get();

        // 1. Top 5 Buku Populer (Paling banyak dipinjam)
        $popularBooks = DB::table('borrows')
            ->join('book_copies', 'borrows.book_copy_id', '=', 'book_copies.id')
            ->join('books', 'book_copies.book_id', '=', 'books.id')
            ->select('books.title', DB::raw('count(borrows.id) as total'))
            ->groupBy('books.id', 'books.title')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // 2. Distribusi Anggota per Grade (X, XI, XII)
        $gradeStats = DB::table('members')
            ->join('classes', 'members.class_id', '=', 'classes.id')
            ->select('classes.grade', DB::raw('count(members.id) as total'))
            ->groupBy('classes.grade')
            ->orderBy('classes.grade')
            ->get();

        // 3. Status Copy Buku (Tersedia vs Dipinjam)
        $copyStatusStats = [
            'available' => BookCopy::where('status', 'available')->count(),
            'borrowed' => BookCopy::whereIn('status', ['borrowed', 'overdue'])->count(),
        ];

        return view('admin.dashboard.index', compact(
            'stats', 
            'dueSoon',
            'chartLabels',
            'chartData',
            'selectedMonthName',
            'selectedYear',
            'categoriesStats',
            'popularBooks',
            'gradeStats',
            'copyStatusStats'
        ));
    }
}