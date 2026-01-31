<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman Perpustakaan</title>
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
        
        .member-card {
            border-left: 4px solid #10b981;
        }
        
        .book-card {
            border-left: 4px solid #3b82f6;
        }
        
        .copy-option {
            transition: all 0.2s ease;
            border: 2px solid #e5e7eb;
        }
        
        .copy-option:hover {
            border-color: #3b82f6;
            background-color: #f0f9ff;
        }
        
        .copy-option.selected {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
    </style>
</head>
<body class="p-4 md:p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('terminal.index') }}" 
                   class="inline-flex items-center text-white hover:text-gray-200 mb-2">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-white">
                    Form Peminjaman Buku
                </h1>
            </div>
            
            <div class="text-white text-right">
                <div class="text-sm opacity-80">Kode Transaksi</div>
                <div class="text-xl font-mono font-bold">
                    {{ \App\Models\Borrow::generateBorrowCode() }}
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border-2 border-green-500 rounded-xl p-6 shadow-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                </div>
                <div class="flex-grow">
                    <h3 class="text-xl font-bold text-green-800 mb-2">PEMINJAMAN BERHASIL!</h3>
                    
                    <!-- Kode Peminjaman Besar -->
                    <div class="mb-4 p-4 bg-white border border-green-300 rounded-lg">
                        <div class="text-center">
                            <div class="text-sm text-gray-600 mb-1">KODE PEMINJAMAN</div>
                            <div class="text-3xl font-mono font-bold text-green-700 tracking-wider">
                                {{ session('borrow_code') }}
                            </div>
                            <div class="mt-2 text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Catat kode ini untuk pengembalian
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detail dalam box -->
                    <div class="bg-white p-4 rounded-lg border border-green-200 mb-4">
                        <div class="whitespace-pre-line text-gray-700">{{ session('success') }}</div>
                    </div>
                    
                    <!-- Pesan Penting -->
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                            <span class="font-bold text-yellow-800">CATAT DAN SIMPAN KODE DI ATAS!</span>
                        </div>
                        <div class="mt-2 text-sm text-yellow-700">
                            Tanpa kode ini, tidak bisa melakukan pengembalian buku.
                        </div>
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
                    
                    // Highlight kode dengan animasi
                    const codeElement = document.querySelector('.text-3xl.font-mono');
                    codeElement.classList.add('animate-pulse');
                    setTimeout(() => {
                        codeElement.classList.remove('animate-pulse');
                    }, 3000);
                }
            });
        </script>

        <style>
            .animate-pulse {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }
        </style>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Member & Book Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Member Information Card -->
                <div class="card p-6 member-card">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user-circle text-green-500 mr-2"></i>
                        Informasi Anggota
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Nama Lengkap</div>
                            <div class="text-lg font-semibold text-gray-800">
                                {{ $member->user->name }}
                            </div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-500">Identitas</div>
                            <div class="text-lg font-semibold text-gray-800 font-mono">
                                @if($member->type === 'student')
                                    NIS: {{ $member->nis }}
                                @else
                                    NIP: {{ $member->nip }}
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-500">Jenis Anggota</div>
                            <div>
                                <span class="px-3 py-1 rounded-full text-sm font-medium 
                                    {{ $member->type === 'student' ? 'bg-blue-100 text-blue-800' : 
                                       ($member->type === 'teacher' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $member->type === 'student' ? 'Siswa' : 
                                       ($member->type === 'teacher' ? 'Guru' : 'Staff') }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-500">Status</div>
                            <div>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Aktif
                                </span>
                            </div>
                        </div>
                        
                        @if($member->type === 'student' && $member->class)
                        <div>
                            <div class="text-sm text-gray-500">Kelas</div>
                            <div class="font-medium">
                                {{ $member->class->grade }} {{ $member->class->class_name }}
                            </div>
                        </div>
                        @endif
                        
                        <div>
                            <div class="text-sm text-gray-500">Buku Dipinjam</div>
                            <div class="font-medium">
                                {{ $member->borrows()->whereIn('status', ['borrowed', 'overdue'])->count() }} / 5
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Book Information Card -->
                <div class="card p-6 book-card">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-book text-blue-500 mr-2"></i>
                        Informasi Buku
                    </h2>
                    
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Book Cover Placeholder -->
                        <div class="w-full md:w-1/3">
                            <div class="bg-gradient-to-r from-blue-100 to-purple-100 h-48 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book-open text-6xl text-blue-600 opacity-50"></i>
                            </div>
                        </div>
                        
                        <!-- Book Details -->
                        <div class="flex-grow">
                            <h3 class="text-2xl font-bold text-gray-800 mb-3">
                                {{ $book->title }}
                            </h3>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-user-edit w-5 mr-2"></i>
                                    <span class="font-medium mr-2">Pengarang:</span>
                                    {{ $book->author }}
                                </div>
                                
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-tag w-5 mr-2"></i>
                                    <span class="font-medium mr-2">Kategori:</span>
                                    {{ $book->category->name ?? 'Tanpa Kategori' }}
                                </div>
                                
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-hashtag w-5 mr-2"></i>
                                    <span class="font-medium mr-2">ISBN:</span>
                                    {{ $book->isbn ?? '-' }}
                                </div>
                                
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-calendar w-5 mr-2"></i>
                                    <span class="font-medium mr-2">Tahun Terbit:</span>
                                    {{ $book->publication_year }}
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Pilih copy buku yang akan dipinjam dari {{ $availableCopies->count() }} copy tersedia.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Borrow Form -->
            <div>
                <div class="card p-6 sticky top-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-clipboard-check text-purple-500 mr-2"></i>
                        Konfirmasi Peminjaman
                    </h2>
                    
                    <form action="{{ route('terminal.borrow.process', $member) }}" method="POST">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        
                        <!-- Select Book Copy -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-copy mr-1"></i> Pilih Copy Buku
                            </label>
                            
                            <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                                @foreach($availableCopies as $copy)
                                <label class="copy-option block p-4 rounded-lg cursor-pointer">
                                    <div class="flex items-center">
                                        <input type="radio" 
                                               name="book_copy_id" 
                                               value="{{ $copy->id }}"
                                               class="mr-3" 
                                               required
                                               {{ $loop->first ? 'checked' : '' }}>
                                        <div>
                                            <div class="font-medium text-gray-800">
                                                Copy #{{ $copy->formatted_inventory_number }}
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            
                            @if($availableCopies->isEmpty())
                            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Tidak ada copy tersedia untuk buku ini.
                            </div>
                            @endif
                        </div>
                        
                        <!-- Loan Period -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-1"></i> Periode Peminjaman
                            </label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-sm text-gray-500">Tanggal Pinjam</div>
                                        <div class="font-bold text-gray-800">
                                            {{ now()->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Jatuh Tempo</div>
                                        <div class="font-bold text-blue-600">
                                            {{ now()->addDays(7)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 text-sm text-gray-600">
                                    <i class="fas fa-clock mr-1"></i>
                                    Durasi: 7 hari (dapat diperpanjang 1x +7 hari)
                                </div>
                            </div>
                        </div>
                        
                        <!-- Terms & Conditions -->
                        <div class="mb-6">
                            <label class="flex items-start">
                                <input type="checkbox" 
                                       name="terms" 
                                       class="mt-1 mr-3" 
                                       required>
                                <span class="text-sm text-gray-700">
                                    Saya setuju dengan 
                                    <span class="font-medium">ketentuan peminjaman</span>:
                                    <ul class="mt-2 text-xs text-gray-600 list-disc pl-5 space-y-1">
                                        <li>Mengembalikan tepat waktu (7 hari)</li>
                                        <li>Denda Rp 1.000/hari jika terlambat</li>
                                        <li>Merawat buku dengan baik</li>
                                        <li>Maksimal 5 buku per anggota</li>
                                    </ul>
                                </span>
                            </label>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="w-full btn-primary text-white py-4 rounded-lg font-bold text-lg">
                                <i class="fas fa-check-circle mr-2"></i>
                                Konfirmasi Peminjaman
                            </button>
                            
                            <a href="{{ route('terminal.index') }}" 
                               class="w-full block text-center py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                Batalkan
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Style copy selection
        document.querySelectorAll('.copy-option').forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            
            radio.addEventListener('change', function() {
                // Remove selected class from all options
                document.querySelectorAll('.copy-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Add selected class to parent
                if (this.checked) {
                    this.closest('.copy-option').classList.add('selected');
                }
            });
            
            // Initialize first option as selected
            if (radio.checked) {
                option.classList.add('selected');
            }
        });
    </script>
    <script>
        // Auto-scroll to top when success message exists
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Auto-copy to clipboard (optional)
                // navigator.clipboard.writeText('{{ session('borrow_code') }}');
            @endif
        });
    </script>
</body>
</html>