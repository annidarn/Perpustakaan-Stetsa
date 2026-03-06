<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Anggota
            </h2>
            <div class="flex space-x-2">
                <button type="button" onclick="openTutorialModal()" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                    <i class="fas fa-question-circle mr-2"></i> Tutorial Import
                </button>
                <button type="button" onclick="openImportModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                    <i class="fas fa-file-import mr-2"></i> Import Excel
                </button>
                <a href="{{ route('admin.members.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                    <i class="fas fa-plus mr-2"></i> Tambah Anggota
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Tutorial Modal -->
    <div id="tutorialModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-book-open mr-2 text-amber-500"></i>Panduan Import Anggota</h3>
                    <button type="button" onclick="closeTutorialModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="space-y-4 text-gray-700 text-sm overflow-y-auto max-h-[70vh] pr-2">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <h4 class="font-bold text-blue-800 mb-2">Langkah 1: Download Template</h4>
                        <p class="mb-3">Gunakan template resmi agar sistem dapat mengenali data Anda dengan benar.</p>
                        <a href="{{ asset('template/import_member.xlsx') }}" download class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors font-bold">
                            <i class="fas fa-download mr-2"></i> Download Template .xlsx
                        </a>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-800 mb-2">Langkah 2: Isi Data</h4>
                        <p class="mb-2">Perhatikan aturan pengisian kolom berikut:</p>
                        <table class="min-w-full divide-y divide-gray-200 border text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-bold">Kolom</th>
                                    <th class="px-3 py-2 text-left font-bold">Aturan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="bg-white">
                                    <td class="px-3 py-2 font-semibold">Nama</td>
                                    <td class="px-3 py-2">Wajib diisi.</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-3 py-2 font-semibold">Tipe</td>
                                    <td class="px-3 py-2">Pilih salah satu: <strong>siswa</strong>, <strong>guru</strong>, atau <strong>staff</strong>.</td>
                                </tr>
                                <tr class="bg-white">
                                    <td class="px-3 py-2 font-semibold">NIS</td>
                                    <td class="px-3 py-2">Wajib unik (tidak boleh dobel). Jika kosong (khusus siswa), sistem akan membuatkan NIS otomatis.</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-3 py-2 font-semibold">Grade & Nama Kelas</td>
                                    <td class="px-3 py-2">Hanya untuk siswa. Contoh: Grade <strong>10</strong>, Nama Kelas <strong>RPL-1</strong>. Sistem akan mencari kelas yang cocok di database.</td>
                                </tr>
                                <tr class="bg-white">
                                    <td class="px-3 py-2 font-semibold">Jenis Kelamin</td>
                                    <td class="px-3 py-2">Isi <strong>L</strong> atau <strong>P</strong>.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-amber-50 p-4 rounded-lg border border-amber-100">
                        <h4 class="font-bold text-amber-800 mb-2">Langkah 3: Upload</h4>
                        <p>Setelah file siap, klik tombol <strong>Import Excel</strong>, pilih file Anda, dan klik <strong>Mulai Import</strong>.</p>
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeTutorialModal()" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors font-bold">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Mulai Import Data</h3>
                    <button type="button" onclick="closeImportModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form action="{{ route('admin.members.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel Anda</label>
                        <input type="file" name="file" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-md p-1">
                        <p class="mt-2 text-xs text-gray-500">Gunakan template .xlsx untuk hasil terbaik.</p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors font-medium">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-bold shadow-sm">
                            <i class="fas fa-upload mr-2"></i> Mulai Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filter -->
            <form method="GET" action="{{ route('admin.members.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-5 lg:grid-cols-6 gap-4">
                    <!-- Search Input -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Cari Anggota</label>
                        <input type="text" name="search" id="search" 
                               value="{{ request('search') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               placeholder="Nama, NIS, atau NIP">
                    </div>
                    
                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Jenis</label>
                        <select name="type" id="type" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">Semua Jenis</option>
                            <option value="student" {{ request('type') == 'student' ? 'selected' : '' }}>Siswa</option>
                            <option value="teacher" {{ request('type') == 'teacher' ? 'selected' : '' }}>Guru</option>
                            <option value="staff" {{ request('type') == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    
                    <!-- Class Filter -->
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700">Kelas</label>
                        <select name="class_id" id="class_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->grade }} {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Lulus</option>
                        </select>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-end space-x-2">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                        <a href="{{ route('admin.members.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div id="batchActions" class="hidden mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg shadow-sm">
                <form id="batchUpdateForm" method="POST" action="{{ route('admin.members.batch.update') }}">
                    @csrf
                    <div id="updateMembersContainer"></div>
                    
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="font-bold text-yellow-800">
                            <i class="fas fa-check-square mr-2"></i>
                            <span id="selectedCount">0</span> anggota dipilih
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <select name="action" id="batchAction" class="rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 text-sm">
                                <option value="">-- Pilih Aksi --</option>
                                <option value="status">Ubah Status</option>
                                <option value="class">Pindah Kelas (Siswa)</option>
                                <option value="delete">Hapus Masal</option>
                            </select>
                        </div>
                        
                        <!-- Status Field -->
                        <div id="statusField" class="hidden flex items-center space-x-2">
                            <select name="status" class="rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 text-sm">
                                <option value="active">Aktif</option>
                                <option value="inactive">Non-Aktif</option>
                                <option value="graduated">Lulus</option>
                            </select>
                        </div>
                        
                        <!-- Class Field -->
                        <div id="classField" class="hidden flex items-center space-x-2">
                            <select name="class_id" class="rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 text-sm">
                                <option value="">-- Pilih Kelas Tujuan --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->grade }} {{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <button type="button" onclick="submitBatchForm()" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-1.5 px-4 rounded text-sm transition-colors shadow-sm">
                                Terapkan
                            </button>
                            <button type="button" onclick="clearSelection()" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Delete Form (terpisah untuk CSRF & Method) -->
                <form id="batchDeleteForm" method="POST" action="{{ route('admin.members.batch.delete') }}" class="hidden">
                    @csrf
                    <div id="deleteMembersContainer"></div>
                </form>
            </div>

            <!-- Members Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                <input type="checkbox" id="selectAll" class="rounded">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS/NIP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas/Tahun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($members as $member)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" 
                                       value="{{ $member->id }}" 
                                       class="member-checkbox rounded">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $loop->iteration + (($members->currentPage() - 1) * $members->perPage()) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $member->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $member->phone ?: 'No HP kosong' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">
                                @if($member->type === 'student')
                                    {{ $member->nis ?: '-' }}
                                @else
                                    {{ $member->nip ?: '-' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $member->type === 'student' ? 'bg-blue-100 text-blue-800' : 
                                       ($member->type === 'teacher' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $member->type === 'student' ? 'Siswa' : 
                                       ($member->type === 'teacher' ? 'Guru' : 'Staff') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($member->type === 'student')
                                    @if($member->class)
                                        <span class="font-medium">{{ $member->class->grade }} {{ $member->class->class_name }}</span><br>
                                        <span class="text-gray-500">Masuk: {{ $member->enrollment_year }}</span>
                                    @else
                                        <span class="text-gray-400">Belum ada kelas</span>
                                    @endif
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $member->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($member->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $member->status === 'active' ? 'Aktif' : 
                                       ($member->status === 'inactive' ? 'Non-Aktif' : 'Lulus') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.members.show', $member) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-sm transition-colors">
                                        <i class="fas fa-eye mr-1"></i>
                                        <span class="text-sm">Detail</span>
                                    </a>
                                    <a href="{{ route('admin.members.edit', $member) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-amber-500 text-white rounded-md hover:bg-amber-600 shadow-sm transition-colors">
                                        <i class="fas fa-edit mr-1"></i>
                                        <span class="text-sm">Edit</span>
                                    </a>
                                    <form action="{{ route('admin.members.destroy', $member) }}" method="POST" 
                                          class="inline" onsubmit="return confirm('Hapus anggota {{ $member->user->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 shadow-sm transition-colors">
                                            <i class="fas fa-trash mr-1"></i>
                                            <span class="text-sm">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                @if(request()->hasAny(['search', 'type', 'status']))
                                    Tidak ditemukan anggota dengan filter tersebut.
                                @else
                                    Belum ada data anggota.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $members->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let selectedMembers = [];
        
        // Toggle select all
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.member-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
                updateMemberSelection(cb.value, cb.checked);
            });
            updateBatchUI();
        });
        
        // Update selection when checkbox clicked
        document.querySelectorAll('.member-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateMemberSelection(this.value, this.checked);
                updateBatchUI();
            });
        });
        
        function updateMemberSelection(memberId, isChecked) {
            memberId = String(memberId);
            if (isChecked) {
                if (!selectedMembers.includes(memberId)) {
                    selectedMembers.push(memberId);
                }
            } else {
                const index = selectedMembers.indexOf(memberId);
                if (index > -1) {
                    selectedMembers.splice(index, 1);
                }
            }
        }
        
        function updateBatchUI() {
            const batchDiv = document.getElementById('batchActions');
            const countSpan = document.getElementById('selectedCount');
            
            countSpan.textContent = selectedMembers.length;
            
            if (selectedMembers.length > 0) {
                batchDiv.classList.remove('hidden');
                // Uncheck select all if not all selected
                const totalCheckboxes = document.querySelectorAll('.member-checkbox').length;
                document.getElementById('selectAll').checked = selectedMembers.length === totalCheckboxes;
            } else {
                batchDiv.classList.add('hidden');
                document.getElementById('selectAll').checked = false;
            }
        }
        
        // Toggle batch fields based on action
        document.getElementById('batchAction').addEventListener('change', function() {
            const action = this.value;
            document.getElementById('statusField').classList.add('hidden');
            document.getElementById('classField').classList.add('hidden');
            
            if (action === 'status') {
                document.getElementById('statusField').classList.remove('hidden');
            } else if (action === 'class') {
                document.getElementById('classField').classList.remove('hidden');
            }
        });
        
        function submitBatchForm() {
            const action = document.getElementById('batchAction').value;
            
            if (!action) {
                alert('Pilih aksi terlebih dahulu!');
                return;
            }
            
            if (selectedMembers.length === 0) {
                alert('Pilih anggota terlebih dahulu!');
                return;
            }
            
            if (action === 'delete') {
                if (confirm(`Hapus ${selectedMembers.length} anggota yang dipilih?`)) {
                    const deleteForm = document.getElementById('batchDeleteForm');
                    const container = document.getElementById('deleteMembersContainer');
                    container.innerHTML = '';
                    selectedMembers.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'member_ids[]';
                        input.value = id;
                        container.appendChild(input);
                    });
                    deleteForm.submit();
                }
            } else {
                const updateForm = document.getElementById('batchUpdateForm');
                const container = document.getElementById('updateMembersContainer');
                container.innerHTML = '';
                
                selectedMembers.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'member_ids[]';
                    input.value = id;
                    container.appendChild(input);
                });
                updateForm.submit();
            }
        }
        
        function clearSelection() {
            selectedMembers = [];
            document.querySelectorAll('.member-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateBatchUI();
            document.getElementById('batchAction').value = '';
            document.getElementById('statusField').classList.add('hidden');
            document.getElementById('classField').classList.add('hidden');
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            clearSelection();
        });

        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
            document.getElementById('importModal').classList.add('flex');
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
            document.getElementById('importModal').classList.remove('flex');
        }

        function openTutorialModal() {
            document.getElementById('tutorialModal').classList.remove('hidden');
            document.getElementById('tutorialModal').classList.add('flex');
        }

        function closeTutorialModal() {
            document.getElementById('tutorialModal').classList.add('hidden');
            document.getElementById('tutorialModal').classList.remove('flex');
        }

        // SweetAlert2 Toast/Modal Configuration
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{!! session('success') !!}",
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: "{!! session('error') !!}",
                    confirmButtonText: 'OK',
                    allowOutsideClick: false // User must click OK
                });
            @endif
        });
    </script>
</x-app-layout>