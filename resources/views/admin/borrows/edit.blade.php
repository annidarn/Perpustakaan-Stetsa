<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Peminjaman
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
                <a href="{{ route('admin.borrows.show', $borrow) }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.borrows.update', $borrow) }}">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                Informasi Dasar
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <div class="text-sm text-gray-500">Kode Peminjaman</div>
                                    <div class="font-mono font-bold text-lg">{{ $borrow->borrow_code }}</div>
                                </div>
                                
                                <div>
                                    <div class="text-sm text-gray-500">Anggota</div>
                                    <div class="font-medium">{{ $borrow->member->user->name }}</div>
                                    <div class="text-sm text-gray-600">
                                        @if($borrow->member->type === 'student')
                                            NIS: {{ $borrow->member->nis }}
                                        @else
                                            NIP: {{ $borrow->member->nip }}
                                        @endif
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="text-sm text-gray-500">Buku</div>
                                    <div class="font-medium">{{ $borrow->bookCopy->book->title }}</div>
                                    <div class="text-sm text-gray-600">
                                        Copy #{{ str_pad($borrow->bookCopy->inventory_number, 5, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="text-sm text-gray-500">Dibuat</div>
                                    <div class="font-medium">{{ $borrow->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Dates & Status -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                                Tanggal & Status
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
                                           value="{{ old('borrow_date', $borrow->borrow_date->format('Y-m-d')) }}"
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
                                           value="{{ old('due_date', $borrow->due_date->format('Y-m-d')) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           required>
                                    @error('due_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Return Date -->
                                <div>
                                    <label for="return_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Kembali
                                    </label>
                                    <input type="date" 
                                           name="return_date" 
                                           id="return_date"
                                           value="{{ old('return_date', $borrow->return_date ? $borrow->return_date->format('Y-m-d') : '') }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <div class="mt-1 text-sm text-gray-500">
                                        Kosongkan jika belum dikembalikan
                                    </div>
                                    @error('return_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" 
                                            id="status"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                            onchange="toggleFineFields()"
                                            required>
                                        <option value="borrowed" {{ old('status', $borrow->status) == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                                        <option value="returned" {{ old('status', $borrow->status) == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                                        <option value="overdue" {{ old('status', $borrow->status) == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Fine Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-money-bill-wave text-yellow-500 mr-2"></i>
                                Informasi Denda
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Fine Amount -->
                                <div>
                                    <label for="fine_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                        Jumlah Denda (Rp)
                                    </label>
                                    <input type="number" 
                                           name="fine_amount" 
                                           id="fine_amount"
                                           value="{{ old('fine_amount', $borrow->fine_amount) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                           min="0"
                                           step="1000">
                                    <div class="mt-1 text-sm text-gray-500">
                                        Biaya keterlambatan
                                    </div>
                                    @error('fine_amount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Fine Paid -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Status Pembayaran Denda
                                    </label>
                                    <div class="flex items-center space-x-4 mt-2">
                                        <label class="flex items-center">
                                            <input type="radio" 
                                                   name="fine_paid" 
                                                   value="1"
                                                   class="mr-2"
                                                   {{ old('fine_paid', $borrow->fine_paid) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Lunas</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" 
                                                   name="fine_paid" 
                                                   value="0"
                                                   class="mr-2"
                                                   {{ !old('fine_paid', $borrow->fine_paid) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Belum</span>
                                        </label>
                                    </div>
                                    @error('fine_paid')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Extension Count -->
                                <div>
                                    <label for="extension_count" class="block text-sm font-medium text-gray-700 mb-2">
                                        Jumlah Perpanjangan
                                    </label>
                                    <select name="extension_count" 
                                            id="extension_count"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                        <option value="0" {{ old('extension_count', $borrow->extension_count) == 0 ? 'selected' : '' }}>Belum</option>
                                        <option value="1" {{ old('extension_count', $borrow->extension_count) == 1 ? 'selected' : '' }}>Sudah 1x (maks)</option>
                                    </select>
                                    <div class="mt-1 text-sm text-gray-500">
                                        Maksimal 1 kali perpanjangan
                                    </div>
                                    @error('extension_count')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Auto Calculate Button -->
                                <div class="flex items-end">
                                    <button type="button" 
                                            id="calculateFineBtn"
                                            class="w-full bg-amber-500 hover:bg-amber-600 text-white py-2 px-4 rounded-md font-medium shadow-sm transition-colors"
                                            onclick="calculateFine()">
                                        <i class="fas fa-calculator mr-2"></i> Hitung Denda Otomatis
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Fine Calculation Result -->
                            <div id="fineCalculationResult" class="mt-4 hidden p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-bold text-blue-800" id="calculationTitle"></div>
                                        <div class="text-sm text-blue-700 mt-1" id="calculationDetails"></div>
                                    </div>
                                    <button type="button" 
                                            class="text-blue-600 hover:text-blue-800"
                                            onclick="applyCalculation()">
                                        <i class="fas fa-check mr-1"></i> Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-sticky-note text-purple-500 mr-2"></i>
                                Catatan
                            </h3>
                            
                            <div>
                                <textarea name="notes" 
                                          id="notes" 
                                          rows="4"
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                          placeholder="Catatan atau keterangan tambahan">{{ old('notes', $borrow->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Warning Message -->
                        <div class="mb-6 bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 text-xl"></i>
                                <div>
                                    <div class="font-bold text-yellow-800">Perhatian!</div>
                                    <div class="text-sm text-yellow-700 mt-1">
                                        Perubahan status peminjaman akan mempengaruhi status ketersediaan buku.
                                        Pastikan data yang diinput sudah benar.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.borrows.show', $borrow) }}" 
                               class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-md shadow-sm transition-colors">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-colors">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Toggle fine fields based on status
        function toggleFineFields() {
            const status = document.getElementById('status').value;
            const fineAmountField = document.getElementById('fine_amount');
            const calculateBtn = document.getElementById('calculateFineBtn');
            
            if (status === 'returned') {
                fineAmountField.disabled = false;
                calculateBtn.disabled = false;
                calculateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else if (status === 'overdue') {
                fineAmountField.disabled = false;
                calculateBtn.disabled = false;
                calculateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                fineAmountField.disabled = true;
                calculateBtn.disabled = true;
                calculateBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
        
        // Calculate fine automatically
        function calculateFine() {
            const borrowDate = new Date(document.getElementById('borrow_date').value);
            const dueDate = new Date(document.getElementById('due_date').value);
            const returnDate = document.getElementById('return_date').value 
                ? new Date(document.getElementById('return_date').value) 
                : new Date();
            
            if (isNaN(borrowDate.getTime()) || isNaN(dueDate.getTime())) {
                alert('Tanggal pinjam dan jatuh tempo harus diisi terlebih dahulu.');
                return;
            }
            
            // Calculate days late
            let daysLate = 0;
            const compareDate = returnDate > dueDate ? returnDate : new Date();
            
            if (compareDate > dueDate) {
                daysLate = Math.floor((compareDate - dueDate) / (1000 * 60 * 60 * 24));
            }
            
            // Calculate fine
            const finePerDay = 1000;
            const calculatedFine = daysLate * finePerDay;
            
            // Show result
            const resultDiv = document.getElementById('fineCalculationResult');
            const title = document.getElementById('calculationTitle');
            const details = document.getElementById('calculationDetails');
            
            if (daysLate > 0) {
                title.textContent = `Denda: Rp ${calculatedFine.toLocaleString('id-ID')}`;
                details.textContent = `Terlambat ${daysLate} hari × Rp ${finePerDay.toLocaleString('id-ID')}/hari`;
                resultDiv.classList.remove('hidden');
            } else {
                title.textContent = 'Tidak ada denda';
                details.textContent = 'Buku dikembalikan tepat waktu atau belum jatuh tempo';
                resultDiv.classList.remove('hidden');
            }
            
            // Store calculated fine
            resultDiv.dataset.calculatedFine = calculatedFine;
        }
        
        // Apply calculated fine to form
        function applyCalculation() {
            const calculatedFine = document.getElementById('fineCalculationResult').dataset.calculatedFine;
            document.getElementById('fine_amount').value = calculatedFine;
            
            // If fine > 0, set status to overdue if not already returned
            if (calculatedFine > 0) {
                const statusSelect = document.getElementById('status');
                if (statusSelect.value !== 'returned') {
                    statusSelect.value = 'overdue';
                    toggleFineFields();
                }
            }
            
            // Hide result
            document.getElementById('fineCalculationResult').classList.add('hidden');
        }
        
        // Auto-calculate when dates change
        document.getElementById('borrow_date').addEventListener('change', function() {
            const dueDate = document.getElementById('due_date');
            if (!dueDate.value) {
                const newDueDate = new Date(this.value);
                newDueDate.setDate(newDueDate.getDate() + 7);
                dueDate.value = newDueDate.toISOString().split('T')[0];
            }
        });
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleFineFields();
            
            // Set min dates
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('borrow_date').min = '2020-01-01';
            document.getElementById('due_date').min = '2020-01-01';
            document.getElementById('return_date').min = '2020-01-01';
            
            // If return date exists and status is returned, enable fine calculation
            if (document.getElementById('return_date').value && 
                document.getElementById('status').value === 'returned') {
                calculateFine();
            }
        });
    </script>
    @endpush
</x-app-layout>