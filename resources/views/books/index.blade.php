<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Buku
            </h2>
            <a href="{{ route('books.create') }}" class="bg-blue-500 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded">
                Tambah Buku
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Search & Filter -->
                    <form method="GET" action="{{ route('books.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Search Input -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Cari Buku</label>
                                <input type="text" name="search" id="search" 
                                       value="{{ request('search') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                       placeholder="Judul, pengarang, atau ISBN">
                            </div>
                            
                            <!-- Category Filter -->
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Filter Kategori</label>
                                <select name="category_id" id="category_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->notation }} - {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex items-end space-x-2">
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded">
                                    <i class="fas fa-search mr-1"></i> Cari
                                </button>
                                <a href="{{ route('books.index') }}" 
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Alert Messages -->
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Books Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Buku</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengarang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ISBN</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Copy</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($books as $book)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $loop->iteration + (($books->currentPage() - 1) * $books->perPage()) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $book->title }}</div>
                                        <div class="text-sm text-gray-500">Terima: {{ \Carbon\Carbon::parse($book->receipt_date)->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $book->author }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $book->category->notation }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $book->isbn }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $book->availableCopiesCount() > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $book->availableCopiesCount() }} / {{ $book->totalCopiesCount() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('books.show', $book) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200">
                                                <i class="fas fa-eye mr-1"></i>
                                                <span class="text-sm">Detail</span>
                                            </a>
                                            <a href="{{ route('books.edit', $book) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-md hover:bg-yellow-200">
                                                <i class="fas fa-edit mr-1"></i>
                                                <span class="text-sm">Edit</span>
                                            </a>
                                            <form action="{{ route('books.destroy', $book) }}" method="POST" 
                                                  class="inline" onsubmit="return confirm('Hapus buku {{ $book->title }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-800 rounded-md hover:bg-red-200">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    <span class="text-sm">Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        @if(request()->has('search') || request()->has('category_id'))
                                            Tidak ditemukan buku dengan filter tersebut.
                                        @else
                                            Belum ada data buku.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $books->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    @endpush
</x-app-layout>