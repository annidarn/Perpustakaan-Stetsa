<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\Admin\BorrowController as AdminBorrowController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('classes', ClassController::class);
    Route::resource('books', BookController::class);
    Route::delete('/books/{book}/copies/{copy}', [BookController::class, 'deleteCopy'])->name('books.delete-copy');
    Route::resource('members', MemberController::class);
    Route::patch('/members/{member}/update-status', [MemberController::class, 'updateStatus'])->name('members.update.status');
    Route::post('/members/batch-update', [MemberController::class, 'batchUpdate'])->name('members.batch.update');
    Route::post('/members/batch-delete', [MemberController::class, 'batchDelete'])->name('members.batch.delete');
});

// Public Terminal Routes (di luar middleware auth)
Route::prefix('terminal')->name('terminal.')->group(function () {
    Route::get('/', [TerminalController::class, 'index'])->name('index');
    Route::get('/search', [TerminalController::class, 'search'])->name('search');
    Route::post('/validate-member', [TerminalController::class, 'validateMember'])->name('validate.member');
    Route::get('/borrow/{member}/{book}', [TerminalController::class, 'showBorrowForm'])->name('borrow.form');
    Route::post('/borrow/{member}', [TerminalController::class, 'processBorrow'])->name('borrow.process');
    Route::get('/borrow/{borrow}/receipt', [TerminalController::class, 'showReceipt'])->name('borrow.receipt');
    Route::get('/return', [TerminalController::class, 'showReturnForm'])->name('return.form');
    Route::post('/return', [TerminalController::class, 'processReturn'])->name('return.process');
});

// Admin Borrows
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('borrows', AdminBorrowController::class);
    Route::post('/borrows/{borrow}/extend', [AdminBorrowController::class, 'extend'])->name('borrows.extend');
    Route::post('/borrows/{borrow}/mark-paid', [AdminBorrowController::class, 'markPaid'])->name('borrows.mark-paid');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('admin.dashboard');
});

