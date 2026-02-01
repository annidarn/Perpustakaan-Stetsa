<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku - Terminal Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3);
        }
        
        .input-code {
            font-size: 1.5rem;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .scan-animation {
            animation: scan 2s ease-in-out infinite;
        }
        
        @keyframes scan {
            0%, 100% { transform: translateY(0); opacity: 0.5; }
            50% { transform: translateY(10px); opacity: 1; }
        }
    </style>
</head>
<body class="p-4 md:p-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('terminal.index') }}" 
                   class="inline-flex items-center text-white hover:text-gray-200 mb-2 bg-gray-500/50 px-3 py-1.5 rounded-md backdrop-blur-sm transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-white">
                    Pengembalian Buku
                </h1>
                <p class="text-white/80">
                    Masukkan kode peminjaman untuk mengembalikan buku
                </p>
            </div>
            
            <div class="text-white text-right">
                <div class="text-sm opacity-80">Hari Ini</div>
                <div class="text-xl font-bold">
                    {{ now()->format('d F Y') }}
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card p-6 md:p-8">
            <!-- Manual Input Form -->
            <div>                
                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
                
                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-600 text-xl mr-3 mt-1"></i>
                        <div>
                            <div class="font-bold text-lg mb-2">Pengembalian Sukses!</div>
                            <div class="whitespace-pre-line text-sm">{{ session('success') }}</div>
                            <div class="mt-4 pt-4 border-t border-green-200">
                                <a href="{{ route('terminal.return.form') }}" 
                                class="inline-flex items-center text-green-700 hover:text-green-900 font-medium">
                                    <i class="fas fa-redo mr-2"></i> Proses Pengembalian Lain
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // Auto-scroll ke notif
                    document.addEventListener('DOMContentLoaded', function() {
                        const notif = document.querySelector('.bg-green-50');
                        if (notif) {
                            notif.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    });
                </script>
                @endif
                
                <form action="{{ route('terminal.return.process') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="borrow_code" class="block text-sm font-medium text-gray-700 mb-3">
                            <i class="fas fa-barcode mr-1"></i> Kode Peminjaman (PINJ-...)
                        </label>
                        
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <input type="text" 
                                   name="borrow_code" 
                                   id="borrow_code"
                                   value="{{ old('borrow_code') }}"
                                   class="input-code w-full pl-12 pr-4 py-4 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                   placeholder="PINJ-YYYYMMDD-001"
                                   required
                                   autofocus>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <div class="scan-animation text-blue-500">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3 text-sm text-gray-600">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-info-circle mr-2"></i>
                                Kode dapat ditemukan di struk peminjaman
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>
                                Contoh: <span class="font-mono ml-1">PINJ-{{ date('Ymd') }}-001</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Help -->
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-8">
                        <h4 class="font-bold text-yellow-800 mb-2 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Informasi Penting
                        </h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li class="flex items-start">
                                <i class="fas fa-circle text-xs mt-1 mr-2"></i>
                                <span class="font-medium">Denda keterlambatan:</span> Rp 1.000 per hari
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-circle text-xs mt-1 mr-2"></i>
                                Buku rusak/hilang akan dikenakan biaya penggantian
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-circle text-xs mt-1 mr-2"></i>
                                Pastikan buku dalam kondisi baik sebelum dikembalikan
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('terminal.index') }}" 
                           class="px-6 py-4 bg-gray-500 text-white rounded-xl hover:bg-gray-600 text-center font-medium shadow-sm transition-colors">
                            <i class="fas fa-home mr-2"></i> Kembali
                        </a>
                        
                        <button type="submit" 
                                class="flex-1 px-6 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-lg shadow-sm transition-colors">
                            <i class="fas fa-check-circle mr-2"></i> Proses Pengembalian
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Recent Returns (Placeholder) -->
            <div class="mt-10 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">
                    <i class="fas fa-history mr-2"></i> Pengembalian Terbaru
                </h3>
                <div class="space-y-3">
                    @php
                        $recentReturns = \App\Models\Borrow::where('status', 'returned')
                            ->orderBy('return_date', 'desc')
                            ->take(3)
                            ->get();
                    @endphp
                    
                    @if($recentReturns->count() > 0)
                        @foreach($recentReturns as $return)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-800">
                                    {{ $return->borrow_code }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $return->return_date->format('H:i') }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium {{ $return->fine_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $return->fine_amount > 0 ? 'Denda: Rp ' . number_format($return->fine_amount, 0, ',', '.') : 'Tepat Waktu' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $return->bookCopy->book->title }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>Belum ada pengembalian hari ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Footer Info -->
        <div class="mt-6 text-center text-sm text-white/70">
            <p>
                <i class="fas fa-headset mr-1"></i> Butuh bantuan? Hubungi petugas perpustakaan
            </p>
        </div>
    </div>

    <script>
        // Auto-format kode peminjaman
        document.getElementById('borrow_code').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            value = value.replace(/[^A-Z0-9\-]/g, '');
            
            // Auto-add "PINJ-" prefix jika dimulai dengan angka/tanggal
            if (value.length > 0 && !value.startsWith('PINJ-')) {
                if (/^\d{8}/.test(value)) {
                    value = 'PINJ-' + value;
                }
            }
            
            e.target.value = value;
        });
        
        // Enter to submit
        document.getElementById('borrow_code').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('button[type="submit"]').click();
            }
        });
        
        // Auto-focus
        document.getElementById('borrow_code').focus();
    </script>
</body>
</html>