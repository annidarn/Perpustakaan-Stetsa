<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Anggota
            </h2>
            <a href="{{ route('members.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                <i class="fas fa-plus mr-2"></i> Tambah Anggota
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filter -->
            <form method="GET" action="{{ route('members.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                        <a href="{{ route('members.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Batch Actions -->
            <div id="batchActions" class="hidden mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <form id="batchUpdateForm" method="POST" action="{{ route('members.batch.update') }}">
                    @csrf
                    <input type="hidden" name="member_ids" id="selectedMembers">
                    
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="font-medium text-yellow-800">
                            <span id="selectedCount">0</span> anggota dipilih
                        </div>
                        
                        <div>
                            <select name="action" id="batchAction" class="rounded-md border-gray-300">
                                <option value="">Pilih Aksi</option>
                                <option value="status">Ubah Status</option>
                                <option value="class">Pindah Kelas (Siswa)</option>
                                <option value="delete">Hapus</option>
                            </select>
                        </div>
                        
                        <!-- Status Field -->
                        <div id="statusField" class="hidden">
                            <select name="status" class="rounded-md border-gray-300">
                                <option value="active">Aktif</option>
                                <option value="inactive">Non-Aktif</option>
                                <option value="graduated">Lulus</option>
                            </select>
                        </div>
                        
                        <!-- Class Field -->
                        <div id="classField" class="hidden">
                            <select name="class_id" class="rounded-md border-gray-300">
                                <option value="">Pilih Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->grade }} {{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <button type="button" onclick="submitBatchForm()" class="ml-2 text-gray-600 hover:text-gray-800">
                                Terapkan
                            </button>
                            <button type="button" onclick="clearSelection()" class="ml-2 text-gray-600 hover:text-gray-800">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Delete Form (terpisah) -->
                <form id="batchDeleteForm" method="POST" action="{{ route('members.batch.delete') }}" class="hidden">
                    @csrf
                    <input type="hidden" name="member_ids" id="deleteMembers">
                </form>
            </div>

            <!-- Alert Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

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
                                    <a href="{{ route('members.show', $member) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-sm transition-colors">
                                        <i class="fas fa-eye mr-1"></i>
                                        <span class="text-sm">Detail</span>
                                    </a>
                                    <a href="{{ route('members.edit', $member) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-amber-500 text-white rounded-md hover:bg-amber-600 shadow-sm transition-colors">
                                        <i class="fas fa-edit mr-1"></i>
                                        <span class="text-sm">Edit</span>
                                    </a>
                                    <form action="{{ route('members.destroy', $member) }}" method="POST" 
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
            const membersInput = document.getElementById('selectedMembers');
            const deleteInput = document.getElementById('deleteMembers');
            
            countSpan.textContent = selectedMembers.length;
            membersInput.value = JSON.stringify(selectedMembers);
            deleteInput.value = JSON.stringify(selectedMembers);
            
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
                    document.getElementById('batchDeleteForm').submit();
                }
            } else {
                document.getElementById('batchUpdateForm').submit();
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
    </script>
</x-app-layout>