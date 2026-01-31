<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman - Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white !important;
                padding: 0 !important;
            }
            
            .receipt-container {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
            }
        }
        
        .receipt-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 0 auto;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .barcode-placeholder {
            background: repeating-linear-gradient(
                90deg,
                #000,
                #000 1px,
                transparent 1px,
                transparent 20px
            );
            height: 60px;
            position: relative;
        }
        
        .barcode-placeholder::after {
            content: attr(data-code);
            position: absolute;
            bottom: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-family: monospace;
            letter-spacing: 5px;
        }
    </style>
</head>
<body class="bg-gray-100 p-4 md:p-8 min-h-screen flex items-center justify-center">
    <div class="receipt-container w-full p-6">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="logo mx-auto mb-3">
                <i class="fas fa-book text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Perpustakaan Digital</h1>
            <p class="text-gray-600 text-sm">Struk Peminjaman Buku</p>
        </div>
        
        <!-- Transaction Info -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <div class="text-left">
                    <div class="text-sm text-gray-500">Kode Transaksi</div>
                    <div class="text-lg font-bold text-gray-800 font-mono">
                        {{ $borrow->borrow_code }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Tanggal</div>
                    <div class="font-medium">
                        {{ $borrow->borrow_date->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
            
            <!-- Barcode Placeholder -->
            <div class="barcode-placeholder mb-2" data-code="{{ $borrow->borrow_code }}"></div>
            <div class="text-center text-xs text-gray-500 mb-6">
                Kode: {{ $borrow->borrow_code }}
            </div>
        </div>
        
        <!-- Member Info -->
        <div class="border-t border-b border-gray-200 py-4 mb-4">
            <h3 class="font-bold text-gray-700 mb-2 flex items-center">
                <i class="fas fa-user mr-2 text-blue-500"></i>
                Informasi Anggota
            </h3>
            <table class="w-full text-sm">
                <tr>
                    <td class="py-1 text-gray-600">Nama</td>
                    <td class="py-1 font-medium">: {{ $borrow->member->user->name }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-600">ID</td>
                    <td class="py-1 font-mono">: 
                        @if($borrow->member->type === 'student')
                            {{ $borrow->member->nis }}
                        @else
                            {{ $borrow->member->nip }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-600">Jenis</td>
                    <td class="py-1">: 
                        {{ $borrow->member->type === 'student' ? 'Siswa' : 
                           ($borrow->member->type === 'teacher' ? 'Guru' : 'Staff') }}
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Book Info -->
        <div class="border-b border-gray-200 py-4 mb-4">
            <h3 class="font-bold text-gray-700 mb-2 flex items-center">
                <i class="fas fa-book mr-2 text-green-500"></i>
                Informasi Buku
            </h3>
            <table class="w-full text-sm">
                <tr>
                    <td class="py-1 text-gray-600">Judul</td>
                    <td class="py-1 font-medium">: {{ $borrow->bookCopy->book->title }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-600">Pengarang</td>
                    <td class="py-1">: {{ $borrow->bookCopy->book->author }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-600">Copy #</td>
                    <td class="py-1 font-mono">: {{ str_pad($borrow->bookCopy->inventory_number, 5, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-600">ISBN</td>
                    <td class="py-1">: {{ $borrow->bookCopy->book->isbn ?? '-' }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Loan Details -->
        <div class="mb-6">
            <h3 class="font-bold text-gray-700 mb-3 flex items-center">
                <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>
                Detail Peminjaman
            </h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-sm text-gray-500">Pinjam</div>
                        <div class="font-bold text-gray-800">
                            {{ $borrow->borrow_date->format('d/m/Y') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Kembali</div>
                        <div class="font-bold text-red-600">
                            {{ $borrow->due_date->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-3 py-1 rounded-full">
                        <i class="fas fa-clock mr-1"></i>
                        Jangka waktu: 7 hari
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Important Notes -->
        <div class="border border-yellow-200 bg-yellow-50 p-4 rounded-lg mb-6">
            <h4 class="font-bold text-yellow-800 mb-2 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Catatan Penting
            </h4>
            <ul class="text-sm text-yellow-700 space-y-1">
                <li class="flex items-start">
                    <i class="fas fa-circle text-xs mt-1 mr-2"></i>
                    Kembalikan sebelum: <span class="font-bold">{{ $borrow->due_date->format('d F Y') }}</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-circle text-xs mt-1 mr-2"></i>
                    Denda keterlambatan: <span class="font-bold">Rp 1.000/hari</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-circle text-xs mt-1 mr-2"></i>
                    Perpanjangan: maksimal 1x (7 hari tambahan)
                </li>
                <li class="flex items-start">
                    <i class="fas fa-circle text-xs mt-1 mr-2"></i>
                    Simpan struk ini sebagai bukti peminjaman
                </li>
            </ul>
        </div>
        
        <!-- Footer -->
        <div class="text-center text-sm text-gray-500 border-t border-gray-200 pt-4">
            <p>Terima kasih telah menggunakan layanan perpustakaan</p>
            <p class="mt-1">Struk ini sah dan dapat digunakan sebagai bukti</p>
            <div class="mt-3 text-xs">
                Dicetak: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
        
        <!-- Action Buttons (Non-print) -->
        <div class="mt-6 flex flex-col sm:flex-row gap-3 no-print">
            <button onclick="window.print()" 
                    class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 flex items-center justify-center">
                <i class="fas fa-print mr-2"></i> Cetak Struk
            </button>
            <a href="{{ route('terminal.index') }}" 
               class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 flex items-center justify-center">
                <i class="fas fa-home mr-2"></i> Kembali
            </a>
            <a href="{{ route('terminal.search') }}" 
               class="flex-1 border border-green-300 text-green-700 py-3 rounded-lg hover:bg-green-50 flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i> Pinjam Lagi
            </a>
        </div>
    </div>

    <script>
        // Auto print option (optional)
        // setTimeout(() => {
        //     if (confirm('Cetak struk peminjaman?')) {
        //         window.print();
        //     }
        // }, 1000);
    </script>
</body>
</html>