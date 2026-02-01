<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrow;
use App\Models\BookCopy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        return view('admin.dashboard.index', compact(
            'stats', 
            'dueSoon'
        ));
    }
}