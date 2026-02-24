<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Perpustakaan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- STATISTIK CARDS -->
            <div class="flex flex-wrap -mx-3 mb-8">
                <!-- Total Anggota -->
                <div class="w-full md:w-1/3 px-3 mb-6">
                    <div class="bg-white border-l-8 border-blue-500 rounded-xl p-6 shadow-lg h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">TOTAL ANGGOTA</p>
                                <p class="text-4xl font-bold text-gray-900 mt-2">{{ $stats['total_members'] }}</p>
                                <p class="text-blue-600 text-xs font-medium mt-2 flex items-center">
                                    <i class="fas fa-users mr-1"></i> Siswa, Guru & Staff
                                </p>
                            </div>
                            <div class="p-3 bg-blue-50 rounded-xl">
                                <i class="fas fa-user-graduate text-2xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Buku -->
                <div class="w-full md:w-1/3 px-3 mb-6">
                    <div class="bg-white border-l-8 border-green-500 rounded-xl p-6 shadow-lg h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">TOTAL BUKU</p>
                                <p class="text-4xl font-bold text-gray-900 mt-2">{{ $stats['total_books'] }}</p>
                                <p class="text-green-600 text-xs font-medium mt-2 flex items-center">
                                    <i class="fas fa-book mr-1"></i> Judul berbeda
                                </p>
                            </div>
                            <div class="p-3 bg-green-50 rounded-xl">
                                <i class="fas fa-book text-2xl text-green-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Copy Tersedia -->
                <div class="w-full md:w-1/3 px-3 mb-6">
                    <div class="bg-white border-l-8 border-emerald-500 rounded-xl p-6 shadow-lg h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">COPY TERSEDIA</p>
                                <p class="text-4xl font-bold text-gray-900 mt-2">{{ $stats['available_copies'] }}</p>
                                <p class="text-emerald-600 text-xs font-medium mt-2 flex items-center">
                                    <i class="fas fa-check-circle mr-1"></i> Dapat dipinjam
                                </p>
                            </div>
                            <div class="p-3 bg-emerald-50 rounded-xl">
                                <i class="fas fa-book-open text-2xl text-emerald-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Peminjaman Hari Ini -->
                <div class="w-full md:w-1/3 px-3 mb-6">
                    <div class="bg-white border-l-8 border-yellow-500 rounded-xl p-6 shadow-lg h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">HARI INI</p>
                                <p class="text-4xl font-bold text-gray-900 mt-2">{{ $stats['today_borrows'] }}</p>
                                <p class="text-yellow-600 text-xs font-medium mt-2 flex items-center">
                                    <i class="fas fa-calendar-day mr-1"></i> {{ now()->format('d M Y') }}
                                </p>
                            </div>
                            <div class="p-3 bg-yellow-50 rounded-xl">
                                <i class="fas fa-hand-holding-heart text-2xl text-yellow-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sedang Dipinjam -->
                <div class="w-full md:w-1/3 px-3 mb-6">
                    <div class="bg-white border-l-8 border-orange-500 rounded-xl p-6 shadow-lg h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">AKTIF</p>
                                <p class="text-4xl font-bold text-gray-900 mt-2">{{ $stats['active_borrows'] }}</p>
                                <p class="text-orange-600 text-xs font-medium mt-2 flex items-center">
                                @if($stats['overdue_borrows'] > 0)
                                    <span class="text-red-600 font-bold"><i class="fas fa-exclamation-triangle mr-1"></i> {{ $stats['overdue_borrows'] }} terlambat</span>
                                @else
                                    <i class="fas fa-exchange-alt mr-1"></i> Belum kembali
                                @endif
                                </p>
                            </div>
                            <div class="p-3 bg-orange-50 rounded-xl">
                                <i class="fas fa-clock text-2xl text-orange-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Denda Belum Bayar -->
                <div class="w-full md:w-1/3 px-3 mb-6">
                    <div class="bg-white border-l-8 border-red-500 rounded-xl p-6 shadow-lg h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">TOTAL DENDA</p>
                                <p class="text-4xl font-bold text-gray-900 mt-2">Rp {{ number_format($stats['total_fines'], 0, ',', '.') }}</p>
                                <p class="text-red-600 text-xs font-medium mt-2 flex items-center">
                                    <i class="fas fa-money-bill-wave mr-1"></i> Belum bayar
                                </p>
                            </div>
                            <div class="p-3 bg-red-50 rounded-xl">
                                <i class="fas fa-receipt text-2xl text-red-600"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Borrowing Trends Chart -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                        Tren Peminjaman (7 Hari Terakhir)
                    </h3>
                    <div class="h-64">
                        <canvas id="borrowChart"></canvas>
                    </div>
                </div>

                <!-- Book Categories Distribution -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        Distribusi Buku
                    </h3>
                    <div class="h-64">
                        <canvas id="categoryChart"></canvas>
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Borrowing Trends Chart
            const ctxBorrow = document.getElementById('borrowChart').getContext('2d');
            new Chart(ctxBorrow, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Jumlah Peminjaman',
                        data: {!! json_encode($chartData) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

            // Category Distribution Chart
            const ctxCategory = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctxCategory, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoriesStats->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($categoriesStats->pluck('total')) !!},
                        backgroundColor: [
                            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#6366f1'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15,
                                font: { size: 11 }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
    @endpush
</x-app-layout>