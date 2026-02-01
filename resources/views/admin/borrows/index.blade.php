<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Daftar Peminjaman
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Kelola semua transaksi peminjaman buku
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.borrows.create') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-sm flex items-center">
                    <i class="fas fa-plus mr-2"></i> Peminjaman Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                    <div class="text-sm text-gray-500">Total Peminjaman</div>
                    <div class="text-2xl font-bold">{{ \App\Models\Borrow::count() }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                    <div class="text-sm text-gray-500">Sedang Dipinjam</div>
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ \App\Models\Borrow::where('status', 'borrowed')->count() }}
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                    <div class="text-sm text-gray-500">Sudah Dikembalikan</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ \App\Models\Borrow::where('status', 'returned')->count() }}
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                    <div class="text-sm text-gray-500">Terlambat</div>
                    <div class="text-2xl font-bold text-red-600">
                        {{ \App\Models\Borrow::where('status', 'overdue')->count() }}
                    </div>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('admin.borrows.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                       placeholder="Kode, nama, atau judul buku">
                            </div>
                            
                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <option value="">Semua Status</option>
                                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                                </select>
                            </div>
                            
                            <!-- Date From -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" 
                                       name="date_from" 
                                       value="{{ request('date_from') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            </div>
                            
                            <!-- Date To -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" 
                                       name="date_to" 
                                       value="{{ request('date_to') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex items-end space-x-2">
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.borrows.index') }}" 
                                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-sm">
                                    Reset
                                </a>
                            </div>
                        </div>
                        
                        <!-- Order By -->
                        <div class="mt-4 flex items-center space-x-4">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-700 mr-2">Urutkan:</span>
                                <select name="order_by" 
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <option value="borrow_date" {{ request('order_by') == 'borrow_date' ? 'selected' : '' }}>Tanggal Pinjam</option>
                                    <option value="due_date" {{ request('order_by') == 'due_date' ? 'selected' : '' }}>Jatuh Tempo</option>
                                    <option value="created_at" {{ request('order_by') == 'created_at' ? 'selected' : '' }}>Tanggal Buat</option>
                                </select>
                            </div>
                            <div class="flex items-center">
                                <select name="order_dir" 
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <option value="desc" {{ request('order_dir') == 'desc' ? 'selected' : '' }}>↓ Terbaru</option>
                                    <option value="asc" {{ request('order_dir') == 'asc' ? 'selected' : '' }}>↑ Terlama</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alerts -->
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

            <!-- Borrows Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($borrows as $borrow)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-mono font-bold text-sm">{{ $borrow->borrow_code }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $borrow->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $borrow->member->user->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            @if($borrow->member->type === 'student')
                                                NIS: {{ $borrow->member->nis }}
                                            @else
                                                NIP: {{ $borrow->member->nip }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $borrow->bookCopy->book->title }}</div>
                                        <div class="text-sm text-gray-500">
                                            Copy #{{ str_pad($borrow->bookCopy->inventory_number, 5, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">
                                            <div class="mb-1">
                                                <span class="text-gray-600">Pinjam:</span>
                                                <span class="font-medium">{{ $borrow->borrow_date->format('d/m/Y') }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Tempo:</span>
                                                <span class="font-medium {{ $borrow->status === 'overdue' ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ $borrow->due_date->format('d/m/Y') }}
                                                </span>
                                            </div>
                                            @if($borrow->return_date)
                                            <div class="mt-1">
                                                <span class="text-gray-600">Kembali:</span>
                                                <span class="font-medium text-blue-600">{{ $borrow->return_date->format('d/m/Y') }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $borrow->status === 'borrowed' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($borrow->status === 'returned' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $borrow->status === 'borrowed' ? 'Dipinjam' : 
                                                   ($borrow->status === 'returned' ? 'Dikembalikan' : 'Terlambat') }}
                                            </span>
                                            
                                            @if($borrow->extension_count > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-redo mr-1"></i> Sudah diperpanjang
                                            </span>
                                            @endif
                                            
                                            @if($borrow->fine_amount > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $borrow->fine_paid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                <i class="fas fa-money-bill-wave mr-1"></i>
                                                Denda: Rp {{ number_format($borrow->fine_amount, 0, ',', '.') }}
                                                {{ $borrow->fine_paid ? '(Lunas)' : '(Belum)' }}
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.borrows.show', $borrow) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-sm transition-colors text-sm">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </a>
                                            
                                            @if($borrow->status === 'borrowed')
                                            <form action="{{ route('admin.borrows.extend', $borrow) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 shadow-sm transition-colors text-sm"
                                                        onclick="return confirm('Perpanjang peminjaman 7 hari?')">
                                                    <i class="fas fa-redo mr-1"></i> Perpanjang
                                                </button>
                                            </form>
                                            @endif
                                            
                                            @if($borrow->fine_amount > 0 && !$borrow->fine_paid)
                                            <form action="{{ route('admin.borrows.mark-paid', $borrow) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm shadow-sm transition-colors"
                                                        onclick="return confirm('Tandai denda sebagai lunas?')">
                                                    <i class="fas fa-check mr-1"></i> Lunas
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                            Tidak ditemukan peminjaman dengan filter tersebut.
                                        @else
                                            Belum ada data peminjaman.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $borrows->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    @endpush
</x-app-layout>