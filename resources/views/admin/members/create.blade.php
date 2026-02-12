<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Anggota Baru
            </h2>
            <a href="{{ route('admin.members.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.members.store') }}">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jenis Anggota -->
                        <div class="mb-6">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Anggota <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="type" value="student" 
                                           class="mr-3" 
                                           {{ old('type', 'student') == 'student' ? 'checked' : '' }}>
                                    <div>
                                        <span class="font-medium">Siswa</span>
                                        <p class="text-sm text-gray-500">Memerlukan NIS dan Kelas</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="type" value="teacher" 
                                           class="mr-3"
                                           {{ old('type') == 'teacher' ? 'checked' : '' }}>
                                    <div>
                                        <span class="font-medium">Guru</span>
                                        <p class="text-sm text-gray-500">Memerlukan NIP</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="type" value="staff" 
                                           class="mr-3"
                                           {{ old('type') == 'staff' ? 'checked' : '' }}>
                                    <div>
                                        <span class="font-medium">Staff</span>
                                        <p class="text-sm text-gray-500">Memerlukan NIP</p>
                                    </div>
                                </label>
                            </div>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- NIS/NIP Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- NIS Field (conditional for students) -->
                            <div id="nis-field">
                                <label for="nis" class="block text-sm font-medium text-gray-700 mb-2">
                                    NIS (Nomor Induk Siswa)
                                </label>
                                <input type="text" name="nis" id="nis" 
                                       value="{{ old('nis') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                       placeholder="Kosongkan untuk auto-generate">
                                <p class="mt-1 text-sm text-gray-500">Hanya untuk siswa</p>
                                @error('nis')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- NIP Field (conditional for teacher/staff) -->
                            <div id="nip-field" class="hidden">
                                <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">
                                    NIP (Nomor Induk Pegawai)
                                </label>
                                <input type="text" name="nip" id="nip" 
                                       value="{{ old('nip') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                       placeholder="Kosongkan untuk auto-generate">
                                <p class="mt-1 text-sm text-gray-500">Untuk guru/staff</p>
                                @error('nip')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Class Field (only for students) -->
                        <div class="mb-6" id="class-field">
                            <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Kelas
                            </label>
                            <select name="class_id" id="class_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="">Pilih Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" 
                                            {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->grade }} {{ $class->class_name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Hanya untuk siswa</p>
                            @error('class_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Enrollment Year (only for students) -->
                        <div class="mb-6" id="enrollment-field">
                            <label for="enrollment_year" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Masuk
                            </label>
                            <input type="number" name="enrollment_year" id="enrollment_year" 
                                   value="{{ old('enrollment_year', date('Y')) }}"
                                   min="2000" max="{{ date('Y') + 1 }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <p class="mt-1 text-sm text-gray-500">Tahun masuk sekolah (otomatis tahun ini)</p>
                            @error('enrollment_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Personal Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Telepon
                                </label>
                                <input type="tel" name="phone" id="phone" 
                                       value="{{ old('phone') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Gender -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Kelamin
                                </label>
                                <select name="gender" id="gender" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-8">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="status" value="active" 
                                           class="mr-3" 
                                           {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                                    <div>
                                        <span class="font-medium">Aktif</span>
                                        <p class="text-sm text-gray-500">Dapat meminjam buku</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="status" value="inactive" 
                                           class="mr-3"
                                           {{ old('status') == 'inactive' ? 'checked' : '' }}>
                                    <div>
                                        <span class="font-medium">Non-Aktif</span>
                                        <p class="text-sm text-gray-500">Tidak dapat meminjam</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="status" value="graduated" 
                                           class="mr-3"
                                           {{ old('status') == 'graduated' ? 'checked' : '' }}>
                                    <div>
                                        <span class="font-medium">Lulus</span>
                                        <p class="text-sm text-gray-500">Untuk siswa yang telah lulus</p>
                                    </div>
                                </label>
                            </div>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('admin.members.index') }}" 
                               class="px-6 py-3 bg-gray-500 text-white rounded-md hover:bg-gray-600 shadow-sm transition-colors">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-colors">
                                <i class="fas fa-save mr-2"></i> Simpan Anggota
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeRadios = document.querySelectorAll('input[name="type"]');
            const nisField = document.getElementById('nis-field');
            const nipField = document.getElementById('nip-field');
            const classField = document.getElementById('class-field');
            const enrollmentField = document.getElementById('enrollment-field');

            function toggleFields() {
                const selectedType = document.querySelector('input[name="type"]:checked').value;
                
                if (selectedType === 'student') {
                    nisField.classList.remove('hidden');
                    nipField.classList.add('hidden');
                    classField.classList.remove('hidden');
                    enrollmentField.classList.remove('hidden');
                    
                    // Make NIS optional, NIP disabled
                    document.getElementById('nis').removeAttribute('disabled');
                    document.getElementById('nip').setAttribute('disabled', true);
                    document.getElementById('class_id').removeAttribute('disabled');
                    document.getElementById('enrollment_year').removeAttribute('disabled');
                } else {
                    nisField.classList.add('hidden');
                    nipField.classList.remove('hidden');
                    classField.classList.add('hidden');
                    enrollmentField.classList.add('hidden');
                    
                    // Make NIP optional, NIS disabled
                    document.getElementById('nis').setAttribute('disabled', true);
                    document.getElementById('nip').removeAttribute('disabled');
                    document.getElementById('class_id').setAttribute('disabled', true);
                    document.getElementById('enrollment_year').setAttribute('disabled', true);
                }
            }

            // Initial toggle
            toggleFields();

            // Add event listeners
            typeRadios.forEach(radio => {
                radio.addEventListener('change', toggleFields);
            });
        });
    </script>
    @endpush
</x-app-layout>