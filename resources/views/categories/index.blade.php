<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kategori Buku
            </h2>
            <a href="{{ route('categories.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                <i class="fas fa-plus mr-2"></i> Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
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

                    <!-- Table -->
                     <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Buku</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($categories as $category)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">{{ $category->notation }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $category->name }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $category->description ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $category->books()->count() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <!-- Tombol Lihat -->
                                            <a href="{{ route('categories.show', $category) }}" 
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-sm transition-colors">
                                                <i class="fas fa-eye mr-1"></i>
                                                <span class="text-sm">Detail</span>
                                            </a>
                                            
                                            <!-- Tombol Edit -->
                                            <a href="{{ route('categories.edit', $category) }}" 
                                            class="inline-flex items-center px-3 py-1.5 bg-amber-500 text-white rounded-md hover:bg-amber-600 shadow-sm transition-colors">
                                                <i class="fas fa-edit mr-1"></i>
                                                <span class="text-sm">Edit</span>
                                            </a>
                                            
                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" 
                                                class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 shadow-sm transition-colors">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    <span class="text-sm">Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Belum ada data kategori.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome CDN -->
    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    @endpush
</x-app-layout>