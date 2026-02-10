<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Anggota: {{ $member->user->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('members.show', $member) }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('members.update', $member) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nama Lengkap -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name', $member->user->name) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Status -->
                        <div class="mb-6">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_verified" value="1" 
                                       {{ $member->user->email_verified_at ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm font-medium text-gray-700">Email Utama Terverifikasi</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-6">
                                Centang ini jika Anda ingin mengaktifkan akun member tanpa perlu verifikasi email manual.
                            </p>
                        </div>

                        <!-- Jenis Anggota (readonly) -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Anggota
                            </label>
                            <div class="p-3 bg-gray-50 rounded-md">
                                <span class="font-medium">
                                    {{ $member->type === 'student' ? 'Siswa' : 
                                       ($member->type === 'teacher' ? 'Guru' : 'Staff') }}
                                </span>
                                <p class="text-sm text-gray-500 mt-1">
                                    Jenis anggota tidak dapat diubah untuk menjaga konsistensi data.
                                </p>
                            </div>
                        </div>

                        <!-- NIS/NIP Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- NIS Field -->
                            <div>
                                <label for="nis" class="block text-sm font-medium text-gray-700 mb-2">
                                    NIS (Nomor Induk Siswa)
                                </label>
                                <input type="text" name="nis" id="nis" 
                                       value="{{ old('nis', $member->nis) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                       {{ $member->type !== 'student' ? 'disabled' : '' }}>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $member->type === 'student' ? 'Nomor Induk Siswa' : 'Hanya untuk siswa' }}
                                </p>
                                @error('nis')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- NIP Field -->
                            <div>
                                <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">
                                    NIP (Nomor Induk Pegawai)
                                </label>
                                <input type="text" name="nip" id="nip" 
                                       value="{{ old('nip', $member->nip) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                       {{ $member->type === 'student' ? 'disabled' : '' }}>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $member->type !== 'student' ? 'Nomor Induk Pegawai' : 'Hanya untuk guru/staff' }}
                                </p>
                                @error('nip')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Class Field (only for students) -->
                        @if($member->type === 'student')
                        <div class="mb-6">
                            <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Kelas
                            </label>
                            <select name="class_id" id="class_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="">Pilih Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" 
                                            {{ old('class_id', $member->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->grade }} {{ $class->class_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        <!-- Enrollment Year (only for students) -->
                        @if($member->type === 'student')
                        <div class="mb-6">
                            <label for="enrollment_year" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Masuk
                            </label>
                            <input type="number" name="enrollment_year" id="enrollment_year" 
                                   value="{{ old('enrollment_year', $member->enrollment_year) }}"
                                   min="2000" max="{{ date('Y') + 1 }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            @error('enrollment_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        <!-- Personal Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Telepon
                                </label>
                                <input type="tel" name="phone" id="phone" 
                                       value="{{ old('phone', $member->phone) }}"
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
                                    <option value="L" {{ old('gender', $member->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender', $member->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
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
                                           {{ old('status', $member->status) == 'active' ? 'checked' : '' }}>
                                    <div>
                                        <span class="font-medium">Aktif</span>
                                        <p class="text-sm text-gray-500">Dapat meminjam buku</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="status" value="inactive" 
                                           class="mr-3"
                                           {{ old('status', $member->status) == 'inactive' ? 'checked' : '' }}>
                                    <div>
                                        <span class="font-medium">Non-Aktif</span>
                                        <p class="text-sm text-gray-500">Tidak dapat meminjam</p>
                                    </div>
                                </label>
                                @if($member->type === 'student')
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="status" value="graduated" 
                                           class="mr-3"
                                           {{ old('status', $member->status) == 'graduated' ? 'checked' : '' }}>
                                    <div>
                                        <span class="font-medium">Lulus</span>
                                        <p class="text-sm text-gray-500">Siswa yang telah lulus</p>
                                    </div>
                                </label>
                                @endif
                            </div>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hidden type field -->
                        <input type="hidden" name="type" value="{{ $member->type }}">

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('members.show', $member) }}" 
                               class="px-6 py-3 bg-gray-500 text-white rounded-md hover:bg-gray-600 shadow-sm transition-colors">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-colors">
                                <i class="fas fa-save mr-2"></i> Update Data Anggota
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
            // Disable NIS/NIP based on member type
            const memberType = "{{ $member->type }}";
            
            if (memberType !== 'student') {
                document.getElementById('nis').setAttribute('disabled', true);
            }
            
            if (memberType === 'student') {
                document.getElementById('nip').setAttribute('disabled', true);
            }
        });
    </script>
    @endpush
</x-app-layout>