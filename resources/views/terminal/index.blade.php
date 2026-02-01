<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman - Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
            }
            
            .terminal-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }
            
            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
            }
            
            .search-input {
                font-size: 1.2rem;
                padding: 20px 30px;
                border-radius: 15px;
                border: 3px solid transparent;
                background: #f8fafc;
                transition: all 0.3s ease;
            }
            
            .search-input:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            }
        </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="terminal-card w-full max-w-4xl p-8 md:p-12">
        <!-- Header -->
        <div class="text-center mb-10">
            <div class="flex justify-center mb-4">
               <x-application-logo style="width: 53px; height: 53px;" />
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                Peminjaman Buku
            </h1>
            <p class="text-gray-600">
                Sistem peminjaman buku perpustakaan SMA Negeri 4 Malang
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div>
                        <div class="text-sm text-blue-700 font-medium">Anggota Aktif</div>
                        <div class="text-2xl font-bold text-gray-800">
                            {{ \App\Models\Member::where('status', 'active')->count() }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-book text-white text-xl"></i>
                    </div>
                    <div>
                        <div class="text-sm text-green-700 font-medium">Buku Tersedia</div>
                        <div class="text-2xl font-bold text-gray-800">
                            {{ \App\Models\BookCopy::where('status', 'available')->count() }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-xl border border-yellow-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-exchange-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <div class="text-sm text-yellow-700 font-medium">Dipinjam Aktif</div>
                        <div class="text-2xl font-bold text-gray-800">
                            {{ \App\Models\Borrow::where('status', 'borrowed')->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Search -->
        <div class="mb-10">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Cari Buku untuk Dipinjam</h2>
            <form action="{{ route('terminal.search') }}" method="POST">
                @csrf
                <div class="relative">
                    <input type="text" 
                           name="query" 
                           class="search-input w-full"
                           placeholder="Masukkan judul, pengarang, atau ISBN buku..."
                           required
                           autofocus>
                    <button type="submit" 
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white p-3 rounded-xl hover:bg-blue-700 shadow-sm transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="mt-3 text-sm text-gray-500 flex items-center">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Contoh: Harry Potter, J.K. Rowling, atau 9780439554930
                </div>
            </form>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="#" 
               class="bg-green-600 text-white p-6 rounded-xl hover:bg-green-700 shadow-md transition-colors">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Peminjaman Baru</h3>
                        <p class="opacity-90">Pinjam buku dengan NIS/NIP</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('terminal.return.form') }}" 
               class="bg-red-600 text-white p-6 rounded-xl hover:bg-red-700 shadow-md transition-colors">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-undo-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Pengembalian</h3>
                        <p class="opacity-90">Kembalikan buku yang dipinjam</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Footer Info -->
        <div class="mt-10 pt-6 border-t border-gray-200">
            <div class="flex flex-wrap justify-between items-center text-sm text-gray-600">
                <div>
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Durasi Pinjam:</strong> 7 hari | 
                    <strong>Maks:</strong> 5 buku | 
                    <strong>Denda:</strong> Rp 1.000/hari
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Script -->
    @if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-slide-in">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.fixed').remove();
        }, 3000);
    </script>
    @endif

    @if(session('error'))
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg animate-slide-in">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.fixed').remove();
        }, 3000);
    </script>
    @endif

    <style>
        @keyframes slide-in {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>
</body>
</html>