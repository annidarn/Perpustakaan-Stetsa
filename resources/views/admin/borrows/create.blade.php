<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Buat Peminjaman Baru
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Tambah peminjaman manual oleh admin
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.borrows.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.borrows.store') }}">
                        @csrf

                        <!-- Member Selection -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-user text-blue-500 mr-2"></i>
                                Pilih Anggota
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Search Member -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Cari Anggota
                                    </label>
                                    <input type="text" 
                                           id="memberSearch"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           placeholder="Nama, NIS, atau NIP"
                                           onkeyup="filterMembers()">
                                </div>
                                
                                <!-- Member List -->
                                <div class="md:col-span-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-2">
                                    @foreach($members as $member)
                                    <label class="member-option block p-4 border border-gray-200 rounded-lg mb-2 cursor-pointer hover:bg-blue-50">
                                        <div class="flex items-center">
                                            <input type="radio" 
                                                   name="member_id" 
                                                   value="{{ $member->id }}"
                                                   class="mr-3 member-radio" 
                                                   required
                                                   data-member-name="{{ $member->user->name }}"
                                                   data-member-id="{{ $member->type === 'student' ? $member->nis : $member->nip }}"
                                                   data-member-type="{{ $member->type }}"
                                                   data-active-borrows="{{ $member->borrows()->whereIn('status', ['borrowed', 'overdue'])->count() }}"
                                                   onchange="updateMemberInfo()">
                                            <div class="flex-grow">
                                                <div class="font-medium text-gray-800">{{ $member->user->name }}</div>
                                                <div class="text-sm text-gray-600 mt-1">
                                                    <span class="inline-flex items-center">
                                                        @if($member->type === 'student')
                                                        <i class="fas fa-graduation-cap mr-1"></i>
                                                        NIS: {{ $member->nis }}
                                                        @else
                                                        <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                        NIP: {{ $member->nip }}
                                                        @endif
                                                        <span class="mx-2">•</span>
                                                        <span class="px-2 py-0.5 text-xs rounded-full 
                                                            {{ $member->type === 'student' ? 'bg-blue-100 text-blue-800' : 
                                                               ($member->type === 'teacher' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                            {{ $member->type === 'student' ? 'Siswa' : 
                                                               ($member->type === 'teacher' ? 'Guru' : 'Staff') }}
                                                        </span>
                                                        <span class="mx-2">•</span>
                                                        <span>Pinjaman aktif: {{ $member->borrows()->whereIn('status', ['borrowed', 'overdue'])->count() }}/5</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                    
                                    @if($members->isEmpty())
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-users text-3xl mb-2"></i>
                                        <p>Tidak ada anggota aktif</p>
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Selected Member Info -->
                                <div id="selectedMemberInfo" class="md:col-span-2 hidden p-4 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-blue-800" id="selectedMemberName"></div>
                                            <div class="text-sm text-blue-700 mt-1">
                                                <span id="selectedMemberId"></span>
                                                <span class="mx-2">•</span>
                                                <span id="selectedMemberType"></span>
                                                <span class="mx-2">•</span>
                                                <span>Pinjaman aktif: <span id="selectedActiveBorrows"></span>/5</span>
                                            </div>
                                        </div>
                                        <div class="text-sm">
                                            <span id="memberStatusBadge" class="px-2 py-1 rounded-full"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Book Selection -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-book text-green-500 mr-2"></i>
                                Pilih Buku
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Search Book -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Cari Buku
                                    </label>
                                    <input type="text" 
                                           id="bookSearch"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           placeholder="Judul, pengarang, atau ISBN"
                                           onkeyup="filterBooks()">
                                </div>
                                
                                <!-- Book List -->
                                <div class="md:col-span-2">
                                    <div id="bookList" class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-2">
                                        @foreach($books as $book)
                                        <div class="book-option mb-4 border border-gray-200 rounded-lg overflow-hidden">
                                            <div class="p-4">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0 w-12 h-16 bg-gradient-to-r from-green-100 to-blue-100 rounded flex items-center justify-center mr-4">
                                                        <i class="fas fa-book text-green-600"></i>
                                                    </div>
                                                    <div class="flex-grow">
                                                        <div class="font-bold text-gray-800 mb-1">{{ $book->title }}</div>
                                                        <div class="text-sm text-gray-600 mb-2">{{ $book->author }}</div>
                                                        
                                                        <!-- Available Copies -->
                                                        <div class="mb-3">
                                                            <div class="text-xs text-gray-500 mb-2">Copy Tersedia:</div>
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach($book->copies as $copy)
                                                                <label class="copy-option inline-flex items-center p-2 border border-gray-300 rounded cursor-pointer hover:bg-blue-50">
                                                                    <input type="radio" 
                                                                           name="book_copy_id" 
                                                                           value="{{ $copy->id }}"
                                                                           class="mr-2" 
                                                                           required
                                                                           data-book-title="{{ $book->title }}"
                                                                           data-copy-number="{{ str_pad($copy->inventory_number, 5, '0', STR_PAD_LEFT) }}"
                                                                           onchange="updateSelectedBook()">
                                                                    <div>
                                                                        <div class="font-medium text-sm">Copy #{{ str_pad($copy->inventory_number, 5, '0', STR_PAD_LEFT) }}</div>
                                                                        <div class="text-xs text-gray-500">
                                                                            Kondisi: {{ $copy->condition === 'good' ? 'Baik' : 'Rusak' }}
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                                @endforeach
                                                            </div>
                                                            @if($book->copies->isEmpty())
                                                            <div class="text-sm text-red-600">
                                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                                Tidak ada copy tersedia
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        
                                        @if($books->isEmpty())
                                        <div class="text-center py-8 text-gray-500">
                                            <i class="fas fa-book text-3xl mb-2"></i>
                                            <p>Tidak ada buku dengan copy tersedia</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Selected Book Info -->
                                <div id="selectedBookInfo" class="md:col-span-2 hidden p-4 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-green-800" id="selectedBookTitle"></div>
                                            <div class="text-sm text-green-700 mt-1">
                                                Copy #<span id="selectedCopyNumber"></span>
                                            </div>
                                        </div>
                                        <div class="text-sm">
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">
                                                <i class="fas fa-check mr-1"></i> Dipilih
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loan Details -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
                                Detail Peminjaman
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Borrow Date -->
                                <div>
                                    <label for="borrow_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Pinjam <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" 
                                           name="borrow_date" 
                                           id="borrow_date"
                                           value="{{ old('borrow_date', date('Y-m-d')) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           required>
                                    @error('borrow_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Due Date -->
                                <div>
                                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Jatuh Tempo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" 
                                           name="due_date" 
                                           id="due_date"
                                           value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           required>
                                    <div class="mt-1 text-sm text-gray-500">
                                        Default: 7 hari dari tanggal pinjam
                                    </div>
                                    @error('due_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Notes -->
                                <div class="md:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Catatan (Opsional)
                                    </label>
                                    <textarea name="notes" 
                                              id="notes" 
                                              rows="3"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                              placeholder="Catatan khusus untuk peminjaman ini">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Alert -->
                        <div id="validationAlert" class="hidden mb-6 bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 text-xl"></i>
                                <div>
                                    <div class="font-bold text-yellow-800" id="alertTitle"></div>
                                    <div class="text-sm text-yellow-700 mt-1" id="alertMessage"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.borrows.index') }}" 
                               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit" 
                                    id="submitBtn"
                                    class="px-6 py-3 bg-blue-600 text-black rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-save mr-2"></i> Simpan Peminjaman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Filter members by search
        function filterMembers() {
            const search = document.getElementById('memberSearch').value.toLowerCase();
            const members = document.querySelectorAll('.member-option');
            
            members.forEach(member => {
                const text = member.textContent.toLowerCase();
                if (text.includes(search)) {
                    member.style.display = 'block';
                } else {
                    member.style.display = 'none';
                }
            });
        }
        
        // Filter books by search
        function filterBooks() {
            const search = document.getElementById('bookSearch').value.toLowerCase();
            const books = document.querySelectorAll('.book-option');
            
            books.forEach(book => {
                const text = book.textContent.toLowerCase();
                if (text.includes(search)) {
                    book.style.display = 'block';
                } else {
                    book.style.display = 'none';
                }
            });
        }
        
        // Update selected member info
        function updateMemberInfo() {
            const selectedRadio = document.querySelector('.member-radio:checked');
            const infoDiv = document.getElementById('selectedMemberInfo');
            const validationAlert = document.getElementById('validationAlert');
            
            if (selectedRadio) {
                // Show info
                document.getElementById('selectedMemberName').textContent = selectedRadio.dataset.memberName;
                document.getElementById('selectedMemberId').textContent = 
                    selectedRadio.dataset.memberType === 'student' ? 
                    'NIS: ' + selectedRadio.dataset.memberId : 
                    'NIP: ' + selectedRadio.dataset.memberId;
                document.getElementById('selectedMemberType').textContent = 
                    selectedRadio.dataset.memberType === 'student' ? 'Siswa' : 
                    (selectedRadio.dataset.memberType === 'teacher' ? 'Guru' : 'Staff');
                document.getElementById('selectedActiveBorrows').textContent = selectedRadio.dataset.activeBorrows;
                
                // Update badge
                const badge = document.getElementById('memberStatusBadge');
                const activeBorrows = parseInt(selectedRadio.dataset.activeBorrows);
                
                if (activeBorrows >= 5) {
                    badge.className = 'px-2 py-1 bg-red-100 text-red-800 rounded-full';
                    badge.textContent = 'Tidak bisa meminjam (5/5)';
                    
                    // Show validation alert
                    validationAlert.classList.remove('hidden');
                    document.getElementById('alertTitle').textContent = 'Anggota tidak dapat meminjam!';
                    document.getElementById('alertMessage').textContent = 
                        'Anggota ini sudah meminjam 5 buku (maksimal). Harus mengembalikan dulu sebelum pinjam lagi.';
                    
                    // Disable submit
                    document.getElementById('submitBtn').disabled = true;
                    
                } else if (activeBorrows >= 3) {
                    badge.className = 'px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full';
                    badge.textContent = 'Hati-hati (' + activeBorrows + '/5)';
                    
                    // Show warning alert
                    validationAlert.classList.remove('hidden');
                    document.getElementById('alertTitle').textContent = 'Peringatan!';
                    document.getElementById('alertMessage').textContent = 
                        'Anggota ini sudah meminjam ' + activeBorrows + ' buku. Maksimal 5 buku per anggota.';
                    
                    // Enable submit
                    document.getElementById('submitBtn').disabled = false;
                    
                } else {
                    badge.className = 'px-2 py-1 bg-green-100 text-green-800 rounded-full';
                    badge.textContent = 'Bisa meminjam (' + activeBorrows + '/5)';
                    
                    // Hide alert
                    validationAlert.classList.add('hidden');
                    
                    // Enable submit
                    document.getElementById('submitBtn').disabled = false;
                }
                
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
                validationAlert.classList.add('hidden');
                document.getElementById('submitBtn').disabled = false;
            }
        }
        
        // Update selected book info
        function updateSelectedBook() {
            const selectedRadio = document.querySelector('input[name="book_copy_id"]:checked');
            const infoDiv = document.getElementById('selectedBookInfo');
            
            if (selectedRadio) {
                document.getElementById('selectedBookTitle').textContent = selectedRadio.dataset.bookTitle;
                document.getElementById('selectedCopyNumber').textContent = selectedRadio.dataset.copyNumber;
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }
        }
        
        // Set due date based on borrow date
        document.getElementById('borrow_date').addEventListener('change', function() {
            const borrowDate = new Date(this.value);
            if (!isNaN(borrowDate.getTime())) {
                const dueDate = new Date(borrowDate);
                dueDate.setDate(dueDate.getDate() + 7);
                document.getElementById('due_date').value = dueDate.toISOString().split('T')[0];
            }
        });
        
        // Style copy selection
        document.querySelectorAll('.copy-option').forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            
            radio.addEventListener('change', function() {
                document.querySelectorAll('.copy-option').forEach(opt => {
                    opt.classList.remove('border-blue-500', 'bg-blue-50');
                });
                
                if (this.checked) {
                    this.closest('.copy-option').classList.add('border-blue-500', 'bg-blue-50');
                }
            });
        });
        
        // Style member selection
        document.querySelectorAll('.member-option').forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            
            radio.addEventListener('change', function() {
                document.querySelectorAll('.member-option').forEach(opt => {
                    opt.classList.remove('border-blue-500', 'bg-blue-50');
                });
                
                if (this.checked) {
                    this.closest('.member-option').classList.add('border-blue-500', 'bg-blue-50');
                }
            });
        });
        
        // Initialize date fields
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            const nextWeekStr = nextWeek.toISOString().split('T')[0];
            
            if (!document.getElementById('borrow_date').value) {
                document.getElementById('borrow_date').value = today;
            }
            
            if (!document.getElementById('due_date').value) {
                document.getElementById('due_date').value = nextWeekStr;
            }
        });
    </script>
    @endpush
</x-app-layout>