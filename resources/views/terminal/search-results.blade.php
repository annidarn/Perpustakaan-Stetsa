<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - Terminal Perpustakaan</title>
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .btn-borrow {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        
        .btn-borrow:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }
        
        .availability-badge {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
        }

        @keyframes slide-in {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .animate-slide-in {
            animation: slide-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
    </style>
</head>
<body class="p-4 md:p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="{{ route('terminal.index') }}" 
                   class="inline-flex items-center text-white hover:text-gray-200 mb-2 bg-gray-500/50 px-3 py-1.5 rounded-md backdrop-blur-sm transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-white">
                    Hasil Pencarian Buku
                </h1>
                <p class="text-white/80">
                    Ditemukan {{ $books->total() }} buku untuk dipinjam
                </p>
            </div>
            
            <!-- Search Again -->
            <div class="hidden md:block">
                <form action="{{ route('terminal.search') }}" method="GET" class="flex">
                    <input type="text" 
                    name="query" 
                    value="{{ old('query', request()->input('query')) }}"
                    class="px-4 py-3 rounded-l-lg w-64 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Cari buku lain...">
                    <button type="submit" 
                            class="bg-white text-blue-600 px-4 rounded-r-lg hover:bg-gray-100">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Search -->
        <div class="md:hidden mb-6">
            <form action="{{ route('terminal.search') }}" method="GET" class="flex">
                <input type="text" 
                       name="query" 
                       value="{{ request('query') }}"
                       class="flex-grow px-4 py-3 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Cari buku lain...">
                <button type="submit" 
                        class="bg-white text-blue-600 px-4 rounded-r-lg hover:bg-gray-100">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Results -->
        @if($books->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($books as $book)
                    <div class="card overflow-hidden">
                        <!-- Book Cover Placeholder -->
                        <div class="h-48 bg-gradient-to-r from-blue-100 to-purple-100 flex items-center justify-center">
                            @php
                                $availableCount = $book->copies->where('status', 'available')->count();
                            @endphp
                            @if($availableCount > 0)
                                <div class="text-center">
                                    <i class="fas fa-book-open text-6xl text-blue-600 opacity-50"></i>
                                    <div class="mt-4">
                                        <span class="availability-badge bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            {{ $availableCount }} copy tersedia
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center">
                                    <i class="fas fa-book text-6xl text-gray-400"></i>
                                    <div class="mt-4">
                                        <span class="availability-badge bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Tidak tersedia
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Book Info -->
                        <div class="p-5">
                            <h3 class="font-bold text-lg text-gray-800 mb-2 line-clamp-2">
                                {{ $book->title }}
                            </h3>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-user-edit mr-2 w-4"></i>
                                    <span class="truncate">{{ $book->author }}</span>
                                </div>
                                
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-tag mr-2 w-4"></i>
                                    <span>{{ $book->category->name ?? 'Tanpa Kategori' }}</span>
                                </div>
                                
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-hashtag mr-2 w-4"></i>
                                    <span>ISBN: {{ $book->isbn ?? '-' }}</span>
                                </div>
                                
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-calendar mr-2 w-4"></i>
                                    <span>Tahun: {{ $book->publication_year }}</span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <button onclick="showBorrowModal('{{ $book->id }}', '{{ addslashes($book->title) }}')"
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-medium text-center shadow-sm transition-colors">
                                    <i class="fas fa-cart-plus mr-2"></i> Pinjam Buku
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($books->hasPages())
                <div class="mt-8 bg-white rounded-lg p-4">
                    {{ $books->withQueryString()->links() }}
                </div>
            @endif
        @else
            <!-- No Results -->
            <div class="card p-8 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-search text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">
                    Buku tidak ditemukan
                </h3>
                <p class="text-gray-600 mb-6">
                    Tidak ada buku yang cocok dengan pencarian "{{ request('query') }}"
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('terminal.index') }}" 
                       class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 shadow-sm transition-colors">
                        <i class="fas fa-home mr-2"></i> Kembali
                    </a>
                    <button onclick="window.history.back()"
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 shadow-sm transition-colors">
                        <i class="fas fa-undo mr-2"></i> Coba Kata Kunci Lain
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Borrow Modal -->
    <div id="borrowModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Pinjam Buku</h3>
                    <button onclick="closeBorrowModal()" 
                            class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <div id="modalBookInfo" class="mb-6">
                    <!-- Book info will be loaded here -->
                </div>
                
                <form id="borrowForm" method="POST" action="{{ route('terminal.validate.member') }}">
                    @csrf
                    <input type="hidden" name="book_id" id="bookId" value="{{ old('book_id') }}">
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-1"></i> Masukkan NIS/NIP Anggota
                        </label>
                        <input type="text" 
                            name="member_identifier" 
                            id="memberIdentifier"
                            value="{{ old('member_identifier') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('member_identifier') border-red-500 bg-red-50 @enderror"
                            placeholder="Contoh: 10001 (NIS) atau T0001 (NIP)"
                            required>
                        <div class="mt-2 text-sm text-gray-500">
                            NIS untuk siswa, NIP untuk guru/staff
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                onclick="closeBorrowModal()"
                                class="px-5 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-sm transition-colors">
                            <i class="fas fa-times mr-2"></i> Batal
                        </button>
                        <button type="submit" 
                                id="submitBorrowBtn"
                                class="px-5 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                            <i class="fas fa-check mr-2"></i> Lanjutkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('error') || session('success') || $errors->any())
    <div id="toast" class="fixed top-4 right-4 {{ session('error') || $errors->any() ? 'bg-red-600' : 'bg-green-600' }} text-white px-6 py-4 rounded-xl shadow-2xl animate-slide-in z-[100] border border-white/20 backdrop-blur-sm">
        <div class="flex items-center space-x-3">
            <i class="fas {{ session('error') || $errors->any() ? 'fa-exclamation-triangle' : 'fa-check-circle' }} text-xl"></i>
            <div class="font-medium text-lg">
                {{ session('error') ?? ($errors->any() ? $errors->first() : (session('success') ?? '')) }}
            </div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast');
            if (toast) {
                toast.classList.add('opacity-0', 'translate-x-20');
                toast.style.transition = 'all 0.5s ease-out';
                setTimeout(() => toast.remove(), 500);
            }
        }, 5000);
    </script>
    @endif

    <script>
    function showBorrowModal(bookId, bookTitle) {
        const modal = document.getElementById('borrowModal');
        const bookInfo = document.getElementById('modalBookInfo');
        
        // Set book info
        bookInfo.innerHTML = `
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h4 class="font-bold text-lg text-blue-800 mb-2">${bookTitle}</h4>
                <p class="text-blue-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    Anda akan meminjam buku ini. Masukkan NIS/NIP anggota.
                </p>
            </div>
        `;
        
        // Set book_id hidden field
        document.getElementById('bookId').value = bookId;
        
        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Focus
        document.getElementById('memberIdentifier').focus();
        
        // Reset validation but keep input if returning from error
        document.getElementById('submitBorrowBtn').disabled = false;
    }
    
    function closeBorrowModal() {
        document.getElementById('borrowModal').classList.add('hidden');
        document.getElementById('borrowModal').classList.remove('flex');
        document.getElementById('memberIdentifier').value = '';
        document.getElementById('submitBorrowBtn').disabled = false;
    }
    
    // Modal Persistence on Error
    @if(old('book_id') && (session('error') || $errors->any()))
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                showBorrowModal('{{ old("book_id") }}', 'Lanjutkan Peminjaman');
            }, 100);
        });
    @endif
    
    // Validasi input real-time
    function validateMemberIdentifier() {
        const identifier = document.getElementById('memberIdentifier').value;
        const submitBtn = document.getElementById('submitBorrowBtn');
        submitBtn.disabled = identifier.trim().length < 3;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const memberInput = document.getElementById('memberIdentifier');
        if (memberInput) {
            memberInput.addEventListener('input', validateMemberIdentifier);
        }
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeBorrowModal();
        });
        
        const modal = document.getElementById('borrowModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target.id === 'borrowModal') closeBorrowModal();
            });
        }
    });
    </script>
</body>
</html>