<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Anggota
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('members.edit', $member) }}" 
                   class="bg-yellow-500 hover:bg-yellow-700 text-black font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('members.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Kembali
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Member Info -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <!-- Member Header -->
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $member->user->name }}</h1>
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                            {{ $member->type === 'student' ? 'bg-blue-100 text-blue-800' : 
                                               ($member->type === 'teacher' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $member->type === 'student' ? 'Siswa' : 
                                               ($member->type === 'teacher' ? 'Guru' : 'Staff') }}
                                        </span>
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                            {{ $member->status === 'active' ? 'bg-green-100 text-green-800' : 
                                               ($member->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ $member->status === 'active' ? 'Aktif' : 
                                               ($member->status === 'inactive' ? 'Non-Aktif' : 'Lulus') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">ID Anggota</div>
                                    <div class="text-lg font-mono font-bold">{{ $member->id }}</div>
                                </div>
                            </div>

                            <!-- Personal Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Pribadi</h3>
                                    <div class="space-y-3">
                                        <div>
                                            <div class="text-sm text-gray-500">No. Identitas</div>
                                            <div class="font-medium">
                                                @if($member->type === 'student')
                                                    NIS: <span class="font-mono">{{ $member->nis ?? '-' }}</span>
                                                @else
                                                    NIP: <span class="font-mono">{{ $member->nip ?? '-' }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-500">Jenis Kelamin</div>
                                            <div class="font-medium">
                                                {{ $member->gender == 'L' ? 'Laki-laki' : ($member->gender == 'P' ? 'Perempuan' : '-') }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-500">Nomor Telepon</div>
                                            <div class="font-medium">{{ $member->phone ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- School/Work Information -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4">
                                        {{ $member->type === 'student' ? 'Informasi Sekolah' : 'Informasi Kerja' }}
                                    </h3>
                                    <div class="space-y-3">
                                        @if($member->type === 'student')
                                            <div>
                                                <div class="text-sm text-gray-500">Kelas</div>
                                                <div class="font-medium">
                                                    @if($member->class)
                                                        {{ $member->class->grade }} {{ $member->class->class_name }}
                                                    @else
                                                        <span class="text-gray-400">Belum ada kelas</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-sm text-gray-500">Tahun Masuk</div>
                                                <div class="font-medium">{{ $member->enrollment_year ?: '-' }}</div>
                                            </div>
                                        @else
                                            <div>
                                                <div class="text-sm text-gray-500">Jabatan</div>
                                                <div class="font-medium">
                                                    {{ $member->type === 'teacher' ? 'Guru' : 'Staff Sekolah' }}
                                                </div>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm text-gray-500">Email Sistem</div>
                                            <div class="font-medium text-sm">{{ $member->user->email }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Akun</h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-600 mb-2">
                                        Akun ini dibuat untuk keperluan sistem. Siswa tidak dapat login, sedangkan guru/staff dapat login dengan:
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <div class="text-sm text-gray-500">Username/Email</div>
                                            <div class="font-mono">{{ $member->user->email }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-500">Password Default</div>
                                            <div class="font-mono">password123</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Borrowing Statistics -->
                    <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Statistik Peminjaman</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <div class="text-3xl font-bold text-blue-700">
                                        {{ $member->borrows->count() }}
                                    </div>
                                    <div class="text-sm text-blue-600">Total Peminjaman</div>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <div class="text-3xl font-bold text-green-700">
                                        {{ $member->borrows->where('status', 'returned')->count() }}
                                    </div>
                                    <div class="text-sm text-green-600">Sudah Dikembalikan</div>
                                </div>
                                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                    <div class="text-3xl font-bold text-yellow-700">
                                        {{ $member->borrows->where('status', 'borrowed')->count() }}
                                    </div>
                                    <div class="text-sm text-yellow-600">Sedang Dipinjam</div>
                                </div>
                                <div class="text-center p-4 bg-red-50 rounded-lg">
                                    <div class="text-3xl font-bold text-red-700">
                                        {{ $member->borrows->where('status', 'overdue')->count() }}
                                    </div>
                                    <div class="text-sm text-red-600">Terlambat</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Quick Actions & Recent Borrows -->
                <div>
                    <!-- Quick Actions Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Aksi Cepat</h3>
                            <div class="space-y-3">
                                @if($member->status === 'active')
                                    <a href="#" 
                                       class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 text-black rounded-md hover:bg-blue-700">
                                        <i class="fas fa-book-reader mr-2"></i>
                                        Buat Peminjaman Baru
                                    </a>
                                @endif
                                
                                <form action="{{ route('members.destroy', $member) }}" method="POST" 
                                      onsubmit="return confirm('Hapus anggota {{ $member->user->name }} secara permanen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full flex items-center justify-center px-4 py-3 bg-red-600 text-black rounded-md hover:bg-red-700">
                                        <i class="fas fa-trash mr-2"></i>
                                        Hapus Anggota
                                    </button>
                                </form>
                                
                                @if($member->status === 'active')
                                    <form action="{{ route('members.update.status', $member) }}" method="POST" 
                                        onsubmit="return confirm('Nonaktifkan akun {{ $member->user->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="inactive">
                                        <button type="submit" 
                                                class="w-full flex items-center justify-center px-4 py-3 bg-yellow-600 text-black rounded-md hover:bg-yellow-700">
                                            <i class="fas fa-user-slash mr-2"></i>
                                            Nonaktifkan Akun
                                        </button>
                                    </form>
                                @elseif($member->status === 'inactive')
                                    <form action="{{ route('members.update.status', $member) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" 
                                                class="w-full flex items-center justify-center px-4 py-3 bg-green-600 text-black rounded-md hover:bg-green-700">
                                            <i class="fas fa-user-check mr-2"></i>
                                            Aktifkan Kembali
                                        </button>
                                    </form>
                                @elseif($member->status === 'graduated' && $member->type === 'student')
                                    <form action="{{ route('members.update.status', $member) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" 
                                                class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 text-black rounded-md hover:bg-blue-700">
                                            <i class="fas fa-graduation-cap mr-2"></i>
                                            Aktifkan Kembali (Alumni → Aktif)
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recent Borrows Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Peminjaman Terbaru</h3>
                            @if($member->borrows->count() > 0)
                                <div class="space-y-4">
                                    @foreach($member->borrows->sortByDesc('borrow_date')->take(5) as $borrow)
                                        <div class="p-3 border rounded-lg hover:bg-gray-50">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="font-medium text-sm">
                                                        {{ $borrow->bookCopy->book->title ?? 'Buku tidak ditemukan' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $borrow->borrow_date->format('d M Y') }}
                                                    </div>
                                                </div>
                                                <span class="px-2 py-1 text-xs font-semibold rounded 
                                                    {{ $borrow->status === 'borrowed' ? 'bg-yellow-100 text-yellow-800' : 
                                                       ($borrow->status === 'returned' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $borrow->status === 'borrowed' ? 'Dipinjam' : 
                                                       ($borrow->status === 'returned' ? 'Dikembalikan' : 'Terlambat') }}
                                                </span>
                                            </div>
                                            @if($borrow->status === 'borrowed')
                                                <div class="mt-2 text-xs">
                                                    <span class="text-gray-600">Jatuh tempo:</span>
                                                    <span class="font-medium {{ $borrow->due_date->isPast() ? 'text-red-600' : 'text-green-600' }}">
                                                        {{ $borrow->due_date->format('d M Y') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 text-center">
                                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                                        Lihat semua peminjaman →
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-book-open text-4xl mb-2"></i>
                                    <p>Belum ada riwayat peminjaman</p>
                                </div>
                            @endif
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