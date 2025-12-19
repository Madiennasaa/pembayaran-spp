<?php

namespace App\Exports;

use App\Models\Murid;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MuridExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * Mengambil data dari database
    */
    public function collection()
    {
        return Murid::all();
    }

    /**
     * Mengatur Header (Judul Kolom) di Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'NISN',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tempat, Tanggal Lahir',
            'Alamat',
        ];
    }

    /**
     * Mengatur isi data per baris
     */
    public function map($murid): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $murid->nisn ?? '-',
            $murid->nama_lengkap,
            $murid->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan', 
            $murid->ttl, 
            $murid->alamat,
        ];
    }

    /**
     * Styling: Border Tabel, Warna Header, dan Posisi Teks
     */
    public function styles(Worksheet $sheet)
    {
        // 1. Menentukan Range Seluruh Tabel (Dari A1 sampai kolom terakhir & baris terakhir)
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();
        $range = 'A1:' . $lastColumn . $lastRow;

        return [
            // A. Style untuk SELURUH TABEL (Garis Tepi / Border)
            $range => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN, // Garis tipis
                        'color' => ['argb' => '000000'], // Warna hitam
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER, // Teks vertikal di tengah
                ],
            ],

            // B. Style KHUSUS HEADER (Baris 1)
            1 => [
                'font' => [
                    'bold' => true, 
                    'size' => 12,
                    'color' => ['argb' => 'FFFFFF'], // Teks Putih
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '4472C4'], // Background Biru Excel
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER, // Rata Tengah
                ],
            ],

            // C. Style KHUSUS Kolom 'No' (Kolom A) -> Rata Tengah
            'A' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],

            // D. Style KHUSUS Kolom 'Jenis Kelamin' (Kolom D) -> Rata Tengah
            'D' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}