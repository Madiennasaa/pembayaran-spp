<?php

namespace App\Exports;

use App\Models\Pemasukan; 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PemasukanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $search;
    protected $bulan;
    protected $tahun;

    public function __construct(?string $search = null, ?string $bulan = null, ?string $tahun = null)
    {
        $this->search = $search;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    /**
    * Mengambil data dari database dengan filter pencarian.
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Pemasukan::query()->with('murid')->where('status', 'lunas');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nisn', 'like', '%' . $this->search . '%')
                  ->orWhereHas('murid', function ($qq) {
                      $qq->where('nama_lengkap', 'like', '%' . $this->search . '%');
                  })
                  ->orWhere('tahun_spp', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->bulan) {
            $query->where('bulan_spp', $this->bulan);
        }

        if ($this->tahun) {
            $query->where('tahun_spp', $this->tahun);
        }

        return $query->get();
    }

    /**
     * Tentukan baris heading untuk file Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'NISN',
            'Periode',
            'Tanggal Bayar',
            'Jumlah',
            'Metode',
            'Status',
        ];
    }

    /**
     * Map data untuk setiap baris
     */
    public function map($pemasukan): array
    {
        // Gunakan static variabel untuk penomoran baris
        static $rowNumber = 0;
        $rowNumber++;

        // Format tanggal menggunakan Carbon
        $tanggalBayarFormatted = \Carbon\Carbon::parse($pemasukan->tanggal_bayar)->format('d/m/Y');
        
        return [
            $rowNumber,
            $pemasukan->murid->nama_lengkap ?? 'Siswa Terhapus',
            $pemasukan->nisn,
            $pemasukan->bulan_spp . ' ' . $pemasukan->tahun_spp,
            $tanggalBayarFormatted,
            $pemasukan->jumlah_bayar,
            strtoupper($pemasukan->metode_pembayaran),
            strtoupper($pemasukan->status),
        ];
    }

    /**
     * Styling: Border Tabel, Warna Header, dan Posisi Teks (Mirip MuridExport)
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
            
            // D. Style KHUSUS Kolom 'Jumlah' dan 'Metode' dan 'Status' -> Rata Tengah
            'F' => [ // Kolom F: Jumlah
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            'G' => [ // Kolom G: Metode
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            'H' => [ // Kolom H: Status
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
