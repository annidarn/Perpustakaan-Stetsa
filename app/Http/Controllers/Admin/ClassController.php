<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    // menampilkan data
    public function index()
    {
        $classes = ClassModel::orderBy('grade')
            ->orderBy('class_name')
            ->paginate(10);
        return view('admin.classes.index', compact('classes'));
    }

    // manampilkan formulir untuk membuat data baru
    public function create()
    {
        return view('admin.classes.create');
    }

    // simpan data yang baru dibuat di penyimpanan
    public function store(Request $request)
    {
        $request->validate([
            'grade' => 'required|string|in:10,11,12',
            'class_name' => 'required|string|max:100',
            'academic_year' => 'required|integer|min:2020|max:2030',
        ]);

        ClassModel::create($request->all());

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    // menampilkan data yang ditentukan
    public function show(ClassModel $class)
    {
        return view('admin.classes.show', compact('class'));
    }

    // menampilkan formulir untuk mengedit data yang ditentukan
    public function edit(ClassModel $class)
    {
        return view('admin.classes.edit', compact('class'));
    }

    // memperbarui data yang ditentukan dalam penyimpanan
    public function update(Request $request, ClassModel $class)
    {
        $request->validate([
            'grade' => 'required|string|in:10,11,12',
            'class_name' => 'required|string|max:100',
            'academic_year' => 'required|integer|min:2020|max:2030',
        ]);

        $class->update($request->all());

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    // hapus data yang ditentukan dari penyimpanan
    public function destroy(ClassModel $class)
    {
        // cek apakah kelas punya anggota
        if ($class->members()->count() > 0) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Tidak dapat menghapus kelas karena masih memiliki anggota.');
        }

        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}