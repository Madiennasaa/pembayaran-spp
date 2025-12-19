<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    // Nama tabel di database (Sesuai dengan migration Anda: 'pembayarans')
    protected $table = 'pembayarans';

    // Kolom yang boleh diisi (Mass Assignable)
    protected $fillable = [
        'nisn',
        'bulan_spp',
        'tahun_spp',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_pembayaran',
        'bukti_transfer',
        'status', // 'pending', 'lunas', 'ditolak'
        'alasan_penolakan',
    ];

    // Relasi ke Model Murid (many-to-one)
    public function murid()
    {
        return $this->belongsTo(Murid::class, 'nisn', 'nisn');
    }
}
