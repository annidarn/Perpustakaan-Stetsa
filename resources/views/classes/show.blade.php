<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Kelas: {{ $class->class_name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('classes.edit', $class) }}" 
                   class="inline-flex items-center px-3 py-1.5 bg-amber-500 text-white rounded-md hover:bg-amber-600 shadow-sm transition-colors">
                    <i class="fas fa-edit mr-1"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('classes.index') }}" 
                   class="inline-flex items-center px-3 py-1.5 bg-gray-500 text-white rounded-md hover:bg-gray-600 shadow-sm transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Informasi Kelas -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold mb-4 text-gray-700">Informasi Kelas</h3>
                                <dl class="space-y-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500">Kelas</dt>
                                        <dd class="mt-1 text-2xl font-bold text-gray-900">
                                            <span class="px-3 py-1 bg-blue-500 text-black rounded-full">
                                                {{ $class->grade }}
                                            </span>
                                        </dd>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500">Nama Kelas</dt>
                                        <dd class="mt-1 text-xl font-semibold text-gray-900">
                                            {{ $class->class_name }}
                                        </dd>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500">Tahun Ajaran</dt>
                                        <dd class="mt-1 text-lg text-gray-900">
                                            {{ $class->academic_year }}/{{ $class->academic_year + 1 }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                            
                            <!-- Statistik -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4 text-gray-700">Statistik</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                                        <div class="text-2xl font-bold text-blue-700">
                                            {{ $class->members()->count() }}
                                        </div>
                                        <div class="text-sm text-blue-600">Total Anggota</div>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-lg text-center">
                                        <div class="text-2xl font-bold text-green-700">
                                            {{ $class->members()->where('status', 'active')->count() }}
                                        </div>
                                        <div class="text-sm text-green-600">Aktif</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Daftar Anggota (jika ada) -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4 text-gray-700">Daftar Anggota</h3>
                            @if($class->members()->count() > 0)
                                <div class="bg-gray-50 rounded-lg p-4 max-h-80 overflow-y-auto">
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($class->members()->with('user')->get() as $member)
                                        <li class="py-3 flex justify-between items-center">
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    {{ $member->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    NIS: {{ $member->nis ?: '-' }}
                                                    • {{ ucfirst($member->type) }}
                                                </div>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                      {{ $member->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $member->status }}
                                            </span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="bg-gray-50 rounded-lg p-8 text-center">
                                    <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                                    <p class="text-gray-500">Belum ada anggota di kelas ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Tombol Hapus -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <form action="{{ route('classes.destroy', $class) }}" method="POST" 
                              onsubmit="return confirm('Hapus kelas {{ $class->class_name }}? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 shadow-sm transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                <span>Hapus Kelas</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    @endpush
</x-app-layout>