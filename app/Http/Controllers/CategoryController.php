<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // menampilkan daftar data
    public function index()
    {
        $categories = Category::orderBy('notation')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    // menampilkan formulir untuk membuat data baru
    public function create()
    {
        return view('categories.create');
    }

    // menyimpan data yang baru dibuat di penyimpanan
    public function store(Request $request)
    {
        $request->validate([
            'notation' => 'required|string|max:10|unique:categories',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    // menampilkan data yang ditentukan
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    // menampilkan formulir untuk mengedit data yang ditentukan
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // memperbarui data yang ditentukan dalam penyimpanan
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'notation' => 'required|string|max:10|unique:categories,notation,' . $category->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    // hapus data yang ditentukan dari penyimpanan
    public function destroy(Category $category)
    {
        // cek apakah kategori punya buku
        if ($category->books()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Tidak dapat menghapus kategori karena masih memiliki buku.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}