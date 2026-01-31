<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Kelas Baru
            </h2>
            <a href="{{ route('classes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('classes.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="grade" class="block text-sm font-medium text-gray-700">Kelas *</label>
                            <select name="grade" id="grade" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                    required>
                                <option value="">Pilih Kelas</option>
                                <option value="10" {{ old('grade') == '10' ? 'selected' : '' }}>Kelas 10</option>
                                <option value="11" {{ old('grade') == '11' ? 'selected' : '' }}>Kelas 11</option>
                                <option value="12" {{ old('grade') == '12' ? 'selected' : '' }}>Kelas 12</option>
                            </select>
                            @error('grade')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="class_name" class="block text-sm font-medium text-gray-700">Nama Kelas *</label>
                            <input type="text" name="class_name" id="class_name" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   value="{{ old('class_name') }}"
                                   placeholder="Contoh: Gotong Royong, Medical Science A"
                                   required>
                            @error('class_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Nama khusus kelas (bukan "10A", tapi "Gotong Royong")</p>
                        </div>

                        <div class="mb-4">
                            <label for="academic_year" class="block text-sm font-medium text-gray-700">Tahun Ajaran *</label>
                            <select name="academic_year" id="academic_year" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                    required>
                                <option value="">Pilih Tahun Ajaran</option>
                                @for ($year = 2020; $year <= 2030; $year++)
                                    <option value="{{ $year }}" {{ old('academic_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}/{{ $year + 1 }}
                                    </option>
                                @endfor
                            </select>
                            @error('academic_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Tahun awal ajaran (contoh: 2024 untuk 2024/2025)</p>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded">
                                Simpan Kelas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>