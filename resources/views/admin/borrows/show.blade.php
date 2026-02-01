<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Peminjaman
                </h2>
                <div class="flex items-center mt-1">
                    <span class="font-mono text-lg font-bold text-gray-700 mr-3">{{ $borrow->borrow_code }}</span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $borrow->status === 'borrowed' ? 'bg-yellow-100 text-yellow-800' : 
                           ($borrow->status === 'returned' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                        {{ $borrow->status === 'borrowed' ? 'Dipinjam' : 
                           ($borrow->status === 'returned' ? 'Dikembalikan' : 'Terlambat') }}
                    </span>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.borrows.edit', $borrow) }}" 
                   class="bg-yellow-500 hover:bg-yellow-700 text-black font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('admin.borrows.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Borrow Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Borrow Information Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                Informasi Peminjaman
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm text-gray-500">Kode Peminjaman</div>
                                    <div class="font-mono font-bold text-lg">{{ $borrow->borrow_code }}</div>
                                </div>
                                
                                <div>
                                    <div class="text-sm text-gray-500">Tanggal Transaksi</div>
                                    <div class="font-medium">{{ $borrow->created_at->format('d F Y H:i:s') }}</div>
                                </div>
                                
                                <div>
                                    <div class="text-sm text-gray-500">Tanggal Pinjam</div>
                                    <div class="font-medium">{{ $borrow->borrow_date->format('d F Y') }}</div>
                                </div>
                                
                                <div>
                                    <div class="text-sm text-gray-500">Jatuh Tempo</div>
                                    <div class="font-medium {{ $borrow->due_date->isPast() && $borrow->status !== 'returned' ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $borrow->due_date->format('d F Y') }}
                                        @if($borrow->due_date->isPast() && $borrow->status !== 'returned')
                                            (Terlambat {{ (int) $borrow->due_date->diffInDays(now()) }} hari)
                                        @endif
                                    </div>
                                </div>
                                
                                @if($borrow->return_date)
                                <div>
                                    <div class="text-sm text-gray-500">Tanggal Kembali</div>
                                    <div class="font-medium text-blue-600">{{ $borrow->return_date->format('d F Y') }}</div>
                                </div>
                                @endif
                                
                                <div>
                                    <div class="text-sm text-gray-500">Perpanjangan</div>
                                    <div class="font-medium">
                                        @if($borrow->extension_count > 0)
                                            <span class="text-green-600">Sudah 1x (maksimal)</span>
                                        @else
                                            <span class="text-gray-500">Belum</span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($borrow->notes)
                                <div class="md:col-span-2">
                                    <div class="text-sm text-gray-500">Catatan</div>
                                    <div class="font-medium p-3 bg-gray-50 rounded-lg mt-1">{{ $borrow->notes }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Member Information Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-user text-green-500 mr-2"></i>
                                Informasi Anggota
                            </h3>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-16 h-16 bg-gradient-to-r from-green-100 to-green-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-2xl text-green-600"></i>
                                </div>
                                <div class="flex-grow">
                                    <h4 class="text-xl font-bold text-gray-800">{{ $borrow->member->user->name }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                        <div>
                                            <div class="text-sm text-gray-500">Identitas</div>
                                            <div class="font-mono">
                                                @if($borrow->member->type === 'student')
                                                    NIS: {{ $borrow->member->nis }}
                                                @else
                                                    NIP: {{ $borrow->member->nip }}
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-500">Jenis</div>
                                            <div>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ $borrow->member->type === 'student' ? 'bg-blue-100 text-blue-800' : 
                                                       ($borrow->member->type === 'teacher' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ $borrow->member->type === 'student' ? 'Siswa' : 
                                                       ($borrow->member->type === 'teacher' ? 'Guru' : 'Staff') }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($borrow->member->type === 'student' && $borrow->member->class)
                                        <div>
                                            <div class="text-sm text-gray-500">Kelas</div>
                                            <div class="font-medium">
                                                {{ $borrow->member->class->grade }} {{ $borrow->member->class->class_name }}
                                            </div>
                                        </div>
                                        @endif
                                        <div>
                                            <div class="text-sm text-gray-500">Status</div>
                                            <div>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ $borrow->member->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                       ($borrow->member->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ $borrow->member->status === 'active' ? 'Aktif' : 
                                                       ($borrow->member->status === 'inactive' ? 'Non-Aktif' : 'Lulus') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Book Information Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-book text-purple-500 mr-2"></i>
                                Informasi Buku
                            </h3>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-16 h-16 bg-gradient-to-r from-purple-100 to-purple-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-book-open text-2xl text-purple-600"></i>
                                </div>
                                <div class="flex-grow">
                                    <h4 class="text-xl font-bold text-gray-800">{{ $borrow->bookCopy->book->title }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                        <div>
                                            <div class="text-sm text-gray-500">Pengarang</div>
                                            <div class="font-medium">{{ $borrow->bookCopy->book->author }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-500">Kategori</div>
                                            <div class="font-medium">{{ $borrow->bookCopy->book->category->name ?? '-' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-500">ISBN</div>
                                            <div class="font-mono">{{ $borrow->bookCopy->book->isbn ?? '-' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-500">Copy #</div>
                                            <div class="font-mono font-bold">
                                                {{ str_pad($borrow->bookCopy->inventory_number, 5, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-500">Status Copy</div>
                                            <div class="font-medium">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ $borrow->bookCopy->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $borrow->bookCopy->status === 'available' ? 'Tersedia' : 'Dipinjam' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Actions & Fine -->
                <div class="space-y-6">
                    <!-- Fine Information Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-money-bill-wave text-yellow-500 mr-2"></i>
                                Informasi Denda
                            </h3>
                            
                            <div class="space-y-4">
                                @if($borrow->fine_amount > 0)
                                    <div class="text-center p-4 {{ $borrow->fine_paid ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                                        <div class="text-3xl font-bold {{ $borrow->fine_paid ? 'text-green-600' : 'text-red-600' }}">
                                            Rp {{ number_format($borrow->fine_amount, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm mt-1 {{ $borrow->fine_paid ? 'text-green-700' : 'text-red-700' }}">
                                            {{ $borrow->fine_paid ? '✅ LUNAS' : '❌ BELUM DIBAYAR' }}
                                        </div>
                                        @if(!$borrow->fine_paid)
                                        <div class="mt-3">
                                            <form action="{{ route('admin.borrows.mark-paid', $borrow) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-full bg-green-600 hover:bg-green-700 text-black py-2 px-4 rounded-lg font-medium"
                                                        onclick="return confirm('Tandai denda sebagai lunas?')">
                                                    <i class="fas fa-check-circle mr-1"></i> Tandai Lunas
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($borrow->status === 'overdue')
                                    <div class="text-sm text-gray-600">
                                        <div class="font-medium mb-1">Detail Perhitungan:</div>
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <div class="flex justify-between mb-1">
                                                <span>Terlambat:</span>
                                                <span class="font-medium">{{ (int) $borrow->due_date->diffInDays($borrow->return_date ?? now()) }} hari</span>
                                            </div>
                                            <div class="flex justify-between mb-1">
                                                <span>Tarif:</span>
                                                <span class="font-medium">Rp 1.000/hari</span>
                                            </div>
                                            <div class="flex justify-between pt-2 border-t border-gray-200">
                                                <span>Total:</span>
                                                <span class="font-bold">Rp {{ number_format($borrow->fine_amount, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @else
                                    <div class="text-center p-4 bg-green-50 rounded-lg">
                                        <div class="text-3xl font-bold text-green-600">Rp 0</div>
                                        <div class="text-sm mt-1 text-green-700">Tidak ada denda</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-700 mb-4">Aksi Cepat</h3>
                            
                            <div class="space-y-3">
                                @if($borrow->status === 'borrowed')
                                <form action="{{ route('admin.borrows.extend', $borrow) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium"
                                            onclick="return confirm('Perpanjang peminjaman 7 hari?')"
                                            {{ $borrow->extension_count >= 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-redo mr-2"></i>
                                        {{ $borrow->extension_count >= 1 ? 'Sudah Diperpanjang (Maks)' : 'Perpanjang 7 Hari' }}
                                    </button>
                                </form>
                                
                                <a href="{{ route('admin.borrows.edit', $borrow) }}" 
                                   class="w-full flex items-center justify-center text-black py-3 px-4 rounded-lg font-medium">
                                    Edit Data
                                </a>
                                @endif
                                
                                <form action="{{ route('admin.borrows.destroy', $borrow) }}" method="POST"
                                      onsubmit="return confirm('Hapus data peminjaman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full flex items-center justify-center text-black py-3 px-4 rounded-lg font-medium"
                                            {{ $borrow->status !== 'returned' ? 'disabled' : '' }}>
                                        Hapus Peminjaman 
                                    </button>
                                </form>
                                
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-700 mb-4">Timeline</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-check text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Peminjaman Dibuat</div>
                                        <div class="text-sm text-gray-500">{{ $borrow->created_at->format('d F Y H:i') }}</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-book text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Buku Dipinjam</div>
                                        <div class="text-sm text-gray-500">{{ $borrow->borrow_date->format('d F Y') }}</div>
                                    </div>
                                </div>
                                
                                @if($borrow->extension_count > 0)
                                <div class="flex items-start">
                                    <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-redo text-purple-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Diperpanjang</div>
                                        <div class="text-sm text-gray-500">+7 hari dari jadwal</div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($borrow->return_date)
                                <div class="flex items-start">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-undo text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Buku Dikembalikan</div>
                                        <div class="text-sm text-gray-500">{{ $borrow->return_date->format('d F Y') }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>
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