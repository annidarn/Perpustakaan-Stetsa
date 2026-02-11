<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\BookCopy;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // menampilkan daftar data
    public function index(Request $request)
    {
        $query = Book::with('category');
        
        // pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }
        
        // filter berdasarkan kategori
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        
        $books = $query->orderBy('title')->paginate(10);
        $categories = Category::orderBy('name')->get();
        
        return view('books.index', compact('books', 'categories'));
    }

    // menampilkan formulir untuk membuat data baru
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('books.create', compact('categories'));
    }

    // simpan data yang baru dibuat di penyimpanan
    public function store(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'publication_year' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'receipt_date' => 'required|date',
            'quantity' => 'required|integer|min:1|max:1000',
            'description' => 'nullable|string',
        ]);

        // 1. buat buku
        $book = Book::create([
            'isbn' => $request->isbn,
            'title' => $request->title,
            'category_id' => $request->category_id,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'publication_year' => $request->publication_year,
            'receipt_date' => $request->receipt_date,
            'description' => $request->description,
        ]);

        // 2. buat copy berdasarkan quantity
        $book->createCopies($request->quantity);

        return redirect()->route('books.index')
            ->with('success', "Buku '{$book->title}' berhasil ditambahkan dengan {$request->quantity} copy.");
    }

    // menampilkan data yang ditentukan
    public function show(Book $book)
    {
        $book->load(['category', 'copies']);
        return view('books.show', compact('book'));
    }

    // menampilkan formulir untuk mengedit data yang ditentukan
    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();
        return view('books.edit', compact('book', 'categories'));
    }

    // memperbarui data yang ditentukan dalam penyimpanan
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'isbn' => 'required|string|max:50' . $book->id,
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'publication_year' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'receipt_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $book->update($request->all());

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    // hapus data yang ditentukan dari penyimpanan
    public function destroy(Book $book)
    {
        $bookTitle = $book->title;
        $copyCount = $book->copies()->count();
        
        $book->copies()->delete();
        $book->delete();

        $message = "Buku '{$bookTitle}' berhasil dihapus.";
        if ($copyCount > 0) {
            $message = "Buku '{$bookTitle}' dan {$copyCount} copy berhasil dihapus.";
        }

        return redirect()->route('books.index')
            ->with('success', $message);
    }
    
    // hapus satu salinan
    public function deleteCopy(Book $book, BookCopy $copy)
    {
        if ($copy->status === 'borrowed') {
            return redirect()->route('books.show', $book)
                ->with('error', 'Tidak dapat menghapus copy yang sedang dipinjam.');
        }

        $inventoryNumber = $copy->formatted_inventory_number;
        $copy->delete();

        return redirect()->route('books.show', $book)
            ->with('success', "Copy No. {$inventoryNumber} berhasil dihapus.");
    }
}