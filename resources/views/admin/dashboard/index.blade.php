<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Perpustakaan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- STATISTIK CARDS -->
            <div class="space-y-6 mb-8">
                <!-- Total Anggota & Total Buku -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Total Anggota -->
                    <div class="bg-white border-l-8 border-blue-500 rounded-xl p-6 shadow-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">TOTAL ANGGOTA</p>
                                <p class="text-5xl font-bold text-gray-900 mt-2">{{ $stats['total_members'] }}</p>
                                <p class="text-blue-600 text-sm font-medium mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    Siswa, Guru & Staff
                                </p>
                            </div>
                            <div class="p-4 bg-blue-50 rounded-xl">
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 5.197a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Buku -->
                    <div class="bg-white border-l-8 border-green-500 rounded-xl p-6 shadow-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">TOTAL BUKU</p>
                                <p class="text-5xl font-bold text-gray-900 mt-2">{{ $stats['total_books'] }}</p>
                                <p class="text-green-600 text-sm font-medium mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                                    </svg>
                                    Judul berbeda
                                </p>
                            </div>
                            <div class="p-4 bg-green-50 rounded-xl">
                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BARIS 2: Copy Tersedia & Peminjaman Hari Ini -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Copy Tersedia -->
                    <div class="bg-white border-l-8 border-emerald-500 rounded-xl p-6 shadow-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">COPY TERSEDIA</p>
                                <p class="text-5xl font-bold text-gray-900 mt-2">{{ $stats['available_copies'] }}</p>
                                <p class="text-emerald-600 text-sm font-medium mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Dapat dipinjam sekarang
                                </p>
                            </div>
                            <div class="p-4 bg-emerald-50 rounded-xl">
                                <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Peminjaman Hari Ini -->
                    <div class="bg-white border-l-8 border-yellow-500 rounded-xl p-6 shadow-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">PEMINJAMAN HARI INI</p>
                                <p class="text-5xl font-bold text-gray-900 mt-2">{{ $stats['today_borrows'] }}</p>
                                <p class="text-yellow-600 text-sm font-medium mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ now()->format('d F Y') }}
                                </p>
                            </div>
                            <div class="p-4 bg-yellow-50 rounded-xl">
                                <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BARIS 3: Sedang Dipinjam & Denda Belum Bayar -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Sedang Dipinjam -->
                    <div class="bg-white border-l-8 border-orange-500 rounded-xl p-6 shadow-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">SEDANG DIPINJAM</p>
                                <p class="text-5xl font-bold text-gray-900 mt-2">{{ $stats['active_borrows'] }}</p>
                                <p class="text-orange-600 text-sm font-medium mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                                    </svg>
                                @if($stats['overdue_borrows'] > 0)
                                    <span class="text-red-600 font-bold">({{ $stats['overdue_borrows'] }} terlambat)</span>
                                @else
                                    Belum dikembalikan
                                @endif
                                </p>
                            </div>
                            <div class="p-4 bg-orange-50 rounded-xl">
                                <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Denda Belum Bayar -->
                    <div class="bg-white border-l-8 border-red-500 rounded-xl p-6 shadow-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">DENDA BELUM BAYAR</p>
                                <p class="text-5xl font-bold text-gray-900 mt-2">Rp {{ number_format($stats['total_fines'], 0, ',', '.') }}</p>
                                <p class="text-red-600 text-sm font-medium mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Total tertunggak
                                </p>
                            </div>
                            <div class="p-4 bg-red-50 rounded-xl">
                                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE: PEMINJAMAN AKAN JATUH TEMPO -->
            @if($dueSoon->count() > 0)
            <div class="mt-8 bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">⏰ Peminjaman Akan Jatuh Tempo (2 Hari Lagi)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Hari</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($dueSoon as $borrow)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $borrow->member->user->name }}</td>
                                <td class="px-6 py-4">{{ $borrow->bookCopy->book->title }}</td>
                                <td class="px-6 py-4">{{ $borrow->borrow_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">{{ $borrow->due_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ max(0, now()->startOfDay()->diffInDays($borrow->due_date)) }} hari
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>