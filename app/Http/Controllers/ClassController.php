<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = ClassModel::orderBy('grade')
            ->orderBy('class_name')
            ->paginate(10);
        return view('classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('classes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'grade' => 'required|string|in:10,11,12',
            'class_name' => 'required|string|max:100',
            'academic_year' => 'required|integer|min:2020|max:2030',
        ]);

        ClassModel::create($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassModel $class)
    {
        return view('classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassModel $class)
    {
        return view('classes.edit', compact('class'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassModel $class)
    {
        $request->validate([
            'grade' => 'required|string|in:10,11,12',
            'class_name' => 'required|string|max:100',
            'academic_year' => 'required|integer|min:2020|max:2030',
        ]);

        $class->update($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassModel $class)
    {
        // Cek apakah kelas punya anggota
        if ($class->members()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Tidak dapat menghapus kelas karena masih memiliki anggota.');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}