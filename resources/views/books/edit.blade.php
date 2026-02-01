<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Buku: {{ $book->title }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('books.show', $book) }}" 
                   class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-sm transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    <span>Detail Buku</span>
                </a>
                <a href="{{ route('books.index') }}" 
                   class="inline-flex items-center px-3 py-1.5 bg-gray-500 text-white rounded-md hover:bg-gray-600 shadow-sm transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('books.update', $book) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kolom Kiri -->
                            <div class="space-y-6">
                                <div>
                                    <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN *</label>
                                    <input type="text" name="isbn" id="isbn" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           value="{{ old('isbn', $book->isbn) }}"
                                           required>
                                    @error('isbn')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">Judul Buku *</label>
                                    <input type="text" name="title" id="title" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           value="{{ old('title', $book->title) }}"
                                           required>
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori *</label>
                                    <select name="category_id" id="category_id" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                            required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->notation }} - {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="author" class="block text-sm font-medium text-gray-700">Pengarang *</label>
                                    <input type="text" name="author" id="author" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           value="{{ old('author', $book->author) }}"
                                           required>
                                    @error('author')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="space-y-6">
                                <div>
                                    <label for="publisher" class="block text-sm font-medium text-gray-700">Penerbit *</label>
                                    <input type="text" name="publisher" id="publisher" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           value="{{ old('publisher', $book->publisher) }}"
                                           required>
                                    @error('publisher')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="publication_year" class="block text-sm font-medium text-gray-700">Tahun Terbit *</label>
                                    <select name="publication_year" id="publication_year" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                            required>
                                        <option value="">Pilih Tahun</option>
                                        @for($year = date('Y') + 5; $year >= 1900; $year--)
                                            <option value="{{ $year }}" {{ old('publication_year', $book->publication_year) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('publication_year')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="receipt_date" class="block text-sm font-medium text-gray-700">Tanggal Diterima *</label>
                                    <input type="date" name="receipt_date" id="receipt_date" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           value="{{ old('receipt_date', $book->receipt_date->format('Y-m-d')) }}"
                                           required>
                                    @error('receipt_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Info Copy (readonly) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Informasi Copy</label>
                                    <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                        <p class="text-sm">
                                            <span class="font-medium">Total Copy:</span> {{ $book->totalCopiesCount() }}
                                        </p>
                                        <p class="text-sm">
                                            <span class="font-medium">Tersedia:</span> {{ $book->availableCopiesCount() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi (full width) -->
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('description', $book->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-sm text-gray-500">
                                        Dibuat: {{ $book->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div class="flex space-x-4">
                                    <a href="{{ route('books.show', $book) }}" 
                                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded shadow-sm transition-colors">
                                        Batal
                                    </a>
                                    <button type="submit" 
                                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow-sm transition-colors">
                                        <i class="fas fa-save mr-2"></i> Update Buku
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-format ISBN (optional)
        document.getElementById('isbn').addEventListener('input', function(e) {
            // Bisa ditambahkan auto-format jika perlu
        });
    </script>
    @endpush
</x-app-layout>