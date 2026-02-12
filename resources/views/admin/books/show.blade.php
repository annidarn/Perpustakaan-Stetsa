<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Buku: {{ $book->title }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('admin.books.edit', $book) }}" 
                   class="inline-flex items-center px-3 py-1.5 bg-amber-500 text-white rounded-md hover:bg-amber-600 shadow-sm transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    <span>Edit Buku</span>
                </a>
                <a href="{{ route('admin.books.index') }}" 
                   class="inline-flex items-center px-3 py-1.5 bg-gray-500 text-white rounded-md hover:bg-gray-600 shadow-sm transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Informasi Buku -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-6 text-gray-700">Informasi Buku</h3>
                            
                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Judul Buku</h4>
                                        <p class="mt-1 text-lg font-semibold">{{ $book->title }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">ISBN</h4>
                                        <p class="mt-1 font-mono">{{ $book->isbn }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Pengarang</h4>
                                        <p class="mt-1">{{ $book->author }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Penerbit</h4>
                                        <p class="mt-1">{{ $book->publisher }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Tahun Terbit</h4>
                                        <p class="mt-1">{{ $book->publication_year }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Tanggal Diterima</h4>
                                        <p class="mt-1">{{ \Carbon\Carbon::parse($book->receipt_date)->format('d/m/Y') }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Kategori</h4>
                                        <p class="mt-1">
                                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $book->category->notation }} - {{ $book->category->name }}
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Total Copy</h4>
                                        <p class="mt-1">
                                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                                {{ $book->availableCopiesCount() > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $book->availableCopiesCount() }} tersedia / {{ $book->totalCopiesCount() }} total
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                @if($book->description)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Deskripsi</h4>
                                    <p class="mt-1 text-gray-700 whitespace-pre-line">{{ $book->description }}</p>
                                </div>
                                @endif

                                <!-- Statistik -->
                                <div class="pt-4 border-t border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-500 mb-3">Statistik</h4>
                                    <div class="grid grid-cols-3 gap-4"> <!-- Ganti dari grid-cols-4 ke grid-cols-3 -->
                                        <div class="bg-blue-50 p-4 rounded-lg text-center">
                                            <div class="text-2xl font-bold text-blue-700">
                                                {{ $book->totalCopiesCount() }}
                                            </div>
                                            <div class="text-sm text-blue-600">Total Copy</div>
                                        </div>
                                        <div class="bg-green-50 p-4 rounded-lg text-center">
                                            <div class="text-2xl font-bold text-green-700">
                                                {{ $book->availableCopiesCount() }}
                                            </div>
                                            <div class="text-sm text-green-600">Tersedia</div>
                                        </div>
                                        <div class="bg-blue-50 p-4 rounded-lg text-center">
                                            <div class="text-2xl font-bold text-blue-700">
                                                {{ $book->copies->where('status', 'borrowed')->count() }}
                                            </div>
                                            <div class="text-sm text-blue-600">Dipinjam</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Copy Buku -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-semibold text-gray-700">Daftar Copy</h3>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $book->copies->count() }} copy
                                </span>
                            </div>

                            <!-- Daftar Copy -->
                            @if($book->copies->count() > 0)
                                <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                                    @foreach($book->copies as $copy)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="font-bold text-lg text-gray-900">
                                                    No. {{ $copy->formatted_inventory_number }}
                                                </div>
                                                
                                                <!-- Badge Status -->
                                                <div class="mt-2">
                                                    <span class="px-3 py-1 text-xs font-semibold rounded border
                                                        {{ $copy->status == 'available' ? 'bg-green-50 text-green-700 border-green-300' : 
                                                        'bg-blue-50 text-blue-700 border-blue-300' }}">
                                                        {{ $copy->status_text }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Catatan -->
                                                @if($copy->notes)
                                                    <div class="mt-2 text-sm text-gray-600">
                                                        <span class="font-medium">Catatan:</span> {{ $copy->notes }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Tombol Hapus -->
                                            <div class="ml-4">
                                                <form action="{{ route('admin.books.delete-copy', ['book' => $book, 'copy' => $copy]) }}" method="POST" 
                                                      onsubmit="return confirm('Hapus copy No. {{ $copy->formatted_inventory_number }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 shadow-sm transition-colors">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                                    <div class="text-4xl mb-3">📚</div>
                                    <p class="text-lg">Belum ada copy fisik.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Hapus Buku -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Aksi Berbahaya</h4>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST" 
                                  onsubmit="return confirm('HAPUS PERMANEN: Buku \"{{ $book->title }}\" dan semua {{ $book->copies->count() }} copy akan dihapus. Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-medium shadow-sm transition-colors">
                                    <i class="fas fa-trash mr-2"></i>
                                    <span>Hapus Buku & Semua Copy</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    @endpush
</x-app-layout>