<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Buku Baru
            </h2>
            <a href="{{ route('books.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('books.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kolom Kiri -->
                            <div class="space-y-6">
                                <div>
                                    <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN *</label>
                                    <input type="text" name="isbn" id="isbn" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           value="{{ old('isbn') }}"
                                           placeholder="Contoh: 978-602-427-122-3"
                                           required>
                                    @error('isbn')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Nomor ISBN buku (wajib)</p>
                                </div>

                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">Judul Buku *</label>
                                    <input type="text" name="title" id="title" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           value="{{ old('title') }}"
                                           placeholder="Judul lengkap buku"
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
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                           value="{{ old('author') }}"
                                           placeholder="Nama pengarang/penulis"
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
                                           value="{{ old('publisher') }}"
                                           placeholder="Nama penerbit"
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
                                            <option value="{{ $year }}" {{ old('publication_year') == $year ? 'selected' : '' }}>
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
                                           value="{{ old('receipt_date', date('Y-m-d')) }}"
                                           required>
                                    @error('receipt_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Tanggal buku diterima di perpustakaan</p>
                                </div>

                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah Copy *</label>
                                    <input type="number" name="quantity" id="quantity" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           value="{{ old('quantity', 1) }}"
                                           min="1" max="1000"
                                           required>
                                    @error('quantity')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Jumlah copy fisik yang masuk (akan generate No Induk otomatis)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi (full width) -->
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                      placeholder="Sinopsis atau deskripsi singkat">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tombol Submit -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-black font-bold py-2 px-6 rounded">
                                    Simpan Buku
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Set receipt_date default to today
        document.getElementById('receipt_date').valueAsDate = new Date();
        
        // Auto-format ISBN (optional)
        document.getElementById('isbn').addEventListener('input', function(e) {
            // Bisa ditambahkan auto-format jika perlu
        });
    </script>
    @endpush
</x-app-layout>