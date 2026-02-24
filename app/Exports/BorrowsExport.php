<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BorrowsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $borrows;

    public function __construct($borrows)
    {
        $this->borrows = $borrows;
    }

    public function collection()
    {
        return $this->borrows;
    }

    public function headings(): array
    {
        return [
            'Kode Peminjaman',
            'Nama Anggota',
            'Judul Buku',
            'Tanggal Pinjam',
            'Jatuh Tempo',
            'Tanggal Kembali',
            'Status',
            'Denda',
            'Status Denda'
        ];
    }

    public function map($borrow): array
    {
        $statusLabels = [
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
        ];

        $status = $statusLabels[$borrow->status] ?? $borrow->status;

        return [
            $borrow->borrow_code,
            $borrow->member->user->name,
            $borrow->bookCopy->book->title,
            $borrow->borrow_date->format('d/m/Y'),
            $borrow->due_date->format('d/m/Y'),
            $borrow->return_date ? $borrow->return_date->format('d/m/Y') : '-',
            $status,
            number_format($borrow->fine_amount, 0, ',', '.'),
            $borrow->fine_paid ? 'Lunas' : ($borrow->fine_amount > 0 ? 'Belum Bayar' : '-')
        ];
    }
}
