<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Naik Kelas Massal
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Update kelas semua siswa secara otomatis
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('members.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="text-3xl font-bold text-blue-700">{{ $stats['grade_10'] }}</div>
                    <div class="text-sm text-blue-600 mt-1">Siswa Kelas 10</div>
                    <div class="mt-2 text-xs text-blue-500">Akan naik ke kelas 11</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="text-3xl font-bold text-green-700">{{ $stats['grade_11'] }}</div>
                    <div class="text-sm text-green-600 mt-1">Siswa Kelas 11</div>
                    <div class="mt-2 text-xs text-green-500">Akan naik ke kelas 12</div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                    <div class="text-3xl font-bold text-purple-700">{{ $stats['grade_12'] }}</div>
                    <div class="text-sm text-purple-600 mt-1">Siswa Kelas 12</div>
                    <div class="mt-2 text-xs text-purple-500">Akan lulus (graduated)</div>
                </div>
            </div>

            <!-- Warning Alert -->
            <div class="mb-8 bg-yellow-50 border border-yellow-200 p-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mr-4"></i>
                    <div>
                        <h3 class="font-bold text-yellow-800 text-lg">PERHATIAN!</h3>
                        <p class="text-yellow-700 mt-1">
                            Fitur ini akan mengupdate <span class="font-bold">SEMUA SISWA AKTIF</span> secara massal.
                            Pastikan data kelas sudah sesuai sebelum melanjutkan.
                        </p>
                        <div class="mt-3 text-sm text-yellow-600">
                            <i class="fas fa-lightbulb mr-1"></i>
                            Disarankan untuk backup database sebelum melakukan aksi ini.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Notification -->
            @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-2xl mr-4"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-bold text-green-800 text-lg mb-2">Naik Kelas Berhasil!</h3>
                        <div class="text-green-700">{!! session('success') !!}</div>
                        
                        @if(session('results'))
                        <div class="mt-4 grid grid-cols-3 gap-4">
                            <div class="bg-green-100 p-3 rounded text-center">
                                <div class="text-2xl font-bold text-green-800">{{ session('results')['promoted'] }}</div>
                                <div class="text-sm text-green-700">Naik Kelas</div>
                            </div>
                            <div class="bg-purple-100 p-3 rounded text-center">
                                <div class="text-2xl font-bold text-purple-800">{{ session('results')['graduated'] }}</div>
                                <div class="text-sm text-purple-700">Lulus</div>
                            </div>
                            <div class="bg-blue-100 p-3 rounded text-center">
                                <div class="text-2xl font-bold text-blue-800">{{ session('results')['skipped'] }}</div>
                                <div class="text-sm text-blue-700">Dilewati</div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-green-200">
                            <a href="{{ route('members.index') }}" 
                            class="inline-flex items-center text-green-700 hover:text-green-900 font-medium">
                                <i class="fas fa-users mr-2"></i> Lihat Daftar Anggota
                            </a>
                            <button onclick="window.location.reload()"
                                    class="ml-4 inline-flex items-center text-blue-700 hover:text-blue-900 font-medium">
                                <i class="fas fa-redo mr-2"></i> Lakukan Lagi
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Error Notification -->
            @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-red-800 text-lg mb-2">Gagal Memproses!</h3>
                        <div class="text-red-700">{{ session('error') }}</div>
                        <div class="mt-3 text-sm text-red-600">
                            <i class="fas fa-lightbulb mr-1"></i>
                            Periksa konfigurasi database dan pastikan semua kelas sudah dibuat.
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-700 mb-6">Konfigurasi Naik Kelas</h3>
                    
                    <form method="POST" action="{{ route('members.promote.process') }}">
                        @csrf
                        
                        <!-- Kelas Mapping -->
                        <div class="space-y-6 mb-8">
                            <!-- Kelas 10 → 11 -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                                    <span class="w-8 h-8 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center mr-3">10</span>
                                    Kelas 10 → Kelas 11
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($classes->where('grade', '10') as $class10)
                                    <div class="flex items-center space-x-4 p-3 border border-gray-100 rounded-lg">
                                        <div class="flex-grow">
                                            <div class="font-medium">{{ $class10->grade }} {{ $class10->class_name }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $class10->members()->where('type', 'student')->where('status', 'active')->count() }} siswa
                                            </div>
                                        </div>
                                        <div class="text-gray-400">
                                            <i class="fas fa-arrow-right"></i>
                                        </div>
                                        <div>
                                            <select name="promote_map[{{ $class10->id }}]" 
                                                    class="rounded-md border-gray-300 text-sm">
                                                <option value="">Pilih Kelas 11</option>
                                                @foreach($classes->where('grade', '11') as $class11)
                                                    <option value="{{ $class11->id }}">
                                                        {{ $class11->grade }} {{ $class11->class_name }}
                                                    </option>
                                                @endforeach
                                                <option value="graduated">LULUSKAN</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Kelas 11 → 12 -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                                    <span class="w-8 h-8 bg-green-100 text-green-800 rounded-full flex items-center justify-center mr-3">11</span>
                                    Kelas 11 → Kelas 12
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($classes->where('grade', '11') as $class11)
                                    <div class="flex items-center space-x-4 p-3 border border-gray-100 rounded-lg">
                                        <div class="flex-grow">
                                            <div class="font-medium">{{ $class11->grade }} {{ $class11->class_name }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $class11->members()->where('type', 'student')->where('status', 'active')->count() }} siswa
                                            </div>
                                        </div>
                                        <div class="text-gray-400">
                                            <i class="fas fa-arrow-right"></i>
                                        </div>
                                        <div>
                                            <select name="promote_map[{{ $class11->id }}]" 
                                                    class="rounded-md border-gray-300 text-sm">
                                                <option value="">Pilih Kelas 12</option>
                                                @foreach($classes->where('grade', '12') as $class12)
                                                    <option value="{{ $class12->id }}">
                                                        {{ $class12->grade }} {{ $class12->class_name }}
                                                    </option>
                                                @endforeach
                                                <option value="graduated">LULUSKAN</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Kelas 12 → Lulus -->
                            <div class="border border-gray-200 rounded-lg p-6">
                                <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                                    <span class="w-8 h-8 bg-purple-100 text-purple-800 rounded-full flex items-center justify-center mr-3">12</span>
                                    Kelas 12 → Lulus
                                </h4>
                                
                                <div class="space-y-3">
                                    @foreach($classes->where('grade', '12') as $class12)
                                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                        <div>
                                            <div class="font-medium">{{ $class12->grade }} {{ $class12->class_name }}</div>
                                            <div class="text-sm text-purple-600">
                                                {{ $class12->members()->where('type', 'student')->where('status', 'active')->count() }} siswa akan lulus
                                            </div>
                                        </div>
                                        <div class="text-purple-700 font-medium">
                                            <i class="fas fa-graduation-cap mr-1"></i> LULUS
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-4 text-sm text-gray-600">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Semua siswa kelas 12 akan diubah statusnya menjadi "Lulus" dan class_id akan di-set NULL.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Options -->
                        <div class="mb-8 p-6 border border-gray-200 rounded-lg">
                            <h4 class="font-bold text-gray-700 mb-4">Opsi Tambahan</h4>
                            
                            <div class="space-y-4">
                                <label class="flex items-start">
                                    <input type="checkbox" name="update_enrollment_year" value="1" class="mt-1 mr-3">
                                    <div>
                                        <span class="font-medium text-gray-700">Update Tahun Masuk</span>
                                        <p class="text-sm text-gray-600">
                                            Tambah +1 tahun pada enrollment_year siswa yang naik kelas.
                                            Contoh: 2023 → 2024
                                        </p>
                                    </div>
                                </label>
                                
                                <label class="flex items-start">
                                    <input type="checkbox" name="create_backup" value="1" class="mt-1 mr-3" checked>
                                    <div>
                                        <span class="font-medium text-gray-700">Buat Backup Data</span>
                                        <p class="text-sm text-gray-600">
                                            Simpan snapshot data siswa sebelum diubah (disimpan di tabel members_backup).
                                        </p>
                                    </div>
                                </label>
                                
                                <label class="flex items-start">
                                    <input type="checkbox" name="notify_students" value="1" class="mt-1 mr-3">
                                    <div>
                                        <span class="font-medium text-gray-700">Buat Notifikasi</span>
                                        <p class="text-sm text-gray-600">
                                            Tambah catatan di field notes setiap siswa tentang kenaikan kelas.
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Preview Button -->
                        <div class="mb-6">
                            <button type="button" 
                                    onclick="previewPromotion()"
                                    class="w-full bg-amber-500 hover:bg-amber-600 text-white py-3 px-4 rounded-lg font-medium shadow-sm transition-colors">
                                <i class="fas fa-eye mr-2"></i> Preview Perubahan
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('members.index') }}" 
                               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium shadow-sm transition-colors"
                                    onclick="return confirm('Proses naik kelas massal? Tindakan ini tidak dapat dibatalkan.')">
                                <i class="fas fa-play mr-2"></i> Jalankan Naik Kelas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewPromotion() {
            alert('Fitur preview akan menampilkan detail perubahan sebelum diproses.\n(Silahkan implementasi AJAX request jika diperlukan)');
            // Bisa diisi dengan AJAX call ke endpoint preview nanti
        }
        
        // Auto-select graduated for grade 12
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select[name^="promote_map"]').forEach(select => {
                const classId = select.name.match(/\[(\d+)\]/)[1];
                // Jika kelas grade 12, auto-select graduated
                const isGrade12 = Array.from(document.querySelectorAll('.border-gray-200:nth-child(3) select'))
                    .some(s => s === select);
                
                if (isGrade12) {
                    select.value = 'graduated';
                    select.disabled = true;
                    select.classList.add('bg-gray-100');
                }
            });
        });
    </script>

        <!-- ... konten HTML sebelumnya ... -->
    <script>
        // ===== BAGIAN B: JAVASCRIPT VALIDATION =====
        // Form validation before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const selects = document.querySelectorAll('select[name^="promote_map"]');
            let hasSelection = false;
            
            selects.forEach(select => {
                if (select.value && select.value !== 'graduated') {
                    hasSelection = true;
                }
            });
            
            if (!hasSelection) {
                e.preventDefault();
                alert('Pilih setidaknya satu mapping kelas untuk diproses!');
                return false;
            }
            
            // Confirm for grade 12 graduation
            const grade12Graduation = Array.from(selects).filter(s => 
                s.closest('.border-gray-200:nth-child(3)') && s.value === 'graduated'
            ).length;
            
            if (grade12Graduation > 0) {
                const confirmed = confirm(
                    `Anda akan meluluskan ${grade12Graduation} kelas 12. ` +
                    `Semua siswa kelas 12 akan berstatus "Lulus". Lanjutkan?`
                );
                if (!confirmed) {
                    e.preventDefault();
                    return false;
                }
            }
            
            return true;
        });
        
        // ===== BAGIAN C (AJAX Preview) - OPSIONAL =====
        // ... kode AJAX preview jika mau ...
        
        // ===== AUTO-SELECT GRADUATED FOR GRADE 12 =====
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select[name^="promote_map"]').forEach(select => {
                // Jika kelas grade 12, auto-select graduated
                const isGrade12 = Array.from(document.querySelectorAll('.border-gray-200:nth-child(3) select'))
                    .some(s => s === select);
                
                if (isGrade12) {
                    select.value = 'graduated';
                    select.disabled = true;
                    select.classList.add('bg-gray-100');
                }
            });
        });
    </script>
</x-app-layout>